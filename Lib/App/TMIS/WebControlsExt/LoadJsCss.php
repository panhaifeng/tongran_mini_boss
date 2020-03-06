<?php
/*
*@给js或css文件自动添加版本号
*版本号为最后一次修改时间
*/
function _ctlLoadJsCss($name,$params){
	$type=pathinfo($params['src']);
	//判断文件是否存在,处理文件地址自动添加版本号
	if(file_exists($params['src']) && filemtime($params['src'])){
		//获取文件最后一次修改的时间
		$ver=filemtime($params['src']);
		$src=$params['src'].'?v='.$ver;
	}else{
		$src=$params['src'];
	}

	//判断文件夹类型
	$file='';
	if(strtolower($type['extension'])=='js'){
		$file='<script language="javascript" type="text/javascript" src="'.$src.'"></script>';
	}else if(strtolower($type['extension'])=='css'){
		$file='<link rel="stylesheet" type="text/css" href="'.$src.'" />';
	}

	return $file;
}
?>