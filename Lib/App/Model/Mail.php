<?php
load_class('TMIS_TableDataGateway');
class Model_Mail extends TMIS_TableDataGateway {
	var $tableName = 'mail_db';
	var $primaryKey = 'id';
    var $belongsTo = array(
		array(
			'tableClass' => 'Model_Acm_User',
			'foreignKey' => 'senderId',
			'mappingName' => 'Sender'
		),
		array(
			'tableClass' => 'Model_Acm_User',
			'foreignKey' => 'accepterId',
			'mappingName' => 'Accepter'
		)

	);

	var $needCreateLog = true;// 需要打日志则加此参数
    var $moduleName = '系统内部邮件消息';// 模块名称

}
?>