<?php
FLEA::loadClass('TMIS_TableDataGateway');
class Model_Jichu_Department extends TMIS_TableDataGateway {
	var $tableName = "jichu_department";
	var $primaryKey = "id";
	var $primaryName = "depName";
    var $needCreateLog = true;// 需要打日志则加此参数
    var $moduleName = '部门管理';// 模块名称
 //    var $hasMany = array(
	// 	array(
	// 		'tableClass' => 'Model_Jichu_Employ',
	// 		'foreignKey' => 'depId',
	// 		'mappingName' => 'Employ'
	// 		//,'linkRemove'=>false,
	// 		//'linkRemoveFillValue'=>0
	// 	)
	// );
	function getDep(){
	    $str="select * from jichu_department";
		$row = $this->findBySql($str);
	    return $row;
	}

	//所有部门
	function getDepartment(){
		$row=$this->getDep();
		foreach($row as & $v){
			$arr[]=array('value'=>$v[$this->primaryKey],'text'=>$v[$this->primaryName]);
		}
		return $arr;
	}
}
?>