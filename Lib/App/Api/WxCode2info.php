<?php
/*********************************************************************\
*  Copyright (c) 2007-2015, TH. All Rights Reserved.
*  Author :li
*  FName  :Api_Lib_Rsp_Ding_Base.php
*  Time   :2018/12/24 08:57:42
*  Remark :小程序对接
\*********************************************************************/
class Api_WxCode2info{
    /**
     * 基础配置
     * @var 参数类型
    */
    var $appid = 'wxc16509bd3ec7baab';
    var $secret = '6d95bcdc8b9656b9f28337751440aca9';
    var $miniKey = "tongran.mini";

    function __construct() {

    }

    /**
     * 获取access_token
     * Time：2019/08/26 16:20:13
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    public function getAccessToken() {
        $miniCacheId = $this->miniKey.".mini.access.token.".$this->appid;
        //先从缓存中获取
        $accesstoken = FLEA::getCache($miniCacheId ,7100);
        if($accesstoken){
            return $accesstoken;
        }else{
             //组织Url
            $url ="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->secret}";
            $response = $this->get(array(
                'url'=>$url
            ));

            FLEA::writeCache($miniCacheId ,$response['body']['access_token']);
            return $response['body']['access_token'].'';
        }
    }

    /**
     * code2Session 换取openid session_key
     * Time：2019/08/26 16:40:03
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function code2openid($code ,$allRes = false){
        $url ="https://api.weixin.qq.com/sns/jscode2session?appid={$this->appid}&secret={$this->secret}&js_code={$code}&grant_type=authorization_code";
        // echo $url;
        $response = $this->get(array(
            'url'=>$url
        ));
        // dump($response);

        //暂时不用，所以不浪费地方存储了，等用了再放开 lwj
        // $miniCacheId = $this->miniKey.".mini.session_key.".$response['body']['openid'];
        // FLEA::writeCache($miniCacheId ,$response['body']['session_key']);

        if($allRes){
            return $response;
        }

        return $response['body']['openid'].'';
    }


    /**
     * @codeCoverageIgnore
     */
    public function get($options) {
        $options['method'] = 'GET';
        $options['https']  = true;
        return self::send($options);
    }

    public function jsonPost($options) {
        if (isset($options['data'])) {
            $options['data'] = json_encode($options['data']);
        }

        $options = array_merge_recursive($options, array(
            'method'  => 'POST',
            'https'   => true,
            'headers' => array('Content-Type: application/json; charset=utf-8'),
        ));

        return self::send($options);
    }

    public function send($options) {
        $ch = curl_init();

        if(strtolower($options['method']) == 'post'){
            curl_setopt($ch, CURLOPT_POST, true);
        }
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $options['method']);
        curl_setopt($ch, CURLOPT_URL, $options['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // curl_setopt($ch, CURLOPT_TIMEOUT_MS, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if($options['https'] == true){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  //验证主机
        }

        if (isset($options['headers'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
        }

        if (isset($options['data'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['data']);
        }

        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // dump($result);
        $body = json_decode($result, TRUE);
        if ($body === NULL) {
            $body = $result;
        }

        curl_close($ch);
        return compact('status', 'body');
    }


}
?>