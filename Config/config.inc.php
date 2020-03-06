<?php
return array(
    'systemName' =>'EQINFO软件5.0',
    'systemV' =>'EQINFO软件5.0',
    'appName' =>'小程序',
    'primaryBusiness'=>'',
    'contact'=>"",
    'pageSize' => 25,
    'webControlsExtendsDir' => APP_DIR . '/TMIS/WebControlsExt',

    'view' => 'FLEA_View_Smarty',
    'viewConfig' => array(
        'smartyDir'         => APP_DIR . '/../Smarty',
        'template_dir'      => APP_DIR . '/Template' . (defined('MEM_THEME_DIR') ? MEM_THEME_DIR :''),
        'compile_dir'       => APP_DIR . '/../../_Cache/Smarty'. (defined('MEM_THEME_DIR') ? MEM_THEME_DIR :''),
        'left_delimiter'    => '<{',
        'right_delimiter'   => '}>',
    ),



	#应用程序标题
    'appTitle' => 'FleaPHP EqDeport',



    #指定默认控制器
    'defaultController' => 'Index',


    #指示默认语言
    'defaultLanguage' => 'chinese-utf8',



    #上传目录和 URL 访问路径
    //'uploadDir' => UPLOAD_DIR,
    //'uploadRoot' => UPLOAD_ROOT,



    #缩略图的大小、可用扩展名
    'thumbWidth' => 166,
    'thumbHeight' => 166,
    'thumbFileExts' => 'gif,png,jpg,jpeg',


    #商品大图片的最大文件尺寸和可用扩展名
    'photoMaxFilesize' => 1000 * 1024,
    'photoFileExts' => 'gif,png,jpg,jpeg',



    #模板显示控制,同一产品不同版本的控制
	'displayTaihao' => 0,	//是否显示销售清单中的胎号栏, 非轮胎经营部都不需要.



    /**
     * 使用默认的控制器 ACT 文件
     *
     * 这样可以避免为每一个控制器都编写 ACT 文件
     */
    //'defaultControllerACTFile' => dirname(__FILE__) . '/Lib/App/Controller/Login.act.php',



    #必须设置该选项为 true，才能启用默认的控制器 ACT 文件
    //'autoQueryDefaultACTFile' => true,


	#Fleaphp框架缓存目录
	'internalCacheDir' => APP_DIR . '/../../_Cache/Fleaphp'
);
?>