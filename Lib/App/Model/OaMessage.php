<?php
load_class('TMIS_TableDataGateway');
class Model_OaMessage extends TMIS_TableDataGateway {
	var $tableName = 'oa_message';
	var $primaryKey = 'id';
    var $belongsTo = array(
		  array(
			'tableClass' => 'Model_Trade_Order',
			'foreignKey' => 'orderId',
			'mappingName' => 'Order'
		)

	);

    var $needCreateLog = true;// 需要打日志则加此参数
    var $moduleName = '系统消息';// 模块名称
}
?>