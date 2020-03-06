<?php
load_class('TMIS_TableDataGateway');
class Model_Project_Account extends TMIS_TableDataGateway {
    var $tableName = 'project_account';
    var $primaryKey = 'id';
    var $sortByKey = 'id desc';

    var $needCreateLog = true;// 需要打日志则加此参数
    var $moduleName = '项目和账号';// 模块名称
}
?>