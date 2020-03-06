<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  Author :wuyou
*  FName  :Login.php
*  Time   :2019/07/17 14:16:27
*  Remark :二维码验证接口
\*********************************************************************/
FLEA::loadClass('Api_Session');
class Api_Lib_Login extends Api_Session {
    function Api_Lib_Login(){
        $this->_modelLogin = FLEA::getSingleton('Model_Login');
    }


    //重写_checkParams,参数是否合法可能包含业务逻辑
    function _checkParams($params) {
        return true;
    }

     /**
     * 登录验证
     * Time：2019/07/17 14:17:33
     * @author Wuyou
    */
    function loginout($params = array(),& $service){
        if($params['sid']){
            $this->sessionDestory($params['sid']);
        }

        if($params['openid']){
            $user = $this->_modelLogin->find(array('openid'=>$params['openid']));
            if($user['id']){
                $data = array(
                    'id'       =>$user['id'],
                    'openid'   =>'',
                    'unionid'  =>'',
                    'nickname' =>'',
                );
                $this->_modelLogin->update($data);
            }
        }

        return array('success'=>true,'msg'=>'安全退出成功');
    }

    /**
     * mp小程序登录验证，一般会传递小程序的一些个人信息过来
     * Time：2019/11/22 09:25:58
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function loginMp($params = array(),& $service){
        if(!$params['userinfo']){
            $service->send_user_error('param userinfo not found');
        }

        //把json转换
        $userinfo = json_decode($params['userinfo'],true);

        if(!is_array($userinfo)){
            $service->send_user_error('param userinfo error');
        }
        $provider = $params['provider'] ? $params['provider'] : 'weixin';
        if(strtolower($provider) == 'weixin'){
            $result = $this->loginBywxcode($userinfo ,$params);
        }

        return $result;
    }

    /**
     * 微信code认证登录
     * Time：2019/11/22 09:33:36
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function loginBywxcode($userinfo ,$params){
        if(!$userinfo['code']){
            return array('openid'=>'','msg'=>'code null');
        }
        //获取微信code对应的openid
        $wxService = FLEA::getSingleton('Api_WxCode2info');
        $openid = $wxService->code2openid($userinfo['code'] ,true);


        $user = array(
                'openid'   =>strval($openid['body']['openid']),
                'unionid'  =>strval($openid['body']['unionid']),
                'msg'      =>strval($openid['body']['errmsg']),
                'errcode'  =>strval($openid['body']['errcode']),
                'nickname' => strval($params['nickname']),
                'userId'   => '',
                'userName' => '',
                'realName' => '',
                'compName' => '',
            );

        //查找openid对应的用户表是否有账户
        if($user['openid']){
            $sql = "SELECT * from acm_userdb where openid = '{$user['openid']}'";
            $uLocal = $this->_modelLogin->findBySql($sql);
            $uLocal = $uLocal[0];
            if($uLocal){
                $user['userId'] = $uLocal['id'];
                $user['userName'] = $uLocal['userName'];
                $user['realName'] = $uLocal['realName'];
            }

            //查找项目的公司名称
            $class = FLEA::getSingleton('TMIS_Controller');
            $user['compName'] = $class->getCompName();
        }

        return array(
            'userinfo'=>$user
        );
    }

    /**
     * 绑定erp的二维码
     * Time：2019/11/22 13:27:02
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function bindMpCode($params = array(),& $service){
        $time = time();
        $openid = strval($params['openid']);
        $nickname = strval($params['nickname']);
        if(!$openid){
            return array('msg'=>'微信身份参数缺失','success'=>false);
        }

        $param = json_decode($params['code'] ,1);
        //有效期是120秒，如果是测试帐号给微信审核的，需要时间长点，防止无法审核
        $expires_in_qr = 120;
        if($param['test'] && $param['test']=='eqinfo'){
            $expires_in_qr = 5*24*3600;//5天有效期
        }
        if(($time - $param['timestamp']) > $expires_in_qr ){
            return array('msg'=>'二维码过期,有效时间两分钟','success'=>false);
        }

        if(!$param['uid'] || !$param['uname']){
            return array('msg'=>'账户信息有误','success'=>false);
        }

        //验证内容否合法
        $localToken = $this->tokenFormat($param);
        if($localToken != $param['token']){
            return array('msg'=>'二维码信息不合法','success'=>false);
        }

        //判断uid和uname是否匹配
        $user = $this->_modelLogin->findByUsername($param['uname']);
        if(!$user || $user['id'] != $param['uid']){
            return array('msg'=>'用户信息不匹配','success'=>false);
        }

        //如果该openid已经绑定了其他帐号，则提示不能绑定多个帐号
        $condition = array();
        $condition['openid'] = $openid;
        $condition[] = array('id',$user['id'] ,'<>');
        $count = $this->_modelLogin->findCount($condition);
        if($count > 0 ){
            return array('msg'=>'该微信已绑定其他帐号,不能绑定多个','success'=>false);
        }

        //如果已经被绑定并且布匹配，不能再次绑定
        if($user['openid'] !='' && $user['openid'] != $openid){
            return array('msg'=>'该账号已经绑定,不能重复绑定','success'=>false);
        }

        //进行绑定到账号的操作，直接保存
        $data = array(
            'id'       => $user['id'],
            'openid'   => $openid,
            'nickname' => $nickname,
        );
        $res = $this->_modelLogin->update($data);

        $class = FLEA::getSingleton('TMIS_Controller');
        $compName = $class->getCompName();

        return array('success'=>true,'msg'=>'绑定完成','userinfo'=>array(
            'userId'   =>$user['id'],
            'userName' =>$user['userName'],
            'compName' =>$compName,
            'openid'   =>$openid,
            'nickname' =>$nickname,
            'realName' =>$user['realName'],
        ));
    }

    //验证和生成二维码的token
    function tokenFormat($param = array()){
        $str = $param['timestamp'].'*'.$param['serverUrl'].'*'.$param['uid'].'*'.$param['uname'];
        return md5($str);
    }
    /**
     * 获取菜单
     * Time：2019/11/25 13:57:38
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function getMenu($params = array(),& $service){
        // error_reporting(E_ALL);
        // itemId和uniapp上一致,id和menu.php中的一致

        $_menu = array(
            array('itemId'=>'cardSample','id'=>'2-1','title'=>'新客户索样'),
            array('itemId'=>'clientSample','id'=>'2-1','title'=>'老客户索样'),
            array('itemId'=>'sampleList','id'=>'2-1','title'=>'索样列表'),
            array('itemId'=>'exhReport','id'=>'3-2','title'=>'员工接待概况'),
            array('itemId'=>'sampleReport','id'=>'3-1','title'=>'被索样品排行'),
            // array('itemId'=>'sendReport','id'=>'3-3','title'=>'索样寄样详情'),
        );

        //判断有权限的保留，没有权限的去掉菜单项目
        //获取用户信息
        $user = array();
        if($params['openid']){
            $user = $this->_modelLogin->find(array('openid'=>$params['openid']));
        }elseif($params['userId']){
            // 待定
            // $user = $this->_modelLogin->find(array('id'=>$params['userId']));
        }

        $m = FLEA::getSingleton('Model_Acm_Func');
        $ret = array();
        foreach ($_menu as & $v) {
            $v['leaf'] = true;
            if($user){
                $a = $m->changeVisible($v, array('userName' => $user['userName']));
                if(!$a) continue;
                $ret[] = $a;
            }else{
                //访客情况展示不需要权限的功能
                if($v['id'] ==''){
                    $ret[] = $v;
                }
            }
        }

        return array('menu'=>$ret,'menuId'=>array_col_values($ret ,'itemId'));
    }

    //检查是否Openid解绑
    function checkUser($params = array(),& $service){
        $user = array();
        if($params['openid']){
            $user = $this->_modelLogin->find(array('openid'=>$params['openid']));
        }

        return array('success'=>($user['id'] > 0),'userId'=>$user['id']+0,'msg'=>'验证完成');
    }
}