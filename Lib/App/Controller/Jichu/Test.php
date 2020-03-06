<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  Author :wuyou
*  FName  :Product.php
*  Time   :2017/07/31 15:56:21
*  Remark :产品档案
\*********************************************************************/
FLEA::loadClass('TMIS_Controller');
class Controller_Jichu_Test extends TMIS_Controller {
  var $_modelExample;
  var $fldMain;
  // /构造函数
  function __construct() {
    $this->_modelExample = &FLEA::getSingleton('Model_Jichu_Client');
  }



  //编辑界面
  function actionElEdit(){
    $tpl = "Jichu/Test.tpl";
    $row = array(
      'text1'=>'value of text1',
      'text2'=>'value of text2',
      'text3'=>'value of text3',
      'date'=>date('2017-01-01'),
      'date1'=>null,
      'file'=>null,
      'sex'=>'1',
      'chk1'=>'off',
      'chkgrp1'=>array('yuwen'),//必须是数组,不可以为null
      'clientId'=>1,
      'colors'=>array(),
      'colors1'=>array(
        array('id'=>1),
        array('id'=>3),
        array('id'=>25),
      ),
      'colorName'=>'红色',//在弹出选择控件中,指定了该字段为需要显示的内容.
    );
    $fields = array(
      //普通文本框,其中的属性参考element-ui官网
      array(
        'type'=>'comp-text',
        'value'=>'默认值',
        'name'=>'text1',
        'title'=>'普通文本框',
        'clearable'=>true,
        'disabled'=>false,
        'placeholder'=>'普通文本框'
      ),
      //自定义onclick的文本框
      array(
        'type'=>'comp-text',
        // 'value'=>'默认',
        'name'=>'text2',
        'title'=>'onclick事件',
        'placeholder'=>''
      ),
      //绑定某个记录的字段
      array(
        'type'=>'comp-text',
        'value'=>'默认值',
        'name'=>'text3',
        'title'=>'绑定row.text3字段',
        'placeholder'=>'该控件绑定了Row.text3字段',
        'bindfield'=>'text2',//绑定记录的字段名
      ),
      //可选可输入(combox),使用静态选项
      array(
        'type'=>'comp-autocomplete',
        // 'value'=>'默认4',
        'name'=>'combox1',
        'title'=>'可选可输',
        'placeholder'=>'带选项为静态数据',
        'options'=>array(
          array('value'=>'aa','text'=>'a'),
          array('value'=>'bb','text'=>'b'),
          array('value'=>'cc','text'=>'c'),
        ),
        'bindfield'=>'text1',//必选,如果不定义会报错
      ),

      //autocomplete from remote,to do ,

      //带前后缀图标的input
      array(
        'type'=>'comp-text',
        // 'value'=>'默认',
        'name'=>'text2',
        'title'=>'带前后缀图标的input',
        'placeholder'=>'带前后缀图标的input',
        //前置图标
        'prefixIcon'=>'el-icon-date',
        //后置图标
        'suffixIcon'=>'el-icon-search',
      ),

      //组合框 comp-group-input,通用表单中用得不多,不考虑封装,类似日期选择,客户弹出选择的控件,另外写组件,


      //日历-日期选择
      array(
        'type'=>'comp-calendar',
        // 'value'=>'默认',
        'name'=>'text2',
        'title'=>'日期选择',
        'placeholder'=>'点击选择',
        'value'=>date('Y-m-d'),
        'bindfield'=>'date',
        //'ctype'=>'date'
      ),

      //日期范围选择,
      array(
        'type'=>'comp-calendar',
        'name'=>'textcc',
        'title'=>'日期选择',
        'placeholder'=>'点击选择',
        'value'=>date('Y-m-d'),
        'bindfield'=>'date1',
        'ctype'=>'daterange',//ctype可以是year/month/date/dates/ week/datetime/datetimerange/daterange
      ),


      //时间选择,todo

      //datatime选择,todo

      //file
      //这个文件列表不适合在表单中显示,考虑在popover(鼠标移上展开效果)进行文件上传,需要另外封装
      array(
        'type'=>'comp-file',
        'name'=>'textfile',
        'title'=>'文件上传',
        'action'=>$this->_url('saveFile'),//上传地址
        'accept'=>'jpg',//接受上传的文件类型
        'limit'=>0,//最大允许上传个数
        // 'value'=>date('Y-m-d'),
        // 'bindfield'=>'file',
      ),

      //回调事件,从elemet文档中查询到支持的事件
      //记录中如果已存在文件,文件列表中如何绑定 todo
      array(
        'type'=>'comp-file',
        'name'=>'file1',
        'title'=>'带回调事件的文件上传',
        'action'=>$this->_url('saveFile'),//上传地址
        'accept'=>'jpg',//接受上传的文件类型
        'limit'=>0,//最大允许上传个数
        // 'value'=>date('Y-m-d'),
        // 'bindfield'=>'file',
      ),

      //image,和文件类似,todo

      //普通select和select2,如果filterable属性为true,显示为select2的效果(可筛选)
      //注意value=>'1',和value=>1是有区别的,默认选项会进行严格匹配
      array(
        'type'=>'comp-select',
        'value'=>'1',
        'name'=>'sel1',
        'title'=>'普通select',
        'placeholder'=>'性别',
        'options'=>array(
          array('text'=>'男(value="0")','value'=>'0'),
          array('text'=>'女(value="1")','value'=>'1'),
          array('text'=>'value=2','value'=>2),
          array('text'=>'value=3','value'=>3),
        ),
        'filterable'=>true,
        'bindfield'=>'sex',
      ),

      //checkbox 开关用
      array(
        'type'=>'comp-checkbox',
        'value'=>'1',
        'name'=>'chk1',
        'title'=>'checkbox作为开关',
        'text'=>'开关',
        'true-label'=>'on',//选中时的值,可选,默认为true
        'false-label'=>'off',//取消选中时的值,可选,默认为false
        'bindfield'=>'chk1',
      ),

      //checkbox 多选项
      array(
        'type'=>'comp-checkbox-group',
        'value'=>'1',
        'name'=>'chkgrp1',
        'title'=>'学科',
        // 'placeholder'=>'性别',
        'options'=>array(
          array('text'=>'语文','value'=>'yuwen'),
          array('text'=>'数学','value'=>'shuxue'),
        ),
        //记录中该字段如果是空,必须是空数组(array()),否则会出现异常
        'bindfield'=>'chkgrp1',
      ),

      // //省市联动 todo,回调事件中改变数据即可,应该很简单

      //弹出选择,clientId,单选
      //搜索条件 todo
      array(
        'type'=>'comp-pop-select',
        'value'=>'1',
        'name'=>'clientId',
        'title'=>'客户弹出选择',
        // 'placeholder'=>'选择客户',
        'action'=>$this->_url('ListClient'),
        'rowKey'=>'id',//弹框列表中每行对应的主键字段,回填入hidden中,可选参数,默认为 'id',
        //显示在文本框中的字段,因为该字段通常需要从其他表中获得(比如compName),所以在修改记录时,该字段需要在后台构造出来
        'displayKey'=>'colorName',
        'bindfield'=>'clientId',
      ),

      //弹出选择,多选
      //搜索条件 todo
      array(
        'type'=>'comp-pop-multi-select',
        'value'=>array(1,2),
        'name'=>'colors',
        'title'=>'弹出选择多个',
        'action'=>$this->_url('ListClient'),
        'rowKey'=>'id',//弹框列表中每行对应的主键字段,回填入hidden中,可选参数,默认为 'id',
        'bindfield'=>'colors',
      ),

      //弹出选择,多选,弹出时有默认选中项(跨页)
      //搜索条件 todo
      array(
        'type'=>'comp-pop-multi-select',
        'value'=>'',
        'name'=>'colors1',
        'title'=>'弹出选择多个-有默认选中项(跨页)',
        'action'=>$this->_url('ListClient'),
        'rowKey'=>'id',//弹框列表中每行对应的主键字段,回填入hidden中,可选参数,默认为 'id',
        'bindfield'=>'colors1',
      ),

      //表单验证

      //tablelist


      //tree

      //多级下拉选择-筒染的纱支选择

      //layer,布局


    );
    $smarty = &$this->_getView();
    $smarty->left_delimiter = '<{';
    $smarty->right_delimiter = '}>';
    $smarty->assign('fields', $fields);
    $smarty->assign('row',$row);
    $smarty->display($tpl);
  }

  //数据列表
  function actionTestList(){
    //搜索框处理,搜索框默认只显示一个关键字搜索,可弹窗中显示高级搜索,类似QQ邮箱效果
    //表头定义
    $arr_field_info = array(
      'id'=>'id',
      'compCode'  => array('text'=>'公司代码','width'=>200),
      'compName'  => array('text'=>'分类名称','width'=>200),
      'people'  => array('text'=>'联系人','width'=>''),
      'tel'  => array('text'=>'电话','width'=>''),
      'address'  => array('text'=>'地址','width'=>''),
      'email'  => array('text'=>'邮件','width'=>''),
      'mobile'  => array('text'=>'手机','width'=>''),
      //如果包含了html,必须设置isHtml=true
      'website'  => array('text'=>'官网地址','width'=>'','isHtml'=>true),
      'tags'  => array('text'=>'标签','width'=>'','isHtml'=>true),
      'btns'  => array('text'=>'按钮','width'=>'','isHtml'=>true),
    );
    $action = $this->_url('ListClient');
    //数据集获得
    // $requestParam = file_get_contents('php://input');
    // $_POST = json_decode($requestParam,true);
    // $pagesize = $_POST['pagesize'] ? $_POST['pagesize'] : 20;
    // $currentPage = $_POST['currentPage'] ? $_POST['currentPage'] : 1;
    // $from = ($currentPage-1)*$pagesize;
    // $str = "select * from jichu_client where 1 ";
    // $str .= " order by id";
    // $str .= " limit {$from},{$pagesize}";
    // $rowset = $this->_modelExample->findBySql($str);
    //模版处理
    $smarty = &$this->_getView();
    $smarty->left_delimiter = '<{';
    $smarty->right_delimiter = '}>';
    $smarty->assign('arr_field_info', $arr_field_info);
    $smarty->assign('action', $action);
    $smarty->assign('textmemo', "<font color='red'>红色代表已完成</font>");

    //高级搜索需要显示哪些搜索项目
    $smarty->assign('searchItems', array(
      'key'=>'',
      'compName'=>'',
      'proCode'=>'',
      'traderId'=>'',
      'dateFrom'=>'',
      'dateTo'=>'',
    ));
    //关键字可匹配的字段,输入关键字后自动提示出来
    $smarty->assign('colsForKey', array(
      array('text'=>'公司代码','col'=>'compCode'),
      array('text'=>'公司名称','col'=>'compName'),
    ));
    //右上角高级功能菜单
    $smarty->assign('menuRightTop', array(
      array('text'=>'导出本页','name'=>'btnExport','icon'=>'el-icon-time'),
      array('text'=>'导出全部','name'=>'btnExportAll'),
      array('text'=>'全部删除','name'=>'btnRemoveAll'),
      array('text'=>'标记完成','name'=>'btnSetOver'),
      //带分割线
      array('text'=>'选中记录','name'=>'btnSelection','divided'=>true),
    ));

    //是否需要记录选中功能,选中的记录会存在于app.$root.multipleSelection中,方便进行定位
    $smarty->assign('multiSelect',true );

    //指定表格的编辑按钮组,
    //如果不设置,不显示编辑列
    //可设置某行的某个按钮不可用,如下
    // $rowset[2]['__btnsDisabled']['不可用1']=true;
    $smarty->assign('editButtons',array(
      array('text'=>'编辑','url'=>'http://www.baidu.com'),//跳转
      //ajax提交,成功后refresh,
      //isRemove如果为true,表示为删除按钮,会默认访问一个url,执行后台删除,成功后刷新
      //后期会支持用户自定义的记录删除动作,to do
      array('text'=>'删除','isRemove'=>true,'removeUrl'=>$this->_url('RemoveAjax')),
      //在dialog中输入备注,属于用户自定义的组件,需要在sontpl中定义组件,
      array('text'=>'dialog插槽','name'=>'btnClientMemo'),//考虑可扩展性,这里
      array('text'=>'触发其他方法','name'=>'btnClientMemo1'),//考虑可扩展性,这里
      //一般会根据数据字段的不同值动态改变,下面的设置不会生效
      //在数据集中可对各行记录设置 $row['__btnsDisabled']['不可用1']=true;
      array('text'=>'不可用1'),
      array('text'=>'不可用2'),
    ));

    //子文件,定义回调事件和自定义组件
    $smarty->assign('sonTpl','Test/Sontpl.js');

    //记录详细信息展开面板中需要展示的字段
    $smarty->assign('arr_field_expand',array(
      'compCode'  => array('text'=>'公司代码'),
      'compName'  => array('text'=>'分类名称'),
      'people'  => array('text'=>'联系人'),
      'tel'  => array('text'=>'电话',),
      'address'  => array('text'=>'地址'),
      'email'  => array('text'=>'邮件'),
    ));
    $smarty->display('Jichu/TestList.tpl');
  }


