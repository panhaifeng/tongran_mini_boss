<?php
load_class('TMIS_TableDataGateway');
class Model_Project_Account2Member extends TMIS_TableDataGateway {
    var $tableName = 'project_account2member';
    var $primaryKey = 'id';
    var $sortByKey = 'id desc';

    var $needCreateLog = false;// 需要打日志则加此参数
    var $moduleName = '项目账号和用户的关联表';// 模块名称
}
?>