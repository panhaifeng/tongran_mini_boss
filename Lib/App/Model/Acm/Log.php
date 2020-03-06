<?php
FLEA::loadClass('TMIS_TableDataGateway');
class Model_Acm_Log extends TMIS_TableDataGateway {
    var $tableName = 'sys_log';
    var $primaryKey = 'id';
}