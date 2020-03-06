<?php
FLEA::loadClass('TMIS_Controller');
class Controller_Acm_SetParamters extends TMIS_Controller {
    var $_modelExample;
    var $title = "系统参数设置";
    var $funcId = '95-4';

    function __construct() {
        $this->_modelExample = FLEA::getSingleton('Model_Acm_SetParamters');
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
                'type'         =>'comp-image',
                'name'         =>'comp_logo_pic',
                'title'        =>'公司Logo',
                'action'       =>$this->_url('UploadImage'),//上传地址
                'actionRemove' =>$this->_url('RemoveImage'),//删除图片时需要从服务器删除,可以不定义
                'accept'       =>'.jpg,.bmp,.PNG',//接受上传的文件类型
                'limit'        =>1,//最大允许上传个数
                'multiple'     =>false,//是否允许多选,
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'comp_taxid',
                'title'     =>'公司税号',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'comp_fax',
                'title'     =>'公司传真',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'comp_tel',
                'title'     =>'公司电话',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'comp_address',
                'title'     =>'公司地址',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'comp_qianzheng',
                'title'     =>'委托人签证',
                'clearable' =>true,
                'value'     =>'',
                'addonEnd'  =>'主管单位委托人签证',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'comp_bankName',
                'title'     =>'开户银行',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'comp_accountId',
                'title'     =>'帐号',
                'clearable' =>true,
                'value'     =>'',
            ),
            // array(
            //     'type'      =>'comp-text',
            //     'name'      =>'comp_name',
            //     'title'     =>'公司名称',
            //     'clearable' =>true,
            //     'value'     =>'',
            // ),
        );

        $params['rules'] = array(

        );

        return $params;
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
        $rows = $this->_modelExample->findAll();
        // dump($rows);exit;

        foreach ($rows as &$v) {
            if(json_decode($v['value'] ,1)){
                $v['value'] = json_decode($v['value'] ,1);
            }

            $initArr[$v['item']] = $v['value'];
        }

        // dump($initArr);exit;
        $this->_edit($initArr);
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
        $smarty->assign('title','应用参数设置 - 基本信息');
        $smarty->assign('row',$Arr);
        $smarty->assign('action',$this->_url('Save'));
        $smarty->display('MainForm.tpl');
    }


    function actionSave() {
        $this->authCheck(0);
        $_POST = $this->axiosPost();
        // dump($_POST);exit;
        $formParams = $this->buildHtml();

        foreach ($formParams['formItems'] as & $v) {
            //查找是否已经设置了
            $tmp = $this->_modelExample->find(array('item'=>$v['name']));

            //如果value是数组，则转换成json
            if(is_array($_POST[$v['name']])){
                $_POST[$v['name']] = json_encode($_POST[$v['name']]);
            }

            //处理要保存的数据
            $data = array(
                'id'       =>$tmp['id'],
                'item'     =>$v['name'],
                'itemName' =>$v['addonEnd'] ? $v['addonEnd'] : $v['title'],
                'value'    =>$_POST[$v['name']].'',
            );

            $this->_modelExample->save($data);
        }

        $ret = array(
          'success'=>true,
          'msg'=>'操作完成',
          // 'targetUrl'=>$this->_url('Edit'),
        );
        echo json_encode($ret);exit;
    }

}
?>