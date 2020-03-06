<?php
    //签名加密+yan
    $CERTI_KEY = 'spe7kbAzhMve9H0j7wrG4fh0o7gb0c6U21ca4rQ3fgb5zd1';
    $GEN_SIGN  = "Api_Lib_Rsp_Mini_genSign";//定义class，class中必须实现gen_sign方法，该方法位入口方法
    //方法映射
    $api_array = array(
        // 用户相关数据
        'get.openid.code'=>array('class'=>'Api_Lib_Rsp_Mini_Tongran_User','method'=>'getOpenid','title'=>'获取用户openid'),
        //用户绑定数据
        'get.member.project.list'=>array('class'=>'Api_Lib_Rsp_Mini_Tongran_User','method'=>'getUserProjectList','title'=>'获取项目列表'),
        'add.project.scancode'=>array('class'=>'Api_Lib_Rsp_Mini_Tongran_User','method'=>'addProject','title'=>'添加并绑定项目'),
        'member.unbind'=>array('class'=>'Api_Lib_Rsp_Mini_Tongran_User','method'=>'unbind','title'=>'添加并绑定项目'),
        //erp数据
        'get.BarMonth.data'=>array('class'=>'Api_Lib_Req_Mini_Tongran_Base','method'=>'BarMonthData','title'=>'合同汇总柱形图数据接口'),
        'get.Month.data'=>array('class'=>'Api_Lib_Req_Mini_Tongran_Base','method'=>'MonthData','title'=>'合同汇总数据接口'),
        'get.Client.data'=>array('class'=>'Api_Lib_Req_Mini_Tongran_Base','method'=>'ClientData','title'=>'返回客户汇总数据'),
        'get.Saler.data'=>array('class'=>'Api_Lib_Req_Mini_Tongran_Base','method'=>'SalerData','title'=>'返回业务员汇总数据'),
        'get.Profit.data'=>array('class'=>'Api_Lib_Req_Mini_Tongran_Base','method'=>'ProfitData','title'=>'返回利润汇总数据'),
        'get.Sales.data'=>array('class'=>'Api_Lib_Req_Mini_Tongran_Base','method'=>'SalesData','title'=>'获取销售额汇总数据'),
        'get.Year.list'=>array('class'=>'Api_Lib_Req_Mini_Tongran_Base','method'=>'YearsData','title'=>'获取订单年份数据'),
        'get.Setting.data'=>array('class'=>'Api_Lib_Req_Mini_Tongran_Base','method'=>'SettingData','title'=>'获取系统配置(判断是否需要报工)'),
        
        /*小程序登录*/
        'login.user.mp' =>array(
            'class'  =>'Api_Lib_Req_Mini_Tongran_Login',
            'method' =>'loginMp',
            'title'  =>'小程序专用接口,获取对应个新信息',
            'params' =>array(
                'userinfo' => array('title'=>'用户信息','type'=>'json'),
                'provider' => '平台名称',
            ),
        ),
        'login.bind.mp' =>array(
            'class'  =>'Api_Lib_Req_Mini_Tongran_Login',
            'method' =>'bindMpCode',
            'title'  =>'小程序专用接口,绑定二维码',
            'params' =>array(
                'code' => array('title'=>'条码信息','type'=>'json'),
            ),
        ),
        'login.logout' =>array(
            'class'  =>'Api_Lib_Req_Mini_Tongran_Login',
            'method' =>'loginout',
            'title'  =>'退出'
        ),
        'menu.list.get' =>array(
            'class'  =>'Api_Lib_Req_Mini_Tongran_Login',
            'method' =>'getMenu',
            'title'  =>'获取首页的菜单项目',
        ),
        'userinfo.openid.check' =>array(
            'class'  =>'Api_Lib_Req_Mini_Tongran_Login',
            'method' =>'checkUser',
            'title'  =>'检查小程序的openid是否解绑',
        ),
    );

?>