  //数据列表,
  function actionTestListNew() {
    //模版处理
    $smarty = &$this->_getView();
    $smarty->left_delimiter = '<{';
    $smarty->right_delimiter = '}>';
    //表头定义
    $smarty->assign('arr_field_info', array(
      'id'=>array('text'=>'id'),
      'compCode'  => array(
        'text'=>'公司代码',
        'width'=>50,//最小130,方便显示4个按钮
        //是否排序:ture当前页排序,false:不排序,custom:用户自定义排序
        //如果要远程排序,必须custom
        'sortable'=>true,
        //是否匹配关键字搜索
        'forKeySearch'=>true,
        //鼠标移上时是否显示操作按钮
        'showButton'=>true,
      ),
      'compName'  => array(
        'text'=>'分类名称',
        'width'=>200,
        //自定义排序事件,发起远程排序请求
        'sortable'=>'custom',
        //是否匹配关键字搜索
        'forKeySearch'=>true,
        //tip效果展现自定义组件,注意后面必须指定组件名
        //组件的定义在sontpl中写
        'isHtml'=>'component',
        'componentType'=>'tip-compName'
      ),
      'people'  => array('text'=>'联系人','width'=>'100'),
      'tel'  => array('text'=>'电话','width'=>'100',),
      'address'  => array('text'=>'地址','width'=>'100','forKeySearch'=>true,),
      'email'  => array('text'=>'邮件','width'=>'100'),
      'mobile'  => array('text'=>'手机','width'=>'200'),
      //如果包含了html,必须设置isHtml=true
      'website'  => array('text'=>'官网地址','width'=>'200','isHtml'=>true),
      'tags'  => array('text'=>'html内容','width'=>'','isHtml'=>true),
      //点击弹开明细
      // 'btns'  => array('text'=>'点击弹出明细','width'=>'200','isHtml'=>'component','componentType'=>'comp-dialog-tablelist','options'=>array(
      //   'action'=>$this->_url('ListClient'),
      // )),
      //点击改变当前行背景色
      'chkColor'=>array(
        'text'=>'点击变色',
        'width'=>'',
        //tip效果展现自定义组件,注意后面必须指定组件名
        //组件的定义在sontpl中写
        'isHtml'=>'component',
        'componentType'=>'checkbox-change-color'
      ),
    ));
    //数据获取地址,
    $smarty->assign('action', $this->_url('ListClient'));

    //导出全部时获取数据的地址
    $smarty->assign('actionExportAll', $this->_url('ExportData'));

    //分页后的文字说明
    $smarty->assign('textAfterPage', "<font color='red'>红色代表已完成</font>");

    //高级搜索需要显示哪些搜索项目
    $smarty->assign('searchItems', array(
      'key'       =>'上海',
      'compName'  =>'',
      'proCode'   =>'',
      'traderId'  =>'',
      'dateFrom'  =>'',
      'dateTo'    =>'',
      'dateRange' =>'',
      'productId' =>'',
      'isOver'    =>'',
    ));
    //关键字可匹配的字段,输入关键字后自动提示出来,
    //如果这里定义了,arr_field_info中的 forKeySearch 配置将失效
    //colsForKey和arr_field_info.forKeySearch可以都不定义,如果不定义,回车默认提交
    $smarty->assign('colsForKey', array(
      array('text'=>'公司代码','col'=>'compCode'),
      array('text'=>'公司名称','col'=>'compName'),
      array('text'=>'电话','col'=>'tel'),
    ));
    //右上角高级功能菜单
    //每个按钮的图表可以指定,如果不指定默认使用模版中默认的 menuRightTopIcon
    $smarty->assign('menuRightTop', array(
      array('text'=>'新增','url'=>$this->_url('add'),'icon'=>'el-icon-plus'),
      array('text'=>'跳转baidu','url'=>"http://www.baidu.com",'icon'=>'el-icon-time'),
      // array('text'=>'导出本页','name'=>'btnExport'),
      // array('text'=>'导出全部','name'=>'btnExportAll'),
      array('text'=>'全部删除','name'=>'btnRemoveAll'),
      array('text'=>'标记完成','name'=>'btnSetOver'),
      //带分割线
      array('text'=>'选中记录','name'=>'btnSelection','divided'=>true),
    ));

    //新增url,
    //原则上不需要再定义addUrl,考虑到老代码调整工作量大,这里保留了addUrl
    //建议需要新增按钮的话,将 menuRightTop[0]定义为新增按钮,保证数据的优雅
    $smarty->assign('addUrl',$this->_url('add'));

    //是否需要记录选中功能,选中的记录会存在于app.$root.multipleSelection中,方便进行定位
    $smarty->assign('multiSelect',true );
    //指定表格的编辑按钮组,
    //如果不设置,不显示编辑列
    //可设置某行的某个按钮不可用,如下
    $smarty->assign('editButtons',array(
      //跳转,调用内置方法,
      array('text'=>'编辑','type'=>'redirect','icon'=>'el-icon-edit','options'=>array(
        //点击后跳转的地址
        'url'=>$this->_url('edit'),
        //如果$row['__disabledButton1']=true,按钮不可用
        'disabledColumn'=>'__disabledButton1'
      )),
      //url 其中有模版变量,代表行记录中对应的字段的值
      array('text'=>'带参数的url','type'=>'redirect','icon'=>'el-icon-time','options'=>array(
        'url'=>$this->_url('edit').'&clientId={id}&compCode={compCode}',
      )),
      //可以不指定icon,icon默认为 defaultEditButtonsIcons 中对应位置的按钮,
      //在返回的数据集中指定__url字段,作为跳转地址,适用于每行记录跳转不同地址的场景
      array('text'=>'每行的url不同','type'=>'redirect','options'=>array(
        'urlColumn'=>'__url',
        //url无效,因为返回的结果集中存在__url字段
        'url'=>'http://www.baidu.com',
      )),
      //ajax提交,成功后重新获取表格数据,
      //id作为默认参数传入
      //调用内置方法
      array('text'=>'删除','type'=>'remove','options'=>array(
        'url'=>$this->_url('RemoveAjax')
      )),
      //自定义方法,在sontpl中定义
      //设置某个按钮为不可用
      array('text'=>'自定义方法','type'=>'func','options'=>array(
        'funcName'=>"userFuncRow",
        //如果$row['__disabledButton3']=true,按钮不可用
        'disabledColumn'=>'__disabledButton3'
      )),
      //自定义组件
      array('text'=>'用户自定义comp','type'=>'comp','options'=>array(
        //组件名,必须在sontpl中自定义
        'type'=>'user-dialog',
        //组件名称,必须指定
        'name'=>'username',
        //点击按钮时,默认执行的组件方法
        //比如自定义的弹窗控件,每次点击后都应该显示出来,
        //大部分情况下都需要定义,否则组件的状态不会改变.
        'onclickButton'=>'show'
      )),
      //其他扩展,todo
      //比如点击后改变背景色
      /*array('text'=>'其他扩展','type'=>'other','options'=>array(
        'funcName'=>'...'//在callback中定义
      )),*/
    ));


    //记录详细信息展开面板中需要展示的字段,
    //目前只是默认一种平铺效果,(多行,每行3列),后期可考虑多几种展示效果 todo
    // $smarty->assign('arr_field_expand',array(
    //   'compCode'  => array('text'=>'公司代码'),
    //   'compName'  => array('text'=>'分类名称'),
    //   'people'  => array('text'=>'联系人'),
    //   'tel'  => array('text'=>'电话',),
    //   'address'  => array('text'=>'地址'),
    //   'email'  => array('text'=>'邮件'),
    // ));

    //定义详细信息展开自定义模版
    $smarty->assign('optExpand',array(
      //展开面板type,可以是
      //comp-expand-form 普通表单形式的面板
      //comp-expand-tabs 带tab效果的展开面板
      'type'=>'comp-expand-tabs',
      //每个tab中组件参数
      'options'=>array(
        //form参数
        array(
          'type'=>'form',
          'title'=>'客户详细',
          'options'=>array(
            'formItems'=>array(
              'compCode'  => array('text'=>'公司代码'),
              'compName'  => array('text'=>'分类名称'),
              'people'  => array('text'=>'联系人'),
              'tel'  => array('text'=>'电话',),
              'address'  => array('text'=>'网址'),
              'email'  => array('text'=>'邮件'),
            ),
          )
        ),
        //table参数
        array(
          'type'=>'table',
          'title'=>'联系人',
          'options'=>array(
            'columns'=>array(
              // 'id'  => array('text'=>'id'),
              'name'  => array('text'=>'姓名'),
              'tel'  => array('text'=>'电话','width'=>'200'),
              'address'  => array('text'=>'地址','width'=>'200'),
            ),
            //每条记录中代表子表记录集的字段
            'sonKey'=>'Peoples',
          )
        )
      ),
    ));
    //子文件,定义回调事件和自定义组件
    $smarty->assign('sonTpl','Test/TableList.js');
    //设置记录的主键,默认是id,有些时候,记录集的id字段可能重复,比如两条子表记录的主表id是一样的.
    $smarty->assign('rowKey','compCode');
    $smarty->display('TableList.tpl');
  }

