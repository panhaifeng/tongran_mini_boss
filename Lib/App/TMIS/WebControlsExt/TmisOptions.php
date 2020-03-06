<?php
/**
*޸by jeff zeng 2007-4-5
*{webcontrol type='TmisOptions' model='' selected=''}
*/
/**
*取得用于option的数组，
*之所以扩展出该方法，是因为大多数基础信息都需要以select形式显示，以供选择.
*firstKey:第一个option的显示文字
*如果需要使option的显示标题为多个字段的值，需要如下设置
class Model_JiChu_Doctor extends TMIS_TableDataGateway {
	var $tableName = 'jichu_doctor';
	var $primaryKey = 'id';
	//var $primaryName = 'doctName';
	var $primaryName = array('doctCode','doctName');//此处必须为数组
	var $primaryNameJoiner = ':';//当需要多个字段显示在option中时的连接符
}
*
*/
function arrInOptions($model,$conditions=NULL,$noEmpty=false,$emptyText='',$valueField='',$valueKey='',$method='') {
	if (empty($emptyText)) $emptyText='请选择';
	//if (empty($valueField)) $emptyText='请选择';
	$fieldNameOfKey = empty($valueField)?$model->primaryName : $valueField;
	$fieldNameOfValue = empty($valueKey)?$model->primaryKey : $valueKey;
	$sortByKey=$model->sortByKey==''?$model->primaryKey:$model->sortByKey;
	$joiner = $model->primaryNameJoiner;


	//$ret = array($firstKey => "");
	if($noEmpty) $ret = array();
	else $ret = array($emptyText => "");

	if($method!='') {
		$arr = $model->$method();
		foreach($arr as $key=>& $v) {
			$ret[$key] = $v;
		}
		return $ret;
	}
	$arrDep = $model->findAll($conditions,$sortByKey,NULL);
	if(is_array($fieldNameOfKey)) {
		foreach($arrDep as & $v) {
			$temp = array();
			foreach($fieldNameOfKey as & $vv) {
				$temp[] = $v[$vv];
			}
			$ret[join($joiner,$temp)] = $v[$fieldNameOfValue];
		}
	} else {
		$arrDep = array_to_hashmap($arrDep,$fieldNameOfKey,$fieldNameOfValue);
		if(count($arrDep)>0) foreach($arrDep as $key=>& $v){
			$ret[$key] = $v;
		}
		//$ret = array_merge($ret,array_to_hashmap($arrDep,$fieldNameOfKey,$fieldNameOfValue));
	}
	return $ret;
}
function _ctlTmisOptions($name, $params)	{    
	if ($params['model']!="") {
		$modelName = "Model_" . $params['model'];
		$_model = FLEA::getSingleton($modelName);
		$arr = arrInOptions($_model,$params['condition'],$params['noempty'],$params['emptyText'],$params['valueField'],$params['valueKey'],$params['method']);
	} elseif ($params['inf']!="") {
		$arr=FLEA::getAppInf($params['inf']);
	}

	if(count($arr)>0) {
		$i =0;
		//需要汇总显示的，处理
		if($_model->optgroup==true){
			FLEA::loadClass('TMIS_Common');
			$letterTrue=true;
			$letter='';
		}
		foreach ($arr as $cap => $value) {
			//处理汇总信息
			if($letterTrue && $value!=''){
				$temp_lett=strtoupper(TMIS_Common::getPinyin($cap));
				if($letter!=substr($temp_lett,0,1)){
					$letter=substr($temp_lett,0,1);
					if($i>1)$ret .="</optgroup>";
					$ret .="<optgroup label='{$letter}'>";
				}
			}
			//拼接选择框
			$ret .= "<option value=\"$value\"";
			if($i==0 && $value=='') $ret .= " style=\"color:#ccc\"";
			//if selected is a var
			if (!is_array($params[selected])) {
				$ret .= ($value == $params[selected]) ? " selected" : "";
			} else {
				$tempArr = array_col_values($params[selected],$_model->primaryKey);
				if (in_array($value,array_col_values($params[selected],$_model->primaryKey))) {
					$ret .= " selected";
				}
			}
			//if selected is a array,
			$ret .= ">$cap</option>\n";
			$i++;
		}
	}
	//dump($ret);exit;
	return $ret;
}
?>