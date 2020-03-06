<?php
load_class('TMIS_TableDataGateway');
class Model_Index extends TMIS_TableDataGateway {
	var $tableName = 'oa_message';
	var $primaryKey = 'id';
	var $primaryName = 'title';
}
?>