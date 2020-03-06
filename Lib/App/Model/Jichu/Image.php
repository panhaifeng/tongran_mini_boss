<?php
load_class('TMIS_TableDataGateway');
class Model_Jichu_Image extends TMIS_TableDataGateway {
    var $tableName = 'jichu_image';
    var $primaryKey = 'id';
    var $sortByKey = 'id desc';

    var $needCreateLog = false;// 需要打日志则加此参数
    var $moduleName = '图片档案';// 模块名称
}
?>