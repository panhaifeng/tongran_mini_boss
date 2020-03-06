<?php
class Controller_Crontab{
    function __construct() {
        $this->_modelExample = FLEA::getSingleton('Model_Crontab');

    }

    //执行保存log
    function actionRun(){
        require "Config/crontab_list.php";
        // dump($crontab_list);exit;
        foreach ($crontab_list as $key => & $v) {
            //未启用的直接pass
            if(strval($v['enabled']) != '1')continue;
            if(!$v['action'])continue;
            //查看action
            $this->_modelExample->parseList($v ,$v['param']);
        }
    }

    //测试，不要使用
    function clearLog(){
        $this->_modelExample->deleteLog();
        return 'finish';
    }

    /**
     * 只调用函数，不等待返回值，避免一个队列卡住导致其他队列死掉的问题
     * Time：2017/11/23 10:17:03
     * @author li
    */
    function actionRunQueueList(){

        //写入缓存
        $startTime = time();
        FLEA::writeCache('Crontab.Queue.StartLast' ,$startTime);

        //获取当前内存情况
        // $nowMemory = memory_get_usage();
        // echo $nowMemory/1024/1024;exit;
        //查找待执行任务
        $condition['result'] = '未执行';
        $condition['enabled'] = '1';
        $condition[] = array('runtime',time(),'<=');
        $rows = $this->_modelExample->findAll($condition ,'id' ,100);
        file_put_contents('_Cache/Crontab/'.date('ymd').'.log', print_r($rows,1),FILE_APPEND);

        $fromplat = strtoupper(substr(PHP_OS,0,3));
        //开始执行
        foreach ($rows as $key => & $v) {
            //判断是否开始了下次任务执行，如果下次执行任务已经开始，避免执行重复，停止该次任务执行
            //判断一句是开始时间和缓存时间，如果缓存时间被更新，则表示该次执行需要停止
            $cacheStartTime = FLEA::getCache('Crontab.Queue.StartLast' ,-1);
            if($cacheStartTime && $cacheStartTime > $startTime){
                break;
            }

            $cmd = 'php -f '.ROOT_DIR_QUEUE.DO_QUEUE." {$v['id']} {$startTime} ";
            echo ($key+1).' => '.$cmd," \r\n";
            file_put_contents('_Cache/Crontab/'.date('ymd').'.log', ($key+1).' => '.$cmd,FILE_APPEND);
            if($fromplat == 'WIN')
            {
                pclose(popen('start /B '.$cmd.' & exit', 'r'));
            }else
            {
                pclose(popen($cmd.' > /dev/null &', 'r'));
            }

            usleep(155000);
        }
        echo time() - $startTime;
        exit;

    }


    //执行计划
    function actionRunQueue(){
        ignore_user_abort(1);
        set_time_limit(0);
        //记录开始队列执行的时间点
        $startTime = time();

        //查找待执行任务
        if(defined('QUEUE_ID') && QUEUE_ID>0){
            $condition['id'] = QUEUE_ID;
            $doById = true;
            $condition[] = array('result','已执行','<>');
        }else{
            $condition['result'] = '未执行';
        }

        $condition['enabled'] = '1';
        $condition[] = array('runtime',time(),'<=');


        $rows = $this->_modelExample->findAll($condition ,'id' ,100);

        //判断时间戳是否被延迟，如果已经被延迟，则不处理了，防止重复处理
        if(defined('QUEUE_TIME') && QUEUE_TIME>0){
            $startTime = QUEUE_TIME;
        }
        file_put_contents('_Cache/Crontab/'.date('ymd').'.log', 'doQueue+++++'.print_r($rows,1),FILE_APPEND);
        //开始执行
        foreach ($rows as $key => & $v) {
            //判断是否开始了下次任务执行，如果下次执行任务已经开始，避免执行重复，停止该次任务执行
            //判断一句是开始时间和缓存时间，如果缓存时间被更新，则表示该次执行需要停止
            $cacheStartTime = FLEA::getCache('Crontab.Queue.StartLast' ,-1);
            if($cacheStartTime && $startTime && $cacheStartTime > $startTime){
                break;
            }

            //更新结果：执行中
            $arr = array(
                'id'       =>$v['id'],
                'result'   =>'执行中',
            );
            $this->_modelExample->update($arr);

            //开始执行函数
            list($controller ,$action) = explode('@',$v['action']);
            $class = FLEA::getSingleton($controller);

            if(!method_exists($class, $action)) {
                continue;
            }
            try{
                $params = unserialize($v['param']) ? unserialize($v['param']) : array();
                $runResult = $class->$action($params);
                $exception = false;
            }catch(Exception $e){
                $runResult = $e->getMessage();
                $exception = true;
            }

            //如果是执行的单条并且顺利执行完成的，则直接删除该任务
            if(defined('QUEUE_ID') && QUEUE_ID>0 && $exception==false){

                // $runResult = serialize(array('response'=>$runResult));
                // //更新执行之后的结果
                // $arr = array(
                //     'id'         =>$v['id'],
                //     'result'     =>'已执行',
                //     'updatetime' =>time(),
                //     'response'   =>$runResult
                // );
                // $this->_modelExample->update($arr);
                $v['response'] = $runResult;
                file_put_contents('_Cache/Crontab/'.date('ymd').'.log', date('Y-m-d H:i:s').'=>'.print_r($v,1),FILE_APPEND);
                $this->_modelExample->removeByPkv($v['id']);
            }else{
                //把执行结果放在array中
                $runResult = serialize(array('response'=>$runResult));
                //更新执行之后的结果
                $arr = array(
                    'id'         =>$v['id'],
                    'result'     =>'已执行',
                    'updatetime' =>time(),
                    'response'   =>$runResult
                );
                $this->_modelExample->update($arr);
            }

        }

        die();

    }



}
?>