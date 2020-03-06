<?php
FLEA::loadClass('TMIS_TableDataGateway');
class Model_Acm_Qrcodeverify extends TMIS_TableDataGateway {
    var $tableName = 'acm_qrcodeverify';
    var $primaryKey = 'id';
    var $needCreateLog = false;

    // 创建二维码验证记录
    function createRecord($userName){
        if(!$userName){
          return false;
        }

        $time = time();
        $token = md5($time.'_'.$userName);

        FLEA::loadClass('TMIS_Common');
        $compCode = TMIS_Common::getCompCode();

        // 项目地址  $_SERVER['REQUEST_SCHEME'].
        $projectAdd = 'http://'.$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'], 0, strripos($_SERVER['PHP_SELF'], '/'));
        $arr = array(
              'token'      => $token,
              'userName'   => $userName,
              'compCode'   => $compCode,
              'timestamp'  => $time,
              'projectAdd' => $projectAdd,
              'status'     => 'CREATED',
        );
        $this->save($arr);
        return $token;
    }
}
?>