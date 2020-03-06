<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  Author :wuyou
*  FName  :Client.php
*  Time   :2017/07/27 17:02:55
*  Remark :客户档案管理
\*********************************************************************/
FLEA::loadClass('TMIS_Controller');
class Controller_Jichu_Client extends TMIS_Controller {
    var $_modelExample;
    var $_modelEmploy;
    var $title = "客户档案";
    var $funcId = '90-5';

    function __construct() {
        $this->_modelExample = FLEA::getSingleton('Model_Jichu_Client');
        $this->_modelEmploy = FLEA::getSingleton('Model_Jichu_Employ');

        $this->cacheKey = 'sync.data.hs.client.update';
        $this->cacheProgress = 'sync.data.hs.client.update.progress';
        $this->cacheTime = 900;
    }

    /**
     * 角色html配置form
     * Time：2018/12/13 15:49:40
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    public function buildHtml() {
        $params = array();
        $params['formItems'] = array(
            array(
                'type'      =>'comp-text',
                'name'      =>'compCode',
                'title'     =>'客户代码',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'compName',
                'title'     =>'客户名称',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-select',
                'name'      =>'traderId',
                'title'     =>'公司员工',
                'filterable'=>true,
                'clearable' =>true,
                'value'     =>'',
                'options'   =>$this->_modelEmploy->getSelect(true),
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'people',
                'title'     =>'客户联系人',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'mobile',
                'title'     =>'手机',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'tel',
                'title'     =>'电话',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'address',
                'title'     =>'地址',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'zipCode',
                'title'     =>'邮编',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'fax',
                'title'     =>'传真',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'email',
                'title'     =>'E-mail',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'bank',
                'title'     =>'开户银行',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'accountId',
                'title'     =>'账号',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'taxId',
                'title'     =>'税号',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'qianzheng',
                'title'     =>'委托人签证',
                'clearable' =>true,
                'value'     =>'',
                'append'    =>'主管单位委托人签证'
            ),
            array(
                'type'      =>'comp-textarea',
                'name'      =>'memo',
                'title'     =>'备注',
                'clearable' =>true,
                'value'     =>'',
            ),
        );

        $params['rules'] = array(
            'compName'=>array(
                array(
                    'required'=>true,
                    'message'=>'客户名称必须',
                )
            ),
            'traderId'=>array(
                array(
                    'required'=>true,
                    'message'=>'公司员工必须',
                )
            ),
        );

        return $params;
    }

    /**
     * @desc ：客户信息编辑
     * Time：2017/07/31 15:26:15
     * @author Wuyou
    */
    function actionEdit(){
        $this->authCheck($this->funcId);
        $row = $this->_modelExample->find($_GET['id']);
        $row['dateEnter']=$row['dateEnter']=='0000-00-00'?'':$row['dateEnter'];
        $row['dateLeave']=$row['dateLeave']=='0000-00-00'?'':$row['dateLeave'];

        $this->_edit($row);
    }

    /**
     * @desc ：新增客户档案
     * Time：2017/07/31 15:26:32
     * @author Wuyou
    */
    function actionAdd(){
        $this->authCheck($this->funcId);
        $this->_edit();
    }

    function _edit($Arr){
        $formParams = $this->buildHtml();

        if(!$Arr){
            foreach ($formParams['formItems'] as $key => $v) {
                $Arr[$v['name']] = $v['value'].'';
            }
        }

        // dump($Arr);exit;

        $smarty = & $this->_getView();

        $smarty->assign('formItems',$formParams['formItems']);
        $smarty->assign('rules',$formParams['rules']);
        $smarty->assign('title','基本信息');
        $smarty->assign('row',$Arr);
        $smarty->assign('action',$this->_url('Save'));
        $smarty->display('MainForm.tpl');
    }

