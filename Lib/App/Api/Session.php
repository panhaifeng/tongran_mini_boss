<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  FName  :Response.php
*  Time   :2014/05/13 18:31:40
*  Remark :处理接口需要的session问题
\*********************************************************************/
class Api_Session {
    var $autoTime = 1296000;  //15天
    var $defaultTime = 43200;//12小时

    function __construct() {

    }

    function _init(){
        $sessionpath = session_save_path();
        $path = $sessionpath.DS."appSessionPath";

        if(!is_dir($path)){
            mkdir($path ,0777);
        }
        ini_set('session.save_path',$path);
        ini_set('session.gc_maxlifetime', $this->autoTime);
        // ini_set("session.cookie_lifetime",$this->autoTime);
    }

    //生成新的session并返回sid
    function setSid($autoLogin = false){
        $this->_init();
        if($_SESSION){
            unset($_SESSION);
            session_destroy();
        }

        session_start();
        $sid = session_id();

        $this->setCookieSid($sid ,$autoLogin);
        $time = $autoLogin ? $this->autoTime : $this->defaultTime;
        // echo $time;exit;
        $_SESSION['SESSION_END_TIME'] = (time()+$time);
        $_SESSION['FROM_PLAT'] = 'APP';
        $_SESSION['AUTO_LOGIN'] = $autoLogin;

        return $sid;
    }

    function setCookieSid($sid ,$autoLogin = false){
        $this->sess_time = $autoLogin ? $this->autoTime : $this->defaultTime;
        $cookie_path = "/";
        $cookie_expires = sprintf("expires=%s;",  gmdate('D, d M Y H:i:s T', time()+$this->sess_time));
        header(sprintf('Set-Cookie: %s=%s; path=%s; %s; httpOnly;', 's', $sid, $cookie_path, $cookie_expires), true);
    }

    //启用session，并判断该session是否有数据
    function sessionStart($sid){
        $this->_init();
        session_id($sid);
        session_start();
        // echo ($_SESSION['SESSION_END_TIME'] - time());
        if($_SESSION['SESSION_END_TIME'] < time()){
            unset($_SESSION);
        }else{
            $time = $_SESSION['AUTO_LOGIN'] ? $this->autoTime : $this->defaultTime;
            $_SESSION['SESSION_END_TIME'] = time()+$time;
        }

        if($_SESSION['FROM_PLAT'] == 'APP'){
            $autoLogin = true;
        }
        $this->setCookieSid($sid ,$autoLogin);
    }

    //开启session并获取sid对应的userid信息
    //通过sid验证身份
    function verifiIdentity($sid ,&$userInfo = array()){
        self::sessionStart($sid);
        // dump($_SESSION);exit;
        if($_SESSION['USERID'] || $_SESSION['WXUSERID']){
            $type = ($_SESSION['USERID'] > 0) ? 'USER' : 'MEMBER';
            $id = ($_SESSION['USERID'] > 0) ? $_SESSION['USERID'] : $_SESSION['WXUSERID'];
            $userInfo = array(
                'id'   =>$id,
                'type' =>$type,
            );
            return true;
        }else{
            return false;
        }
    }

    //destory——session
    function sessionDestory($sid){
        $this->_init();
        //先开启
        session_id($sid);
        session_start();

        //销毁
        unset($_SESSION);
        session_destroy();
    }

}

?>