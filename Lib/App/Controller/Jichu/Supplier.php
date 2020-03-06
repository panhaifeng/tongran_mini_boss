<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  Author :wuyou
*  FName  :Supplier.php
*  Time   :2019/02/15 14:16:28
*  Remark :供应商档案
\*********************************************************************/
FLEA::loadClass('TMIS_Controller');
class Controller_Jichu_Supplier extends TMIS_Controller {
    var $_modelExample;
    var $title = "供应商档案";
    var $funcId = '90-9';

    function __construct() {
        $this->_modelExample = FLEA::getSingleton('Model_Jichu_Supplier');
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
                'name'      =>'compCode',
                'title'     =>'供应商代码',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'compName',
                'title'     =>'供应商名称',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'people',
                'title'     =>'联系人',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'mobile',
                'title'     =>'手机',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'address',
                'title'     =>'地址',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'zipCode',
                'title'     =>'邮编',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'fax',
                'title'     =>'传真',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'email',
                'title'     =>'E-mail',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-textarea',
                'name'      =>'memo',
                'title'     =>'备注',
                'clearable' =>true,
                'value'     =>'',
            ),
        );

        $params['rules'] = array(
            'compName'=>array(
                array(
                    'required'=>true,
                    'message'=>'供应商名称必须',
                )
            ),
        );

        return $params;
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
        $row = $this->_modelExample->find($_GET['id']);

        $this->_edit($row);
    }

    function _edit($Arr = array()) {
        $formParams = $this->buildHtml();
        if(!$Arr){
            foreach ($formParams['formItems'] as $key => $v) {
                $Arr[$v['name']] = $v['value'].'';
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
            'compCode'        =>array('text'=>'供应商代码','width'=>''),
            'compName'        =>array('text'=>'供应商名称','width'=>'240'),
            'people'          =>array('text'=>'联系人','width'=>''),
            'mobile'          =>array('text'=>'手机','width'=>''),
            'email'           =>array('text'=>'e-mail','width'=>''),
            'address'         =>array('text'=>'地址','width'=>''),
            'memo'            =>array('text'=>'备注','width'=>''),
        );

        $smarty->assign('title', '供应商列表');
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

        $rightMenu = array(
            // array('text'=>"更新环思客户数据",'name'=>'btnUpdateClients')
        );
        $smarty->assign('menuRightTop', $rightMenu);

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


        $sql = "SELECT x.*
                from jichu_supplier x
                where find_in_set('{$this->_modelExample->mark}',typeMark)";

        if($arr['key']!='') {
            $sql.=" and (x.compCode like '%{$arr['key']}%' or x.compName like '%{$arr['key']}%')";
        }
        $sql .=" order by id desc";

        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = new TMIS_Pager($sql,null,null,$pagesize ,($currentPage - 1));
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
     * @desc ：供应商档案保存
     * Time：2019/02/15 14:18:38
     * @author Wuyou
    */
    function actionSave() {
        $auth = $this->authCheck($this->funcId ,true);

        if(!$auth){
            $ret = array('msg'=>'没有登陆或编辑权限','success'=>false);
            echo json_encode($ret);exit;
        }
        //post参数
        $post = $this->axiosPost();

        if(empty($post['id'])) {
            $typeMark = $this->_modelExample->mark;
            $post['typeMark'] = $typeMark;
            $condition = array();
            $condition[] = array('compName',$post['compName'],'=');
            $condition[] = array('typeMark',$post['typeMark'],'=');
            $count = $this->_modelExample->findCount($condition);

            if($count > 0) {
                $ret = array('msg'=>'公司名称重复','success'=>false);
                echo json_encode($ret);exit;
            }
            //判断公司名称是否已存在，且类型不为当前类型；存在则加上标记
            $sql = "SELECT * FROM jichu_supplier WHERE compName='{$post['compName']}' and typeMark<>'{$typeMark}'";
            $sameComp = $this->_modelExample->findBySql($sql);
            if($sameComp[0]['id']>0){
                if(strpos($sameComp[0]['typeMark'], $typeMark)===FALSE){
                    $sameComp[0]['typeMark'] .= ",{$typeMark}";
                    $post = $sameComp[0];
                    $msgSuccess = "：存在相同".($typeMark == 's' ? '加工户' : '供应商')."，已保存并合并";
                }else{
                    $post = $sameComp[0];
                }
            }
        } else {
            //修改时判断是否重复
            $condition = array();
            $condition[] = array('compName',$post['compName'],'=');
            $condition[] = array('id',$post['id'],'<>');
            $count = $this->_modelExample->findCount($condition);

            if($count > 0) {
                $ret = array('msg'=>'公司名称重复','success'=>false);
                echo json_encode($ret);exit;
            }
        }

        //数据保存
        $result = $this->_modelExample->save($post);
        if($result){
            $ret = array(
                'success' =>true,
                'msg'     =>'操作成功'.$msgSuccess,
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
     * @desc ：删除
     * Time：2019/02/15 14:18:11
     * @author Wuyou
    */
    function actionRemove() {
        $this->authCheck($this->funcId);
        // 删除验证 TODO
        // if($_GET['id']!="") {
        //     $sql="SELECT count(*) as cnt FROM `caigou` where supplierId=".$_GET['id'];
        //     $re=$this->_modelExample->findBySql($sql);
        //     //dump($re);exit;
        //     if($re[0]['cnt']>0) {
        //         js_alert('此供应商有订单记录存在，不允许删除',null,$this->_url('Right'));
        //     }
        // }
        parent::actionRemove();
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
            'Model_Caigou_Plan'          => 'supplierId',
            'Model_Cangku_Yuanliao_Ruku' => 'supplierId',
            'Model_Caiwu_Yf_Guozhang'    => 'supplierId',
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