  //tablelist_v1.1
  function actionTableList1() {
    //模版处理
    $smarty = &$this->_getView();
    $smarty->left_delimiter = '<{';
    $smarty->right_delimiter = '}>';
    //指定表格的编辑按钮组,
    //如果不设置,不显示编辑列
    //可设置某行的某个按钮不可用,如下
    $editButtons = array(
      //跳转,调用内置方法,
      array('text'=>'编辑','type'=>'redirect','icon'=>'el-icon-edit','options'=>array(
        //点击后跳转的地址
        'url'=>$this->_url('edit'),
        //如果$row['__disabledButton1']=true,按钮不可用
        'disabledColumn'=>'__disabledButton1'
      )),
      //url 其中有模版变量,代表行记录中对应的字段的值
      array('text'=>'带参数的url','type'=>'redirect','icon'=>'el-icon-time','options'=>array(
        'url'=>$this->_url('edit').'&clientId={id}&compCode={compCode}',
      )),
      //可以不指定icon,icon默认为 defaultEditButtonsIcons 中对应位置的按钮,
      //在返回的数据集中指定__url字段,作为跳转地址,适用于每行记录跳转不同地址的场景
      array('text'=>'每行的url不同','type'=>'redirect','options'=>array(
        'urlColumn'=>'__url',
        //url无效,因为返回的结果集中存在__url字段
        'url'=>'http://www.baidu.com',
      )),
      //ajax提交,成功后重新获取表格数据,
      //id作为默认参数传入
      //调用内置方法
      array('text'=>'删除','type'=>'remove','options'=>array(
        'url'=>$this->_url('RemoveAjax')
      )),
      //自定义方法,在sontpl中定义
      //设置某个按钮为不可用
      array('text'=>'自定义方法','type'=>'func','options'=>array(
        'funcName'=>"userFuncRow",
        //如果$row['__disabledButton3']=true,按钮不可用
        'disabledColumn'=>'__disabledButton3'
      )),
      //自定义组件
      array('text'=>'用户自定义comp','type'=>'comp','options'=>array(
        //组件名,必须在sontpl中自定义
        'type'=>'user-dialog',
        //组件名称,必须指定
        'name'=>'username',
        //点击按钮时,默认执行的组件方法
        //比如自定义的弹窗控件,每次点击后都应该显示出来,
        //大部分情况下都需要定义,否则组件的状态不会改变.
        'onclickButton'=>'show'
      )),
      //其他扩展,todo
      //比如点击后改变背景色
      /*array('text'=>'其他扩展','type'=>'other','options'=>array(
        'funcName'=>'...'//在callback中定义
      )),*/
    );
    //表头定义
    $smarty->assign('arr_field_info', array(
      'id'=>array('text'=>'id'),
      //按钮显示列,鼠标移上后显示按钮组
      'compCode'  => array(
        'text'=>'公司代码',
        'width'=>50,//最小130,方便显示4个按钮
        //是否排序:ture当前页排序,false:不排序,custom:用户自定义排序
        //如果要远程排序,必须custom
        'sortable'=>true,
        //是否匹配关键字搜索
        'forKeySearch'=>true,
        //鼠标移上时是否显示操作按钮
        'showButton'=>true,
        'editButtons'=>$editButtons,
      ),
      //如果包含了html,必须设置 'type'=>'html'
      'website'  => array('text'=>'官网地址','width'=>'200','type'=>'html'),
      'tags'  => array('text'=>'html内容','width'=>'100','type'=>'html'),
      //列中显示组件
      'compName'  => array(
        'text'=>'公司名称',
        'width'=>200,
        //自定义排序事件,发起远程排序请求
        'sortable'=>'custom',
        //是否匹配关键字搜索
        'forKeySearch'=>true,
        //tip效果展现自定义组件,注意后面必须指定组件名
        //组件的定义在sontpl中写
        'type'=>'component',
        'options'=>array('type'=>'tip-compName'),
      ),
      'people'  => array('text'=>'联系人','width'=>'100',),
      'address'  => array('text'=>'地址','width'=>'100','forKeySearch'=>true,),
      'email'  => array('text'=>'邮件','width'=>'100'),
      //手机改变后，电话一起改变
      'mobile'  => array('text'=>'手机','width'=>'200','type'=>'component','options'=>array('type'=>'text-list','colName'=>'mobile')),
      'tel'  => array('text'=>'电话','width'=>'100','type'=>'component','options'=>array('type'=>'text-list','colName'=>'tel','readonly'=>true)),

      //点击弹开明细
      'btns'  => array('text'=>'点击弹出明细','width'=>'200','type'=>'component','options'=>array(
        'type'=>'comp-dialog-tablelist-link',
        'title'=>'提示',
        'action'=>$this->_url('ListClient'),
        //id可能从夫
        'rowKey'=>'compCode',
        //为了获得弹窗实例,尽量不要和别的组件重名
        'name'=>'jefftest',
        //指定弹开时需要附加在action后的参数,可以在数据集中进行指定或者使用语法模版
        //注意:下面的row.compCode代表当前行的compCode字段,row是代表当前行,不能改
        'params'=>array('compCode'=>'row.compCode','compName'=>'row.compName'),
      )),
      //点击改变当前行背景色
      'chkColor'=>array(
        'text'=>'点击变色',
        'width'=>'',
        //tip效果展现自定义组件,注意后面必须指定组件名
        //组件的定义在sontpl中写
        'type'=>'component',
        'options'=>array('type'=>'checkbox-change-color'),
      ),
    ));
    //数据获取地址,
    $smarty->assign('action', $this->_url('ListClient'));

    //导出全部时获取数据的地址
    $smarty->assign('actionExportAll', $this->_url('ExportData'));

    //分页后的文字说明
    $smarty->assign('textAfterPage', "<font color='red'>v1.1支持comp-dialog-tablelist-link(列表弹框)组件</font>");

    //高级搜索需要显示哪些搜索项目
    $smarty->assign('searchItems', array(
      'key'       =>'上海',
      'compName'  =>'',
      'proCode'   =>'',
      'traderId'  =>'',
      'dateFrom'  =>'',
      'dateTo'    =>'',
      'dateRange' =>'',
      'productId' =>'',
      'isOver'    =>'',
    ));

    //右上角高级功能菜单
    //每个按钮的图表可以指定,如果不指定默认使用模版中默认的 menuRightTopIcon
    $smarty->assign('menuRightTop', array(
      array('text'=>'新增','url'=>$this->_url('add'),'icon'=>'el-icon-plus'),
      array('text'=>'跳转baidu','url'=>"http://www.baidu.com",'icon'=>'el-icon-time'),
      // array('text'=>'导出本页','name'=>'btnExport'),
      // array('text'=>'导出全部','name'=>'btnExportAll'),
      array('text'=>'全部删除','name'=>'btnRemoveAll'),
      array('text'=>'标记完成','name'=>'btnSetOver'),
      //带分割线
      array('text'=>'选中记录','name'=>'btnSelection','divided'=>true),
    ));

    //是否需要记录选中功能,选中的记录会存在于app.$root.multipleSelection中,方便进行定位
    $smarty->assign('multiSelect',true );

    //定义详细信息展开自定义模版
    $smarty->assign('optExpand',array(
      //展开面板type,可以是
      //comp-expand-form 普通表单形式的面板
      //comp-expand-tabs 带tab效果的展开面板
      'type'=>'comp-expand-tabs',
      //每个tab中组件参数
      'options'=>array(
        //form参数
        array(
          'type'=>'form',
          'title'=>'客户详细',
          'options'=>array(
            'formItems'=>array(
              'compCode'  => array('text'=>'公司代码'),
              'compName'  => array('text'=>'分类名称'),
              'people'  => array('text'=>'联系人'),
              'tel'  => array('text'=>'电话',),
              'address'  => array('text'=>'网址'),
              'email'  => array('text'=>'邮件'),
            ),
          )
        ),
        //table参数
        array(
          'type'=>'table',
          'title'=>'联系人',
          'options'=>array(
            'columns'=>array(
              // 'id'  => array('text'=>'id'),
              'name'  => array('text'=>'姓名'),
              'tel'  => array('text'=>'电话','width'=>'200'),
              'address'  => array('text'=>'地址','width'=>'200'),
            ),
            //每条记录中代表子表记录集的字段
            'sonKey'=>'Peoples',
          )
        )
      ),
    ));
    //子文件,定义回调事件和自定义组件
    $smarty->assign('sonTpl','Test/TableList.js');
    //设置记录的主键,默认是id,有些时候,记录集的id字段可能重复,比如两条子表记录的主表id是一样的.
    $smarty->assign('rowKey','compCode');
    $smarty->display('TableList_v1.1.tpl');
  }

  //删除测试
  function actionRemoveAjax() {
    $requestParam = file_get_contents('php://input');
    $_POST = json_decode($requestParam,true);
    $id = $_POST['row']['id'];

    $m = &FLEA::getSingleton('Model_Jichu_Client');

    $ret = $m->removeByPkv($id);
    // dump($ret);exit;
    $ret = array(
      'success'=>true,
      'msg'=>'删除成功aaaa',
    );
    echo json_encode($ret);exit;
  }



