<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  Author :wuyou
*  FName  :Factory.php
*  Time   :2019/02/15 15:59:02
*  Remark :加工户档案
\*********************************************************************/
FLEA::loadClass('Controller_Jichu_Supplier');
class Controller_Jichu_Factory extends Controller_Jichu_Supplier {
    var $_modelExample;
    var $title = "加工户档案";
    var $funcId = '90-10';

    function __construct() {
        $this->_modelExample = FLEA::getSingleton('Model_Jichu_Factory');
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
                'title'     =>'加工户代码',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'compName',
                'title'     =>'加工户名称',
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
                    'message'=>'加工户名称必须',
                )
            ),
        );

        return $params;
    }

    /**
     * @desc ：加工户档案查询
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
            'compCode'        =>array('text'=>'加工户代码','width'=>''),
            'compName'        =>array('text'=>'加工户名称','width'=>'240'),
            'people'          =>array('text'=>'联系人','width'=>''),
            'mobile'          =>array('text'=>'手机','width'=>''),
            'email'           =>array('text'=>'e-mail','width'=>''),
            'address'         =>array('text'=>'地址','width'=>''),
            'memo'            =>array('text'=>'备注','width'=>''),
        );

        $smarty->assign('title', '加工户列表');
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
     * @desc ：员工档案删除 验证是否已经被使用
     * Time：2017/07/31 15:49:06
     * @author lwj
    */
    function actionRemoveAjax() {
        $post = $this->axiosPost();
        $id = intval($post['row']['id']);

        //查找是否已经被使用
        $array = array(
            // 'Model_Jichu_Employ' => 'depId',
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