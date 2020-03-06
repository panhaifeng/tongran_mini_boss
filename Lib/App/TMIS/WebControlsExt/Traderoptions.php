<?php
function _ctlTraderoptions($name, $params)	{
	$emptyText = $params['emptyText']?$params['emptyText']:'选择业务员';
	$ret = "<option value='' style='color:#CCC'>{$emptyText}</option>";

	$m = & FLEA::getSingleton("Model_Jichu_Employ");
	$arr = $m->getTrader();

	//2012-8-28 by jeff 如果$params['selected']为已离职的，也需要显示出来
	if($params['selected']) {
		//如果传递过来的是数组，则特殊处理
		if (!is_array($params['selected'])) {
			$s = $m->find(array('id'=>$params['selected']));
			if($s['isFire']==1) {
			$arr[] = $s;
		}
		}else{
			$traderId=array_col_values($params['selected'],'id');
			$condition['in()'] = array('id'=>$traderId);
			$s = $m->findAll($condition);

			foreach($s as & $v){
				if($v['isFire']==1){
					$arr[] = $v;
				}
			}
		}
	}
		// dump($arr);exit;
	$letterTrue=$m->optgroup==true?true:false;
	$letter='';
	if($arr) foreach ($arr as $key => & $value) {
		//开始添加汇总信息
		if($letterTrue){
			if($letter!=substr($value['letters'],0,1)){
				$letter=substr($value['letters'],0,1);
				if($letter!='')$ret .="<optgroup label='{$letter}'>";
			}
		}

		//拼接下拉框
		$ret .= "<option value='{$value['id']}'";
		if (!is_array($params['selected'])) {
			if($params['selected']==$value['id']) $ret .= " selected";
		}else{
			if (in_array($value['id'],array_col_values($params['selected'],'id'))) {
					$ret .= " selected";
			}
		}
		
		if($value['isFire']) $ret.= " style='background-color:pink;'";
		$ret .= ">{$value['employName']}";
		if($value['isFire']) $ret.= "(离职)";
		$ret .= "</option>";

		//开始添加汇总信息
		if($letterTrue){
			if($letter!=substr($arr[$key+1]['letters'],0,1)){
				if($letter!='')$ret .="</optgroup>";
			}
		}
	}	
	return $ret;
}
?>