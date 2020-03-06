<?php
/**
 *޸by jeff zeng 2007-4-5
 *{webcontrol type='Clientoptions' selected=''}
 *以联动select方式显示供应商控件
 *因为建立的客户不多，所以改为普通的select控件
 */
function _ctlClientoptions($name,$params) {
	$m = & FLEA::getSingleton('Model_Jichu_Client');
	$sql = "select * from jichu_client where 1";

	//查找关联到的业务员的信息
	if($params['isLimit']=='1'){
		$mUser = & FLEA::getSingleton('Model_Acm_User');
		/*$canSeeAllOrder = $mUser->canSeeAllOrder($_SESSION['USERID']);
		if(!$canSeeAllOrder) {//如果不能看所有订单，得到当前用户的关联业务员
			 $traderId = $mUser->getTraderIdByUser($_SESSION['USERID']);
			 if($traderId)$sql .= " and traderId in($traderId)";
		}*/
		$traderArr = $mUser->getTraderIdByUser($_SESSION['USERID']);
        if($traderArr['_ALL_'] == false){
            $traderId = join(',',$traderArr['Traders']);
            !$traderId && $traderId = '-1';
            $sql .= " and traderId in ({$traderId})";
        }
	}
	$sql.=" order by ";
	$kg = & FLEA::getAppInf('khqcxs');
	if($kg)$sql.=" letters";
	else $sql.=" compCode";
	// dump($sql);
	$rowset = $m->findBySql($sql);
	// dump($rowset);exit;
	$ret=$m->options($rowset,$params);
	// $ret = "<option value='' style='color:#ccc'>选择客户</option>";
	//用于判断letter是否相同，
	// $letterTrue=$m->optgroup==true?true:false;
	// if(!$kg)$letterTrue=false;
	// $letter='';
	// foreach($rowset as $k => & $v) {
	// 	//根据开关变量khqcxs判断是显示客户全称还是显示客户助记码
	// 	if($kg) $temp = $v['compName'];
	// 	else {
	// 		if($v['compCode']=='') $temp = $v['compName'];
	// 		else $temp = $v['compCode'];
	// 	}
	// 	//$temp = 'aa';
	// 	//$ret .= "<option value='{$v['id']}'>{$temp}</options>";
	// 	//开始添加汇总信息
	// 	if($letterTrue){
	// 		if($letter!=substr($v['letters'],0,1)){
	// 			$letter=substr($v['letters'],0,1);
	// 			if($letter!='')$ret .="<optgroup label='{$letter}'>";
	// 		}
	// 	}
	// 	//拼接下拉框
	// 	$ret .= "<option value='{$v['id']}'";
	// 	if($params['selected']==$v['id']) $ret .= " selected";
	// 	$ret .= ">{$temp}</option>";

	// 	//开始添加汇总信息
	// 	if($letterTrue){
	// 		if($letter!=substr($rowset[$k+1]['letters'],0,1)){
	// 			if($letter!='')$ret .="</optgroup>";
	// 		}
	// 	}
	// }
	return $ret;
}
?>