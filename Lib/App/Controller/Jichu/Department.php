<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  Author :wuyou
*  FName  :Department.php
*  Time   :2017/07/31 15:32:31
*  Remark :部门档案管理
\*********************************************************************/
FLEA::loadClass('TMIS_Controller');
class Controller_Jichu_Department extends TMIS_Controller {
    var $_modelExample;
    var $funcId = '90-2';
    function __construct() {
        $this->_modelExample = FLEA::getSingleton("Model_Jichu_Department");
    }

    /**
     * 角色html配置form
     * Time：2018/12/13 15:49:40
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    public function buildHtml() {
        $params = array();
        $params['formItems'] = array(
            array(
                'type'      =>'comp-text',
                'name'      =>'depName',
                'title'     =>'部门名称',
                'clearable' =>true,
                'value'     =>'',
            )
        );

        $params['rules'] = array(
            'depName'=>array(
                array(
                    'required'=>true,
                    'message'=>'部门名称必须',
                )
            ),
        );

        return $params;
    }

    /**
     * @desc ：部门档案查询
     * Time：2017/07/31 15:33:05
     * @author li
    */
    function actionRight() {
        $this->authCheck($this->funcId);

        $searchItems = array(
            'key'        =>''
        );
        $smarty = & $this->_getView();
        $arrFieldInfo = array(
            "id"       =>array('text'=>"编号",'width'=>''),
            "depName"  =>array('text'=>"部门名称",'width'=>''),
        );

        $smarty->assign('title', '部门列表');
        $smarty->assign('arr_field_info', $arrFieldInfo);
        $smarty->assign('action', $this->_url('GetRows'));
        $smarty->assign('searchItems', $searchItems);
        $smarty->assign('colsForKey', array(
                array('text' =>'关键字','col'=>'key')
        ));

        $smarty->assign('editButtons',array(
            array('text'=>'编辑','type'=>'redirect','icon'=>'el-icon-edit','options'=>array(
                //点击后跳转的地址
                'url'            =>$this->_url('Edit').'&id={id}',
                'disabledColumn' =>'__disabledEdit',
            )),
            array('text'=>'删除','type'=>'remove','options'=>array(
                'url'            =>$this->_url('RemoveAjax'),
                'disabledColumn' =>'__disabledRemove',
            )),
        ));

        $smarty->assign('addUrl',$this->_url('Add'));

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

        $requestParam = file_get_contents('php://input');
        $_POST = json_decode($requestParam,true);

        $pagesize    = $_POST['pagesize'];
        $currentPage = $_POST['currentPage'];

        $arr = $_POST;

        $condition=array();
        if($arr['key']!='') {
            $condition[] = array('depName',"%{$arr['key']}%",'like');
        }

        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = & new TMIS_Pager($this->_modelExample,$condition,'id asc',$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();
        // dump($rowset);exit;
        /*foreach($rowset as & $v){

        }*/

        $ret = array(
          'total'   =>$pager->totalCount,
          'columns' =>array(),
          'rows'    =>$rowset,
        );
        echo json_encode($ret);exit;
    }

    /**
     * 新增
     * Time：2018/12/17 13:35:37
     * @author li
    */
    function actionAdd() {
        $this->authCheck($this->funcId);
        $this->_edit();
    }

    /**
     * 编辑修改
     * Time：2018/12/17 13:36:35
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function actionEdit() {
        $this->authCheck($this->funcId);
        $arr = $this->_modelExample->find($_GET['id']);
        $this->_edit($arr);
    }

    function _edit($Arr = array()) {
        $formParams = $this->buildHtml();
        if(!$Arr){
            foreach ($formParams['formItems'] as $key => $v) {
                $Arr[$v['name']] = '';
            }
        }

        // dump($Arr);exit;

        $smarty = & $this->_getView();

        $smarty->assign('formItems',$formParams['formItems']);
        $smarty->assign('rules',$formParams['rules']);
        $smarty->assign('title','基本信息');
        $smarty->assign('row',$Arr);
        $smarty->assign('action',$this->_url('Save'));
        $smarty->display('MainForm.tpl');
    }

    /**
     * 保存
     * Time：2018/12/17 13:41:52
     * @author li
    */
    function actionSave() {
        $this->authCheck($this->funcId);
        $post = $this->axiosPost();

        if(empty($post['id'])) {
            $condition = array();
            $condition['depName'] = $post['depName'];
            $count = $this->_modelExample->findCount($condition);

            if($count > 0) {
                $ret = array('msg'=>'部门名称重复','success'=>false);
                echo json_encode($ret);exit;
            }
        } else {
            //修改时判断是否重复
            $condition = array();
            $condition['depName'] = $post['depName'];
            $condition[] = array('id',$post['id'],'<>');
            $count = $this->_modelExample->findCount($condition);

            if($count > 0) {
                $ret = array('msg'=>'部门名称重复','success'=>false);
                echo json_encode($ret);exit;
            }
        }

        //数据保存
        $result = $this->_modelExample->save($post);
        if($result){
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

    /**
     * @desc ：员工档案删除 验证是否已经被使用
     * Time：2017/07/31 15:49:06
     * @author lwj
    */
    function actionRemoveAjax() {
        $post = $this->axiosPost();
        $id = intval($post['row']['id']);

        //查找是否已经被使用
        $array = array(
            'Model_Jichu_Employ' => 'depId',
        );

        $isOccupy = false;

        foreach ($array as $key =>  & $v) {
            $tmpModel = FLEA::getSingleton($key);
            $condition = array();
            $condition[$v] = $id;
            $count = $tmpModel->findCount($condition);

            //如果某个表中存在，则不能删除
            if($count > 0){
                $isOccupy = true;
                $msg = $tmpModel->moduleName;
                break;
            }
        }

        if($isOccupy){
            $ret = array(
                'success' =>false,
                'msg'     =>'被'.$msg.'使用，禁止删除',
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
}
?>