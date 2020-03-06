<?php
load_class('TMIS_TableDataGateway');
class Model_OaMessageClass extends TMIS_TableDataGateway {
	var $tableName = 'oa_message_class';
	var $primaryKey = 'id';

	/**
	 * 组成options的数据集
	 * Time：2014/07/08 16:04:32
	 * @author li
	 * @return Array
	*/
	function getOptions(){
		$res = $this->findAll();
		$row=array();
		foreach ($res as & $v) {
			$row[]=array(
				'text'=>$v['className'],
				'value'=>$v['className']
			);
		}
		return $row;
	}
}
?>