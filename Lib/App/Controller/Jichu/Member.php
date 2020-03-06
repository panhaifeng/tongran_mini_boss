<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  Author :wuyou
*  FName  :Supplier.php
*  Time   :2019/02/15 14:16:28
*  Remark :供应商档案
\*********************************************************************/
FLEA::loadClass('TMIS_Controller');
class Controller_Jichu_Member extends TMIS_Controller {
    var $_modelExample;

    var $funcId = '1-2';
    var $funcId2 = '1-1';
    var $funcId3 = '1-3';

    function __construct() {
        $this->_modelExample = FLEA::getSingleton('Model_Jichu_Member');
        $this->_modelAccount2Member = FLEA::getSingleton('Model_Project_Account2Member');
        $this->_modelAccount = FLEA::getSingleton('Model_Project_Account');
    }


    /**
     * @desc ：供应商档案查询
     * Time：2017/07/31 15:33:05
     * @author li
    */
    function actionRight() {
        $this->authCheck($this->funcId);

        $searchItems = array(
            'key'   =>'',
        );
        $smarty = & $this->_getView();
        $arrFieldInfo = array(
            'tel'        =>array('text'=>'手机号','width'=>''),
            'nickname'   =>array('text'=>'微信昵称','width'=>''),
            'time'       =>array('text'=>'创建时间','width'=>''),
            'headimgurl' =>array('text'=>'头像','width'=>'','isHtml'=>true),
            'openid'     =>array('text'=>'openid','width'=>''),
        );

        $smarty->assign('title', '用户列表');
        $smarty->assign('arr_field_info', $arrFieldInfo);
        $smarty->assign('action', $this->_url('GetRows'));
        $smarty->assign('searchItems', $searchItems);
        $smarty->assign('colsForKey', array(
                array('text' =>'关键字','col'=>'key')
        ));

        // $smarty->assign('editButtons',array(
        //     array('text'=>'编辑','type'=>'redirect','icon'=>'el-icon-edit','options'=>array(
        //         //点击后跳转的地址
        //         'url'            =>$this->_url('Edit').'&id={id}',
        //         'disabledColumn' =>'__disabledEdit',
        //     )),
        //     array('text'=>'删除','type'=>'remove','options'=>array(
        //         'url'            =>$this->_url('RemoveAjax'),
        //         'disabledColumn' =>'__disabledRemove',
        //     )),
        // ));

        //定义详细信息展开自定义模版
        $smarty->assign('optExpand',array(
          //展开面板type,可以是
          //comp-expand-form 普通表单形式的面板
          //comp-expand-tabs 带tab效果的展开面板
          'type'=>'comp-expand-tabs',
          //每个tab中组件参数
          'options'=>array(
            //table参数
            array(
              'type'=>'table',
              'title'=>'项目账号列表',
              'options'=>array(
                'columns'=>array(
                  'compCode' => array('text'=>'公司编号','width'=>'150'),
                  'compName' => array('text'=>'公司名称','width'=>'200'),
                  'userName' => array('text'=>'账号','width'=>'150'),
                  'shenhe'   => array('text'=>'审核状态','width'=>'150'),
                  'url'      => array('text'=>'项目地址','width'=>''),
                ),
                //每条记录中代表子表记录集的字段
                'sonKey'=>'Projects',
              )
            )
          ),
        ));
        // $smarty->assign('menuRightTop', $rightMenu);

        // $smarty->assign('addUrl',$this->_url('Add'));
        $smarty->display('TableList.tpl');
    }

