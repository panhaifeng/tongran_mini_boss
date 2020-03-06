<?php
/*********************************************************************\
*  Copyright (c) 2007-2015, TH. All Rights Reserved.
*  Author :li
*  FName  :Api_Lib_Rsp_Ding_Base.php
*  Time   :2018/12/24 08:57:42
*  Remark :小程序对接
\*********************************************************************/
FLEA::loadClass('Api_Lib_Rsp_Mini_Base');
class Api_Lib_Rsp_Mini_Tongran_User extends Api_Lib_Rsp_Mini_Base{
    var $miniKey = "Tongran";
    function __construct() {
        $this->_model = FLEA::getSingleton('Model_Jichu_Member');
    }

    /**
     * 获取iopenid
     * Time：2019/08/26 16:34:41
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function getOpenid($params,& $service){
        //验证参数
        if(!$params['code']){
            $service->send_user_error('ERROR_DATA : code is null');
        }

        //获取 access_token
        // $access_token = $this->getAccessToken();

        //openid
        $openid = parent::code2openid($params['code']);

        return array('openid'=>$openid);
    }

    /**
     * 获取账号关系
     * Time：2019/08/26 16:34:41
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function getUserProjectList($params,& $service){
        $openid = $params['openid'];
        $list = array();
        //获取本地openid和项目的关联关系
        if($openid){
            $sql = "SELECT p.compName,p.url,p.userName,p.id,m.id as mid
                from project_account p
                left join project_account2member pm on p.id=pm.paid
                left join jichu_member m on m.id=pm.mid
                where m.openid='{$openid}'
                order by p.id";
            // dump($sql);
            $list = $this->_model->findBySql($sql);
            if($list)foreach ($list as $key => & $v) {
                $v['showText'] = $v['compName'].' '.$v['userName'];
            }
        }

        return array('projectList'=>$list);
    }

    /**
     * addProject
     * Time：2019/08/28 13:49:06
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function addProject($params,& $service){
        $scanCode = $params['scanCode'];
        //如果存在链接和指定参数
        if(strpos($scanCode , '&params=')){
            $scanCode = substr($scanCode, strpos($scanCode , '&params=')+8);
        }

        $data = json_decode($scanCode ,true);
        // dump($data );

        if(!$scanCode){
            $service->send_user_error('二维码参数错误');
        }

        if(!$params['openid']){
            $service->send_user_error('微信身份信息获取失败');
        }
        if(!$data['platfrom']){
            $service->send_user_error('二维码信息不完整');
        }
        if(!$data['userName']){
            $service->send_user_error('二维码信息不完整');
        }
        if(!$data['compName']){
            $service->send_user_error('二维码信息不完整');
        }

        //验证二维码有效性
        $qrCode = FLEA::getSingleton('Controller_Event_MiniQrCode');
        $verifyResult = $qrCode->verifyQrCode($data);
        //验证时间戳不能超过太多的时间
        $time = abs(time() - $data['timestamp']);
        if($time > 300){
            $service->send_user_error('二维码验证失败：时间过期');
        }
        if(!$verifyResult){
            $service->send_user_error('二维码验证失败：不合法');
        }

        //验证全部通过，则保存到数据表中，进行关联
        $modelAccount = FLEA::getSingleton('Model_Project_Account');
        $modelProject2Account = FLEA::getSingleton('Model_Project_Account2Member');
        $condition = array();
        $condition['url'] = $data['platfrom'];
        $condition['userName'] = $data['userName'];
        //查找账号是否已经存在
        $account =$modelAccount->find($condition);
        if(!$account){
            //保存账号信息
            $_accountData = array(
                'userName' =>$data['userName'],
                'url'      =>$data['platfrom'],
                'compName' =>$data['compName'],
            );
            $accountId = $modelAccount->save($_accountData);
        }else{
            $accountId = $account['id'];
            $_accountData = $account;
        }

        //确认会员是否存在:openid
        $userTemp = $this->_model->find(array('openid'=>$params['openid']));
        if(!$userTemp){
            $dataUser = array(
                'openid'=> $params['openid']
            );
            $userId = $this->_model->save($dataUser);
        }else{
            $userId = $userTemp['id'];
        }

        //查找绑定关系
        $pa2m = $modelProject2Account->find(array('mid'=>$userId,'paid'=>$accountId));
        if(!$pa2m){
            $_dataMap = array(
                'mid'  =>$userId,
                'paid' =>$accountId,
            );
            $modelProject2Account->save($_dataMap);
        }

        //组织需要返回的项目数据
        $project = array_merge(array('id'=>$accountId,'showText'=>$data['compName'].' '.$data['userName']) ,$_accountData);
        //返回新的项目数据
        return $project;
    }


    /**
     * 解除绑定
     * Time：2019/08/30 12:33:05
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function unbind($params,& $service){
        $openid = $params['openid'];
        if(!$openid){
            $service->send_user_error('缺少微信个人信息');
        }

        $member = $this->_model->find(array('openid'=>$openid));

        //开始解除
        if($member){
            $modelProject2Account = FLEA::getSingleton('Model_Project_Account2Member');
            $modelProject2Account->removeByConditions(array('mid'=>$member['id']));
        }

        return array('success'=>true,'msg'=>'');
    }
}
?>