  //动态表单展示
  function actionTestPanel() {
    $tpl = "Test/TestPanel.tpl";
    $action = $this->_url('saveDemo');
    // fileList: [
    //     {name: 'food.jpeg', url: ''},
    //     {name: 'food2.jpeg', url: ''}
    //   ],
    $row = array(
      'id'=>'1',//hidden,可以不用写在fields中,
      'compCode'=>'eqinfo',//text
      'compName'=>'易奇科技',//text
      'people'=>'',//自动完成,combox
      'createDate'=>'',//登记日期,
      'vDate'=>'',//有效日期,日期范围
      //图片默认值必须是数组,所以保存到数据中时建议将name和url分开保存,或者可以保存json字串
      'pic'=>array(
        array('name'=>'food.jpeg','url'=>'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100'),
        array('name'=>'food2.jpeg','url'=>'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100'),
      ),
      'traderId'=>'3',//select,注意要和option中的value类型一致,不能写成'3',否则默认选中会失效
      'isStop'=>true,//checkbox
      'compFrom'=>array('网站推广')  ,//客户来源 多选项
      'associateClientId'=>10,//上家客户,弹出选择
      'compName'=>'张三',//用来作为上家客户的默认值
      //下家客户,弹出多选,注意默认值必须为数据集
      'xiajia'=>array(
        array('id'=>1),
        array('id'=>2),
      ),
    );

    $fields = array(
      array(
        'type'=>'comp-text',
        'value'=>'',
        'name'=>'compCode',
        'title'=>'客户编码',
        'clearable'=>true,
        //后置图标
        'suffixIcon'=>'el-icon-search',
        'placeholder'=>'4-6位字母或者数字',
      ),
      //公司名change时会检测是否存在相同的代码和名字的记录
      array(
        'type'=>'comp-text',
        'value'=>'',
        'name'=>'compName',
        'title'=>'公司名称',
        'clearable'=>true,
        'placeholder'=>'汉字',
        //前置图标
        'prefixIcon'=>'el-icon-date',
      ),
      array(
        'type'=>'comp-autocomplete',
        'name'=>'people',
        'title'=>'联系人',
        'placeholder'=>'联系人',
        //下面的text没用
        'options'=>array(
          array('value'=>'联系人a','text'=>'a'),
          array('value'=>'联系人b','text'=>'b'),
          array('value'=>'联系人c','text'=>'c'),
        ),
        'bindfield'=>'text1',//必选,如果不定义会报错
      ),

      // 'createDate'=>'2018-11-16',//登记日期,
      array(
        'type'=>'comp-calendar',
        'name'=>'createDate',
        'title'=>'登记日期',
        'placeholder'=>'点击选择',
      ),

      // 'vDate'=>'2018-11-16',//有效日期,日期范围
      array(
        'type'=>'comp-calendar',
        'name'=>'vDate',
        'title'=>'日期选择',
        'placeholder'=>'点击选择',
        'value'=>date('Y-m-d'),
        'bindfield'=>'date1',
        'ctype'=>'daterange',//ctype可以是year/month/date/dates/ week/datetime/datetimerange/daterange
      ),

      // 'isStop'=>'是否停用',//checkbox
      array(
        'type'=>'comp-checkbox',
        'name'=>'isStop',
        'title'=>'是否停用',
        'text'=>'停用',
        // 'true-label'=>'on',//选中时的值,可选,默认为true
        // 'false-label'=>'off',//取消选中时的值,可选,默认为false
        // 'bindfield'=>'chk1',
      ),
      // 'compFrom'=>'email',//客户来源 多选项
      array(
        'type'=>'comp-checkbox-group',
        'name'=>'compFrom',
        'title'=>'客户来源',
        'options'=>array(
          array('text'=>'网站推广','value'=>'网站推广'),
          array('text'=>'老客户介绍','value'=>'老客户介绍'),
        ),
      ),
      // 'associateClientId'=>10,//关联客户,
      array(
        'type'=>'comp-pop-select',
        'value'=>'1',
        'name'=>'associateClientId',
        'title'=>'上家客户',
        'action'=>$this->_url('ListClient'),
        'rowKey'=>'id',//弹框列表中每行对应的主键字段,回填入hidden中,可选参数,默认为 'id',
        'displayKey'=>'compName',//显示在文本框中的字段,因为该字段通常需要从其他表中获得(比如compName),所以在修改记录时,该字段需要在后台构造出来
      ),

      // 'xiajia'=>array(3,4),//下家客户,弹出多选
      array(
        'type'=>'comp-pop-multi-select',
        'value'=>array(1,2),
        'name'=>'xiajia',
        'title'=>'下家客户',
        'action'=>$this->_url('ListClient'),
        'rowKey'=>'id',//弹框列表中每行对应的主键字段,回填入hidden中,可选参数,默认为 'id',
      ),

      // 'traderId'=>3,//select,注意不能写成'3',否则默认选中会失效
      array(
        'type'=>'comp-select',
        'value'=>'1',
        'name'=>'traderId',
        'title'=>'业务员',
        'placeholder'=>'选择业务员',
        'options'=>array(
          array('text'=>'张三','value'=>'1'),
          array('text'=>'李四','value'=>'2'),
          array('text'=>'王五','value'=>'3'),
          array('text'=>'赵六','value'=>'4'),
        ),
        'filterable'=>true,
        // 'bindfield'=>'sex',
      ),

      // 'pic'=>'aaa.jpg',//图片上传
      array(
        'type'=>'comp-image',
        'name'=>'pic',
        'title'=>'营业执照',
        'action'=>$this->_url('saveFile'),//上传地址
        // 'action'=>'aa.php',//上传地址
        'accept'=>'.jpg,.bmp,.PNG',//接受上传的文件类型
        'limit'=>3,//最大允许上传个数
        'multiple'=>true,//是否允许多选,
      ),
      //文件上传
       array(
        'type'=>'comp-file',
        'name'=>'file',
        'title'=>'工程文件',
        'action'=>$this->_url('saveFile'),//上传地址
        'accept'=>'.doc',//接受上传的文件类型
        'limit'=>3,//最大允许上传个数
        'multiple'=>true,//是否允许多选,
      ),
    );
    //验证
    $rules = array(
      'compCode'=>array(
        array(
          'required'=>true,
          'message'=>'请输入活动名称',
          // 'trigger'=>'blur'
        )
      ),
      'compName'=>array(
        array(
          'required'=>true,
          'message'=>'请输入公司名称',
          // 'trigger'=>'blur'
        )
      )
    );

    $smarty = &$this->_getView();
    $smarty->left_delimiter = '<{';
    $smarty->right_delimiter = '}>';
    $smarty->assign('fields', $fields);
    $smarty->assign('rules', $rules);
    $smarty->assign('row',$row);
    $smarty->assign('action',$action);
    $smarty->display($tpl);
  }
  //动态表单展示
  function actionTestForm() {
    $tpl = "Jichu/TestForm.tpl";
    $action = $this->_url('saveDemo');
    // fileList: [
    //     {name: 'food.jpeg', url: ''},
    //     {name: 'food2.jpeg', url: ''}
    //   ],
    $row = array(
      'id'=>'1',//hidden,可以不用写在fields中,
      'compCode'=>'eqinfo',//text
      'compName'=>'易奇科技',//text
      'people'=>'',//自动完成,combox
      'createDate'=>'',//登记日期,
      'vDate'=>'',//有效日期,日期范围
      //图片默认值必须是数组,所以保存到数据中时建议将name和url分开保存,或者可以保存json字串
      'pic'=>array(
        array('name'=>'food.jpeg','url'=>'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100'),
        array('name'=>'food2.jpeg','url'=>'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100'),
      ),
      'traderId'=>3,//select,注意要和option中的value类型一致,不能写成'3',否则默认选中会失效
      'isStop'=>true,//checkbox
      'compFrom'=>array('网站推广')  ,//客户来源 多选项
      'associateClientId'=>10,//上家客户,弹出选择
      'compName'=>'张三',//用来作为上家客户的默认值
      //下家客户,弹出多选,注意默认值必须为数据集
      'xiajia'=>array(
        array('id'=>1),
        array('id'=>2),
      ),
    );

    $fields = array(
      array(
        'type'=>'comp-text',
        'value'=>'',
        'name'=>'compCode',
        'title'=>'客户编码',
        'clearable'=>true,
        //后置图标
        'suffixIcon'=>'el-icon-search',
        'placeholder'=>'4-6位字母或者数字',
      ),
      //公司名change时会检测是否存在相同的代码和名字的记录
      array(
        'type'=>'comp-text',
        'value'=>'',
        'name'=>'compName',
        'title'=>'公司名称',
        'clearable'=>true,
        'placeholder'=>'汉字',
        //前置图标
        'prefixIcon'=>'el-icon-date',
      ),
      array(
        'type'=>'comp-autocomplete',
        'name'=>'people',
        'title'=>'联系人',
        'placeholder'=>'联系人',
        //下面的text没用
        'options'=>array(
          array('value'=>'联系人a','text'=>'a'),
          array('value'=>'联系人b','text'=>'b'),
          array('value'=>'联系人c','text'=>'c'),
        ),
        'bindfield'=>'text1',//必选,如果不定义会报错
      ),

      // 'createDate'=>'2018-11-16',//登记日期,
      array(
        'type'=>'comp-calendar',
        'name'=>'createDate',
        'title'=>'登记日期',
        'placeholder'=>'点击选择',
      ),

      // 'vDate'=>'2018-11-16',//有效日期,日期范围
      array(
        'type'=>'comp-calendar',
        'name'=>'vDate',
        'title'=>'日期选择',
        'placeholder'=>'点击选择',
        'value'=>date('Y-m-d'),
        'bindfield'=>'date1',
        'ctype'=>'daterange',//ctype可以是year/month/date/dates/ week/datetime/datetimerange/daterange
      ),

      // 'isStop'=>'是否停用',//checkbox
      array(
        'type'=>'comp-checkbox',
        'name'=>'isStop',
        'title'=>'是否停用',
        'text'=>'停用',
        // 'true-label'=>'on',//选中时的值,可选,默认为true
        // 'false-label'=>'off',//取消选中时的值,可选,默认为false
        // 'bindfield'=>'chk1',
      ),
      // 'compFrom'=>'email',//客户来源 多选项
      array(
        'type'=>'comp-checkbox-group',
        'name'=>'compFrom',
        'title'=>'客户来源',
        'options'=>array(
          array('text'=>'网站推广','value'=>'网站推广'),
          array('text'=>'老客户介绍','value'=>'老客户介绍'),
        ),
      ),
      // 'associateClientId'=>10,//关联客户,
      array(
        'type'=>'comp-pop-select',
        'value'=>'1',
        'name'=>'associateClientId',
        'title'=>'上家客户',
        'action'=>$this->_url('ListClient'),
        'rowKey'=>'id',//弹框列表中每行对应的主键字段,回填入hidden中,可选参数,默认为 'id',
        'displayKey'=>'compName',//显示在文本框中的字段,因为该字段通常需要从其他表中获得(比如compName),所以在修改记录时,该字段需要在后台构造出来
      ),

      // 'xiajia'=>array(3,4),//下家客户,弹出多选
      array(
        'type'=>'comp-pop-multi-select',
        'value'=>array(1,2),
        'name'=>'xiajia',
        'title'=>'下家客户',
        'action'=>$this->_url('ListClient'),
        'rowKey'=>'id',//弹框列表中每行对应的主键字段,回填入hidden中,可选参数,默认为 'id',
      ),

      // 'traderId'=>3,//select,注意不能写成'3',否则默认选中会失效
      array(
        'type'=>'comp-select',
        'value'=>'1',
        'name'=>'traderId',
        'title'=>'业务员',
        'placeholder'=>'选择业务员',
        'options'=>array(
          array('text'=>'张三','value'=>1),
          array('text'=>'李四','value'=>2),
          array('text'=>'王五','value'=>3),
          array('text'=>'赵六','value'=>4),
        ),
        'filterable'=>true,
        // 'bindfield'=>'sex',
      ),

      // 'pic'=>'aaa.jpg',//图片上传
      array(
        'type'=>'comp-image',
        'name'=>'pic',
        'title'=>'营业执照',
        'action'=>$this->_url('saveFile'),//上传地址
        // 'action'=>'aa.php',//上传地址
        'accept'=>'.jpg,.bmp,.PNG',//接受上传的文件类型
        'limit'=>3,//最大允许上传个数
        'multiple'=>true,//是否允许多选,
      ),
      //文件上传
       array(
        'type'=>'comp-file',
        'name'=>'file',
        'title'=>'工程文件',
        'action'=>$this->_url('saveFile'),//上传地址
        'accept'=>'.doc',//接受上传的文件类型
        'limit'=>3,//最大允许上传个数
        'multiple'=>true,//是否允许多选,
      ),
    );
    //验证
    $rules = array(
      'compCode'=>array(
        array(
          'required'=>true,
          'message'=>'请输入活动名称',
          // 'trigger'=>'blur'
        )
      ),
      'compName'=>array(
        array(
          'required'=>true,
          'message'=>'请输入公司名称',
          // 'trigger'=>'blur'
        )
      )
    );

    //svn库

    //tablelist界面

    //自定义表单验证

    //主从表单组件

    //表单的美化,排版,panel效果

    //container构造菜单等

    //如何保证后期的优化能同步到多个项目中

    //基础框架功能
      //权限,
      //登陆,修改密码
      //常规基础档案

    //测试项目

    $smarty = &$this->_getView();
    $smarty->left_delimiter = '<{';
    $smarty->right_delimiter = '}>';
    $smarty->assign('fields', $fields);
    $smarty->assign('rules', $rules);
    $smarty->assign('row',$row);
    $smarty->assign('action',$action);
    $smarty->display($tpl);
  }
  //通用表单模版
  function actionTestFormNew() {
    $row = array(
      'id'=>'1',//hidden,可以不用写在fields中,
      'compCode'=>'eqinfo',//text
      'compName'=>'易奇科技',//text
      'people'=>'',//自动完成,combox
      'createDate'=>'',//登记日期,
      'vDate'=>'',//有效日期,日期范围
      //图片默认值必须是数组,所以保存到数据中时建议将name和url分开保存,或者可以保存json字串
      'pic'=>array(
        array('name'=>'food.jpeg','imageId'=>11,'url'=>'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100'),
        array('name'=>'food2.jpeg','imageId'=>22,'url'=>'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100'),
      ),
      //select,注意要和option中的value类型一致,不能写成'3',否则默认选中会失效
      //另外注意,从数据库中取出的字段值都是string类型,所以构造options时也需要使用字符串(加上单引号),
      'traderId'=>3,
      'isStop'=>true,//checkbox
      'compFrom'=>array('网站推广',2)  ,//客户来源 多选项
      'associateClientId'=>10,//上家客户,弹出选择
      'associateClientId1'=>11,//上家客户,弹出选择,多个弹出选择框测试
      // 'compName'=>'张三',//用来作为上家客户的默认值
      //下家客户,弹出多选,注意默认值必须为数据集
      'xiajia'=>array(
        array('id'=>1197),
        array('id'=>1198),
        array('id'=>1162),
      ),
      'xiajia1'=>array(
        array('id'=>1197),
        array('id'=>1198),
        array('id'=>1177),
      ),
    );

    $formItems = array(
      // 'pic'=>'aaa.jpg',//图片上传
      array(
        'type'=>'comp-image',
        'name'=>'pic',
        'title'=>'营业执照',
        'action'=>$this->_url('saveFile'),//上传地址
        'actionRemove'=>$this->_url('removeFile'),//删除图片时需要从服务器删除,可以不定义
        // 'action'=>'aa.php',//上传地址
        'accept'=>'.jpg,.bmp,.PNG',//接受上传的文件类型
        'limit'=>3,//最大允许上传个数
        'multiple'=>true,//是否允许多选,
      ),
      // 'xiajia'=>array(3,4),//下家客户,弹出多选
      array(
        'type'=>'comp-popup-multi-select',
        // 'value'=>array(1,2),
        'name'=>'xiajia1',
        'title'=>'下家客户',
        'action'=>$this->_url('ListClient'),
        //弹框列表中每行对应的主键字段,回填入hidden中,可选参数,默认为 'id',
        'rowKey'=>'id',
        //选中结果中的字段,用来回显结果用
        'displayKey'=>'compName',
      ),
      array(
        'type'=>'comp-text',
        'value'=>'',
        'name'=>'compCode',
        'title'=>'客户编码',
        'clearable'=>true,
        //后置图标
        'suffixIcon'=>'el-icon-search',
        'placeholder'=>'4-6位字母或者数字',
        'prepend'=>'前置文字',//也可以写成 'addonPre'=>'前置文字',
        'append'=>'后置文字',//也可以写成 'addonEnd'=>'后置文字'

      ),
      //公司名change时会检测是否存在相同的代码和名字的记录
      array(
        'type'=>'comp-text',
        'value'=>'',
        'name'=>'compName',
        'title'=>'公司名称',
        'clearable'=>true,
        'placeholder'=>'汉字',
        //前置图标
        'prefixIcon'=>'el-icon-date',
        'addonPre'=>'<i class="el-icon-info">前置html</i>',
        'addonEnd'=>'<a href="http://www.baidu.com">百度</a>',
      ),
      //autocomplete
      array(
        'type'=>'comp-autocomplete',
        'name'=>'people',
        'title'=>'联系人',
        'clearable'=>true,
        'placeholder'=>'联系人',
        //下面的text没用
        'options'=>array(
          array('value'=>'联系人a','text'=>'a'),
          array('value'=>'联系人b','text'=>'b'),
          array('value'=>'联系人c','text'=>'c'),
        ),
        'bindfield'=>'text1',//必选,如果不定义会报错
      ),

      // 'createDate'=>'2018-11-16',//登记日期,
      array(
        'type'=>'comp-calendar',
        'name'=>'createDate',
        'title'=>'登记日期',
        'placeholder'=>'点击选择',
      ),

      // 'vDate'=>'2018-11-16',//有效日期,日期范围
      array(
        'type'=>'comp-calendar',
        'name'=>'vDate',
        'title'=>'日期选择',
        'placeholder'=>'点击选择',
        'value'=>date('Y-m-d'),
        'bindfield'=>'date1',
        'ctype'=>'daterange',//ctype可以是year/month/date/dates/ week/datetime/datetimerange/daterange
      ),

      // 'isStop'=>'是否停用',//checkbox
      array(
        'type'=>'comp-checkbox',
        'name'=>'isStop',
        'title'=>'是否停用',
        'text'=>'停用',
        // 'true-label'=>'on',//选中时的值,可选,默认为true
        // 'false-label'=>'off',//取消选中时的值,可选,默认为false
        // 'bindfield'=>'chk1',
      ),
      // 'compFrom'=>'email',//客户来源 多选项
      array(
        'type'=>'comp-checkbox-group',
        'name'=>'compFrom',
        'title'=>'客户来源',
        'options'=>array(
          array('text'=>'网站推广','value'=>'网站推广'),
          array('text'=>'老客户介绍','value'=>1),//注意类型必须和row中的字段保持一致(1 != '1')
          array('text'=>'展会来源','value'=>2),
          array('text'=>'电话','value'=>3,'disabled'=>true),
        ),
      ),
      // 'associateClientId'=>10,//关联客户,
      array(
        'type'=>'comp-popup-select',
        'value'=>'1',
        'name'=>'associateClientId',
        'title'=>'上家客户',
        'action'=>$this->_url('ListClient'),
        //弹框列表中每行对应的主键字段,回填入hidden中,可选参数,默认为 'id',
        'rowKey'=>'id',
        //弹出选择时选定记录中的字段,需要回显在text中,
        'displayKey'=>'compName',
        //默认显示在文本框中值
        //因为该字段通常需要从其他表中获得(比如compName),所以在修改记录时,该字段需要在row中存在
        'displayText'=>'张三',
      ),
      array(
        'type'=>'comp-popup-select',
        'value'=>'2',
        'name'=>'associateClientId1',
        'title'=>'上家客户1',
        'action'=>$this->_url('ListClient'),
        //弹框列表中每行对应的主键字段,回填入hidden中,可选参数,默认为 'id',
        'rowKey'=>'id',
        //弹出选择时选定记录中的字段,需要回显在text中,
        'displayKey'=>'compName',
        //默认显示在文本框中值
        //因为该字段通常需要从其他表中获得(比如compName),所以在修改记录时,该字段需要在row中存在
        'displayText'=>'李四',
      ),
      // 'xiajia'=>array(3,4),//下家客户,弹出多选
      array(
        'type'=>'comp-popup-multi-select',
        'value'=>array(1,2),
        'name'=>'xiajia',
        'title'=>'下家客户',
        'action'=>$this->_url('ListClient'),
        //弹框列表中每行对应的主键字段,回填入hidden中,可选参数,默认为 'id',
        'rowKey'=>'id',
        //选中结果中的字段,用来回显结果用
        'displayKey'=>'compName',
      ),

      // 'traderId'=>3,//select,注意不能写成'3',否则默认选中会失效
      array(
        'type'=>'comp-select',
        'value'=>'1',
        'name'=>'traderId',
        'title'=>'业务员',
        'placeholder'=>'选择业务员',
        'options'=>array(
          array('text'=>'张三','value'=>1),
          array('text'=>'李四','value'=>2),
          array('text'=>'王五','value'=>3),
          array('text'=>'赵六','value'=>4),
        ),
        'filterable'=>true,
        // 'bindfield'=>'sex',
      ),



      //文件上传
      array(
        'type'=>'comp-file',
        'name'=>'file',
        'title'=>'工程文件',
        'action'=>$this->_url('saveFile'),//上传地址
        'accept'=>'.doc',//接受上传的文件类型
        'limit'=>3,//最大允许上传个数
        'multiple'=>true,//是否允许多选,
      ),
    );
    //验证
    $rules = array(
      'compCode'=>array(
        array(
          'required'=>true,
          'message'=>'请输入活动名称',
          // 'trigger'=>'blur'
        )
      ),
      'compName'=>array(
        array(
          'required'=>true,
          'message'=>'请输入公司名称',
          // 'trigger'=>'blur'
        )
      )
    );

    $smarty = &$this->_getView();
    $smarty->left_delimiter = '<{';
    $smarty->right_delimiter = '}>';
    //表单项
    $smarty->assign('formItems', $formItems);
    //表单验证规则
    $smarty->assign('rules', $rules);
    //数据集
    $smarty->assign('row',$row);
    //表单提交地址
    $smarty->assign('action',$this->_url('saveDemo'));
    //sontpl
    $smarty->assign('sonTpl','Test/MainForm.js');
    //通用模版
    $smarty->display('MainForm.tpl');
  }

