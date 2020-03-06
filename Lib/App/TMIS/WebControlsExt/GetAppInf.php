<?php
/**
*޸by jeff zeng 2007-4-5
*{webcontrol type='GetAppInf' varName='compName'}
*在模板中获取配置文件中的变量。
*/
function _ctlGetAppInf($name,$params)	{
	return FLEA::getAppInf($params[varName]);
}
?>