    /**
     * @desc ：部门档案查询
     * Time：2017/07/31 15:33:05
     * @author li
    */
    function actionRight() {
        $this->authCheck($this->funcId);

        $searchItems = array(
            'compName'   =>'',
            'traderId'   =>'',
        );
        $smarty = & $this->_getView();
        $arrFieldInfo = array(
            'compCode'        =>array('text'=>'客户代码','width'=>''),
            'compName'        =>array('text'=>'客户名称','width'=>'240','forKeySearch'=>true,),
            // 'compFullName' =>array('text'=>'客户名称(全)','width'=>''),
            'employName'      =>array('text'=>'业务员','width'=>''),
            'people'        =>array('text'=>'联系人','width'=>''),
            'mobile'          =>array('text'=>'手机','width'=>''),
            'tel'             =>array('text'=>'电话','width'=>''),
            /*'image'         =>array(
            'text'            =>'名片',
            'width'           =>'',
            'isHtml'          =>'component',
            'componentType'   =>'tip-card-image'
            ),*/
            'email'           =>array('text'=>'e-mail','width'=>''),
            'address'         =>array('text'=>'地址','width'=>''),
            'qianzheng'       =>array('text'=>'主管单位委托人签证','width'=>''),
            'memo'            =>array('text'=>'备注','width'=>''),
        );

        $smarty->assign('title', '客户列表');
        $smarty->assign('arr_field_info', $arrFieldInfo);
        $smarty->assign('action', $this->_url('GetRows'));
        $smarty->assign('searchItems', $searchItems);
        // $smarty->assign('colsForKey', array(
        //         array('text' =>'关键字','col'=>'key')
        // ));

        $smarty->assign('editButtons',array(
            array('text'=>'编辑','type'=>'redirect','icon'=>'el-icon-edit','options'=>array(
                //点击后跳转的地址
                'url'            =>$this->_url('Edit').'&id={id}',
                'disabledColumn' =>'__disabledEdit',
            )),
            array('text'=>'删除','type'=>'remove','options'=>array(
                'url'            =>$this->_url('RemoveAjax'),
                'disabledColumn' =>'__disabledRemove',
            )),
        ));

        $rightMenu = array(
            // array('text'=>"更新环思客户数据",'name'=>'btnUpdateClients')
        );
        $smarty->assign('menuRightTop', $rightMenu);

        $smarty->assign('addUrl',$this->_url('Add'));
        $smarty->assign('sonTpl','Jichu/ClientTable.js');
        $smarty->display('TableList.tpl');
    }

