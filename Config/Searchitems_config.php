<?php
  $search_items_config = array(
            'key'         =>array('type'=>'comp-text','name'=>'key','title'=>'关键字','clearable'=>true),
            'compName'    =>array('type'=>'comp-text','name'=>'compName','title'=>'客户名称','clearable'=>true),
            'userName'    =>array('type'=>'comp-text','name'=>'userName','title'=>'用户名','clearable'=>true),
            'name'        =>array('type'=>'comp-text','name'=>'name','title'=>'姓名','clearable'=>true),
            'proName'     =>array('type'=>'comp-text','name'=>'proName','title'=>'产品名称','clearable'=>true),
            'proCode'     =>array('type'=>'comp-text','name'=>'proCode','title'=>'产品编码','clearable'=>true),
            'color'       =>array('type'=>'comp-text','name'=>'color','title'=>'颜色','clearable'=>true),
            'pihao'       =>array('type'=>'comp-text','name'=>'pihao','title'=>'批号','clearable'=>true),
            'jianhao'     =>array('type'=>'comp-text','name'=>'jianhao','title'=>'件号','clearable'=>true),
            'phone'       =>array('type'=>'comp-text','name'=>'phone','title'=>'手机号','clearable'=>true),
            'orderCode'   =>array('type'=>'comp-text','name'=>'orderCode','title'=>'订单号','clearable'=>true),
            'clientOrder' =>array('type'=>'comp-text','name'=>'clientOrder','title'=>'客户合同号','clearable'=>true),
            'fapiaoCode'  =>array('type'=>'comp-text','name'=>'fapiaoCode','title'=>'发票号','clearable'=>true),
            'fapiaoType'  =>array('type'=>'comp-text','name'=>'fapiaoType','title'=>'发票类型','clearable'=>true),
            'reason'      =>array('type'=>'comp-text','name'=>'reason','title'=>'原因','clearable'=>true),
            'itemName'    =>array('type'=>'comp-text','name'=>'itemName','title'=>'科目名称','clearable'=>true),
            'primaryKey'  =>array('type'=>'comp-text','name'=>'primaryKey','title'=>'主键值','clearable'=>true),
            'logcontent'  =>array('type'=>'comp-text','name'=>'logcontent','title'=>'日志内容','clearable'=>true),
            'planCode'    =>array('type'=>'comp-text','name'=>'planCode','title'=>'计划单号','clearable'=>true),
            'traderId' =>array(
                'type'       =>'comp-select',
                'name'       =>'traderId',
                'title'      =>'选择业务员',
                'clearable'  =>true,
                'funcName'   =>'Model_Jichu_Employ@getSelect',
                'filterable' =>true,
                'multiple'   =>true
            ),//filterable表示是否支持搜索，效果select2，funcName表示数据方法来源，return的data需要有text和value数据,multiple表示多选
            'dateFrom' =>array('type'=>'comp-calendar','name'=>'dateFrom','title'=>'起始日期','clearable'=>true),
            'dateTo'   =>array('type'=>'comp-calendar','name'=>'dateTo','title'=>'截至日期','clearable'=>true),
            'dateRange'=>array('type'=>'comp-calendar','name'=>'dateRange','title'=>'选择日期','clearable'=>true,'ctype'=>'daterange'),
            'isWaixiao'  =>array('type'=>'comp-select','name'=>'isWaixiao','title'=>'内/外销','options'=>array(
                array('text'=>'全部','value'=>''),
                array('text'=>'内销','value'=>'内销'),
                array('text'=>'外销','value'=>'外销'),
            )),
            'isOver'  =>array('type'=>'comp-select','name'=>'isOver','title'=>'是否完成','options'=>array(
                array('text'=>'全部','value'=>''),
                array('text'=>'已完成','value'=>'1'),
                array('text'=>'未完成','value'=>'0'),
            )),
            'haveKc'  =>array('type'=>'comp-select','name'=>'haveKc','title'=>'是否有库存','options'=>array(
                array('text'=>'全部','value'=>''),
                array('text'=>'仅看有库存','value'=>'kucun'),
                array('text'=>'仅看本期入库','value'=>'ruku'),
                array('text'=>'仅看本期出库','value'=>'chuku'),
            )),
            'isShenhe'  =>array('type'=>'comp-select','name'=>'isShenhe','title'=>'是否审核','options'=>array(
                array('text'=>'全部','value'=>''),
                array('text'=>'未审核','value'=>'NULL'),
                array('text'=>'通过','value'=>'yes'),
                array('text'=>'不通过','value'=>'no'),
            )),
            'isFire'  =>array('type'=>'comp-select','name'=>'isFire','title'=>'是否离职','options'=>array(
                array('text'=>'全部','value'=>''),
                array('text'=>'已离职','value'=>'1'),
                array('text'=>'未离职','value'=>'0'),
            )),
            'isChuku'  =>array('type'=>'comp-select','name'=>'isChuku','title'=>'是否已出库','options'=>array(
                array('text'=>'全部','value'=>''),
                array('text'=>'已出库','value'=>'yes'),
                array('text'=>'未出库','value'=>'no'),
            )),
            'productId'=>array(
                'type'=>'comp-popup-select',
                'name'=>'productId',
                'title'=>'选择产品',
                'clearable'=>true,
                'action'=>'?controller=Jichu_Product&action=ListPro',
                'rowKey'=>'id',
                'displayKey'=>'proName',
                'displayText'=>''
            ),
            'materialId'=>array(
                'type'=>'comp-popup-select',
                'name'=>'materialId',
                'title'=>'选择物料',
                'clearable'=>true,
                'action'=>'?controller=Jichu_Material&action=ListPro',
                'rowKey'=>'id',
                'displayKey'=>'proName',
                'displayText'=>''
            ),
            'depId' =>array(
                'type'       =>'comp-select',
                'name'       =>'depId',
                'title'      =>'选择部门',
                'clearable'  =>true,
                'funcName'   =>'Model_Jichu_Department@getDepartment',
                'filterable' =>true,
                // 'multiple'   =>true
            ),
            'clientId' =>array(
                'type'       =>'comp-select',
                'name'       =>'clientId',
                'title'      =>'选择客户',
                'clearable'  =>true,
                'funcName'   =>'Model_Jichu_Client@getOptions',
                'filterable' =>true,
                'multiple'   =>true
            ),
            'bankId' =>array(
                'type'       =>'comp-select',
                'name'       =>'bankId',
                'title'      =>'银行账户',
                'clearable'  =>true,
                'funcName'   =>'Model_Jichu_Bank@getOptions',
                'filterable' =>true,
                'multiple'   =>true
            ),
            'createrId' =>array(
                'type'       =>'comp-select',
                'name'       =>'createrId',
                'title'      =>'制单人',
                'clearable'  =>true,
                'funcName'   =>'Model_Jichu_Employ@getEmploy',
                'filterable' =>true,
                'multiple'   =>true
            ),
            'procedureId' =>array(
                'type'       =>'comp-select',
                'name'       =>'procedureId',
                'title'      =>'工序',
                'clearable'  =>true,
                'funcName'   =>'Model_Jichu_Procedure@getOptions',
                'filterable' =>true,
                'multiple'   =>true
            ),
            'procedureIdKind' =>array(
                'type'       =>'comp-select',
                'name'       =>'procedureIdKind',
                'title'      =>'工序',
                'clearable'  =>true,
                'funcName'   =>'Model_Jichu_Procedure@getProcedureKind',
                'filterable' =>true,
                'multiple'   =>true
            ),
            'employId' =>array(
                'type'       =>'comp-select',
                'name'       =>'employId',
                'title'      =>'选择员工',
                'clearable'  =>true,
                'funcName'   =>'Model_Jichu_Employ@getEmploy',
                'filterable' =>true,
                'multiple'   =>true
            ),
            'supplierId' =>array(
                'type'       =>'comp-select',
                'name'       =>'supplierId',
                'title'      =>'选择供应商',
                'clearable'  =>true,
                'funcName'   =>'Model_Jichu_Supplier@getSuppliers',
                'filterable' =>true,
                'multiple'   =>true
            ),
            'dateYear' =>array(
                'type'     =>'comp-select',
                'name'     =>'dateYear',
                'title'    =>'年',
                'funcName' =>'TMIS_Common@getYear',
                'clearable'  =>false,
            ),
  );