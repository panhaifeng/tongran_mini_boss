<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  Author :Jeff
*  FName  :api.php
*  Time   :2014/05/13 18:31:40
*  Remark :api访问的公用接口
\*********************************************************************/
define('APP_DIR','Lib/App');

//define('NEED_DB_LOG', true);//是否开启数据库保存操作日志功能
//define('NEED_DB_LOG_TIME', 120);//数据表日志有效时间(天)

define('ROOT_DIR_QUEUE',dirname(__FILE__));
define('DO_QUEUE','/doQueue.php');


// define('sessionSaveHandler','redis');
define('DEPLOY_MODE', true);//定义为发布模式，不写日志define('DEPLOY_MODE', true);//定义为发布模式，不写日志
require('Lib/FLEA/FLEA.php');
FLEA::loadAppInf("Config/config.inc.php");
FLEA::loadAppInf("Config/compInfo.php");
FLEA::import('Lib/App');
FLEA::runMVC();
?>