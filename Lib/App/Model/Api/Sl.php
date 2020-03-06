<?php
load_class('TMIS_TableDataGateway');
class Model_Api_Sl extends TMIS_TableDataGateway {
    var $tableName = 'response_sl2_logs';
    var $primaryKey = 'api_id';


    //生成rpc_id
    function _gen_rpc_id() {
        $_rand = rand(100000,999999);
        $rpc_id = uniqid().$_rand;
        return $rpc_id;
    }

    /**
     * 清空时间长的日志记录
     */
    function deleteLogs(){
        //日志保留30天
        $time = time() - (30*24*60*60);
        $sql = "delete from response_sl2_logs where calltime < '{$time}' and isCalc <> 'no'";
        return $this->execute($sql);
    }
}