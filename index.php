<?php

//header('location:Building.html');
define('APP_DIR','Lib/App');
/////
define('ERROR_SYS',true);//定义为true:系统显示错误信息，定义false:系统不显示错误信息
define('ERROR_SYS_DIR','Lib/App/TMIS/ErrorSys.php');//处理错误信息地址
define('DEPLOY_MODE', true);//定义为发布模式，不写日志define('DEPLOY_MODE', true);//定义为发布模式，不写日志

define('NEED_DB_LOG', true);//是否开启数据库保存操作日志功能
define('NEED_DB_LOG_TIME', 30);//数据表日志有效时间(天)

define('ROOT_DIR_QUEUE',dirname(__FILE__));
define('DO_QUEUE','/doQueue.php');
//使用的模板路径
// define('MEM_THEME_DIR','/IndexVue');

require('Lib/FLEA/FLEA.php');
FLEA::loadAppInf("Config/config.inc.php");
FLEA::loadAppInf("Config/compInfo.php");
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
FLEA::import('Lib/App');
FLEA::runMVC();
?>