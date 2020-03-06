<?php
load_class('TMIS_TableDataGateway');
class Model_Sys_Log extends TMIS_TableDataGateway {
	var $tableName = 'sys_log';
	var $primaryKey = 'id';
    //是否需要打日志，默认需要，标记false则不打
    var $needCreateLog = false;
    //标记需要打日志的模块名称，不必填
    var $moduleName = "系统日志";

    /**
     * 系统日志保存
     * Time：2017/12/15 13:08:41
     * @author li
     * @param $array array 需要打日志的参数
    */
    public function log($params = array() ,$model = '' ,$mdlName = '' ,$compare = false)
    {
        try{
            //判断是否需要log，不需要日志则直接返回false
            if(!defined('NEED_DB_LOG') ||  NEED_DB_LOG != true){
                return false;
            }

            //开始整理日志信息
            if(is_string($model)){
                $_model = FLEA::getSingleton($model);
            }elseif(is_object($model)){
                $_model = $model;
                $model = get_class($model);
            }else{
                return false;
            }

            //判断该model是否需要打日志
            if($_model->needCreateLog == false){
                return false;
            }


            // 获得操作人主机ip和主机名
            // $ip = gethostbyname($_ENV['COMPUTERNAME']); //获取本机的局域网IP
            // $pcName = gethostbyaddr($ip); //获取本机主机名
            if($_SERVER['REMOTE_ADDR'] != $ip){
                $ip .= /*"@".*/$_SERVER['REMOTE_ADDR'];
            }

            //如果没有设置mdlName
            if(!$mdlName){
                $mdlName = $_model->moduleName ? $_model->moduleName : $_model->tableName;
            }

            $params2 = $params;
            if($compare == true && is_array($params) && $params[$_model->primaryKey]){
                $oldData = $_model->find($params[$_model->primaryKey]);
                if($oldData){
                    $diff1 = $this->array_diff_assoc2_deep($params ,$oldData);
                    $diff2 = $this->array_diff_assoc2_deep($oldData ,$params);
                    $diff  = array_merge($diff1 ,$diff2);
                    unset($diff['Submit']);
                    unset($diff['fromAction']);
                    unset($diff['submitValue']);
                    unset($diff['fromController']);
                    $params2 = array(
                        '原值'=>$oldData,
                        '新值'=>$params2,
                        '差异'=>$diff,
                    );
                }
            }
            //开始整理model内容
            $log_data = array(
                'userId'       => $_SESSION['USERID'],
                'userName'     => $_SESSION['USERNAME'],
                'realName'     => $_SESSION['REALNAME'],
                'ip'           => $ip,
                'pcName'       => $pcName.'',
                'model'        => $model,
                'mdlName'      => $mdlName,
                'primaryKey'   => $_model->primaryKey,
                'primaryValue' => is_array($params) ? $params[$_model->primaryKey] : $params,
                'log'          => serialize($params2),
                'time'         => time(),
            );
            $this->save($log_data);
        }catch(Exception $e){
            #code
        }
    }

    //比对两个数组差异
    function array_diff_assoc2_deep($array1, $array2) {
        $ret = array();
        foreach ($array1 as $k => $v) {
            $tmp = array();
            if (!isset($array2[$k])){
                $ret[$k] = $v;
            }else if (is_array($v) && is_array($array2[$k])){
                $tmp = $this->array_diff_assoc2_deep($v, $array2[$k]);
                $tmp && $ret[$k] = $tmp;
            }
            else if ($v !=$array2[$k]) {
                $ret[$k] = $v;
            }else
            {
                unset($array1[$k]);
            }

        }
        return $ret;
    }

    //执行删除日志的动作
    function clearLog(){
        if(defined('NEED_DB_LOG_TIME') && NEED_DB_LOG_TIME > 0){
            $time = time() - (NEED_DB_LOG_TIME * 24 * 60 * 60);

            $sql = "DELETE FROM `sys_log` WHERE `time` <= '{$time}'";
            $this->execute($sql);
        }
    }

}
?>