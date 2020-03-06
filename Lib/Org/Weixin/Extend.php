<?php
FLEA::org('Weixin/Base.php');
class Weixin_Extend extends Weixin_Base {
    private $appId;
    private $appSecret;

    public function __construct($appId, $appSecret) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    /**
     * ps ：推送普通信息
     * Time：2018/06/13 14:32:44
     * @author zou
    */
    public function replyCustomer($touser,$content){
        $ACC_TOKEN = $this->get_access_token($this->appId,$this->appSecret);
        $data = '{
            "touser":"'.$touser.'",
            "msgtype":"text",
            "text":
            {
                 "content":"'.$content.'"
            }
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$ACC_TOKEN;

        $result = $this->http_request($url,$data);
        $final = json_decode($result);
        return $final;
    }

    /**
     * ps ：推送模板信息
     * Time：2018/06/13 14:32:44
     * @author zou
     * @param 参数类型 openid,客户姓名等（参数自定）
     * @return 返回值类型
    */
    function sendMessage($touser,$templateId,$url='',$arr=array()){
        //获取全局token
        $ACC_TOKEN = $this->get_access_token($this->appId,$this->appSecret);
        foreach ($arr as $key => &$val) {
            $data[$key] = array(
                "value"=>$val,
                "color"=>"#173177"
            );
        }
        $post_data = array(
            "touser"=>$touser,
            "template_id"=>$templateId,
            "url"=>$url,
            "data"=>$data
        );
        $post_data = json_encode($post_data);

        //模板信息请求地址
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$ACC_TOKEN;
        $result = $this->http_request($url,$post_data);

        $final = json_decode($result);
        return $final;
    }
}