    /**
     * 获取计划任务的数据
     * @author li
    */
    public function actionGetRows()
    {
        $auth = $this->authCheck(0 ,true);

        if(!$auth){
            $ret = array(
              'total'   =>0,
              'columns' =>array(),
              'rows'    =>array(),
            );
            echo json_encode($ret);exit;
        }

        $requestParam = file_get_contents('php://input');
        $_POST = json_decode($requestParam,true);

        $pagesize    = $_POST['pagesize'];
        $currentPage = $_POST['currentPage'];

        //处理搜搜
        $keyField    = isset($_POST['colForKey']) ? $_POST['colForKey'] : 'key';
        $post[$keyField] = $_POST['key'];
        if($keyField!='key') $_POST['key'] = '';

        $arr = $_POST;

        $condition = array();

        //得到当前用户的关联业务员
        $mUser = FLEA::getSingleton('Model_Acm_User');
        $traderArr = $mUser->getTraderIdByUser($_SESSION['USERID']);

        if($traderArr['_ALL_'] == false){
            $condition['in()'] = array('traderId'=>$traderArr['Traders']);
        }

        if($arr['compName']!='') {
            // $condition[] = array('compCode',"%{$arr['key']}%",'like','or');
            $condition[] = array('compName',"%{$arr['compName']}%",'like');
        }

        if($arr['traderId']!=0) {
            $condition[] = array('traderId',"{$arr['traderId']}",'=');
        }

        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = & new TMIS_Pager($this->_modelExample,$condition,'id desc',$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();
        // dump($rowset);exit;
        foreach($rowset as & $v){
            $v['compDate'] = $v['compDate']=='0000-00-00' ? '' : $v['compDate'];

            //查找业务员姓名
            if($v['traderId']){
                $traderInfo = $this->_modelEmploy->find($v['traderId']);
                $v['employName'] = $traderInfo['employName'];
            }

            //名片信息
            if($v['imageId']){
                $v['imageSrc'] = $this->getImagePath($v['imageId']);
            }
        }

        $ret = array(
          'total'   =>$pager->totalCount,
          'columns' =>array(),
          'rows'    =>$rowset,
        );
        echo json_encode($ret);exit;
    }


    /**
     * @desc ：客户档案保存
     * Time：2017/07/31 15:29:19
     * @author Wuyou
    */
    function actionSave() {
        $auth = $this->authCheck($this->funcId ,true);

        if(!$auth){
            $ret = array('msg'=>'没有登陆或编辑权限','success'=>false);
            echo json_encode($ret);exit;
        }
        //post参数
        $post = $this->axiosPost();

        if(empty($post['id'])) {
            $condition = array();
            $condition[] = array('compName',$post['compName'],'=');
            $count = $this->_modelExample->findCount($condition);

            if($count > 0) {
                $ret = array('msg'=>'公司名称重复','success'=>false);
                echo json_encode($ret);exit;
            }
        } else {
            //修改时判断是否重复
            $condition = array();
            $condition[] = array('compName',$post['compName'],'=');
            $condition[] = array('id',$post['id'],'<>');
            $count = $this->_modelExample->findCount($condition);

            if($count > 0) {
                $ret = array('msg'=>'公司名称重复','success'=>false);
                echo json_encode($ret);exit;
            }
        }

        //数据保存
        $result = $this->_modelExample->save($post);
        if($result){
            $ret = array(
                'success' =>true,
                'msg'     =>'操作成功',
            );
        }else{
            $ret = array(
                'success' =>false,
                'msg'     =>'操作失败',
            );
        }

        echo json_encode($ret);exit;

    }

    /**
     * @desc ：客户档案删除 存在报价和订单的客户不允许删除
     * Time：2017/07/31 15:30:14
     * @author Wuyou
    */
    function actionRemove() {
        // 删除验证
        if($_GET['id']!="") {
            $sql="SELECT count(*) as cnt FROM `trade_order` where clientId=".$_GET['id'];
            $re=$this->_modelExample->findBySql($sql);
            //dump($re);exit;
            if($re[0]['cnt']>0) {
                js_alert('此客户有订单记录存在，不允许删除',null,$this->_url('Right'));
            }
        }
        parent::actionRemove();
    }

    /**
     * @desc ：员工档案删除 验证是否已经被使用
     * Time：2017/07/31 15:49:06
     * @author lwj
    */
    function actionRemoveAjax() {
        $post = $this->axiosPost();
        $id = intval($post['row']['id']);

        //查找是否已经被使用
        $array = array(
            'Model_Trade_Order' => 'clientId',
        );

        $isOccupy = false;

        foreach ($array as $key =>  & $v) {
            $tmpModel = FLEA::getSingleton($key);
            $condition = array();
            $condition[$v] = $id;
            $count = $tmpModel->findCount($condition);

            //如果某个表中存在，则不能删除
            if($count > 0){
                $isOccupy = true;
                $msg = $tmpModel->moduleName;
                break;
            }
        }

        if($isOccupy){
            $ret = array(
                'success' =>false,
                'msg'     =>'被'.$msg.'使用，禁止删除',
            );
            echo json_encode($ret);exit;
        }


        $res = $this->_modelExample->removeByPkv($id);

        if($res){
            $ret = array(
                'success' =>true,
                'msg'     =>'删除成功',
            );
        }else{
            $ret = array(
                'success' =>false,
                'msg'     =>'删除失败',
            );
        }
        echo json_encode($ret);exit;
    }

    /**
     * @desc ：在模式对话框中显示待选择的客户，返回某个客户的json对象
     * Time：2017/07/31 15:31:55
     * @author Wuyou
    */
    function actionPopup() {
        FLEA::loadClass('TMIS_Pager');
        $arr = TMIS_Pager::getParamArray(array(
            'clientCode'=>'',
            'clientName' => '',
        ));
        //业务员只能看自己的客户
        $mUser = & FLEA::getSingleton('Model_Acm_User');
        //如果不能看所有订单，得到当前用户的关联业务员
        $traderArr = $mUser->getTraderIdByUser($_SESSION['USERID']);

        $condition=array();

        if($traderArr['_ALL_'] == false){
            $condition['in()'] = array('traderId'=>$traderArr['Traders']);
        }

        if($arr['clientCode']!='') {
            $condition[] = array('compCode',"%{$arr['clientCode']}%",'like');
        }

        if($arr['clientName']!='') {
            $condition[] = array('compName',"%{$arr['clientName']}%",'like');
        }

        // dump($condition);die;
        $pager = new TMIS_Pager($this->_modelExample,$condition,'id asc'); //id asc 不能改变 改变则有问题 记录
        $rowset =$pager->findAll();

        if(count($rowset)>0) foreach($rowset as & $v){
            $str="select * from jichu_employ where id='{$v['traderId']}'";
            $re=mysql_fetch_assoc(mysql_query($str));
            $v['traderName']=$re['employName'];
            if($re['isFire']==1){
                $v['fire']='是';
            }
        }
        $arr_field_info = array(
             'compCode'          =>'客户代码',
             'Trader.employName' =>'业务员',
             'address'           =>'地址',
             'people'          =>'联系人',
             'dianhua'           =>'固定电话',
             'mobile'            =>'手机',
             'email'             =>'e-mail',
             'zipCode'           =>'邮编',
             'payAccount'        =>'到付帐号',
             'billingData'       =>'开票资料',
             'memo'              =>'备注',
        );

        $smarty = & $this->_getView();
        $smarty->assign('title', '选择客户');
        $pk = $this->_modelExample->primaryKey;
        $smarty->assign('pk', $pk);
        $smarty->assign('add_display','none');
        $smarty->assign('arr_field_info',$arr_field_info);
        $smarty->assign('arr_field_value',$rowset);
        $smarty->assign('add_url',$this->_url('add',array('fromAction'=>$_GET['action'])));
        $smarty->assign('s',$arr);
        $smarty->assign('page_info',$pager->getNavBar($this->_url($_GET['action'],$arr)));
        $smarty->assign('arr_condition',$arr);
        $smarty->assign('clean',true);
        $smarty-> display('Popup/CommonNew.tpl');
    }

    function actionGetJsonByKey() {
        $sql = "select * from jichu_client where (
            compName like '%{$_GET['code']}%' or zhujiCode like '%{$_GET['code']}%' or compCode like '%{$_GET['code']}%'
        )";
        $arr = $this->_modelExample->findBySql($sql);
        echo json_encode($arr);exit;
    }
    //根据传入的id获得具体信息,订单录入时根据客户定位业务员时用到
    function actionGetJsonById() {
        $sql = "select * from jichu_client where id='{$_GET['id']}'";
        $arr = $this->_modelExample->findBySql($sql);
        echo json_encode($arr[0]);exit;
    }
    //根据业务员查找客户
    function actionGetJsonByTraderId() {
        $sql = "select * from jichu_client where 1";
        if($_GET['traderId']!='')$sql.=" and traderId='{$_GET['traderId']}'";
        $mUser = & FLEA::getSingleton('Model_Acm_User');
        // $canSeeAllOrder = $mUser->canSeeAllOrder($_SESSION['USERID']);
        /*if(!$canSeeAllOrder) {
            //如果不能看所有订单，得到当前用户的关联业务员
            $traderId = $mUser->getTraderIdByUser($_SESSION['USERID']);
            if($traderId)$sql .= " and traderId in ({$traderId})";
        }*/

        $traderArr = $mUser->getTraderIdByUser($_SESSION['USERID']);
        if($traderArr['_ALL_'] == false){
            $traderId = join(',',$traderArr['Traders']);
            !$traderId && $traderId = '-1';
            $sql .= " and traderId in ({$traderId})";
        }

        // $sql.=" order by convert(trim(compName) USING gbk)";
        // $arr = $this->_modelExample->findBySql($sql);
        $sql.=" order by ";
        $kg = & FLEA::getAppInf('khqcxs');
        if($kg)$sql.=" letters";
        else $sql.=" compCode";

        $arr = $this->_modelExample->findBySql($sql);

        //生成下拉框
        $ret=$this->_modelExample->options($arr);
        echo json_encode($ret);exit;
    }

