<?php
/*********************************************************************\
*  Copyright (c) 2007-2015, TH. All Rights Reserved.
*  Author :li
*  FName  :Image.php
*  Time   :2019/07/22 12:38:39
*  Remark :图片上传功能服务断
\*********************************************************************/
class Controller_Apply_Qrcode extends FLEA_Controller_Action{

    function __construct() {

    }

    //展示二维码图片
    function actionBuild(){
        //判断参数是否都有
        $params = array(
            'compName'  => $_GET['compName'],
            'platfrom'  => $_GET['platfrom'],
            'timestamp' => $_GET['timestamp'] ? $_GET['timestamp'] : time(),
            'userName'  => $_GET['userName'],
            'token'     => $_GET['token']
        );

        // dump($params);exit;
        if(!$params['compName']){
            echo "缺少参数compName";
            exit;
        }
        if(!$params['platfrom']){
            echo "缺少参数platfrom";
            exit;
        }
        if(!$params['userName']){
            echo "缺少参数userName";
            exit;
        }
        if(!$params['token']){
            echo "缺少参数token";
            exit;
        }
        //验证token是否合法
        $token = sha1($params['platfrom'].'+'.$params['userName'].'+'.$_GET['timestamp']);
        if($token != $params['token']){
            echo "参数token不正确";
            exit;
        }
        unset($params['token']);

        //调用生成的类
        $qrCode = FLEA::getSingleton('Controller_Event_MiniQrCode');
        $qrCode->buildQrCode($params);


        //日志
        sysDbLog(array_merge(array('error_message'=>$error_message),$params),'Model_Jichu_Member','生成绑定小程序的二维码图片');
    }

    /**
     * 筒染的小程序二维码
     * Time：2019/08/30 14:49:49
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function actionMiniQrcode(){

        $img = "Resource/Image/qrcode/tongran_boss.png";

        Header("Content-type: image/jpeg");
        $imgInfo = imagecreatefromjpeg($img);
        imagejpeg($imgInfo);
        imagedestroy($imgInfo);
    }
}
?>