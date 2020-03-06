<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  Author :wuyou
*  FName  :Material.php
*  Time   :2019/02/14 10:57:57
*  Remark :原料档案
\*********************************************************************/
FLEA::loadClass('TMIS_Controller');
class Controller_Jichu_Material extends TMIS_Controller {
    var $_modelExample;
    var $funcId = '90-6';
    function __construct() {
        $this->_modelExample = FLEA::getSingleton("Model_Jichu_Material");
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
                'type'=>'comp-autocomplete',
                'name'=>'kind',
                'title'=>'类别',
                'clearable'=>true,
                'placeholder'=>'支持输入和筛选历史记录',
                //下面的text没用
                'options'   =>$this->_modelExample->getKinds(),
                'bindfield'=>'text1',//必选,如果不定义会报错
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'proCode',
                'title'     =>'编号',
                'clearable' =>true,
                'value'     =>'',
                'append'    =>'留空系统自动生成',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'proName',
                'title'     =>'品名',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'guige',
                'title'     =>'规格',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'type',
                'title'     =>'型号',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'        =>'comp-autocomplete',
                'name'        =>'unit',
                'title'       =>'单位',
                'clearable'   =>true,
                'options'     =>$this->_modelExample->getUnits(),
                'placeholder' =>'支持输入和筛选历史记录',
                'value'       =>'',
                'bindfield'   =>'unit2',//必选,如果不定义会报错
            ),
            array(
                'type'  =>'comp-textarea',
                'name'  =>'memo',
                'title' =>'备注',
            ),
        );

        $params['rules'] = array(
            'guige'=>array(
                array(
                    'required'=>true,
                    'message'=>'规格必须',
                )
            ),
            // 'kind'=>array(
            //     array(
            //         'required'=>true,
            //         'message'=>'类别必须',
            //     )
            // ),
            'proName'=>array(
                array(
                    'required'=>true,
                    'message'=>'品名必须',
                )
            ),
        );

        return $params;
    }

    /**
     * @desc ：原料档案
     * Time：2019/02/14 13:16:37
     * @author Wuyou
    */
    function actionRight() {
        $this->authCheck($this->funcId);

        $searchItems = array(
            'key'        =>''
        );
        $smarty = & $this->_getView();
        $arrFieldInfo = array(
            "proCode"  =>array('text'=>"编号",'width'=>''),
            "kind"     =>array('text'=>"类别",'width'=>''),
            "proName"  =>array('text'=>"品名",'width'=>''),
            "guige"    =>array('text'=>"规格",'width'=>''),
            "type"     =>array('text'=>"型号",'width'=>''),
            "unit"     =>array('text'=>"单位",'width'=>''),
            "memo"     =>array('text'=>"备注",'width'=>''),
        );

        $smarty->assign('title', '原料档案');
        $smarty->assign('arr_field_info', $arrFieldInfo);
        $smarty->assign('action', $this->_url('GetRows'));
        $smarty->assign('searchItems', $searchItems);
        $smarty->assign('colsForKey', array(
            array('text'=>'关键字','col'=>'key'),
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

        $post = $this->axiosPost();

        $pagesize    = $post['pagesize'];
        $currentPage = $post['currentPage'];

        $keyField    = isset($post['colForKey']) ? $post['colForKey'] : 'key';
        //处理搜索
        $post[$keyField] = $post['key'];
        if($keyField!='key') $post['key'] = '';

        $arr = $post;

        $condition=array();
        if($arr['key']!='') {
            $condition[] = array('proCode',"%{$arr['key']}%",'like','or');
            $condition[] = array('proName',"%{$arr['key']}%",'like');
        }

        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = & new TMIS_Pager($this->_modelExample,$condition,'id desc',$pagesize ,($currentPage - 1));
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

        //处理原料编号编码问题，自动生成的编码
        !$post['proCode'] && $post['proCode'] = $this->_autoCode('YL','','jichu_material','proCode');

        if(empty($post['id'])) {
            $condition = array();
            $condition[] = array('proCode',$post['proCode'],'=');
            $count = $this->_modelExample->findCount($condition);
            if($count){
                $ret = array('msg'=>'该编号已存在','success'=>false);
                echo json_encode($ret);exit;
            }

            $condition = array();
            $condition[] = array('proName',$post['proName'],'=');
            $condition[] = array('guige',$post['guige'],'=');
            $condition[] = array('type',$post['type'],'=');
            $count = $this->_modelExample->findCount($condition);
            if($count){
                $ret = array('msg'=>'该品名规格型号已存在','success'=>false);
                echo json_encode($ret);exit;
            }
        } else {
            $condition = array();
            $condition[] = array('id',$post['id'],'<>');
            $condition[] = array('proCode',$post['proCode'],'=');
            $count = $this->_modelExample->findCount($condition);
            if($count){
                $ret = array('msg'=>'该编号已存在','success'=>false);
                echo json_encode($ret);exit;
            }

            $condition = array();
            $condition[] = array('id',$post['id'],'<>');
            $condition[] = array('proName',$post['proName'],'=');
            $condition[] = array('guige',$post['guige'],'=');
            $condition[] = array('type',$post['type'],'=');
            $count = $this->_modelExample->findCount($condition);
            if($count){
                $ret = array('msg'=>'该品名规格型号已存在','success'=>false);
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

    //获得产品列表页面：选择框
    function actionListPro() {
        //注意axios请求的content-type为 "application/json;charset=UTF-8",必须使用php流方式接收,
        //参考https://www.cnblogs.com/winyh/p/7911204.html
        $post = $this->axiosPost();

        $pagesize = $post['pagesize'];
        $currentPage = $post['currentPage'];

        $arr = $post;

        $sql="select x.* from jichu_material x
                where 1";

        if($arr['key']!='') {
            $sql.=" and (x.proCode like '%{$arr['key']}%' or x.proName like '%{$arr['key']}%' or x.guige like '%{$arr['key']}%'  or x.kind like '%{$arr['key']}%')";
        }
        $sql .=" order by id desc";
        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = new TMIS_Pager($sql,null,null,$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();

        foreach ($rowset as $key => & $v) {
            $v['materialDesc'] = $v['kind'].' '.$v['proCode'].' '.$v['proName'].' '.$v['guige'].' '.$v['type'];
        }

        //表头信息
        $arr_field_info = array(
            "proCode"  =>array('text'=>"编号",'width'=>''),
            "kind"     =>array('text'=>"类别",'width'=>''),
            "proName"  =>array('text'=>"品名",'width'=>''),
            "guige"    =>array('text'=>"规格",'width'=>''),
            "type"     =>array('text'=>"型号",'width'=>''),
            "unit"     =>array('text'=>"单位",'width'=>''),
            "memo"     =>array('text'=>"备注",'width'=>''),
        );

        $ret = array(
          'total'   =>$pager->totalCount,
          'columns' =>$arr_field_info,
          'rows'    =>$rowset,
        );
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
            'Model_Shengchan_Plan_Material'       => 'materialId',
            'Model_Cangku_Yuanliao_Ruku2Product'  => 'materialId',
            'Model_Cangku_Yuanliao_Chuku2Product' => 'materialId',
            'Model_Caiwu_Yf_Guozhang'             => 'materialId',
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