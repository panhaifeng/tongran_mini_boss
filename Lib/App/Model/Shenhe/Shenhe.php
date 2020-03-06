<?php
FLEA::loadClass('TMIS_TableDataGateway');
class Model_Shenhe_Shenhe extends TMIS_TableDataGateway {
    var $tableName = 'shenhe_db';
    var $primaryKey = 'id';

    var $belongsTo = array(
        array(
            'tableClass' => 'Model_Acm_User',
            'foreignKey' => 'userId',
            'mappingName' => 'User'
        )
    );

    var $needCreateLog = true;// 需要打日志则加此参数
    var $moduleName = '审核表';// 模块名称

    /**
     * 审核前文字提示个性化配置
     * @var array
    */
    var $_buildHtml = array(
        'Trade_PlanFreeze' => 'Model_Trade_PlanFreeze@getShenheText',
    );

    /**
     * 审核保存前处理
     * @var array
    */
    var $_beforeSave = array(
        'Trade_PlanFreeze' => 'Model_Trade_PlanFreeze@checkFreeze',
    );

    /**
     * 审核保存后处理
     * @var array
    */
    var $_afterSave = array();
}
?>