  //一对多通用表单
  function actionTestMainson() {
    $tpl = "Jichu/MainSon.tpl";
    $action = $this->_url('saveDemo');
    $row = array(
      'id'=>'1',
      'orderCode'=>'10001',
      'orderDate'=>'2018-11-28',
      'traderId'=>2,
      'clientId'=>'100',
      'compName'=>'常州易奇',
      'clientId1'=>'101',
      'compName1'=>'常州易奇1',
      'memo'=>'订单备注',
      'Products'=>array(
        array('id'=>'1','productId'=>1,'proName'=>'软件0','number'=>100,'danjia'=>1.2,'money'=>120,'memo'=>'memo1','isHanshui'=>true,'productId1'=>11,'proName1'=>'软件11',),
        array('id'=>'2','productId'=>2,'proName'=>'软件1','number'=>200,'danjia'=>2,'money'=>400,'memo'=>'memo2','isHanshui'=>false,'productId1'=>22,'proName1'=>'软件22',),
        array('id'=>'3','productId'=>3,'proName'=>'软件2','number'=>200,'danjia'=>2,'money'=>400,'memo'=>'memo2','isHanshui'=>1,'productId1'=>33,'proName1'=>'软件33',),
        array('id'=>'4','productId'=>4,'proName'=>'软件3','number'=>200,'danjia'=>2,'money'=>400,'memo'=>'memo2','isHanshui'=>0,'productId1'=>44,'proName1'=>'软件44',),
        array('id'=>'5','productId'=>5,'proName'=>'软件4','number'=>200,'danjia'=>2,'money'=>400,'memo'=>'memo2','isHanshui'=>true,'productId1'=>55,'proName1'=>'软件55',),
        array('id'=>'6','productId'=>6,'proName'=>'软件5','number'=>200,'danjia'=>2,'money'=>400,'memo'=>'memo2','isHanshui'=>false,'productId1'=>66,'proName1'=>'软件66',),
        array('id'=>'7','productId'=>7,'proName'=>'软件6','number'=>200,'danjia'=>2,'money'=>400,'memo'=>'memo2','isHanshui'=>false,'productId1'=>77,'proName1'=>'软件77',),
        array('id'=>'8','productId'=>8,'proName'=>'软件7','number'=>200,'danjia'=>2,'money'=>400,'memo'=>'memo2','isHanshui'=>true,'productId1'=>88,'proName1'=>'软件88',),
        array('id'=>'9','productId'=>9,'proName'=>'软件8','number'=>200,'danjia'=>2,'money'=>400,'memo'=>'memo2','isHanshui'=>true,'productId1'=>99,'proName1'=>'软件99',),
      ),
    );

    $mainFormItems = array(
      //id为hidden,可以不用订单,已经在数据集中了.
      array(
        'type'=>'comp-text',
        // 'value'=>'',
        'name'=>'orderCode',
        'title'=>'订单编号',
        'clearable'=>true,
      ),
      array(
        'type'=>'comp-calendar',
        'name'=>'orderDate',
        'title'=>'登记日期',
        'placeholder'=>'点击选择',
      ),
      // 'traderId'=>3,//select,注意不能写成'3',否则默认选中会失效
      array(
        'type'=>'comp-select',
        'value'=>'1',
        'name'=>'traderId',
        'title'=>'业务员',
        'placeholder'=>'选择业务员',
        'options'=>array(
          array('text'=>'张三','value'=>1),
          array('text'=>'李四','value'=>2),
          array('text'=>'王五','value'=>3),
          array('text'=>'赵六','value'=>4),
        ),
      ),
      // 'associateClientId'=>10,//关联客户,
      array(
        'type'=>'comp-pop-select',
        'name'=>'clientId',
        'title'=>'客户',
        'action'=>$this->_url('ListClient'),
        //弹框选择列表中每行对应的主键字段,回填入hidden中,可选参数,默认为 'id',
        'rowKey'=>'id',
        //弹框选择列表中回显在文本框中的字段,
        'displayKey'=>'compName',
        //默认显示在文本框中值
        //因为该字段通常需要从其他表中获得(比如compName),所以在修改记录时,该字段需要在后台构造出来
        'displayText'=>$row['compName'],
      ),
      array(
        'type'=>'comp-pop-select',
        'name'=>'clientId1',
        'title'=>'客户1',
        'action'=>$this->_url('ListClient'),
        //弹框选择列表中每行对应的主键字段,回填入hidden中,可选参数,默认为 'id',
        'rowKey'=>'id',
        //弹框选择列表中回显在文本框中的字段,
        'displayKey'=>'compName',
        //默认显示在文本框中值
        //因为该字段通常需要从其他表中获得(比如compName),所以在修改记录时,该字段需要在后台构造出来
        'displayText'=>$row['compName1'],
      ),
      array(
        'type'=>'comp-textarea',
        'name'=>'memo',
        'title'=>'备注',
      ),
    );

    $smarty = &$this->_getView();
    $smarty->left_delimiter = '<{';
    $smarty->right_delimiter = '}>';
    $smarty->assign('mainFormItems', $mainFormItems);
    $smarty->assign('rules', $rules);
    $smarty->assign('row',$row);
    $smarty->assign('action',$action);
    //设置记录集中代表子表记录的字段名
    $smarty->assign('sonKey','Products');
    //子表列表区域表头定义
    $smarty->assign('columnsSon',array(
      'productId'=>array('text'=>'productId','width'=>100),
      'proName'=>array('text'=>'品名','width'=>200),
      'productId1'=>array('text'=>'productId1','width'=>100),
      'proName1'=>array('text'=>'品名1','width'=>200),
      'number'=>array('text'=>'数量','width'=>100),
      'money'=>array('text'=>'金额','width'=>100),
      'memo'=>array('text'=>'备注','width'=>100),
      'isHanshui'=>array('text'=>'含税'),
      'danjia'=>array('text'=>'单价'),
    ));
    //子表记录编辑表单项
    $smarty->assign('sonFormItems',array(
      'productId'=>array(
        'type'=>'comp-popup-select',
        'name'=>'productId',
        'title'=>'产品',
        'action'=>$this->_url('ListPro'),
        'rowKey'=>'id',
        //选中记录的一个字段,该字段对应的值为控件的值
        'displayKey'=>'proName',
        //这个属性会根据选中行动态变化,所以需要在通用模版中的编辑按钮点击时进行动态设置,参考handleEditSon
        'displayText'=>'',
        //选中记录时,diaplayText改变,
        //同时要改变的还有子表记录的某个字段,
        //比如选中产品后,不光要改变son['productId'],
        //还需要改变son['proName'],
        //这样子表table中的品名才能和选中记录保持一致
        //如果不设置,默认为displayKey的值(如果只有一个popup控件可不设置)
        'textKey'=>'proName',
      ),
      'productId1'=>array(
        'type'=>'comp-popup-select',
        'name'=>'productId1',
        'title'=>'其他产品',
        'action'=>$this->_url('ListPro'),
        'rowKey'=>'id',
        'displayKey'=>'proName',
        //这个属性会根据选中行动态变化,所以需要在通用模版中的编辑按钮点击时进行动态设置,参考handleEditSon
        'displayText'=>'',
        //选中记录时,diaplayText改变,
        //同时要改变的还有子表记录的某个字段,
        //比如选中产品后,不光要改变son['productId'],
        //还需要改变son['proName'],
        //这样子表table中的品名才能和选中记录保持一致
        //如果不设置,默认为displayKey的值
        'textKey'=>'proName1',
      ),
      'danjia'=>array('type'=>'comp-text','name'=>'danjia','title'=>'单价'),
      'number'=>array('type'=>'comp-text','name'=>'number','title'=>'数量'),
      'money'=>array('type'=>'comp-text','name'=>'money','title'=>'金额'),
      'isHanshui'=>array('type'=>'comp-select','name'=>'isHanshui','title'=>'是否含税','options'=>array(
        array('text'=>'否','value'=>false),
        array('text'=>'是','value'=>true),
      )),
      'memo'=>array('type'=>'comp-textarea','name'=>'memo','title'=>'备注'),
    ));
    //需要进行数据渲染的列,声明渲染回调函数
    $smarty->assign('columnsFormatter',array(
      'isHanshui'=>'isHanshuiFormatter',//isHanshuFormatter需要在sonTpl中定义
      'otherCol'=>'otherColFormatter',
    ));
    //表单验证声明
    $smarty->assign('rules',array(
      'traderId'=>array(
        array('required'=>true,'message'=>'选择业务员',)
      ),
      'clientId'=>array(
        array('required'=>true,'message'=>'客户必填',),
        // array('validator'=>'test','message'=>'客户必填',)
      ),
      'orderCode'=>array(
        array('required'=>true,'message'=>'订单编号必填',),
        array('validator'=>'checkOrderCode')
      ),
      // 'Products'=>array(
      //   // array('validator'=>'checkProducts')
      // ),
    ));
    $smarty->display($tpl);
  }

