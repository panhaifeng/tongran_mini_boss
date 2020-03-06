#!/usr/local/bin/php
<?php
//把目录指向当前运行文件目录
chdir(dirname(__FILE__));

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
ignore_user_abort(1);
set_time_limit(0);

define('ROOT_DIR_QUEUE',dirname(__FILE__));
define('DO_QUEUE','/doQueue.php');
define('APP_DIR','Lib/App');
/////
define('ERROR_SYS',true);//定义为true:系统显示错误信息，定义false:系统不显示错误信息
define('ERROR_SYS_DIR','Lib/App/TMIS/ErrorSys.php');//处理错误信息地址
define('DEPLOY_MODE', false);//定义为发布模式，不写日志define('DEPLOY_MODE', true);//定义为发布模式，不写日志

require('Lib/FLEA/FLEA.php');
FLEA::loadAppInf("Config/config.inc.php");
FLEA::loadAppInf("Config/compInfo.php");
FLEA::import('Lib/App');
FLEA::runMVC();