    //开票抬头设置
    function actionSetTaitou(){
        $rows=$this->_modelTaitou->findAll(array('clientId'=>$_GET['clientId']));
        //dump($rows);exit;
        $smarty = & $this->_getView();
        $smarty->assign('title', '开票抬头设置');
        $smarty->assign("aRow", $rows);
        $smarty->display('Jichu/ClientTaitou.tpl');
    }

    //保存抬头设置
    function actionSaveTaitou(){
        //dump($_POST);exit;
        $rows=array(
            'taitou'=>$_POST['taitou'],
            'clientId'=>$_POST['clientId'],
            'memo'=>$_POST['memo']
        );
        if($rows) $this->_modelTaitou->save($rows);
        // js_alert(null,'window.parent.parent.showMsg("设置成功");window.parent.location.href=window.parent.location.href');
        js_alert('保存成功！','',$this->_url('SetTaitou',array('clientId'=>$_POST['clientId'])));
    }

    //删除抬头设置
    function actionDelTaitouAjax(){
        if($_GET['id']!='') {
            if($this->_modelTaitou->removeByPkv($_GET['id'])) {
                echo json_encode(array('success'=>true));
                exit;
            }
        }
    }
    //新增会员提醒
    function actionNewClient(){
        $this->authCheck('90-1');
        FLEA::loadClass('TMIS_Pager');
        $today = date('w')>0 ? date('w')-1 : 6;//星期几：0（星期7）~ 6（星期六）
        $dateFrom = date('Y-m-d',mktime(0,0,0,date('m'),date('d')-$today,date('Y')));
        $dateTo = date('Y-m-d H:i:s');
        $sql="SELECT * from jichu_client where compDate>='{$dateFrom}' and compDate<= '{$dateTo}'";
        $pager = &new TMIS_Pager($sql);
        $rowset = $pager->findAll();
        foreach ($rowset as $key =>& $v) {
            $v['sex']=$v['sex']==0?'女':'男';
        }
        $smarty = & $this->_getView();
        $arrField = array(
            'compCode'=>'公司编码',
            'compName'=>'客户名称',
            'com_type'=>'客户类型',
            'sex'=>'性别',
            // 'edu'=>'预存款',
            'compDate'=>'注册时间',
            'mobile'=>'手机',
            'email'=>'Email',
            'memo'=>'备注',
        );
        $smarty->assign('arr_field_info', $arrField);
        $smarty->assign('arr_condition', $arr);
        $smarty->assign('add_display', 'none');
        $smarty->assign('arr_field_value', $rowset);
        $smarty->assign("page_info", $pager->getNavBar($this->_url($_GET['action'])));
        $smarty->display('TblList.tpl');
    }

