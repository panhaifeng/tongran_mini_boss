<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  Author :lwj
*  FName  :Shenhe.php
*  Time   :2019年5月7日
*  Remark :审核通用
\*********************************************************************/
FLEA::loadClass('TMIS_Controller');
class Controller_Shenhe_Shenhe extends TMIS_Controller {
    var $_modelExample;
    var $funcId;
    function __construct() {
        $this->_modelExample = FLEA::getSingleton('Model_Shenhe_Shenhe');
    }

    /**
     * 审核的第一个页面
     * Time：2019/05/07 13:23:51
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function actionEdit(){
        FLEA::loadClass('TMIS_Input');
        $_GET = TMIS_Input::check_input($_GET);
        $tableId = intval($_GET['tableId']);

        //找到对应的Model
        $model = 'Model_'.$_GET['model'];
        $class = FLEA::getSingleton($model);
        $table_name = $class->tableName;
        $moduleName = $class->moduleName;
        $nodeId = '';//审核节点，暂时未空

        if(!$table_name){
            echo "加载的class有问题,无法加载数据";
            exit;
        }

        //获取是否已经审核过了
        $Arr = $this->_modelExample->find(array('nodeName'=>$_GET['model'],'tableId'=>$tableId,'nodeId'=>$nodeId));
        // dump($Arr);exit;
        //处理数据
        if(!$Arr){
            $Arr = array(
                'nodeName' =>$_GET['model'],
                'tableId'  =>$tableId,
                'nodeId'   =>$nodeId,
            );
        }

        //个性化动态文字设置
        if($this->_modelExample->_buildHtml[$_GET['model']]){
            list($ctl ,$func) = explode('@', $this->_modelExample->_buildHtml[$_GET['model']]);
            $service = FLEA::getSingleton($ctl);
            $moduleName = $service->$func($tableId);
        }
        $formParams = $this->buildHtml($moduleName);

        // dump($formParams);exit;

        $smarty = & $this->_getView();

        $smarty->assign('formItems',$formParams['formItems']);
        $smarty->assign('rules',$formParams['rules']);
        $smarty->assign('title','审核基本信息');
        $smarty->assign('row',$Arr);
        $smarty->assign('action',$this->_url('Save'));
        $smarty->display('MainForm.tpl');
    }

    /**
     * 角色html配置form
     * Time：2018/12/13 15:49:40
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    public function buildHtml($title) {
        $params = array();
        $params['formItems'] = array(
            array(
                'type'       =>'comp-message-alert',
                'alertType'  =>'info',
                'name'       =>'_title',
                'alertTitle' =>'审核'.$title,
                'closable'   =>false,
            ),
            array(
                'type'       =>'comp-select',
                'name'       =>'status',
                'title'      =>'审核状态',
                'value'      =>'',
                'clearable'  =>false,
                'options'    =>array(
                    array('text' =>'通过','value'=>'yes'),
                    array('text' =>'不通过','value'=>'no'),
                    array('text' =>'取消','value'=>'remove'),
                ),
                'filterable' =>true,
                'multiple'   =>false
            ),
            array(
                'type'      =>'comp-textarea',
                'name'      =>'memo',
                'title'     =>'原因',
                'clearable' =>true,
                'value'     =>'',
            ),
        );

        $params['rules'] = array(
            'status'=>array(
                array(
                    'required'=>true,
                    'message'=>'审核状态必须',
                )
            )
        );

        return $params;
    }

    /**
     * @desc
     * Time：2017/07/31 15:48:32
     * @author lwj
    */
    function actionSave() {
        $auth = $this->authCheck(0 ,true);

        if(!$auth){
            $ret = array(
                'success' =>true,
                'msg'     =>'登录过期',
            );
            echo json_encode($ret);exit;
        }

        $post = $this->axiosPost();

        // dump($post);exit;

        //暂时默认是最后一级
        $post['last'] = 'yes';

        !$post['shenheDate'] && $post['shenheDate'] = date('Y-m-d H:i:s');
        $post['userId'] = $_SESSION['USERID'];

        //保存前个性化处理
        if($this->_modelExample->_beforeSave[$post['nodeName']]){
            list($ctl ,$func) = explode('@', $this->_modelExample->_beforeSave[$post['nodeName']]);
            $service = FLEA::getSingleton($ctl);
            $checkResult = $service->$func($post);
            if($checkResult['success'] == false){
                $ret = array(
                    'success' =>false,
                    'msg'     =>$checkResult['msg'],
                );
                echo json_encode($ret);exit;
            }
        }

        if(!$post['id']){
            $tmpRow = $this->_modelExample->find(array('nodeName'=>$post['nodeName'],'tableId'=>$post['tableId'],'nodeId'=>$post['nodeId']));
            $tmpRow['id'] && $post['id'] = $tmpRow['id'];
        }

        //如果是取消审核，则删除审核记录
        if($post['status'] == 'remove'){
            $post['id'] && $result = $this->_modelExample->removeByPkv($post['id']);
        }else{
            //数据保存
            $result = $this->_modelExample->save($post);
        }


        if($result){
            $this->updateTableTarget($post);

            //保存后个性化处理
            if($this->_modelExample->_afterSave[$post['nodeName']]){
                list($ctl ,$func) = explode('@', $this->_modelExample->_afterSave[$post['nodeName']]);
                $service = FLEA::getSingleton($ctl);
                $service->$func($post);
            }

            $ret = array(
                'success' =>true,
                'msg'     =>'操作成功',
            );
        }else{
            $ret = array(
                'success' =>false,
                'msg'     =>'操作失败',
            );
        }

        echo json_encode($ret);exit;
    }

