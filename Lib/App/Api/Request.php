<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  Author :Jeff
*  FName  :Response.php
*  Time   :2014/05/13 18:31:40
*  Remark :api接口的请求类
\*********************************************************************/
class Api_Request{

    public $_timeout = 30;
    public $taskname = '';  // 请求任务名称

    function __construct() {
        require('Config/api/certi.default.php');
        $this->api_url = $certKey['url'];
        $this->token   = $certKey['token'];
        $this->method  = $certKey['method'];

        $this->_class = __CLASS__;
    }

    /**
     * @desc ：调用门店宝api
     * @author li 2015/09/29 16:11:38
     * @param params
     * @return 返回值类型
    */
    function api_caller($params = array()) {
        $sys_time_out = $this->timeout == 0 ? 0 : ($this->timeout+5);
        set_time_limit($sys_time_out);
        //获取任务号
        $this->rpc_id = self::gen_uniq_process_id();
        $params_arr = array(
            'timestamp' => time(),
            'task' => $this->rpc_id,
        );

        $params = array_merge($params , $params_arr);

        //签名
        $params['sign'] = self::gen_sign($params ,$this->token);

        $res = self::_http_post($this->api_url , $params ,$this->rpc_id);
        return $res;
    }

    /**
     * @desc ：调用门店宝api重试按钮
     * @author li 2015/09/29 16:11:38
     * @param params
     * @return 返回值类型
    */
    function re_api_caller($params ,$rpc_id ) {
        //获取任务号
        $this->rpc_id = $rpc_id;

        $params['timestamp'] = time();
        $params['task'] = $rpc_id;
        unset($params['sign']);
        //签名
        $params['sign'] = self::gen_sign($params ,$this->token);

        $res = self::_http_post($this->api_url , $params ,$rpc_id ,true);
        return $res;
    }

    /**
     * @desc http 进行api  请求
     * @param type $url 请求地址
     * @param type $params 应用级参数
     */
    private function _http_post($url = '', $params = array() ,$rpc_id = null ,$re_request = false) {
        // 记录日志
        $apiModel = FLEA::getSingleton('Model_Api_Logs');
        //本次使用的哪个配置文件
        $apiModel->set_certiVer("Config/api/certi.default.php");

        //请求的方法说明
        $taskname = $this->taskname ? $this->taskname : $this->method[$params['method']]['title'];

        $rpc_id = $apiModel->requestLog($params, $rpc_id ,$taskname ,$re_request);

        $response = self::_post($params ,$url ,$curl_error_code);

        if ($response === false) {
            if($curl_error_code == 28){
                $apiModel->updateLog(array('status' => 'fail', 'msg' => '请求超时'), $rpc_id);
            }
        } else {
            //处理callback函数
            try{
                $callback = $this->method[$params['method']]['callback'];
                if($callback){
                    list($callClass ,$callMethod) = explode('@',$callback);
                    if($callClass && $callMethod){
                        $object = FLEA::getSingleton($callClass);
                        $callResult = $object->$callMethod($response ,$params ,$callMsg);
                    }
                }
            }catch(Exception $e){

            }
            //开始调整日志
            $res_result = json_decode($response, 1);
            if ($res_result['rsp'] != 'succ') {
                if(is_array($res_result)){
                    $_msg = $res_result['res'];
                }else{
                    $_msg = $res_result;
                }
                $apiModel->updateLog(array('status'=>'fail','msg'=>$_msg,'response_json'=>$response),$rpc_id);
            } else {
                $apiModel->updateLog(array('status'=>'success','response_json'=>$response),$rpc_id);
            }
        }

        return $response;
    }

    //post
    public function _post($data, $url ,&$errorCode)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上, 0为直接输出屏幕，非0则不输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
        //为了支持cookie
        //curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        //curl_excc会输出内容，而$result只是状态标记
        $response = curl_exec($ch);
        $errorCode = curl_errno($ch);
        //释放curl句柄
        curl_close($ch);

        if(0 !== $errorCode) {
            return false;
        }
        return $response;
    }

    /**
     * 签名处理
     * Time：2017/04/26 09:02:38
     * @author li
    */
    private function gen_sign($params){
        $str_key_value = $this->assemble($params);
        return strtoupper(md5(strtoupper(md5($str_key_value)).$this->token));
    }

    /**
     * 字符串处理
     * Time：2017/04/26 09:02:38
     * @author li
    */
    private function assemble($params)
    {
        if(!is_array($params))  return null;
        ksort($params, SORT_STRING);
        $sign = '';
        foreach($params AS $key=>$val){
            if(is_null($val))   continue;
            if(is_bool($val))   $val = ($val) ? 'true' : 'false';
            $sign .= $key . (is_array($val) ? $this->assemble($val) : $val);
        }
        return $sign;
    }

    /**
     * 获取请求接口的id
     * Time：2016/10/11 16:05:34
     * @author li
    */
    private function gen_uniq_process_id(){
        $_rand = rand(100000,999999);
        $rpc_id = uniqid().$_rand;
        return $rpc_id;
    }

    /**
     * 超时时间设置
     */
    public function set_timeout($timeout) {
        $this->_timeout = $timeout;
    }

    /**
     * 设置请求任务的名字
     */
    public function set_taskname($taskname) {
        $this->taskname = $taskname;
    }

    /**
     * 设置请求的url
     */
    public function set_api_url($url) {
        $this->api_url = $url;
    }

    /**
     * 设置请求任务的名字
     */
    public function set_api_url_domain($domain) {
        if(strpos($this->api_url, '{domain}') !== false){
            $this->api_url = str_replace('{domain}' ,$domain ,$this->api_url);
        }
    }
}