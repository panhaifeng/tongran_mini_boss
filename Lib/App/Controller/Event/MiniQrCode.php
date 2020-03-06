<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  Author :lwj
*  FName  :Shenhe.php
*  Time   :2019年5月7日
*  Remark :小程序需要扫码绑定的二维码生成和验证是否合法
\*********************************************************************/
FLEA::loadClass('TMIS_Controller');
class Controller_Event_MiniQrCode extends TMIS_Controller {

    function __construct() {
        $this->_token = "da1f5E8ftbCbGtoBtd67afza7sdf89a5dqv0Ladf5aKd9";
    }

    /**
     * 生成二维码
     * Time：2019/08/28 13:57:46
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function buildQrCode($params = array()){
        FLEA::loadClass('TMIS_Input');
        $string = $params['timestamp'].'-'.$params['platfrom'].'-'.$params['userName'];
        $params['sign'] = TMIS_Input::verifyTokenString($string ,$this->_token);

        FLEA::org('phpqrcode/phpqrcode.php');
        $errorCorrectionLevel = 'L';//容错级别
        $matrixPointSize = 5;//生成图片大小

        $str_params = json_encode($params);
        $str_params = url('Event_MiniQrCode','showError')."&params=".$str_params;
        // dump($str_params);exit;
        $qrcode = QRcode::png($str_params,false,$errorCorrectionLevel,$matrixPointSize,2);
    }

    /**
     * 验证二维码有效性
     * Time：2019/08/28 13:58:07
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function verifyQrCode($params){
        FLEA::loadClass('TMIS_Input');
        $string = $params['timestamp'].'-'.$params['platfrom'].'-'.$params['userName'];
        $sign = TMIS_Input::verifyTokenString($string ,$this->_token);
        if($sign == $params['sign']){
            return true;
        }else{
            return false;
        }
    }

    /**
     * ps ：防止微信扫描后出现问题，这里直接跳转一个地址，让微信不好直接显示字符串，搞的客户一头雾水
     * Time：2019/09/04 15:36:21
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function actionShowError(){
        echo '<meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />';
        echo "请进入“易奇筒染”小程序，再扫描该二维码登录账号";
        exit;
    }
}

?>