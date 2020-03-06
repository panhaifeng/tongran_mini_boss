<?php
/*********************************************************************\
*  Copyright (c) 2007-2019, TH. All Rights Reserved.
*  Author :li
*  FName  :Api_Lib_Rsp_Mini_genSign.php
*  Time   :2019年8月26日
*  Remark :小程序需要用的签名算法，简化
\*********************************************************************/
class Api_Lib_Rsp_Mini_genSign {

    //加密算法
    public function gen_sign($params=array() ,$token = '' ,& $service)
    {
        $time = abs(time() - $params['timestamp']);

        if($time > 300 ){
            $service->send_user_error('timestamp is error');
        }

        $tmpStr = $params['timestamp'] . '&' . $params['method'] . '&' . $params['version'] . '&' . $token;

         //白名单验证
        if(isset($params['api_url']) && !isset($params['debugger'])){
            $res = $this->urlWhite($params['api_url']);
            if(!$res){
                $msg = "url '{$params['api_url']}' connection failed";
                $service->send_user_error($msg ,array('errmsg'=>$msg));
            }
        }

        return md5($tmpStr);
    }

    /**
     * 白名单处理
     * Time：2019/09/06 15:27:50
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function urlWhite($url){
        $array = array(
            'eqinfo.com.cn',
            '47.97.21.230',
            '120.26.58.61',
            '139.196.23.87',
        );

        $verify = false;
        //验证是否存在白名单中
        foreach ($array as & $v) {
            $pos = strpos($url, $v);
            if($pos !== false && $pos >= 0 && $pos <= 20){
                $verify = true;
                break;
            }
        }
        //如果不在白名单中，则返回错误
        return $verify;
    }
}
?>