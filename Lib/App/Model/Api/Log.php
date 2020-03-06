<?php
load_class('TMIS_TableDataGateway');
class Model_Api_Log extends TMIS_TableDataGateway {
    var $tableName = 'api_log';
    var $primaryKey = 'id';
    // var $primaryName = 'itemName';
}