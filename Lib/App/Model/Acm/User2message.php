<?php
FLEA::loadClass('TMIS_TableDataGateway');
class Model_Acm_User2message extends TMIS_TableDataGateway {
	var $tableName = 'acm_user2message';
	var $primaryKey = 'id';
	var $belongsTo = array (
		array(
			'tableClass' => 'Model_Acm_User',
			'foreignKey' => 'userId',
			'mappingName' => 'User'
		),
		array(
			'tableClass' => 'Model_OaMessage',
			'foreignKey' => 'messageId',
			'mappingName' => 'Message'
		)
	);
	
}
?>