  function actionTestMainsonNew() {
    $action = $this->_url('saveDemo');
    $row = array(
      'id'=>'1',
      'orderCode'=>'10001',
      'orderDate'=>'2018-11-28',
      'traderId'=>2,
      'traderId1'=>1,
      'clientId'=>'100',
      'compName'=>'常州易奇',
      'clientId1'=>'101',
      'compName1'=>'常州易奇1',
      'memo'=>'订单备注',
      'Products'=>array(
        array('id'=>'1','productId'=>1,'proName'=>'软件0','number'=>100,'danjia'=>1.2,'money'=>120,'memo'=>'memo1','isHanshui'=>true,'productId1'=>11,'proName1'=>'士大夫','productId2'=>'331','__disabledButton1'=>true,
          // 'clientId7'=>array(),'clientName7'=>'',
          'pic'=>array(
            array('name'=>'food.jpeg','imageId'=>11,'url'=>'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100'),
            array('name'=>'food2.jpeg','imageId'=>22,'url'=>'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100'),
          ),
          'picDesc'=>'2张',
        ),
        array('id'=>'2','productId'=>2,'proName'=>'软件1','number'=>200,'danjia'=>2,'money'=>400,'memo'=>'memo2','isHanshui'=>false,'productId1'=>22,'proName1'=>'软件22','productId2'=>'111',
          'pic'=>array(
            array('name'=>'food.jpeg','imageId'=>11,'url'=>'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100'),
          ),
          'picDesc'=>'1张',
        ),
        array('id'=>'3','productId'=>3,'proName'=>'软件2','number'=>200,'danjia'=>2,'money'=>400,'memo'=>'memo2','isHanshui'=>1,'productId1'=>33,'proName1'=>'软件33',),
        array('id'=>'4','productId'=>4,'proName'=>'软件3','number'=>200,'danjia'=>2,'money'=>400,'memo'=>'memo2','isHanshui'=>0,'productId1'=>44,'proName1'=>'软件44',),
        array('id'=>'5','productId'=>5,'proName'=>'软件4','number'=>200,'danjia'=>2,'money'=>400,'memo'=>'memo2','isHanshui'=>true,'productId1'=>55,'proName1'=>'软件55',),
        array('id'=>'6','productId'=>6,'proName'=>'软件5','number'=>200,'danjia'=>2,'money'=>400,'memo'=>'memo2','isHanshui'=>false,'productId1'=>66,'proName1'=>'软件66',),
        array('id'=>'7','productId'=>7,'proName'=>'软件6','number'=>200,'danjia'=>2,'money'=>400,'memo'=>'memo2','isHanshui'=>false,'productId1'=>77,'proName1'=>'软件77',),
        array('id'=>'8','productId'=>8,'proName'=>'软件7','number'=>200,'danjia'=>2,'money'=>400,'memo'=>'memo2','isHanshui'=>true,'productId1'=>88,'proName1'=>'软件88',),
        array('id'=>'9','productId'=>9,'proName'=>'软件8','number'=>200,'danjia'=>2,'money'=>400,'memo'=>'memo2','isHanshui'=>true,'productId1'=>99,'proName1'=>'软件99',),
      ),
    );

    //子表记录加入关联表对象及外键
    foreach($row['Products'] as $k=>&$v) {
      $v['clientId'] = $k+1;
      $v['clientName'] = "客户{$k}";
      // $v['Client'] = array(
      //   'id'=>$k+1,
      //   'compCode'=>'compCode'.$k,
      //   'compName'=>'公司名'.$k,
      // );
    }
    $mainFormItems = array(
      //id为hidden,可以不用订单,已经在数据集中了.
      array(
        'type'=>'comp-text',
        // 'value'=>'',
        'name'=>'orderCode',
        'title'=>'订单编号',
        'clearable'=>true,
      ),
      array(
        'type'=>'comp-calendar',
        'name'=>'orderDate',
        'title'=>'登记日期',
        'placeholder'=>'点击选择',
      ),
      // 'traderId'=>3,//select,注意不能写成'3',否则默认选中会失效
      array(
        'type'=>'comp-select',
        'value'=>'1',
        'name'=>'traderId',
        'title'=>'业务员',
        'placeholder'=>'选择业务员',
        'options'=>array(
          array('text'=>'张三','value'=>1),
          array('text'=>'李四','value'=>2),
          array('text'=>'王五','value'=>3),
          array('text'=>'赵六','value'=>4),
        ),
      ),
      //traderId改变后,traderId1的options会减少
      array(
        'type'=>'comp-select',
        'value'=>2,
        'name'=>'traderId1',
        'title'=>'联动业务员',
        'placeholder'=>'联动业务员',
        'options'=>array(
          array('text'=>'张三','value'=>1),
          array('text'=>'李四','value'=>2),
          array('text'=>'王五','value'=>3),
          array('text'=>'赵六','value'=>4),
        ),
      ),
      // 'associateClientId'=>10,//关联客户,
      array(
        'type'=>'comp-popup-select',
        'name'=>'clientId',
        'title'=>'客户',
        'action'=>$this->_url('ListClient'),
        //弹框选择列表中每行对应的主键字段,回填入hidden中,可选参数,默认为 'id',
        'rowKey'=>'id',
        //弹框选择列表中回显在文本框中的字段,
        'displayKey'=>'compName',
        //默认显示在文本框中值
        //因为该字段通常需要从其他表中获得(比如compName),所以在修改记录时,该字段需要在后台构造出来
        'displayText'=>$row['compName'],
      ),
      array(
        'type'=>'comp-popup-select',
        'name'=>'clientId1',
        'title'=>'客户1',
        'action'=>$this->_url('ListClient'),
        //弹框选择列表中每行对应的主键字段,回填入hidden中,可选参数,默认为 'id',
        'rowKey'=>'id',
        //弹框选择列表中回显在文本框中的字段,
        'displayKey'=>'compName',
        //默认显示在文本框中值
        //因为该字段通常需要从其他表中获得(比如compName),所以在修改记录时,该字段需要在后台构造出来
        'displayText'=>$row['compName1'],
      ),
      array(
        'type'=>'comp-textarea',
        'name'=>'memo',
        'title'=>'备注',
      ),
    );

    $smarty = &$this->_getView();
    $smarty->left_delimiter = '<{';
    $smarty->right_delimiter = '}>';
    //如果不指定,不显示
    $smarty->assign('formTitle',"在后台中设置formTitle模版变量");
    //主表单项
    $smarty->assign('mainFormItems', $mainFormItems);
    //待修改的记录集
    $smarty->assign('row',$row);
    //数据保存url
    $smarty->assign('action',$action);

    //设置记录集中代表子表记录的字段名
    $smarty->assign('sonKey','Products');

    //子表列表区域表头定义
    //如果不需要合计的字段,设置summation字段为false
    $smarty->assign('columnsSon',array(
      // 'id'=>array('text'=>'id','summation'=>false),
      // 'productId'=>array('text'=>'productId','width'=>100,'summation'=>false),
      //如果是操作按钮列(showButton==true),列宽必须>=130,如果小于130,在渲染时会变成130
      'proName'=>array('text'=>'品名','width'=>50,'showButton'=>true),
      'productId1'=>array('text'=>'productId1','width'=>100),
      'productId2'=>array('text'=>'productId2','width'=>100),
      'proName1'=>array('text'=>'品名1','width'=>200),
      'danjia'=>array('text'=>'单价','width'=>100),
      'number'=>array('text'=>'数量','width'=>100),
      'money'=>array('text'=>'金额','width'=>100),
      'memo'=>array('text'=>'备注','width'=>100),
      //支持列渲染函数,对应callback中的方法名
      'isHanshui'=>array('text'=>'含税','formatter'=>'isHanshuiFormatter','width'=>''),
      //客户id是数字,在子表列表中需要显示为其他字段信息
      'clientId7'=>array('text'=>'客户7','width'=>'100','displayKey'=>'clientName7'),
      'checkboxgroup'=>array('text'=>'爱好','width'=>'100','displayKey'=>'aihao'),
      'pic'=>array('text'=>'图片','width'=>'','displayKey'=>'picDesc'),
    ));
    //子表记录编辑表单项
    $smarty->assign('sonFormItems',array(
      'productId'=>array(
        'type'=>'comp-popup-select',
        'name'=>'productId',
        'title'=>'产品',
        'action'=>$this->_url('ListPro'),
        'rowKey'=>'id',
        //选中记录的一个字段,该字段对应的值为控件的值
        'displayKey'=>'proName',
        //这个属性会根据选中行动态变化,所以需要在通用模版中的编辑按钮点击时进行动态设置,参考handleEditSon
        'displayText'=>'',
        //从弹框列表中选中记录时,diaplayText改变,
        //同时要改变的还有子表记录的某个字段,
        //比如选中产品后,不光要改变son['productId'],
        //还需要改变son['proName'],
        //这样子表table中的品名才能和选中记录保持一致
        //如果不设置,默认为displayKey的值(如果只有一个popup控件可不设置)
        'textKey'=>'proName',
      ),
      'productId1'=>array(
        'type'=>'comp-popup-select',
        'name'=>'productId1',
        'title'=>'其他产品',
        'action'=>$this->_url('ListPro'),
        'rowKey'=>'id',
        'displayKey'=>'proName',
        //这个属性会根据选中行动态变化,所以需要在通用模版中的编辑按钮点击时进行动态设置,参考handleEditSon
        'displayText'=>'',
        //选中记录时,diaplayText改变,
        //同时要改变的还有子表记录的某个字段,
        //比如选中产品后,不光要改变son['productId'],
        //还需要改变son['proName'],
        //这样子表table中的品名才能和选中记录保持一致
        //如果不设置,默认为displayKey的值
        'textKey'=>'proName1',
      ),
      'productId2'=>array(
        'type'=>'comp-cascader',
        'name'=>'productId2',
        'title'=>'原纱cascader',
        'rowKey'=>'id',
        'displayKey'=>'name',
        'displayText'=>'',
        'textKey'=>'name',
        'parentId'=>1,
        'urlTree'=>"?controller=jichu_test&action=GetTreeJson",
        'urlPath'=>"?controller=jichu_test&action=GetPath",
      ),
      'danjia'=>array('type'=>'comp-text','name'=>'danjia','title'=>'单价1'),
      'number'=>array('type'=>'comp-text','name'=>'number','title'=>'数量'),
      'money'=>array('type'=>'comp-text','name'=>'money','title'=>'金额','disabled'=>true),
      'isHanshui'=>array(
        'type'=>'comp-select',
        'name'=>'isHanshui',
        'title'=>'是否含税',
        'options'=>array(
          array('text'=>'否','value'=>false),
          array('text'=>'是','value'=>true),
        )
      ),
      'pic'=>array(
        'type'=>'comp-image',
        'name'=>'pic',
        'title'=>'营业执照',
        'action'=>$this->_url('saveFile'),//上传地址
        'actionRemove'=>$this->_url('removeFile'),//删除图片时需要从服务器删除,可以不定义
        // 'action'=>'aa.php',//上传地址
        'accept'=>'.jpg,.bmp,.PNG',//接受上传的文件类型
        'limit'=>3,//最大允许上传个数
        'multiple'=>true,//是否允许多选,
      ),
      'memo'=>array('type'=>'comp-textarea','name'=>'memo','title'=>'备注'),
      //下拉选择
      'clientId7'=>array(
        'type'=>'comp-select',
        'name'=>'clientId7',
        'title'=>'客户7',
        'multiple'   =>true,
        'options'=>array(
          array('text'=>'张三','value'=>1),
          array('text'=>'李四','value'=>2),
        )
      ),
      'checkboxgroup'=>array(
        'type'=>'comp-checkbox-group',
        'name'=>'checkboxgroup',
        'title'=>'爱好',
        'options'=>array(
          array('text'=>'打牌','value'=>'1'),
          array('text'=>'敲代码','value'=>'2'),
        )
      )
    ));

    //是否隐藏子表表头中的新增按钮,可以不设置-默认为false,
    $smarty->assign('hideButtonAddSon',false);

    //ajax删除明细记录的url
    $smarty->assign('urlRemoveSon',$this->_url('RemoveAjax'));

    //子记录的操作按钮,
    //如果不设置sonButtons变量,默认显示删除和复制两个按钮
    //如果只需要修改或者删除按钮,也需要指定
    //如果部使用默认的修改删除方法,改写funcName
    $smarty->assign('sonButtons',array(
      //修改按钮-最简模式
      //handleEditSon为内置方法,也可指定为sontple中定义的其他方法
      //图标默认为 defaultEditButtonsIcons[0],无需指定
      array('text'=>'修改-简单','type'=>'edit'),
      //修改按钮-完整模式
      //使用指定图标
      //使用指定func,sontpl中定义
      array('text'=>'修改按钮-完整模式','type'=>'edit','icon'=>'el-icon-time','options'=>array(
        'funcName'=>'handleEditSon1',//如果methods中不存在,从sontpl中去找
        //如果子表记录中存在__url字段,则会将$son['__url']作为ajax提交地址
        //适用于每行子表记录对应不同的ajax地址的场景
        'disabledColumn'=>'__disabledButton1',
      )),
      //删除按钮,
      //handleDelete为内置方法,也可指定为sontple中定义的其他方法
      //图标默认为 defaultEditButtonsIcons[1],无需指定
      //如果指定了,使用指定的图标
      //如果子表记录中的__disabledButton1字段的值为true,删除按钮不可用
      array('text'=>'删除','type'=>'remove','options'=>array(
        'funcName'=>'handleDelete',
        'url'=>$this->_url('RemoveAjax'),
        //如果子表记录中存在__url字段,则会将$son['__url']作为ajax提交地址
        //适用于每行子表记录对应不同的ajax地址的场景
        'urlColumn'=>'__url',
      )),
      //复制
      //从第四个按钮开始,不需要指定icon,都会显示在dropdown中
      //handleCopy为内置方法,也可指定为sontple中定义的其他方法
      //如果指定了,使用指定的图标
      array('text'=>'复制','type'=>'copy','options'=>array(
        'funcName'=>'handleCopy',//通用模版中存在handleCopy方法,所以不会考虑sontpl中的方法了
      )),
      //用户自定义按钮,
      //可以不指定icon,使用默认按钮
      array('text'=>'自定义动作','type'=>'func','icon'=>'el-icon-news','options'=>array(
        'funcName'=>'userFunc',//在sontpl中定义
      )),
      //自定义组件
      array('text'=>'自定义组件','type'=>'comp','options'=>array(
        //组件名,必须在sontpl中自定义
        'type'=>'user-comp',
        //组件名称,必须指定
        'name'=>'username',
        //点击按钮时,默认执行的组件方法
        //比如自定义的弹窗控件,每次点击后都应该显示出来,
        //大部分情况下都需要定义,否则组件的状态不会改变.
        'onclickButton'=>'show',
      )),
      //handleEditSon为内置方法,也可指定为sontple中定义的其他方法
      //图标默认为 defaultEditButtonsIcons[0],无需指定
      //如果指定了,使用指定的图标
      /*array('text'=>'修改','isEdit'=>true,'funcName'=>'handleEditSon','icon'=>'el-icon-news'),

      //handleDelete为内置方法,也可指定为sontple中定义的其他方法
      //图标默认为 defaultEditButtonsIcons[1],无需指定
      //如果指定了,使用指定的图标
      array('text'=>'删除','isRemove'=>true,'funcName'=>'handleDelete'),

      //handleCopy为内置方法,也可指定为sontple中定义的其他方法
      //图标默认为 defaultEditButtonsIcons[2],无需指定
      //如果指定了,使用指定的图标
      array('text'=>'复制','isCopy'=>true,'funcName'=>'handleCopy','icon'=>'el-icon-check'),

      //从第四个按钮开始,不需要指定icon,都会显示在dropdown中
      array('text'=>'其他按钮','funcName'=>'setGongyi'),
      array('text'=>'工艺','funcName'=>'setGongyi'),*/
    ));

    //子记录列表中除了修改删除外的其他按钮,funcName对应的方法必须在sonTpl中另外定义,
    //已经取消了.
    // $smarty->assign('otherButtons',array(
    //   array(
    //     'text'=>'工艺',
    //     'funcName'=>'setGongyi',
    //     // 'icon'=>'el-icon-delete',
    //   )
    // ));

    //表单验证声明
    $smarty->assign('rules',array(
      'traderId'=>array(
        array('required'=>true,'message'=>'选择业务员',)
      ),
      'clientId'=>array(
        array('required'=>true,'message'=>'客户必填',),
        // array('validator'=>'test','message'=>'客户必填',)
      ),
      'orderCode'=>array(
        array('required'=>true,'message'=>'订单编号必填',),
        array('validator'=>'checkOrderCode')
      ),
    ));
    $smarty->assign('sonTpl','Test/MainSonForm.js');
    $smarty->display('MainSonForm.tpl');
  }

