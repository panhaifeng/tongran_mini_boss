<?php
$_sysMenu = array(
  array(
    'text'=>'项目管理',
    'expanded'=> false,
    'id'=>'1',
    'iconSpan'=>'layui-icon-senior',
    'children'=>array(
      array(
        'text'     =>'项目账号管理',
        'expanded' => false,
        'src'      =>'?controller=Jichu_Member&action=ListProject',
        'leaf'     =>true,
        'id'       =>'1-1',
        'iconCls'  =>'x-tree-icon-hide',
      ),
    ),
  ),
  array(
    'text'=>'基础档案',
    'expanded'=> false,
    'id'=>'90',
    'iconSpan'=>'layui-icon-app',
    'children'=>array(
      array(
        'text'=>'部门管理',
        'expanded'=> false,
        'src'=>'?controller=Jichu_Department&action=Right',
        'leaf'=>true,
        'id'=>'90-2',
        'iconCls'=>'x-tree-icon-hide',
      ),
      array(
        'text'=>'员工管理',
        'expanded'=> false,
        'src'=>'?controller=Jichu_Employ&action=Right',
        'leaf'=>true,
        'id'=>'90-3',
        'iconCls'=>'x-tree-icon-hide',
      ),
      /*array(
        'text'=>'产品分类',
        'expanded'=> false,
        'src'=>'?controller=Jichu_ProKind&action=Right',
        'leaf'=>true,
        'id'=>'90-7',
        'iconCls'=>'x-tree-icon-hide',
      ),*/
      array(
        'text'=>'产品档案',
        'expanded'=> false,
        'src'=>'?controller=Jichu_Product&action=Right',
        'leaf'=>true,
        'id'=>'90-4',
        'iconCls'=>'x-tree-icon-hide',
      ),
      array(
        'text'=>'客户档案',
        'expanded'=> false,
        'src'=>'?controller=Jichu_Client&action=Right',
        'leaf'=>true,
        'id'=>'90-5',
        'iconCls'=>'x-tree-icon-hide',
      ),
    )
  ),

  array(
    'text'=>'系统设置',
    'expanded'=> false,
    'id'=>'95',
    'iconSpan'=>'layui-icon-set',
    'children'=>array(
      // array(
      //   'text'=>'自定义字段设置',
      //   'iconCls'=>'x-tree-icon-hide',
      //   'expanded'=> false,
      //   'src'=>'?controller=Jichu_Custom&action=Right',
      //   'leaf'=>true,
      //   'id'=>'95-3',
      // ),
      array(
        'text'=>'系统参数设置',
        'iconCls'=>'x-tree-icon-hide',
        'expanded'=> false,
        'src'=>'?controller=Acm_SetParamters&action=Edit',
        'leaf'=>true,
        'id'=>'95-4',
      ),
      array(
        'text'=>'图片管理',
        'expanded'=> false,
        'src'=>'?controller=Jichu_Image&action=Right',
        'leaf'=>true,
        'id'=>'95-1',
        'iconCls'=>'x-tree-icon-hide',
      ),
      array(
          'text'=>'API日志',
          'iconCls'=>'x-tree-icon-hide',
          'expanded'=> false,
          'src'=>'?controller=Api_Logs&action=Right',
          'leaf'=>true,
          'id'=>'95-3',
        ),
      array(
        'text'=>'操作日志',
        'iconCls'=>'x-tree-icon-hide',
        'expanded'=> false,
        'src'=>'?controller=Sys_Log&action=Right',
        'leaf'=>true,
        'id'=>'95-5',
      ),
      array(
        'text'=>'执行队列',
        'iconCls'=>'x-tree-icon-hide',
        'expanded'=> false,
        'src'=>'?controller=CrontabLog&action=Right',
        'leaf'=>true,
        'id'=>'95-6',
      ),
    )
  ),
  array(
    'text'=>'权限管理',
    'expanded'=> false,
    'id'=>'99',
    'iconSpan'=>'layui-icon-auz',
    'children'=>array(
      array(
        'text'=>'用户管理',
        'expanded'=>false,
        'src'=>'?controller=Acm_User&action=right',
        'leaf'=>true,
        'id'=>'99-1'
      ),
      array(
        'text'=>'组管理',
        'expanded'=> false,
        'src'=>'?controller=Acm_Role&action=right',
        'leaf'=>true,
        'id'=>'99-2',
      ),
      array(
        'text'=>'权限设置',
        'expanded'=> false,
        'src'=>'?controller=Acm_Func&action=setQx',
        'leaf'=>true,
        'id'=>'99-3',
      ),
    )
  )
);
?>
