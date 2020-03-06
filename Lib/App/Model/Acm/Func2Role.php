<?php
FLEA::loadClass('TMIS_TableDataGateway');
class Model_Acm_Func2Role extends TMIS_TableDataGateway {
	var $tableName = 'acm_func2role';
	var $primaryKey = 'id';
    var $needCreateLog = false;// 需要打日志则加此参数
}
?>