    function actionSaveclientExport(){
        set_time_limit(0);
        $temp=array();$arr=array();
        foreach ($_FILES as $k=> &$v){
            for($i=0;$i<count($v['name']);$i++){
                foreach ($v as $key=> &$value){
                    $temp[$key]=$value;
                }
                $arr[][$k]=$temp;
            }
        }
        if($arr['0']['clientExport']['name']==''){
            js_alert("没有选择文件禁止保存",'window.history.go(-1)');
        }
        $path= 'upload/jichu/';
        foreach ($arr as &$v){
            $temp1='';
            foreach ($v as $kk=>&$vv){
                $temp1=$kk;
            }
            $dizhi['path']=$v[$kk];
            $filePath[$kk][]= $this->_importAttac($dizhi,$path);
        }
        $filePath=$filePath['clientExport']['0']['filePath'];
        // dump($filePath);die;
        $arr = $this->_readExcel($filePath,0);
        // dump($arr);die;
        foreach ($arr as $key => &$v) {
            if ($key <1) continue;
            // if(!$v[2]) continue;
            $_client = $this->_modelExample->find(array('compCode'=>trim((string)$v[3])));
            if($_client['id']) continue;
            $_employ = $this->_modelEmploy->find(array('employName'=>trim((string)$v[1])));
            $data=array(
                'id'=>'',
                'compName'=>trim((string)$v[2]),
                'compCode'=>trim((string)$v[3]),
                'traderId'=>trim((string)$_employ['id']),
                );
            $result=$this->_modelExample->save($data);
        }
        echo json_encode(array('success'=>true,'msg'=>'导入成功！'));exit;
    }