    //修改对应的逻辑表中的字段
    function updateTableTarget($params = array()){
        //判断是否还有其他审核字段你
        if($params['status'] == 'remove'){
            $count = $this->_modelExample->findCount(array('nodeName'=>$params['nodeName'],'tableId'=>$params['tableId']));
            $params['status'] = $count > 0 ? 'ing' : '';
        }else{
            $params['status'] = $params['last'] == 'yes' ? $params['status'] : 'ing';
        }

        $class = FLEA::getSingleton('Model_'.$params['nodeName']);
        $data = array(
            'shenhe' =>$params['status'],
            'id'     =>$params['tableId'],
        );
        $res = $class->update($data);
        return $res;
    }

    //查询
    function actionLog(){
        $searchItems = array(
            'tableId' =>$_GET['tableId'],
            'model'   =>$_GET['model'],
        );
        $smarty = & $this->_getView();
        $arrFieldInfo = array(
            "realName"   =>array('text'=>"审核人",'width'=>''),
            "shenheDate" =>array('text'=>"时间",'width'=>''),
            "status"     =>array('text'=>"审核状态",'width'=>''),
            "memo"       =>array('text'=>"原因",'width'=>''),
        );

        $smarty->assign('title', '审核记录');
        $smarty->assign('arr_field_info', $arrFieldInfo);
        $smarty->assign('action', $this->_url('GetRows'));
        $smarty->assign('searchItems', $searchItems);

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
        //处理搜搜
        $post[$keyField] = $post['key'];
        if($keyField!='key') $post['key'] = '';

        $arr = $post;

        $sql="SELECT x.*,y.realName from shenhe_db x
        left join acm_userdb y on y.id=x.userId
        where 1";

        if($arr['tableId']!='') {
            $sql.=" and x.tableId='{$arr['tableId']}'";
        }
        if($arr['model']!='') {
            $sql.=" and x.nodeName='{$arr['model']}'";
        }
        $sql .=" order by x.id asc";
        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = new TMIS_Pager($sql,null,null,$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();
        // dump($rowset);exit;
        foreach ($rowset as $key => & $v) {
            $v['status'] = $this->shenheFormat($v['status']);
        }


        $ret = array(
          'total'   =>$pager->totalCount,
          'columns' =>array(),
          'rows'    =>$rowset,
        );
        echo json_encode($ret);exit;
    }
}

?>