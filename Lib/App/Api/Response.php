<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  FName  :Response.php
*  Time   :2014/05/13 18:31:40
*  Remark :api接口的响应类
\*********************************************************************/
class Api_Response {

    function __construct() {
        $this->path = array();
    }

    /**
     * @desc ：调用api对应的方法，
     * @param 参数类型
     * @return json
    */
    function response() {
        ignore_user_abort();
        set_time_limit(0);
        $this->process_id = uniqid().rand(100000,999999);
        $this->calltime = time();
        header('Process-id: '.$this->process_id);
        header('Connection: close');
        flush();

        if(get_magic_quotes_gpc()){
            self::strip_magic_quotes($_GET);
            self::strip_magic_quotes($_POST);
        }

        $_PARAMS = array_merge($_GET, $_POST);

        $this->begin(__FUNCTION__);

        set_error_handler(array(&$this,'error_handle'),E_ERROR);
        set_error_handler(array(&$this,'user_error_handle'),E_USER_ERROR);

        #加载配置文件获取所有api数组
        $version = $_PARAMS['version'];
        $mapper_file = "Config/api/config.".strtolower($version).".php";
        if(file_exists($mapper_file)){
            include $mapper_file;
            $this->certi_key = $CERTI_KEY;
            $this->gen_sign  = $GEN_SIGN;
        }else{
            $this->send_user_error('VERSION_NOT_NULL', 'VERSION不匹配');
        }

        //获取处理入口和对应参数
        list($method,$params) = $this->parse_rpc_request($_PARAMS);

        $result = array();
        #处理数据，开始保存请求日志
        $model = FLEA::getSingleton('Model_Api_Logs');

        if (1==1) {
            if(isset($api_array) && isset($api_array[$method])){
                //开始访问接口
                $object = $api_array[$method];

                //先写入日志
                $logData = array(
                    'rpc_id'     =>$this->process_id,
                    'calltime'   =>$this->calltime,
                    'version'    =>$version,
                    'title'      =>$object['title'],
                    'status'     =>'running',
                    'method'     =>$method,
                    'worker'     =>$object['class'].'@'.$object['method'],
                    'params'     =>serialize($params),
                    'api_type'   =>'response',
                    'ip'         =>$_SERVER['REMOTE_ADDR'],
                    'createtime' =>time(),
                );
                //保存日志
                $model->save($logData);

                //开始访问接口
                $class = FLEA::getSingleton($object['class']);
                $result = $class->$object['method']($params,$this);
                $output = $this->end();
            }else{
                $output = $this->end();
                $msg = '不存在'.$_REQUEST['method'].'接口';
                $output = $msg;
                $status = 'fail';
            }
        }else{
            $output = $this->end();
            $output = '该请求已经处理，不能在处理了！';
        }


        $result_json = array(
            'rsp'=> $status ? $status : 'succ',
            'data'=>$result,
            'res'=>strip_tags($output)
        );

        $this->rpc_response_end($result, $this->process_id, $result_json);
        echo json_encode($result_json);
    }


    /**
     * 处理参数信息
     * Time：2017/04/26 09:02:38
     * @author li
    */
    private function parse_rpc_request($request){
        if(!isset($request['method'])){
            $this->send_user_error('ERROR_METHOD_NULL', 'Method 不存在');
            return false;
        }

        $sign = $request['sign'];
        unset($request['sign']);

        //判断时间戳
        // if($request['timestamp']){
        //     $time = abs(time() - $request['timestamp']);
        //     if($time > 120){
        //         $this->send_user_error('time error', '请设置您的时间为北京最新时间');
        //         return false;
        //     }
        // }

        if($this->certi_key){
            //默认的加密方法
            if(!$this->gen_sign){
                $self_sign = $this->gen_sign($request);
            }else{
                //使用自定义的加密方法，自定义一个类，类中必须实现gen_sign入口函数，
                $class = FLEA::getSingleton($this->gen_sign);
                $self_sign = $class->gen_sign($request ,$this->certi_key ,$this);
            }

            //判断sign是否一致
            if($self_sign != $sign){
                $this->send_user_error('SIGN_ERROR', '签名错误');
                return false;
            }
        }

        //获取method
        $method = $request['method'];
        $array_unset = array('method','version');
        foreach ($variable as $pk) {
            unset($request[$pk]);
        }

        return array($method ,$request);
    }

    /**
     * 签名处理
     * Time：2017/04/26 09:02:38
     * @author li
    */
    private function gen_sign($params){
        $str_key_value = $this->assemble($params);
        return strtoupper(md5(strtoupper(md5($str_key_value)).$this->certi_key));
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
     * 处理传递的参数
     * Time：2017/04/26 09:02:38
     * @author li
    */
    public function strip_magic_quotes(&$var){
        foreach($var as $k=>$v){
            if(is_array($v)){
                self::strip_magic_quotes($var[$k]);
            }else{
                $var[$k] = stripcslashes($v);
            }
        }
    }


    private function begin()
    {
        /*register_shutdown_function(array(&$this, 'shutdown'));
        array_push($this->path, 'begin');
        @ob_start();*/
    }//End Function

    private function end($shutdown=false){
        /*if($this->path){
            $this->finish = true;
            $content = ob_get_contents();
            ob_end_clean();

            if($shutdown){
                echo json_encode(array(
                    'rsp'=>'fail',
                    'res'=>$content,
                    'data'=>null,
                ));
                die;
            }
            return $content;
        }*/
    }

    public function shutdown(){
        $this->end(true);
    }

    private function rpc_response_end($result, $process_id, $result_json)
    {
        if (isset($process_id) && $process_id){
            #更新日志信息
            $model = FLEA::getSingleton('Model_Api_Logs');
            $sql = "SELECT apilog_id from api_logs where rpc_id='{$process_id}' limit 0,1";
            $row = $model->findBySql($sql);
            if($row){
                $row = $row[0];
                switch($result_json['rsp']){
                    case 'succ':
                        $status="success";
                        break;
                    case 'fail':
                        $status="fail";
                        break;
                }

                $data = array(
                    'apilog_id'     =>$row['apilog_id'],
                    'msg'           =>$result_json['res'],
                    'response_json' =>json_encode($result_json),
                    'createtime'    =>time(),
                    'status'        =>$status,
                );

                $model->update($data);
            }
        }

    }

    function error_handle($error_code, $error_msg){
        $this->send_user_error('4007', $error_msg);
    }

    function user_error_handle($error_code, $error_msg){
        $this->send_user_error('4007', $error_msg);
    }

    public function send_user_error($code, $data)
    {

        $this->end();
        $res = array(
            'rsp'   =>  'fail',
            'res'   =>  $code,
            'data'  =>  $data,
        );

        $this->rpc_response_end($data,$this->process_id, $res);
        echo json_encode($res);
        exit;
    }//End Function
}