<?php
FLEA::loadClass('TMIS_TableDataGateway');
class Model_Acm_Sninfo extends TMIS_TableDataGateway {
	var $tableName = 'acm_sninfo';
	var $primaryKey = 'id';
//	var $belongsTo = array (
//		array(
//			'tableClass' => 'Model_Acm_User',
//			'foreignKey' => 'userId',
//			'mappingName' => 'User'
//		)
//	);
	//var $primaryName = 'funcName';
//	/var $manyToMany = array(
//		array (
//			'tableClass' => 'Model_Acm_Role' ,
//			'mappingName' => 'roles',
//			'joinTable' => 'acm_func2role',
//			'foreignKey' => 'funcId',
//			'assocForeignKey' => 'roleId'
//		)
//	);/
	
}