  //消息提示
  function actionTestMessage(){
    $smarty = &$this->_getView();
    $smarty->left_delimiter = '<{';
    $smarty->right_delimiter = '}>';
    $smarty->display('Test/TestMessage.tpl');
  }

  function actionTestAttrs(){
    $smarty = &$this->_getView();
    $smarty->left_delimiter = '<{';
    $smarty->right_delimiter = '}>';
    $smarty->display('Test/TestMessage.tpl');
  }

  function actionTestBus() {
    $smarty = &$this->_getView();
    $smarty->left_delimiter = '<{';
    $smarty->right_delimiter = '}>';
    $smarty->display('Test/TestBus.tpl');
  }
  //后台保存数据的演示
  function actionSaveDemo() {
    $requestParam = file_get_contents('php://input');
    $_POST = json_decode($requestParam,true);

    $ret = array(
      'success'=>true,
      'msg'=>'后台保存成功',
    );
    echo json_encode($ret);exit;
  }

  //获得客户列表页面的字段定义
  function actionListClient() {
    //注意axios请求的content-type为 "application/json;charset=UTF-8",必须使用php流方式接收,
    //参考https://www.cnblogs.com/winyh/p/7911204.html
    $requestParam = file_get_contents('php://input');
    $_POST = json_decode($requestParam,true);
    $_POST['sortOrder'] = $_POST['sortOrder']=='descending' ? 'desc' : 'asc';
    $_POST['sortBy'] = $_POST['sortBy']==''? 'id' : $_POST['sortBy'];
    // dump($_POST);exit;

    $pagesize = $_POST['pagesize'];
    $currentPage = $_POST['currentPage'];
    $key = $_POST['key'];
    $keyField = isset($_POST['colForKey'])?$_POST['colForKey']:'compName';
    $from = ($currentPage-1)*$pagesize;

    $arr = array();
    //获得数据集
    $str = "select * from jichu_client where 1 ";
    if($key!='') $str .= " and {$keyField} like '%{$key}%'";
    $str .= " order by {$_POST['sortBy']} {$_POST['sortOrder']}";
    $str .= " limit {$from},{$pagesize}";
    $rowset = $this->_modelExample->findBySql($str);
    // dump($str);dump($rowset);exit;
    foreach($rowset as $k=> &$v) {
      // unset($v['id']);
      $v['mobile1'] = $v['mobile'];
      $v['mobile2'] = $v['mobile'];
      $v['mobile3'] = $v['mobile'];
      $v['website'] = "<a href='http://www.baidu.com'>baidu</a>";
      $v['tags'] = "<i class='el-icon-edit'></i><i class='el-icon-delete'></i>";
      //注意不能写组件,只能用原生的html标签.
      $v['btns'] = '点击弹开1';
      //设置编辑按钮的url
      $v['__url'] = "http://www.baidu.com?a=0";
      //增加
      if($k==1) {
        //设置背景色,在el-table.stripe 的情况下可能失效
        $v['__bgColor']='oldlace';
        //设置编辑按钮不可用
        $v['__disabledButton3']=true;
        $v['__disabledButton1']=true;
      }
      if($k==2) {
        $v['__bgColor']='#f0f9eb';
      }

      //设置联系人
      $v['Peoples'] = array(
        array('name'=>'张三'.$k,'tel'=>'13901200101','address'=>'常州科教城创研港2号楼1203'),
        array('name'=>'李四'.$k,'tel'=>'13901200101','address'=>'常州科教城创研港2号楼1203'),
        array('name'=>'王五'.$k,'tel'=>'13901200101','address'=>'常州科教城创研港2号楼1203'),
        array('name'=>'王五'.$k,'tel'=>'13901200101','address'=>'常州科教城创研港2号楼1203'),
        array('name'=>'王五'.$k,'tel'=>'13901200101','address'=>'常州科教城创研港2号楼1203'),
        array('name'=>'王五'.$k,'tel'=>'13901200101','address'=>'常州科教城创研港2号楼1203'),
        array('name'=>'王五'.$k,'tel'=>'13901200101','address'=>'常州科教城创研港2号楼1203'),
        array('name'=>'王五'.$k,'tel'=>'13901200101','address'=>'常州科教城创研港2号楼1203'),
        array('name'=>'王五'.$k,'tel'=>'13901200101','address'=>'常州科教城创研港2号楼1203'),
        array('name'=>'王五'.$k,'tel'=>'13901200101','address'=>'常州科教城创研港2号楼1203'),
      );

    }



    //表头信息
    $arr_field_info = array(
      // "_edit"    => '操作',
      "id"    => 'id',
      'compName'  => array('text'=>'公司名称'),
      'compCode'  => array('text'=>'公司代码','width'=>''),
      // 'colorPt'  => array('text'=>'潘通色号','width'=>80),
      // 'memo'  => array('text'=>'备注'),//最后一列最好不要设置宽度,表格会自动拉伸到100%
    );
    //测试id重复的问题
    $rowset[0]['id'] = $rowset[1]['id'];
    $ret = array(
      'total'=>300,
      'columns'=>$arr_field_info,
      'rows'=>$rowset,
    );
    echo json_encode($ret);exit;
  }
  //获得产品列表页面
  function actionListPro() {
    //注意axios请求的content-type为 "application/json;charset=UTF-8",必须使用php流方式接收,
    //参考https://www.cnblogs.com/winyh/p/7911204.html
    $requestParam = file_get_contents('php://input');
    $_POST = json_decode($requestParam,true);

    $pagesize = $_POST['pagesize'];
    $currentPage = $_POST['currentPage'];
    $key = $_POST['key'];
    $keyField = isset($_POST['colForKey'])?$_POST['colForKey']:'compName';
    $from = ($currentPage-1)*$pagesize;

    //表头信息
    $arr_field_info = array(
      // "_edit"    => '操作',
      // "id"    => 'id',
      'proCode'  => array('text'=>'产品代码'),
      'proName'  => array('text'=>'产品名称'),
      'guige'  => array('text'=>'规格'),
      // 'colorPt'  => array('text'=>'潘通色号','width'=>80),
      // 'memo'  => array('text'=>'备注'),//最后一列最好不要设置宽度,表格会自动拉伸到100%
    );

    $ret = array(
      'total'=>4,
      'columns'=>$arr_field_info,
      'rows'=>array(
        array('id'=>1,'proCode'=>'001','proName'=>'产品A','guige'=>'1*2*3'),
        array('id'=>2,'proCode'=>'002','proName'=>'产品B','guige'=>'1*2*3'),
        array('id'=>3,'proCode'=>'003','proName'=>'产品C','guige'=>'1*2*3'),
        array('id'=>4,'proCode'=>'004','proName'=>'产品D','guige'=>'1*2*3'),
      ),
    );
    echo json_encode($ret);exit;
  }


  //获得表头定义,
  //在pop-select控件中,为了简化url定义,该方法和获得记录的方法合并为一个方法,
  function actionGetColumn() {
    //表头信息
    $arr_field_info = array(
      "_edit"    => '操作',
      'colorCode'  => array('text'=>'色号','width'=>80),
      'colorName'  => array('text'=>'名称','width'=>80),
      'colorPt'  => array('text'=>'潘通色号','width'=>80),
      'memo'  => array('text'=>'备注'),//最后一列最好不要设置宽度,表格会自动拉伸到100%
    );
    $ret = array(
      'columns'=>$arr_field_info
    );
    echo json_encode($ret);exit;
  }

  //保存上传图片或者文件action
  function actionSaveFile() {
    $requestParam = file_get_contents('php://input');
    $_POST = json_decode($requestParam,true);
    //如果失败,不能输出json对象,而必须如下:返回503状态码,因为element-upload组件是根据状态码来判断是否成功
    /*
    header('HTTP/1.1 503 Service Unavailable');
    exit;
    */
    //处理导入文件
    $ret = array(
      'success'=>true,
      'msg'=>'保存成功',
      //图片在服务器上的路径,必须为有效的图片地址,否则图片不会显示
      'imgPath'=>'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100',
      'imageId'=>1000,
    );
    echo json_encode($ret);exit;
  }

  //删除图片或者文件
  function actionRemoveFile() {
    $requestParam = file_get_contents('php://input');
    $_POST = json_decode($requestParam,true);

    $url = $_POST['url'];
    //根据url提取文件名,进行删除

    $ret = array(
      'success'=>true,
      'msg'=>'图片删除成功',
      'url'=>$url,
    );
    echo json_encode($ret);exit;
  }


  /**
   * 获取编辑界面的配置信息
   * Time：2017/11/02 08:58:44
   * @author li
  */
  function _getFields(){
    $this->fldMain = array(
      'catCode' => array('title' => '分类编码', 'type' => 'text', 'value' => ''),
      'catName' => array('title' => '分类名称', 'type' => 'text', 'value' => ''),
      'id' => array('type' => 'hidden', 'value' => ''),
    );

    $this->rules = array(
      'catCode'=>'required repeat',
      'catName'=>'required repeat'
    );
  }

