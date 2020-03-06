<?php
load_class('TMIS_TableDataGateway');
class Model_Jichu_ClientTaitou extends TMIS_TableDataGateway {
	var $tableName = 'jichu_client_taitou';
	var $primaryKey = 'id';
//	var $primaryName = 'compName';
	var $belongsTo = array(
		array(
			'tableClass' => 'Model_Jichu_Client',
			'foreignKey' => 'clientId',
			'mappingName' => 'Client'
		)

	);
}
?>