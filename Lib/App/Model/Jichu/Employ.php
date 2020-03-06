<?php
load_class('TMIS_TableDataGateway');
class Model_Jichu_Employ extends TMIS_TableDataGateway {
	var $tableName = 'jichu_employ';
	var $primaryKey = 'id';
	var $primaryName = 'employName';
    var $needCreateLog = true;// 需要打日志则加此参数
    var $codeField = 'employCode';// 编号字段
    var $moduleName = '员工管理';// 模块名称
	var $belongsTo = array (
		array(
			'tableClass' => 'Model_Jichu_Department',
			'foreignKey' => 'depId',
			'mappingName' => 'Dep'
		)
	);


	function getTrader($all = true){
	    $str="select x.* from jichu_employ x
		left join jichu_department y on y.id=x.depId
		where depName like '%销售%' or depName like '%业务%'
		";
		if($all == false){
			$str .= " and isFire=0";
		}
		if($_SESSION['USERID']){
            $mUser = FLEA::getSingleton('Model_Acm_User');
            $traderArr = $mUser->getTraderIdByUser($_SESSION['USERID']);

            if($traderArr['_ALL_'] == false){
                $traderIds = join(',',$traderArr['Traders']);
                !$traderIds && $traderIds = '-1';
                $str .= " and x.id in ({$traderIds})";
            }
        }

		$row = $this->findBySql($str);
	    return $row;
	}
	//形成下拉框选项
	//$isFire = true 展示所有业务员，为false表示不展示已离职的
	function getSelect($all = true){
		$row = $this->getTrader($all);
		foreach($row as & $v){
			$arr[]=array('value'=>$v[$this->primaryKey],'text'=>$v[$this->primaryName].($v['isFire'] == 0 ? '' : '(已离职)'));
		}
		return $arr;
	}


	function getEmp($all = true){
	    $str="select x.* from jichu_employ x
		left join jichu_department y on y.id=x.depId
		where 1";
		if($all == false){
			$str .= " and isFire=0";
		}
		$row = $this->findBySql($str);
	    return $row;
	}

	//所有员工
	function getEmploy($all = true){
		$row = $this->getEmp($all);

		foreach($row as & $v){
			$arr[]=array('value'=>$v[$this->primaryKey],'text'=>$v[$this->primaryName].($v['isFire'] == 0 ? '' : '(已离职)'));
		}
		return $arr;
	}

}
?>