  /**
   * @desc ：产品档案查询
   * Time：2017/07/31 16:18:03
   * @author Wuyou
  */
  function actionRight() {
    $this->authCheck('90-5');
    FLEA::loadClass('TMIS_Pager');
    $arr = TMIS_Pager::getParamArray(array(
        'key' => ''
    ));
    $str = "select * from jichu_cat where 1 ";
    if ($arr['key'] != '') $str .= " and catName like '%{$arr['key']}%'";

    $str .= " order by orderSort desc,id desc";
    $pager = new TMIS_Pager($str);
    $rowset = $pager->findAll();
    if (count($rowset) > 0) foreach($rowset as &$v) {
        $v['_edit'] = $this->getEditHtml($v['id']) . ' ' . $this->getRemoveHtml($v['id']);
    }

    $arr_field_info = array(
      "_edit"    => '操作',
      'catCode'  => array('text'=>'分类编码','width'=>200),
      'catName'  => array('text'=>'分类名称','width'=>200),
    );

    $smarty = &$this->_getView();
    $smarty->assign('title', '分类列表');
    $smarty->assign('arr_field_info', $arr_field_info);
    $smarty->assign('arr_field_value', $rowset);
    $smarty->assign('arr_condition', $arr);
    $smarty->assign('page_info', $pager->getNavBar($this->_url($_GET['action'], $arr)));
    $smarty->display('TblList.tpl');
  }

  /**
   * @desc ：产品弹出选择
   * Time：2017/07/31 16:22:28
   * @author Wuyou
  */
  function actionPopup() {
    // dump($_GET);exit;
    FLEA::loadClass('TMIS_Pager');
    $arr = TMIS_Pager::getParamArray(array(
        'key' => ''
    ));
    $str = "select * from jichu_cat where 1 ";
    if ($arr['key'] != '') $str .= " and catName like '%{$arr['key']}%'";
    //排序
    $str .= " order by orderSort desc,id desc";

    $pager = new TMIS_Pager($str);
    $rowset = $pager->findAll();
    $smarty = &$this->_getView();
    $smarty->assign('title', '选择产品分类');
    $arr_field_info = array(
      'catCode'  => '分类编码',
      'catName'  => '分类名称',
    );

    $smarty->assign('arr_field_info',$arr_field_info);
    $smarty->assign('arr_field_value',$rowset);
    $smarty->assign('add_display','none');
    $smarty->assign('arr_condition',$arr);
    $smarty->assign('page_info',$pager->getNavBar($this->_url('Popup',$arr)));
    $smarty-> display('Popup/CommonNew.tpl');
  }

  /**
   * @desc ：新增产品档案
   * Time：2017/07/31 16:25:19
   * @author Wuyou
  */
  function actionAdd() {
    $this->authCheck('90-5');
    //加载field数组
    $this->_getFields();

    $smarty = & $this->_getView();
    $smarty->assign('fldMain',$this->fldMain);
    $smarty->assign('title','添加产品分类');
    $smarty->assign('rules',$this->rules);
    $smarty->display('Main/A1.tpl');
  }

  /**
   * @desc ：产品信息编辑
   * Time：2017/07/31 16:30:57
   * @author Wuyou
  */
  function actionEdit() {
    echo "actionEdit";exit;
    $this->authCheck('90-5');
    //加载field数组
    $this->_getFields();

    $row = $this->_modelExample->find($_GET['id']);
    $this->fldMain = $this->getValueFromRow($this->fldMain,$row);
    $smarty = &$this->_getView();
    $smarty->assign('fldMain',$this->fldMain);
    $smarty->assign('rules',$this->rules);
    $smarty->assign('title', '编辑产品分类');
    $smarty->assign('aRow', $row);
    $smarty->display('Main/A1.tpl');
  }

  /**
   * @desc ：产品信息保存
   * Time：2017/07/31 16:28:22
   * @author Wuyou
  */
  function actionSave() {
    $this->authCheck('90-5');

    $this->_modelExample->save($_POST);
    js_alert(null, 'window.parent.showMsg("保存成功")', $this->_url($_POST['fromAction']));
    exit;
  }

  /**
   * @desc ：产品档案删除 存在订单则不允许删除
   * Time：2017/07/31 15:30:14
   * @author Wuyou
  */
  function actionRemove() {
      // 删除验证
      if($_GET['id']!="") {
          $sql="SELECT count(*) as cnt FROM `jichu_product` where catId=".$_GET['id'];
          $re = $this->_modelExample->findBySql($sql);
          //dump($re);exit;
          if($re[0]['cnt']>0) {
              js_alert('分类已有产品档案，不允许删除',null,$this->_url('Right'));
          }
      }
      parent::actionRemove();
  }

  function errorStatus($num){//网页返回码
    static $http = array (
      100 => "HTTP/1.1 100 Continue",
      101 => "HTTP/1.1 101 Switching Protocols",
      200 => "HTTP/1.1 200 OK",
      201 => "HTTP/1.1 201 Created",
      202 => "HTTP/1.1 202 Accepted",
      203 => "HTTP/1.1 203 Non-Authoritative Information",
      204 => "HTTP/1.1 204 No Content",
      205 => "HTTP/1.1 205 Reset Content",
      206 => "HTTP/1.1 206 Partial Content",
      300 => "HTTP/1.1 300 Multiple Choices",
      301 => "HTTP/1.1 301 Moved Permanently",
      302 => "HTTP/1.1 302 Found",
      303 => "HTTP/1.1 303 See Other",
      304 => "HTTP/1.1 304 Not Modified",
      305 => "HTTP/1.1 305 Use Proxy",
      307 => "HTTP/1.1 307 Temporary Redirect",
      400 => "HTTP/1.1 400 Bad Request",
      401 => "HTTP/1.1 401 Unauthorized",
      402 => "HTTP/1.1 402 Payment Required",
      403 => "HTTP/1.1 403 Forbidden",
      404 => "HTTP/1.1 404 Not Found",
      405 => "HTTP/1.1 405 Method Not Allowed",
      406 => "HTTP/1.1 406 Not Acceptable",
      407 => "HTTP/1.1 407 Proxy Authentication Required",
      408 => "HTTP/1.1 408 Request Time-out",
      409 => "HTTP/1.1 409 Conflict",
      410 => "HTTP/1.1 410 Gone",
      411 => "HTTP/1.1 411 Length Required",
      412 => "HTTP/1.1 412 Precondition Failed",
      413 => "HTTP/1.1 413 Request Entity Too Large",
      414 => "HTTP/1.1 414 Request-URI Too Large",
      415 => "HTTP/1.1 415 Unsupported Media Type",
      416 => "HTTP/1.1 416 Requested range not satisfiable",
      417 => "HTTP/1.1 417 Expectation Failed",
      500 => "HTTP/1.1 500 Internal Server Error",
      501 => "HTTP/1.1 501 Not Implemented",
      502 => "HTTP/1.1 502 Bad Gateway",
      503 => "HTTP/1.1 503 Service Unavailable",
      504 => "HTTP/1.1 504 Gateway Time-out"
    );
    header($http[$num]);
    exit();
  }

  function actionPromise() {
    $smarty = &$this->_getView();
    $smarty->display('test/Promise.tpl');
  }

  function actionGetPromise() {
    $arr = array(
      'success'=>true,
      // 'success'=>false,
      'msg'=>'成功'
    );
    echo json_encode($arr);
  }

  //测试异步组件
  function actionAsyncComp() {
    $smarty = &$this->_getView();
    $smarty->display('test/AsyncComp.tpl');
  }

  //测试异步组件
  function actionScrollbar() {
    $smarty = &$this->_getView();
    $smarty->display('test/Scrollbar.tpl');
  }

  //导出excle
  function actionExportXls() {
    $smarty = &$this->_getView();
    $smarty->display('test/ExportXls.tpl');
  }
  //根据传入的参数返回对应数据
  function actionExportData() {
    $requestParam = file_get_contents('php://input');
    $_POST = json_decode($requestParam,true);
    // dump($_POST);
    $page=$_POST['page'];
    $pageSize = $_POST['pageSize'];
    $from = ($page-1)*$pageSize;

    $sql = "select * from jichu_client where 1 ";
    $sql .= " limit {$from},{$pageSize}";
    $ret = $this->_modelExample->findBySql($sql);
    echo json_encode(array(
      'rows'=>$ret
    ));
  }

  //得到树形结构数组
  function actionGetTreeJson() {
    $requestParam = file_get_contents('php://input');
    $_POST = json_decode($requestParam,true);
    $parentId = $_POST['parentId'];

    //从数据库中获得$parentId下所有的子节点
    //可能是一次性获得整个树,也可能是获得直接子节点
    //暂时只考虑第一种情况,
    $tree = array(
      array('label'=>'1(from后台)','value'=>'1','children'=>array(
        array('label'=>'1-1(from后台)','value'=>'11','children'=>array(
          array('label'=>'1-1-1(from后台)','value'=>'111'),
          array('label'=>'1-1-2(from后台)','value'=>'112'),
        )),
        array('label'=>'1-2(from后台)','value'=>'12')
      )),
      array('label'=>'2(from后台)','value'=>'2','children'=>array(
          array('label'=>'2-1(from后台)','value'=>'21',),
          array('label'=>'2-2(from后台)','value'=>'22',)
      )),
      array('label'=>'3(from后台)','value'=>'3','children'=>array(
        array('label'=>'3-3(from后台)','value'=>'33','children'=>array(
          array('label'=>'3-3-1(from后台)','value'=>'331'),
        ))
      )),
    );
    echo json_encode(array(
      'success'=>true,
      'tree'=>$tree
    ));exit;
  }

  //根据子节点,取得路径信息
  function actionGetPath() {
    $requestParam = file_get_contents('php://input');
    $_POST = json_decode($requestParam,true);
    //子节点
    $nodeId = $_POST['id'];
    //截至节点,如果null,表示获取到根节点的路径
    $rootId = $_POST['rootId'];

    //从数据库中获得从nodeId到rootId的路径
    $arr = array(
      '111'=>array('1','11','111'),
      '112'=>array('1','11','112'),
      '12'=>array('1','12','331'),
      '21'=>array('2','21'),
      '22'=>array('2','22'),
      '331'=>array('3','33','331'),
    );
    echo json_encode(array(
      'success'=>true,
      'path'=>$arr[$nodeId]
    ));exit;
  }

  function actionTestEditTable() {
    $smarty = &$this->_getView();
    $smarty->display('test/TestEditTable.tpl');
  }

  function actionDialog() {
    $smarty = &$this->_getView();
    $smarty->left_delimiter = '<{';
    $smarty->right_delimiter = '}>';
    //表单项
    $smarty->assign('formItems', $formItems);
    //表单验证规则
    $smarty->assign('rules', $rules);
    //数据集
    $smarty->assign('row',$row);
    //表单提交地址
    $smarty->assign('action',$this->_url('saveDemo'));
    //sontpl
    $smarty->assign('sonTpl','Test/MainForm.js');
    //通用模版
    $smarty->display('test/Dialog.tpl');
  }

  function actionShowPdf(){
    $smarty = &$this->_getView();
    $smarty->display('test/showPdf.tpl');
  }

  function actionCreatePdf(){
    include_once ('/vendor/autoload.php');
    $dompdf = new \Dompdf\Dompdf();

    $html = '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <body style="font-family:simsun">
              <h1>欢迎来到PHP中文网</h1>

              <table class="table table-bordered">

                  <tr>

                      <th colspan="2">信息表</th>

                  </tr>

                  <tr>

                      <th>名称</th>

                      <td>'.$_POST['name'].'</td>

                  </tr>

                  <tr>

                      <th>Email</th>

                      <td>'.$_POST['email'].'</td>

                  </tr>

                  <tr>

                      <th>网址</th>

                      <td>'.$_POST['url'].'</td>

                  </tr>

                  <tr>

                      <th>内容</th>

                      <td>'.nl2br($_POST['say']).'</td>

                  </tr>

                  <tr>

                      <th>图片</th>

                      <td>
                        <img src="http://pic1.nipic.com/2008-08-14/2008814183939909_2.jpg"/>
                        <img src="Resource/Image/LoginNew/yiqi.png" />
                      </td>

                  </tr>
              </table>

            </body>';

      $url = __DIR__ . '/../../Template/IndexVue/Test/showPdf1.html';
      $html=file_get_contents($url);

      $dompdf->loadHtml($html);

      // $dompdf->setPaper('A4');

      $dompdf->render(); /* 将HTML呈现为PDF*/

      $dompdf->stream(time('YmdHis').'.pdf'); /*将生成的PDF输出到浏览器 */
  }

  function actionTest() {
    $smarty = &$this->_getView();
    $smarty->display('test/test_min.tpl');
  }


}

?>