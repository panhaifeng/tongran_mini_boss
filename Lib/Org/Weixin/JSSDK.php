<?php
FLEA::org('Weixin/Base.php');
class Weixin_JSSDK extends Weixin_Base {
    private $appId;
    private $appSecret;

    public function __construct($appId, $appSecret) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    public function getSignPackage() {
        $jsapiTicket = $this->get_jsapi_ticket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function get_jsapi_ticket() {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $_file_session = 'weixin_eqinfo.txt';
        $_txt_session = unserialize(file_get_contents($_file_session));

        // dump($_txt_session);exit;
        if($_txt_session['wx_jsapi_ticket'] && time() <= $_txt_session['wx_jsapi_ticket_time']) {
            return $_txt_session['wx_jsapi_ticket'];
        } else {
            //获取access_token
            $accessToken = $this->get_access_token($this->appId,$this->appSecret);
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$accessToken;
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            //放入session
            $_SESSION['wx_jsapi_ticket'] = $ticket;
            $_SESSION['wx_jsapi_ticket_time'] = time() + 7000;

            $_txt_session['wx_jsapi_ticket'] = $ticket;
            $_txt_session['wx_jsapi_ticket_time'] = $_SESSION['wx_jsapi_ticket_time'];
            //放到缓存中
            file_put_contents($_file_session, serialize($_txt_session));

            return $ticket;
        }
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

}