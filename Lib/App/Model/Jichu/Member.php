<?php
load_class('TMIS_TableDataGateway');
class Model_Jichu_Member extends TMIS_TableDataGateway {
    var $tableName = 'jichu_member';
    var $primaryKey = 'id';
    var $sortByKey = 'id desc';

    var $needCreateLog = true;// 需要打日志则加此参数
    var $moduleName = '用户档案';// 模块名称
}
?>