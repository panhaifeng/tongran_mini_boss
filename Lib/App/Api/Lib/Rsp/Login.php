<?php
class Api_Lib_Rsp_Login {

    function __construct() {
         $this->_model = FLEA::getSingleton('Model_Acm_Qrcodeverify');
    }

    function runTest($params,& $service){
        $service->send_user_error('ERROR_DATA' ,array(2));
        return array(1,2,3,4,5);
    }

    /**
     * @desc ：二维码验证 回写验证状态
     * Time：2019/07/17 14:17:33
     * @author Wuyou
    */
    function callback($params = array(),& $service){
        $params = $params['params'];
        $row = $this->_model->find(array('token'=>$params['token']));
        if($row['id'] > 0){
            $_data = array(
                'id'      =>$row['id'],
                'status'  =>$params['status'],
                'message' =>$params['message'],
            );
            $this->_model->update($_data);
        }else{
            $service->send_user_error('ERROR_TOKEN:未找到token相关的验证记录');
        }

        // __TRY();

        // $row['id'] && $this->_model->update($_data);

        // $ex = __CATCH();
        // if (__IS_EXCEPTION($ex)) {
        //     $service->send_user_error('E4499:'.$ex->getMessage());
        // }

        $data = array(
            'success'   => true,
            'msg'       => '请求成功',
            'errorcode' => '0',
        );

        return $data;
    }
}
?>