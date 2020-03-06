<?php
load_class('TMIS_TableDataGateway');
class Model_Acm_SetParamters extends TMIS_TableDataGateway {
	var $tableName = 'sys_set';
	var $primaryKey = 'id';
	var $primaryName = 'itemName';
}
?>