<?php
class Weixin_Base {
    private $token;

    public function __construct($token) {
        $this->token = $token;
    }

    /**
     * weixin验证url
     * Time：2015/12/24 16:56:18
     * @author zhangyan
     * @param 参数类型
     * @return 返回值类型  http://lnx.eqinfo.com.cn/openWx.php/WXApp/valid
    */
    public function valid(){
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){ //调用验证字段
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        if (!$this->token) {
            throw new Exception('TOKEN is not defined!');
        }
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $tmpArr = array($this->token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * ps ：接口汇总，统一经过
     * Time：2015/12/28 09:35:18
     * @author zhangyan
     * @param 参数类型
     * @return 返回值类型
    */
    //回复消息
    public function getResult(){
        //get post data, May be due to the different environments
        $postStr = file_get_contents("php://input");//$GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $MsgType=$postObj->MsgType;
            switch($MsgType){
               case "event":
                $result = $this->receiveEvent($postObj);
               break;
               case "text":
                $result = $this->receiveText($postObj);
               break;
            }
            return $result;
        }else {
            echo "没有接收到任何信息！";
            exit;
        }
    }

    /**
     * ps ：用户发送消息的时候回复消息
     * Time：2015/12/18 12:33:11
     * @author zhangyan
     * @param 参数类型
     * @return 返回值类型
    */
    function receiveText($postObj){
        $fromUsername = $postObj->FromUserName; //请求消息的用户
        $toUsername = $postObj->ToUserName; //"我"的公众号id
        $keyword = trim($postObj->Content); //消息内容
        $content = "";
        //设置关键字
        if(strstr($keyword, "你好")){
            $content = "您好，我是Jado绿能侠，很高兴为你服务";
        }elseif(strstr($keyword, "测试")){
            $content = "测试";
        }elseif(strstr($keyword, "绿能侠")){
            $content = "test.lvnengxia.cn";
        }else{
            $content = null;
        }
        //同时返回个人信息
        $result = '';
        if($content != ''){
            $result = $this->transmitText($postObj,$content);
        }
        return $result;
    }

    /**
     * ps ：得到是关注的时候才会输出会员信息
     * Time：2015/12/18 12:33:22
     * @author zhangyan
     * @param 参数类型
     * @return 返回值类型
    */
    function receiveEvent($postObj){
        $content = "";
        switch (strtoupper($postObj->Event)){
            case "SUBSCRIBE":
                $content = "Jado绿能侠欢迎您";
                break;
            case "VIEW":
                break;
            case "CLICK":
                switch ($postObj->EventKey)
                    {
                        case "jf_current_balance_1":
                            //$content = $this->getPoint($postObj->FromUserName);
                        break;
                    }
                break;
            default:
                break;
        }

        $result = '';
        if($content != ''){
            $result = $this->transmitText($postObj,$content);
        }
        return $result;
    }

    /**
     * ps ：关注的时候，回复关注信息，返回用户信息
     * Time：2015/12/18 12:32:27
     * @author zhangyan
     * @param 参数类型
     * @return 返回值类型
    */
    function transmitText($postObj,$content,$msgType='text'){
        $textTpl = "<xml>
           <ToUserName><![CDATA[%s]]></ToUserName>
           <FromUserName><![CDATA[%s]]></FromUserName>
           <CreateTime>%s</CreateTime>
           <MsgType><![CDATA[%s]]></MsgType>
           <Content><![CDATA[%s]]></Content>
           <FuncFlag>0</FuncFlag>
           </xml>";
        $resultStr= sprintf($textTpl,$postObj->FromUserName,$postObj->ToUserName,time(),$msgType,$content);
        return $resultStr;
    }

    /**
     * ps ：获取access_token
     * Time：2015/12/18 12:32:44
     * @author zhangyan
     * @param 参数类型
     * @return 返回值类型
    */
    function get_access_token($appId,$appSecret){

        $_file_session = 'weixin_eqinfo.txt';
        $_txt_session = unserialize(file_get_contents($_file_session));

        // dump($_txt_session);exit;
        if($_txt_session['wx_access_token'] && time() <= $_txt_session['wx_access_token_time']) {
            return $_txt_session['wx_access_token'];
        } else {
            //获取access_token
            $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appId.'&secret='.$appSecret;

            $access = $this->http_request($url_get);

            //放入session
            $_SESSION['wx_access_token'] = $access['access_token'];
            $_SESSION['wx_access_token_time'] = time() + $access['expires_in'];

            $_txt_session['wx_access_token'] = $access['access_token'];
            $_txt_session['wx_access_token_time'] = $_SESSION['wx_access_token_time'];
            //放到缓存中
            file_put_contents($_file_session, serialize($_txt_session));

            // dump($access);exit;
            return $access['access_token'];
        }
    }

    /**
     * 在网页端处理事件需要获取user信息
     * 网页上需要微信的授权access_token ，该token 和之前的access_token不同
     * Time：2015/12/31 13:18:06
     * @author li
    */
    function get_access_token_auth($appId,$appSecret,$code=''){
        $_file_session = 'weixin_eqinfo.txt';
        $_txt_session = unserialize(file_get_contents($_file_session));

        // session_start();
        if($_txt_session['wx_access_token_auth'] && time() <= $_txt_session['wx_access_token_auth_time']) {
            return $_txt_session['wx_access_token_auth'];
        } else {
            //获取access_token
            if($_SESSION['wxweb']['refresh_token']!=''){
                $url_get='https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$appId.'&grant_type=refresh_token&refresh_token='.$_SESSION['wxweb']['refresh_token'];
            }elseif($code!=''){
                $url_get='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appId.'&secret='.$appSecret.'&code='.$code.'&grant_type=authorization_code';
            }else{
                return false;
            }

            //获取access_token
            $access = $this->http_request($url_get);

            //放入session
            // echo $url_get;
            // unset($_SESSION['wxweb']);
            $_SESSION['wxweb']['access_token'] = $access['access_token'];
            $_SESSION['wxweb']['openId'] = $access['openid'];
            $_SESSION['wxweb']['refresh_token'] = $access['refresh_token'];
            $_SESSION['wxweb']['access_token_time'] = time() + $access['expires_in'];


            $_txt_session['wx_access_token_auth'] = $access['access_token'];
            $_txt_session['wx_access_token_auth_time'] = $_SESSION['wxweb']['access_token_time'];
            //放到缓存中
            file_put_contents($_file_session, serialize($_txt_session));

            return $access['access_token'];
        }
    }

    /**
     * 访问
     * Time：2015/12/29 15:24:08
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    public function http_request($url ,$data=null ,$timeout=8 ,$decode=true){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $output = curl_exec($ch);
        curl_close($ch);

        $decode && $output = json_decode( $output, true );

        return $output;
    }

    //生成授权地址并跳转
    public function get_code($appId, $redirect_uri, $scope='snsapi_base', $response_type='code', $state='STATE'){
        $api_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
        $api_url = $api_url.'appid='.$appId.'&redirect_uri='.urlencode($redirect_uri).'&response_type='.$response_type.'&scope='.$scope.'&state='.$state.'#wechat_redirect';
        // dump($api_url);die;
        header('Location:'.$api_url);
        exit;
    }

    //code换取openId
    public function code2openid($appID ,$appsecret ,$code){
        $curl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appID.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code';
        $result = $this->http_request($curl);
        // $result = json_decode($content ,1);
        // dump($result);exit;
        return $result['openid'];
    }

}