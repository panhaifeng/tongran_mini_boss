<?php
    //签名加密+yan
    $CERTI_KEY = 'spe7kbAzhMve9H0j7wrG4fh0o7gb0c6U21ca4rQ3fgb5zd1';
    $GEN_SIGN  = "Api_Lib_Rsp_Mini_genSign";//定义class，class中必须实现gen_sign方法，该方法位入口方法
    //方法映射
    $api_array = array(
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