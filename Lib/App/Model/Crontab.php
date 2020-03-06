<?php
load_class('TMIS_TableDataGateway');
class Model_Crontab extends TMIS_TableDataGateway {
    var $tableName = 'crontab';
    var $primaryKey = 'id';

    /**
     * 添加需要一分钟后就要执行的任务
     * Time：2017/11/01 12:42:36
     * @param:cron: array(
                        'delay'       =>0,//延迟执行时间，秒
                        'type'        =>'normal',//延迟执行时间，秒
                        'description' =>'描述内容',
                        'action'      =>'controller_test@test'//controller@action结构
                     )
     * @param param:array 执行的时候需要的参数
     * @author li
    */
    function publish($cron = array() ,$param = array()){
        $cur_time = time();
        $next_time = time() + intval($cron['delay']);

        $arr = array(
            'result'      =>'未执行',
            'description' =>$cron['description'],
            'action'      =>$cron['action'],
            'enabled'     =>'1',
            'runtime'     =>$next_time,
            'createtime'  =>$cur_time,
            'param'       =>serialize($param),
        );
        // dump($arr);

        $crontab_id = $this->save($arr);
        if($crontab_id > 0 && $cron['type'] == 'quick'){
            //执行队列，立即执行，准及时
            $this->doQueueRow($crontab_id);
        }
        return $crontab_id;
    }

    /**
     * 直接执行队列任务
     * Time：2018/07/19 12:52:19
     * @author li
    */
    function doQueueRow($cid = ''){
        if($cid){
            $startTime = time();
            $cmd = 'php -f '.ROOT_DIR_QUEUE.DO_QUEUE." {$cid} {$startTime} ";


            $fromplat = strtoupper(substr(PHP_OS,0,3));

            //file_put_contents('_Cache/Crontab/'.date('ymd').'.log','quick =>'.$cmd,FILE_APPEND);

            if($fromplat == 'WIN')
            {
                pclose(popen('start /B '.$cmd.' & exit', 'r'));
            }else
            {
                pclose(popen($cmd.' > /dev/null &', 'r'));
            }
        }
    }

    //开始添加
    function parseList($cron = array() ,$param = array()){
        //查找是否已经保存在表中
        $row = $this->find(array(
            'action'=>$cron['action'],
            'enabled'=>'1'
        ),'runtime desc');

        $cur_time = time();
        $prevTmp = 23*60*60;
        $last_time = $row['runtime']>$cur_time ? $row['runtime'] : 0;


        //如果最后一次执行时间大于当前时间，表示还没有执行
        if($last_time > $cur_time){
            return false;
        }
        //处理下次执行时间
        $next_time = $this->parse($cron['schedule'],$last_time);
        $minus_time = $next_time - $cur_time;

        // dump($row);
        // echo date('ymd H:i:s' ,$next_time),'<br>',date('ymd H:i:s' ,$last_time),'<br>',$minus_time,'<br>',date('ymd H:i:s' ,$cur_time),'<br>';
        if($minus_time >= 0 && $minus_time <= 60){

            //如果之前保存的信息未执行，则需要等待执行后再更新下次执行时间
            $arr = array(
                'result'      =>'未执行',
                'description' =>$cron['description'],
                'action'      =>$cron['action'],
                'enabled'     =>$cron['enabled'],
                'schedule'    =>$cron['schedule'],
                'runtime'     =>$next_time,
                'createtime'  =>$cur_time,
                'param'       =>serialize($param),
            );
            // dump($arr);exit;
            return $this->save($arr);
        }
    }
    /**
     * 分析下次执行时间
     * Time：2017/11/01 11:00:33
     * @author li
    */
    public function parse($_cron_string,$_after_timestamp=null)
    {
        $now = time();
        if(!preg_match('/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i',trim($_cron_string))){
            return false;
        }
        if($_after_timestamp && !is_numeric($_after_timestamp)){
            return false;
        }
        $cron   = preg_split("/[\s]+/i",trim($_cron_string));
        $start  = empty($_after_timestamp)? $now :$_after_timestamp;


        $date   = array(    'minutes'   =>$this->_parseCronNumbers($cron[0],0,59),
                            'hours'     =>$this->_parseCronNumbers($cron[1],0,23),
                            'dom'       =>$this->_parseCronNumbers($cron[2],1,31),
                            'month'     =>$this->_parseCronNumbers($cron[3],1,12),
                            'dow'       =>$this->_parseCronNumbers($cron[4],0,6),
                        );
        // limited to time()+366 - no need to check more than 1year ahead
        for($i=60;$i<=60*60*24*366;$i+=60){
            if( in_array(intval(date('j',$start+$i)),$date['dom']) &&
                in_array(intval(date('n',$start+$i)),$date['month']) &&
                in_array(intval(date('w',$start+$i)),$date['dow']) &&
                in_array(intval(date('G',$start+$i)),$date['hours']) &&
                in_array(intval(date('i',$start+$i)),$date['minutes'])

                ){
                    $resultTime = $start+$i;
                    /*if($resultTime < $now){
                        $resultTime = $now;
                    }*/
                    return $resultTime;
            }
        }
        return null;
    }
    //执行时间计算
    protected function _parseCronNumbers($s,$min,$max)
    {
        $result = array();

        $v = explode(',',$s);
        foreach($v as $vv){
            $vvv  = explode('/',$vv);
            $step = empty($vvv[1])?1:$vvv[1];
            $vvvv = explode('-',$vvv[0]);
            $_min = count($vvvv)==2?$vvvv[0]:($vvv[0]=='*'?$min:$vvv[0]);
            $_max = count($vvvv)==2?$vvvv[1]:($vvv[0]=='*'?$max:$vvv[0]);

            for($i=$_min;$i<=$_max;$i+=$step){
                $result[$i]=intval($i);
            }
        }
        ksort($result);
        return $result;
    }

    //清空过期的数据
    function deleteLog(){
        //删除1天之前完成的队列任务
        $daysago = time() - (1*24*60*60);
        $sql = "delete from crontab WHERE `result`='已执行' and `createtime` <= '{$daysago}'";
        $result = $this->dbo->execute($sql);
        return $result;
    }
}
?>