    /**
     * 同步环思基础档案
     * Time：2019/01/22 10:15:42
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function actionBeforesynchrodata(){
        set_time_limit(10);
        $auth = $this->authCheck($this->funcId ,true);
        if(!$auth){
            $ret = array('msg'=>'登录权限过期','success'=>false,'status'=>'false');
            echo json_encode($ret);exit;
        }

        //先判断是否正在执行同步数据方法
        $status = FLEA::getCache($this->cacheKey ,$this->cacheTime);
        if($status['update'] == 'true'){
            $ret = array('msg'=>'正在同步中......','success'=>false,'status'=>'true');
            echo json_encode($ret);exit;
        }

        //查找环思有多少条基础档案
        try{
            $event = FLEA::getSingleton('Controller_Event_Hs_Client');
            $count = $event->findCount();
        }catch(Exception $e){
            $count = '连接环思异常';
        }

        $ret = array('msg'=>$count,'success'=>(intval($count) > 0),'status'=>'false');
        echo json_encode($ret);exit;
    }

    /**
     * 停止同步
     * Time：2019/01/22 10:15:42
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function actionSyncupdateStop(){
        $auth = $this->authCheck($this->funcId ,true);
        if(!$auth){
            $ret = array('msg'=>'登录权限过期','success'=>false);
            echo json_encode($ret);exit;
        }

        //停止同步

        //删除同步的标记
        $this->removeUpdateCache();

        $ret = array('msg'=>'操作成功','success'=>true);
        echo json_encode($ret);exit;
    }


    /**
     * 同步进度获取
     * Time：2019/01/22 10:15:42
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function _syncProgress($post  =array()){
        $getProgress = true;
        $getNum = 0;
        //获取当前的同步进度
        while($getProgress){
            $progress = FLEA::getCache($this->cacheProgress ,600);
            $status = FLEA::getCache($this->cacheKey ,$this->cacheTime);
            //如果相同，则继续等待获取
            if(strval($post['progress']) == strval($progress['progress']) && $progress['progress'] < 100){
                sleep(2);
            }else{
                $getProgress = false;
            }

            $getNum++;
            if($getNum > 6){
                $getProgress = false;
            }
        }

        return $ret = array('progress'=>$progress['progress']+0,'success'=>true ,$progress);
    }

    //对外输出信息
    function actionSyncprogress(){
        $auth = $this->authCheck($this->funcId ,true);
        if(!$auth){
            $ret = array('msg'=>'登录权限过期','success'=>false);
        }else{
            $ret = $this->_syncProgress($_GET);
        }

        //servlet服务数据header输出
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        echo "data: ".json_encode($ret)."\n\n";
        flush();
    }

    /**
     * 同步环思基础档案
     * Time：2019/01/22 10:15:42
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function actionSynchrodata(){
        $auth = $this->authCheck($this->funcId ,true);
        if(!$auth){
            $ret = array('msg'=>'登录权限过期','success'=>false);
            echo json_encode($ret);exit;
        }

        //全局缓存开始同步的信息
        $this->startUpdateCache();
        //把进度数据拉到0
        $status = array(
            'progress'    =>0,
            'totalCount'  =>0,
            'finishCount' =>0,
            'time'        =>time(),
        );
        // dump($status);
        FLEA::writeCache($this->cacheProgress ,$status);

        $post = $this->axiosPost();
        //开始同步
        #code
        $croService = FLEA::getSingleton('Model_Crontab');
        $croService->publish(
            array(
                'delay'       =>0,//延迟执行时间，秒
                'type'        =>'quick',//延迟执行时间，秒
                'description' =>'同步环思物料基础数据',
                'action'      =>'Controller_Event_Hs_Client@synchroData'//controller@action结构
            ),
            array(
                'cacheKey'      => $this->cacheKey,
                'cacheProgress' => $this->cacheProgress,
                'startIndex'    => $post['startIndex'],
            )
        );

        $ret = array('msg'=>'开始同步数据','success'=>true);
        echo json_encode($ret);exit;
    }

    //标记同步和时间
    function startUpdateCache(){
        //标记在同步
        $status = array(
            'update' =>'true',
            'time'   =>time(),
        );
        return FLEA::writeCache($this->cacheKey ,$status);
    }

    //标记同步和时间
    function removeUpdateCache(){
        return FLEA::purgeCache($this->cacheKey);
    }

    //标记同步和时间
    function stopUpdateCache(){
        $status = array(
            'update' =>'false',
            'stop'   =>'true',
            'time'   =>time(),
        );
        return FLEA::writeCache($this->cacheKey ,$status);
    }

    //标记同步和时间
    function actionStopUpdate(){
        $this->stopUpdateCache();
        $ret = array('msg'=>'已停止同步数据','success'=>true);
        echo json_encode($ret);exit;
    }
}
?>