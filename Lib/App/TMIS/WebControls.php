<?php
FLEA::loadClass('FLEA_WebControls');
class TMIS_WebControls extends FLEA_WebControls {
	// 自定义控件的函数名必须是 _ctl 开头，并且接受 $name 和 $attribs 参数
	function _ctlProductsList($name, $attribs)	{
		// 从 $attribs 中提取出需要的参数，例如 sort（排序）、length（读取多少记录）
		extract(FLEA_WebControls::extractAttribs($attribs, array('sort', 'length')));
		// 根据情况设置默认值
		if (!$sort) { $sort = 'created DESC'; }
		if ($length <= 0) { $length = 5; }
	
		// 取得产品表数据入口
		$tableProducts = FLEA::getSingleton('Table_Products');
		/* @var $tableProducts Table_Products */
		
		// 按照指定的排序方式读取产品记录
		$rowset = $tableProducts->findAll(null, $sort, $length);
	
		// 取得模版引擎对象
		$view = FLEA::getSingleton(FLEA::getAppInf('view'));
		$view->assign('product_list', $rowset);
		return $view->fetch('block_product_list.html');
	}
	
	function _ctlTmisOptions($name, $attribs)	{
		return "show TmisOptions";
	}
}
?>