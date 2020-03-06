<?php
FLEA::loadClass('TMIS_Controller');
class Controller_OaMessageClass extends TMIS_Controller {
	var $_modelExample;
	// var $_tplEdit = "OaMessageClass.tpl";
	function Controller_OaMessageClass() {
		$this->_modelExample = &FLEA::getSingleton('Model_OaMessageClass');

		$this->fldMain = array(
			'id'=>array('title'=>'','type'=>'hidden','value'=>''),
        	'className'=>array('title'=>'通知类别','type'=>'text','value'=>''),
        	'isWindow'=>array('title'=>'是否弹出提示窗','type'=>'radio','value'=>'0','radios'=>array(
        			array('title'=>'是','value'=>0),
        			array('title'=>'否','value'=>1)
        	)),
        );

        $this->rules = array(
			'className'=>'required repeat'
		);

	}
	function actionRight() {
		$this->authCheck();
		$title = '通知类别列表'; 
		// /////////////////////////////模板文件
		$tpl = 'TblList.tpl'; 
		// /////////////////////////////表头
		$arr_field_info = array('_edit' => '操作',
			"className" => '类别名称',
			'isWindow' => array('text' => '是否弹出提示窗', 'width' => 110),
			);

		FLEA::loadClass('TMIS_Pager');
		$arr = TMIS_Pager::getParamArray(array('key' => ''
				));
		if ($arr['key'] != '') {
			$condition[] = array('className', "%{$arr['key']}%", 'like');
		}
		$pager = &new TMIS_Pager($this->_modelExample, $condition, 'id desc');
		$rowset = $pager->findAll();
		foreach($rowset as &$v) {
			$v['_edit'] = $this->getEditHtml($v['id']) . '&nbsp;&nbsp;' . $this->getRemoveHtml($v['id']);
			$v['isWindow'] = $v['isWindow'] == 0 ?'是':'否';
		}
		$smarty = &$this->_getView();
		$smarty->assign('title', $title);
		$smarty->assign('arr_field_info', $arr_field_info);
		$smarty->assign('arr_field_value', $rowset);
		$smarty->assign('arr_condition', $arr);
		$smarty->assign('arr_js_css', $this->makeArrayJsCss(array('calendar')));
		$smarty->assign('page_info', $pager->getNavBar($this->_url($_GET['action'], $arr)));
		$smarty->display($tpl);
	}

	function actionEdit(){
        $row = $this->_modelExample->find(array('id'=>$_GET['id']));
        $this->fldMain = $this->getValueFromRow($this->fldMain,$row);

        $smarty = & $this->_getView();
        $smarty->assign('fldMain',$this->fldMain);
        $smarty->assign('rules',$this->rules);
        $smarty->assign('title','信息编辑');
        $smarty->assign('aRow',$row);
        $smarty->display('Main/A1.tpl');
    }

    function actionAdd($Arr){
        $smarty = & $this->_getView();
        $smarty->assign('fldMain',$this->fldMain);
        $smarty->assign('title','信息编辑');
        $smarty->assign('rules',$this->rules);
        $smarty->display('Main/A1.tpl');
    }

}

?>