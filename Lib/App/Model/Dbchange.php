<?php
load_class('TMIS_TableDataGateway');
class Model_Dbchange extends TMIS_TableDataGateway {
	var $tableName = 'sys_dbchange_log';
	var $primaryKey = 'id';

    var $needCreateLog = false;// 需要打日志则加此参数
    var $moduleName = '系统表维护日志';// 模块名称
}
?>