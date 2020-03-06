<?php
load_class('TMIS_TableDataGateway');
class Model_Api_Logs extends TMIS_TableDataGateway {
    var $tableName = 'api_logs';
    var $primaryKey = 'apilog_id';

    var $certiVer = '';


    /**
     * 标准的请求api的日志
     * Time：2018/03/26 13:37:14
     * @author li
    */
    public function requestLog($params=array(), $rpc_id = '', $taskname = '',$re_request = false)
    {
        $time = time();
        $ip = '127.0.0.1';

        if ($re_request == false) {
            empty($rpc_id) && $rpc_id = $this->_gen_rpc_id();

            $version = $params['version'] ? $params['version'] : $params['v'];
            $data = array(
                'rpc_id'        => $rpc_id,
                'calltime'      => $time,
                'params'        => serialize($params),
                'version'       => $version.'',
                'api_type'      => 'request',
                'status'        => 'sending',
                'method'        => $params['method'],
                'worker'        => $params['method'],
                'title'         => trim($taskname),
                'createtime'    => $time,
                'last_modified' => $time,
                'retry'         => 0,
                'ip'            => $ip,
            );
        } else {
            $sql = "SELECT apilog_id from api_logs where rpc_id='{$rpc_id}' limit 0,1";
            $row = $this->findBySql($sql);

            if($row){
                $row = $row[0];
                $retry = $row['retry'] + 1;
                $data = array(
                    'apilog_id'     => $row['apilog_id'],
                    'calltime'      => $time,
                    'createtime'    => $time,
                    'last_modified' => $time,
                    'retry'         => $retry,
                    'status'        => 'sending',
                    'ip'            => $ip,
                );
            }
        }

        $apilog_id = $this->save($data);
        return $rpc_id;
    }

    //生成rpc_id
    function _gen_rpc_id() {
        $_rand = rand(100000,999999);
        $rpc_id = uniqid().$_rand;
        return $rpc_id;
    }


    /**
     * 标准的请求api的日志
     * Time：2018/03/26 13:37:14
     * @author li
    */
    public function updateLog($params=array(), $rpc_id = '')
    {

        //查询主键
        $sql = "SELECT apilog_id,method,retry,status from api_logs where rpc_id='{$rpc_id}' limit 0,1";
        $row = $this->findBySql($sql);
        if(!$row){
            return false;
        }
        $row = $row[0];

        //开始处理更新的结果
        $data = $params;
        $data['createtime'] = time();
        $data['next_modified'] = 0;

        //处理下次重试时间

        if($this->certiVer!=''){
            include $this->certiVer;

            $defaultTime = $certKey['next_call_time'];
            $privateTime = $certKey['method'][$row['method']]['next_call_time'];
            $retry = $row['retry'];

            if($retry > -1 && $data['status'] == 'fail'){
                //设置重新请求的时间机制
                $time_list = $defaultTime;
                if(isset($certKey['method'][$row['method']]['next_call_time'])){
                    $time_list = $privateTime;
                }
                if(isset($time_list[$retry])){
                    $retrytime = 60 * $time_list[$retry];
                    $data['next_modified'] = time() + $retrytime;
                }
            }
        }


        $data['apilog_id'] = $row['apilog_id'];

        $data['apilog_id'] &&  $this->update($data);

        return true;
    }

    /**
     * 设置加载的配置文件
     */
    public function set_certiVer($certiVer) {
        $this->certiVer = $certiVer;
    }

    /**
     * 清空时间长的日志记录
     */
    function deleteLogs(){
        //日志保留50天
        $time = time() - (50*24*60*60);
        $sql = "delete from api_logs where calltime < '{$time}' ";
        return $this->execute($sql);
    }
}