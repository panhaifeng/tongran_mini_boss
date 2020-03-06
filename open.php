<?php
//header('location:Building.html');
define('APP_DIR','Lib/App');
/////
define('ERROR_SYS',false);//定义为true:系统显示错误信息，定义false:系统不显示错误信息
define('ERROR_SYS_DIR','Lib/App/TMIS/ErrorSys.php');//处理错误信息地址

// define('sessionSaveHandler','redis');

define('ROOT_DIR_QUEUE',dirname(__FILE__));
define('DO_QUEUE','/doQueue.php');

//define('DEPLOY_MODE', true);//定义为发布模式，不写日志define('DEPLOY_MODE', true);//定义为发布模式，不写日志
require('Lib/FLEA/FLEA.php');
FLEA::loadAppInf("Config/config.inc.php");
FLEA::loadAppInf("Config/compInfo.php");
//url模式，
FLEA::loadAppInf(array('urlMode'=>'URL_PATHINFO'));

//在index.php中生成open.php模式的url，可以使用这种方式
//url('main','welcome',array(),null,array('mode'=>'URL_PATHINFO','bootstrap'=>'open.php'));
//在open.php入口进入程序中生成的url，默认就是上面的方式，所以只要使用url('ctl','action')就可以了，如果期望生成index.php入口的url,则使用url('main','welcome',array(),null,array('mode'=>'URL_STANDARD','bootstrap'=>'index.php'));

FLEA::import('Lib/App');
FLEA::runMVC();
?>