    /**
     * 获取计划任务的数据
     * @author li
    */
    public function actionGetRows()
    {

        $auth = $this->authCheck(0 ,true);

        if(!$auth){
            $ret = array(
              'total'   =>0,
              'columns' =>array(),
              'rows'    =>array(),
            );
            echo json_encode($ret);exit;
        }

        $post = $this->axiosPost();

        $pagesize    = $post['pagesize'];
        $currentPage = $post['currentPage'];

        $keyField    = isset($post['colForKey']) ? $post['colForKey'] : 'key';
        //处理搜索
        $post[$keyField] = $post['key'];
        if($keyField!='key') $post['key'] = '';

        $arr = $post;

        $sql = "SELECT x.*
                from jichu_member x
                where 1";

        if($arr['key']!='') {
            $sql.=" and (x.tel like '%{$arr['key']}%' or x.nickname like '%{$arr['key']}%')";
        }
        $sql .=" order by id desc";

        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = new TMIS_Pager($sql,null,null,$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();
        // dump($rowset);exit;
        foreach($rowset as & $v){
            $v['time'] = date('Y-m-d H:i:s' ,$v['time']);
            $v['headimgurl'] = $this->_imageHtml($v['headimgurl'],'height:40px;');

            //查找对应的项目和审核状态
            // $v['Projects'] = array();
            $sql = "SELECT x.*,y.compName,y.compCode,y.url,y.userName from project_account2member x
            left join project_account y on x.paid=y.id
            where x.mid='{$v['id']}'
            order by x.shenhe asc";
            $v['Projects'] = $this->_modelExample->findBySql($sql);
        }

        $ret = array(
          'total'   =>$pager->totalCount,
          'columns' =>array(),
          'rows'    =>$rowset,
        );
        echo json_encode($ret);exit;
    }

    function actionRemoveAjax(){
        $requestParam = file_get_contents('php://input');
        $_POST = json_decode($requestParam,true);
        $id = intval($_POST['row']['id']);
        //查找是否可以删除
        $sql = "select count(id) as cnt from cangku_yl_ruku where supplierId='{$id}'";
        $re=$this->_modelExample->findBySql($sql);
        if($re[0]['cnt'] > 0){
            $ret = array(
                'success' =>false,
                'msg'     =>'删除失败:已有入库单，禁止删除',
            );
            echo json_encode($ret);exit;
        }

        $res = $this->_modelExample->removeByPkv($id);

        if($res){
            $ret = array(
                'success' =>true,
                'msg'     =>'删除成功',
            );
        }else{
            $ret = array(
                'success' =>false,
                'msg'     =>'删除失败',
            );
        }
        echo json_encode($ret);exit;
    }

    function actionListProject(){
        $this->authCheck($this->funcId3);

        $searchItems = array(
            'key'   =>'',
        );
        $smarty = & $this->_getView();
        $arrFieldInfo = array(
            'compCode' => array('text'=>'公司编号','width'=>'150'),
            'compName' => array('text'=>'公司名称','width'=>'200'),
            'userName' => array('text'=>'账号','width'=>'150'),
            'url'      => array('text'=>'项目地址','width'=>''),

        );

        $smarty->assign('title', '用户列表');
        $smarty->assign('arr_field_info', $arrFieldInfo);
        $smarty->assign('action', $this->_url('GetRowsProject'));
        $smarty->assign('searchItems', $searchItems);
        $smarty->assign('colsForKey', array(
                array('text' =>'关键字','col'=>'key')
        ));

        //定义详细信息展开自定义模版
        $smarty->assign('optExpand',array(
          //展开面板type,可以是
          //comp-expand-form 普通表单形式的面板
          //comp-expand-tabs 带tab效果的展开面板
          'type'=>'comp-expand-tabs',
          //每个tab中组件参数
          'options'=>array(
            //table参数
            array(
              'type'=>'table',
              'title'=>'绑定用户列表',
              'options'=>array(
                'columns'=>array(
                    'tel'        =>array('text'=>'手机号','width'=>''),
                    'nickname'   =>array('text'=>'微信昵称','width'=>''),
                    'time'       =>array('text'=>'创建时间','width'=>''),
                    // 'headimgurl' =>array('text'=>'头像','width'=>'','isHtml'=>true),
                    'openid'     =>array('text'=>'openid','width'=>''),
                ),
                //每条记录中代表子表记录集的字段
                'sonKey'=>'Projects',
              )
            )
          ),
        ));

        $smarty->display('TableList.tpl');
    }

    /**
     * 获取计划任务的数据
     * @author li
    */
    public function actionGetRowsProject(){

        $auth = $this->authCheck(0 ,true);

        if(!$auth){
            $ret = array(
              'total'   =>0,
              'columns' =>array(),
              'rows'    =>array(),
            );
            echo json_encode($ret);exit;
        }

        $post = $this->axiosPost();

        $pagesize    = $post['pagesize'];
        $currentPage = $post['currentPage'];

        $keyField    = isset($post['colForKey']) ? $post['colForKey'] : 'key';
        //处理搜索
        $post[$keyField] = $post['key'];
        if($keyField!='key') $post['key'] = '';

        $arr = $post;

        $sql = "SELECT x.*
                from project_account x
                where 1";

        if($arr['key']!='') {
            $sql.=" and (x.compName like '%{$arr['key']}%' or x.userName like '%{$arr['key']}%')";
        }
        $sql .=" order by id desc";

        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = new TMIS_Pager($sql,null,null,$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();
        // dump($rowset);exit;
        foreach($rowset as & $v){

            //查找对应的项目和审核状态
            // $v['Projects'] = array();
            $sql = "SELECT x.*,y.tel,y.nickname,y.headimgurl,y.openid,y.time from project_account2member x
            left join jichu_member y on x.mid=y.id
            where x.paid='{$v['id']}'
            order by x.shenhe asc";
            $v['Projects'] = $this->_modelExample->findBySql($sql);
            foreach ($v['Projects'] as & $son) {
                // $son['headimgurl'] = $this->_imageHtml($son['headimgurl'],'height:40px;');
                $son['time'] = date('Y-m-d H:i:s' ,$son['time']);
            }
        }

        $ret = array(
          'total'   =>$pager->totalCount,
          'columns' =>array(),
          'rows'    =>$rowset,
        );
        echo json_encode($ret);exit;
    }

    function actionListShenhe(){
        $this->authCheck($this->funcId2);

        $searchItems = array(
            'key'          =>'',
            'shenheStatus' =>'申请审核',
        );
        $smarty = & $this->_getView();
        $arrFieldInfo = array(
            'compName'   => array('text'=>'公司名称','width'=>'150'),
            'userName'   => array('text'=>'账号','width'=>'100'),
            'shenhe'     =>array('text'=>'审核状态','width'=>'100','isHtml'=>true),
            'tel'        =>array('text'=>'手机号','width'=>'100'),
            'url'        => array('text'=>'项目地址','width'=>'100'),
            'nickname'   =>array('text'=>'微信昵称','width'=>''),
            'time'       =>array('text'=>'创建时间','width'=>'150'),
            'headimgurl' =>array('text'=>'头像','width'=>'','isHtml'=>true),
            'openid'     =>array('text'=>'openid','width'=>''),
            'compCode'   => array('text'=>'公司编号','width'=>''),
        );

        // $smarty->assign('editButtons',array(
        //      array('text'=>'审核为通过','icon'=>'el-icon-check','type'=>'func','options'=>array(
        //         'funcName'=>"shenheYes",
        //         'disabledColumn'=>'__disabledButton1'
        //     )),
        //      array('text'=>'审核不通过','icon'=>'el-icon-close','type'=>'func','options'=>array(
        //         'funcName'=>"shenheNo",
        //         'disabledColumn'=>'__disabledButton2'
        //     )),
        // ));
        $smarty->assign('menuRightTop', array(
          array('text'=>'审核通过','name'=>'shenheYes'),
          array('text'=>'审核不通过','name'=>'shenheNo'),
        ));

        $smarty->assign('title', '用户列表');
        $smarty->assign('arr_field_info', $arrFieldInfo);
        $smarty->assign('action', $this->_url('GetRowsShenhe'));
        $smarty->assign('searchItems', $searchItems);
        $smarty->assign('colsForKey', array(
                array('text' =>'关键字','col'=>'key')
        ));
        $smarty->assign('multiSelect',true );
        $smarty->assign('sonTpl','Jichu/MemberList.js');
        $smarty->display('TableList.tpl');
    }

    /**
     * 获取计划任务的数据
     * @author li
    */
    public function actionGetRowsShenhe(){

        $auth = $this->authCheck(0 ,true);

        if(!$auth){
            $ret = array(
              'total'   =>0,
              'columns' =>array(),
              'rows'    =>array(),
            );
            echo json_encode($ret);exit;
        }

        $post = $this->axiosPost();

        $pagesize    = $post['pagesize'];
        $currentPage = $post['currentPage'];

        $keyField    = isset($post['colForKey']) ? $post['colForKey'] : 'key';
        //处理搜索
        $post[$keyField] = $post['key'];
        if($keyField!='key') $post['key'] = '';

        $arr = $post;

        $sql = "SELECT x.*,m.tel,m.nickname,m.headimgurl,m.openid,m.time,y.shenhe,y.id as a2mId
                from project_account x
                left join project_account2member y on y.paid=x.id
                left join jichu_member m on y.mid=m.id
                where 1";

        if($arr['key']!='') {
            $sql.=" and (x.compName like '%{$arr['key']}%' or x.userName like '%{$arr['key']}%' or m.tel like '%{$arr['key']}%')";
        }
        if($arr['shenheStatus']!='') {
            $sql.=" and y.shenhe='{$arr['shenheStatus']}'";
        }
        $sql .=" order by y.shenhe desc,m.id asc";

        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = new TMIS_Pager($sql,null,null,$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();
        // dump($rowset);exit;
        foreach($rowset as & $v){
            $v['headimgurl'] = $this->_imageHtml($v['headimgurl'],'height:40px;');
            $v['time'] = date('Y-m-d H:i:s' ,$v['time']);

            $v['shenhe'] == '申请审核' && $v['shenhe'] = "<i style='color:#F56C6C;' title='未完成'>{$v['shenhe']}</i>";
        }

        $ret = array(
          'total'   =>$pager->totalCount,
          'columns' =>array(),
          'rows'    =>$rowset,
        );
        echo json_encode($ret);exit;
    }

    //确认审核结果
    function actionShenheConfirm(){
        $post = $this->axiosPost();
        $id = $post['id'];

        $shenhe = '不通过';
        if($post['shenhe'] == 'yes'){
            $shenhe = '通过';
        }

        if(is_array($id)){
            $id = join(',',$id);
        }else{
            $id = intval($id);
        }
        if($id){
            $sql = "update project_account2member set shenhe='{$shenhe}' where id in({$id})";
            $res = $this->_modelAccount2Member->execute($sql);
        }

        // $data = array(
        //   'id'     =>$id,
        //   'shenhe' =>$shenhe,
        // );
        // $res = $this->_modelAccount2Member->update($data);

        //发送通知消息给钉钉
        if($shenhe == '通过'){
            //账号信息
            $sql = "SELECT x.userName,x.compName,m.tel from project_account x
            left join project_account2member y on x.id=y.paid
            left join jichu_member m on m.id=y.mid
            where y.id in ({$id})";
            $rows = $this->_modelAccount2Member->findBySql($sql);
            foreach ($rows as $key => & $v) {
                $userName[] = " * ".$v['compName'].': '.$v['userName'].' ('.$v['tel'].") ";
            }
            $userName = join(" \n ",$userName);

            //组织消息信息
            $baseUrl = FLEA::getCache('base.url.server' ,-1);

            $mdlCrotab = FLEA::getSingleton('Model_Crontab');
            $service = FLEA::getSingleton('Controller_Event_DingTalk');
            $msg =array(
                'title'=>'审核扫码验证登录身份',
                'text'=>("#### 以下账号审核{$shenhe} @13775052508 \n" .
                    $userName .
                    " \n "  .
                    "  ###### 审核人{$_SESSION['USERNAME']} ".date('m-d H:i:s')." [详情]({$baseUrl})")
            );
            $msgM = $service->markDown($msg, array('13775052508'));
            $mdlCrotab->publish(
                array(
                    'type'        =>'quick',
                    'description' =>'发送钉钉群消息',
                    'action'      =>'Controller_Event_DingTalk@talk',
                ),
                array('msg'=>$msgM)
            );
        }
        //end
        echo json_encode(array('msg'=>'操作完成','success'=>true));
    }
}
?>