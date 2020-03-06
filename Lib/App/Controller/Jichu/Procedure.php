<?php
/*********************************************************************\
*  Copyright (c) 2007-2015, TH. All Rights Reserved.
*  Author :li
*  FName  :Dingagent.php
*  Time   :2018/12/25 10:56:58
*  Remark :基础档案
\*********************************************************************/
FLEA::loadClass('TMIS_Controller');
class Controller_Jichu_Procedure extends TMIS_Controller {
    var $funcId = '90-11';
    /**
     * 构造函数
     * @var 参数类型
    */
    function __construct(){
        $this->_modelExample = FLEA::getSingleton('Model_Jichu_Procedure');
        $this->_modelPkind = FLEA::getSingleton('Model_Jichu_ProKind');
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
        $params['mainFormItems'] = array(
            array(
                'type'      =>'comp-text',
                'name'      =>'kindName',
                'title'     =>'产品分类',
                'readonly'  =>true,
                'value'     =>'',
            ),
        );
        $params['sonFormItems'] = array(
            'itemName'=>array(
                'type'      =>'comp-text',
                'name'      =>'itemName',
                'title'     =>'工序名称',
                'clearable' =>true,
                'value'     =>'',
            ),
            'price'=>array(
                'type'      =>'comp-text',
                'name'      =>'price',
                'title'     =>'单价',
                'clearable' =>true,
                'value'     =>'',
                'addonEnd'  =>'员工产量单价',
            ),
        );

        $params['columnsSon'] = array(
            'id'       =>array('text'=>'ID','width'=>'','showButton'=>true),
            'itemName' =>array('text'=>'工序名称','width'=>''),
            'price'    =>array('text'=>'单价','width'=>''),
        );

        $params['rules'] = array(
            'kindName'=>array(
                array('required'=>true,'message'=>'产品分类必须',)
            ),
        );

        return $params;
    }


    /**
     * @desc ：员工档案查询
     * Time：2017/07/31 15:45:28
     * @author li
    */
    function actionRight() {
        $this->authCheck($this->funcId);

        $searchItems = array(
            'key'    =>'',
        );
        $smarty = & $this->_getView();
        $arrFieldInfo = array(
            "id"       =>array('text'=>"#ID",'width'=>'100'),
            "kindName" =>array('text'=>"产品分类",'width'=>'','forKeySearch'=>true),
            "itemName"    =>array('text'=>"工序",'width'=>''),
        );

        $smarty->assign('title', '工序列表');
        $smarty->assign('arr_field_info', $arrFieldInfo);
        $smarty->assign('action', $this->_url('GetRows'));
        $smarty->assign('searchItems', $searchItems);

        $smarty->assign('editButtons',array(
            array('text'=>'编辑','type'=>'redirect','icon'=>'el-icon-edit','options'=>array(
                //点击后跳转的地址
                'url'            =>$this->_url('Edit').'&id={id}',
                'disabledColumn' =>'__disabledEdit',
            )),
        ));

        // $smarty->assign('addUrl',$this->_url('Add'));
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
              'title'=>'工序信息',
              'options'=>array(
                'columns'=>array(
                  'itemName' => array('text'=>'工序名称','width'=>''),
                  'price'    => array('text'=>'单价','width'=>''),
                ),
                //每条记录中代表子表记录集的字段
                'sonKey'=>'ProSon',
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

        $sql="select x.* from jichu_prokind x where 1";

        if($arr['key']!='') {
            $sql.=" and x.kindName like '%{$arr['key']}%'";
        }
        $sql .=" order by x.id desc";

        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = new TMIS_Pager($sql,null,null,$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();
        // dump($rowset);exit;
        foreach ($rowset as $key => & $v) {
            //查找工序信息
            $sql = "select * from jichu_procedure where kid = '{$v['id']}'";
            $res = $this->_modelExample->findBySql($sql);
            $v['ProSon'] = $res;

            $v['itemName'] = '无';
            if($res){
                $v['itemName'] = '已设置';
            }
        }

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
        $row = $this->_modelPkind->find($_GET['id']);

        $sql = "select * from jichu_procedure where kid = '{$row['id']}'";
        $res = $this->_modelExample->findBySql($sql);
        $row['Items'] = $res;

        $this->_edit($row);
    }

    function _edit($Arr = array()) {
        $formParams = $this->buildHtml();
        if(!$Arr){
            foreach ($formParams['mainFormItems'] as $key => $v) {
                $Arr[$v['name']] = $v['value'].'';
            }
        }

        if(!$Arr['Items']) {
            $Arr['Items'] = array();
        }

        // dump($Arr);exit;

        $smarty = & $this->_getView();

        $smarty->assign('sonFormItems',$formParams['sonFormItems']);
        $smarty->assign('mainFormItems',$formParams['mainFormItems']);
        $smarty->assign('columnsSon',$formParams['columnsSon']);
        $smarty->assign('sonKey','Items');
        $smarty->assign('urlRemoveSon',$this->_url('RemoveSonAjax'));
        $smarty->assign('rules',$formParams['rules']);
        $smarty->assign('title','基本信息');
        $smarty->assign('sonButtons',array(
            array('text'=>'修改','type'=>'edit'),
            array('text'=>'删除','type'=>'remove','options'=>array(
                'funcName'=>'handleDelete',
                'url'=>$this->_url('RemoveSonAjax'),
                //如果子表记录中存在__url字段,则会将$son['__url']作为ajax提交地址
                //适用于每行子表记录对应不同的ajax地址的场景
                'urlColumn'=>'__url',
            )),
        ));
        $smarty->assign('row',$Arr);
        $smarty->assign('action',$this->_url('Save'));
        $smarty->display('MainSonForm.tpl');
    }

    /**
     * 档案保存
     * Time：2017/07/31 15:48:32
     * @author li
    */
    function actionSave() {
        $auth = $this->authCheck($this->funcId ,true);
        if(!$auth){
            $ret = array('msg'=>'登录权限过期','success'=>false);
            echo json_encode($ret);exit;
        }
        $post = $this->axiosPost();

        $data = array();
        foreach ($post['Items'] as $key => &$v) {
            if($v['itemName'] && $post['id']){
                $data[] = array(
                    'kid'      =>$post['id'],
                    'id'       =>$v['id'],
                    'itemName' =>$v['itemName'],
                    'price'    =>$v['price'],
                );
            }
        }
        // dump($data);exit;
        if(!$data){
            $ret = array('msg'=>'无有效工序信息','success'=>false);
            echo json_encode($ret);exit;
        }
        //数据保存
        $result = $this->_modelExample->saveRowset($data);
        if($result){
            $ret = array(
                'success'   =>true,
                'msg'       =>'操作成功',
                'targetUrl' =>$this->_url('Edit' ,array('id'=>$post['id'])),
            );
        }else{
            $ret = array(
                'success' =>false,
                'msg'     =>'操作失败',
            );
        }

        echo json_encode($ret);exit;
    }


    function actionRemoveSonAjax() {
        $requestParam = file_get_contents('php://input');
        $_POST = json_decode($requestParam,true);
        $id = intval($_POST['id']);
        //查找是否允许删除
        $sql = "select count(id) as cnt from shengchan_plan2procedure where procedureId = '{$id}'";
        $count = $this->_modelExample->findBySql($sql);
        if($count[0]['cnt'] > 0){
            $ret = array(
                'success' =>false,
                'msg'     =>'生产制作单已使用工序，不能删除',
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