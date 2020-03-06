<?php
class Api_Lib_Rsp_Test {

    function __construct() {
        $this->path = array();
    }

    function runTest($params,& $service){
        $service->send_user_error('ERROR_DATA' ,array(2));
        return array(1,2,3,4,5);
    }
}
?>