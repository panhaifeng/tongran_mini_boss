<?php
load_class('TMIS_TableDataGateway');
class Model_Jichu_Client extends TMIS_TableDataGateway {
	var $tableName = 'jichu_client';
	var $primaryKey = 'id';
    var $primaryName = 'compName';
	var $sortByKey = ' convert(trim(compName) USING gbk)';
    var $optgroup = true;
    var $needCreateLog = true;// 需要打日志则加此参数
    var $codeField = 'compCode';// 编号字段
    var $moduleName = '客户管理';// 模块名称
	 //   var $optgroup = true;

	var $belongsTo = array(
		array(
			'tableClass' => 'Model_Jichu_Employ',
			'foreignKey' => 'traderId',
			'mappingName' => 'Trader'
		),
	);


    function getOptions(){
        $sql = "select * from jichu_client where 1 ";
        if($_SESSION['USERID']){
            $mUser = FLEA::getSingleton('Model_Acm_User');
            $traderArr = $mUser->getTraderIdByUser($_SESSION['USERID']);

            if($traderArr['_ALL_'] == false){
                $traderIds = join(',',$traderArr['Traders']);
                !$traderIds && $traderIds = '-1';
                $sql .= " and traderId in ({$traderIds})";
            }
        }

        $row = $this->findBySql($sql);
        foreach($row as & $v){
            $arr[]=array('value'=>$v[$this->primaryKey],'text'=>$v[$this->primaryName]);
        }
        return $arr;
    }
}
?>