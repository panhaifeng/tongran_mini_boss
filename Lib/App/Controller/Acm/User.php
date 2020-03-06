<?php
FLEA::loadClass('TMIS_Controller');
class Controller_Acm_User extends TMIS_Controller {
	var $_modelUser;
	var $funcId = 1;
	function __construct() {
		$this->_modelUser = FLEA::getSingleton('Model_Acm_User');
		$this->_modelRole = FLEA::getSingleton('Model_Acm_Role');
	}

	public function buildPwdHtml()
	{
		$params = array();
		$params['formItems'] = array(
			array(
		        'type'=>'comp-input',
		        'name'=>'passwd',
		        'inputType'=>'password',
		        'title'=>'新密码',
		        'clearable'=>true,
		        'value'=>'',
		    ),
		    array(
		        'type'=>'comp-input',
		        'name'=>'passwdConfirm',
		        'inputType'=>'password',
		        'title'=>'确认密码',
		        'clearable'=>true,
		        'value'=>'',
		    ),
        );

        $params['rules'] = array(
			'passwd'=>array(
		        array(
		        	'required'=>true,
		        	'message'=>'请输入新密码',
		          // 'trigger'=>'blur'
		        )
		    ),
		    'passwdConfirm'=>array(
		        array(
		        	'validator'=>'passwdConfirm',
		          // 'message'=>'两次密码必须一致',
		          // 'trigger'=>'blur'
		        )
		    ),
		);

		return $params;
	}

	function actionChangePwd() {
		$this->authCheck(0);
		//加载html表单配置
		$formParams = $this->buildPwdHtml();

		$userId = $_SESSION['USERID'];
		$aUser = $this->_modelUser->find($userId);
		$row['passwd'] = '';
		$row['id']=$aUser['id'];

		$smarty = & $this->_getView();

	    $smarty->assign('formItems',$formParams['formItems']);
	    $smarty->assign('rules',$formParams['rules']);
	    $smarty->assign('title','修改密码 - '.$aUser['userName']);
	    $smarty->assign('row',$row);
	    $smarty->assign('form',array('submit'=>array('text'=>'提交')));
	    $smarty->assign('action',$this->_url('SavePwd'));
	    $smarty->assign('sonTpl','Acm/ChangePwd.js');
	    $smarty->display('MainForm.tpl');
	}

	public function actionSavePwd()
	{
		$requestParam = file_get_contents('php://input');
	    $data = json_decode($requestParam,true);
	    //验证数据
	    FLEA::loadClass('TMIS_Input');
	    $data = TMIS_Input::check_input($data);

	    if(!$data['id']){
			$ret = array(
		      'success'=>false,
		      'msg'=>'身份信息丢失，修改失败',
		    );
		    echo json_encode($ret);exit;
	    }

	    if(!$data['passwd']){
			$ret = array(
		      'success'=>false,
		      'msg'=>'密码不能为空',
		    );
		    echo json_encode($ret);exit;
	    }

	    $data['passwd'] = _md5($data['passwd']);
	    $res = $this->_modelUser->update($data);
	    if($res){
		    $ret = array(
		      'success'=>true,
		      'msg'=>'密码修改成功',
		    );
		}else{
			$ret = array(
		      'success'=>false,
		      'msg'=>'修改密码失败，请重试',
		    );
		}
	    echo json_encode($ret);exit;
	}


	/**
	 * ps ：
	 * Time：2018/12/13 08:51:58
	 * @author li
	*/
	public function actionRight() {
		$this->authCheck('99-1');
		$searchItems = array(
			'key'        =>'',
            'userName'   =>'',
            'phone'   	 =>'',
        );
        $smarty = & $this->_getView();
        $arrFieldInfo = array(
			// "id"           =>array('text'=>"编号",'width'=>120),
			"userName"         =>array('text'=>"用户名",'width'=>'150'),
			"realName"         =>array('text'=>"真实姓名",'width'=>'150'),
			"phone"            =>array('text'=>"手机号",'width'=>'150'),
			"showQrCodeVerify" =>array('text'=>"是否验证身份",'width'=>'150','isHtml'=>'true'),
			"roleName"         =>array('text'=>"角色",'width'=>''),
			"tradersName"      =>array('text'=>"关联业务员",'width'=>''),
        );

        $smarty->assign('title', '用户列表');
        $smarty->assign('arr_field_info', $arrFieldInfo);
        $smarty->assign('action', $this->_url('GetRows'));
        $smarty->assign('searchItems', $searchItems);
        $smarty->assign('colsForKey', array(
				array('text' =>'关键字','col'=>'key'),
				array('text' =>'用户名','col'=>'userName'),
				array('text' =>'手机'  ,'col'=>'phone'),
        ));

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
	    	array('text'=>'扫码验证身份','type'=>'func','icon'=>'el-icon-check','options'=>array(
                'disabledColumn' =>'__disabledOver',
                'funcName'       =>"QrCodeVerify",
            )),
		));

        // $rightMenu = array(
        //     // array('text'=>"新增用户",'name'=>'btnAddNewUser','url'=>$this->_url('Add'))
        // );
        // $smarty->assign('menuRightTop', $rightMenu);
        $smarty->assign('addUrl',$this->_url('Add'));
        $smarty->assign('sonTpl',array(
            'Acm/UserList.js'
        ));
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
        $keyField    = isset($_POST['colForKey']) ? $_POST['colForKey'] : 'key';
        //处理搜搜
        $_POST[$keyField] = $_POST['key'];
        if($keyField!='key') $_POST['key'] = '';

        $arr = $_POST;
        $condition=array();
		if($arr['key'] != '') {
			$condition[] = array('realName',"%{$arr['key']}%",'like');
		}
		if($arr['userName'] != '') {
			$condition[] = array('userName',"%{$arr['userName']}%",'like');
		}
		if($arr['phone'] != '') {
			$condition[] = array('phone',"%{$arr['phone']}%",'like');
		}

        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = & new TMIS_Pager($this->_modelUser,$condition,'id asc',$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();
        // dump($rowset);exit;

        foreach($rowset as & $v){
            $v['roleName'] =join(' | ',array_col_values($v['roles'],'roleName'));
            $v['tradersName'] =join(' | ',array_col_values($v['traders'],'employName'));
			if($v['userName']=='admin') {
				$v['__disabledEdit']   = true;
				$v['__disabledRemove'] = true;
			}

			$v['showQrCodeVerify'] = $v['qrCodeVerify']==1 ? '是' : '';
        }

        $ret = array(
          'total'   =>$pager->totalCount,
          'columns' =>array(),
          'rows'    =>$rowset,
        );
        echo json_encode($ret);exit;
    }

    function actionRemoveAjax() {
		$requestParam = file_get_contents('php://input');
		$_POST = json_decode($requestParam,true);
		$id = intval($_POST['row']['id']);
		$res = $this->_modelUser->removeByPkv($id);

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


	function actionIndex() {
		$arrLeftList = array(
			"Acm_User" =>"用户管理",
			"Acm_Role" =>"角色管理",
			"Acm_Func" =>"模块定义"
		);

		$smarty = & $this->_getView();
		$smarty->assign('arr_left_list', $arrLeftList);
		$smarty->assign('title', '用户管理');
		$smarty->assign('caption', '权限管理');
		//$smarty->assign('child_caption', "应付款凭据录入");
		$smarty->assign('controller', 'Acm_User');
		$smarty->assign('action', 'right');
		$smarty->display('Welcome.tpl');
	}
	function actionAdd() {
		$this->authCheck('99-1');
		$this->_edit(array());
	}
	function actionEdit() {
		$this->authCheck('99-1');
		// dump($_GET);exit;
		$aUser = $this->_modelUser->find($_GET[id]);
		// dump($aUser);die;
		//$dbo = FLEA::getDbo(false);
		//dump($dbo->log);exit;
		$this->_edit($aUser);
	}
	function actionSave() {
		$this->authCheck('99-1');

		$requestParam = file_get_contents('php://input');
		$_POST = json_decode($requestParam,true);

		//验证数据
	    FLEA::loadClass('TMIS_Input');
	    $_POST = TMIS_Input::check_input($_POST);

		// dump($_POST);exit;
		if(empty($_POST['traders'])){
			$_POST['traders'][0] = 0;
		}
		if(empty($_POST['roles'])){
			$_POST['roles'][0] = 0;
		}

		$telVerfi = TMIS_Input::verfiMobile($_POST['phone']);
        if(!$telVerfi){
            $ret = array('msg'=>'手机号不合法','success'=>false);
            echo json_encode($ret);exit;
        }

		if(empty($_POST['id'])) {
			$sql = "SELECT count(*) as cnt FROM `acm_userdb` where userName='".$_POST['userName']."'";
			$rr = $this->_modelUser->findBySql($sql);
			if($rr[0]['cnt']>0) {
				$ret = array('success' =>false,'msg'=>'用户名重复');
			    echo json_encode($ret);exit;
			}

			$sql = "SELECT count(*) as cnt FROM `acm_userdb` where phone='".$_POST['phone']."'";
			$rr = $this->_modelUser->findBySql($sql);
			if($rr[0]['cnt']>0) {
				$ret = array(
					'success' =>false,
					'msg'     =>'手机号重复',
			    );
			    echo json_encode($ret);exit;
			}

		} else {
			//修改时判断是否重复
			$str ="SELECT count(*) as cnt FROM `acm_userdb` where id!=".$_POST['id']." and (userName='".$_POST['userName']."')";
			$ret=$this->_modelUser->findBySql($str);
			if($ret[0]['cnt']>0) {
				$ret = array(
					'success' =>false,
					'msg'     =>'用户名重复',
			    );
			    echo json_encode($ret);exit;
			}

			//修改时判断是否重复
			$str ="SELECT count(*) as cnt FROM `acm_userdb` where id!=".$_POST['id']." and (phone='".$_POST['phone']."')";
			$ret=$this->_modelUser->findBySql($str);
			if($ret[0]['cnt']>0) {
				$ret = array(
					'success' =>false,
					'msg'     =>'手机号重复',
			    );
			    echo json_encode($ret);exit;
			}
		}

		if(!$_POST['passwd'] && !$_POST['id']){
			$ret = array(
				'success' =>false,
				'msg'     =>'密码不能为空',
		    );
		    echo json_encode($ret);exit;
		}

		//如果存在密码，则加密处理
		if($_POST['passwd']){
			$_POST['passwd'] = _md5($_POST['passwd']);
		}else{
			unset($_POST['passwd']);
		}

		// dump($_POST);die;
		$this->_modelUser->save($_POST);

		$ret = array(
			'success' =>true,
			'msg'     =>'用户操作成功',
	    );
	    echo json_encode($ret);exit;
	}

	function actionRemove() {
		$this->authCheck('99-1');
		$this->_modelUser->removeByPkv($_GET['id']);
		js_alert(null,"window.parent.showMsg('成功删除')",$this->_url('right'));
	//redirect($this->_url('right'));
	}

	public function buildHtml()
	{

		$mEmploy = FLEA::getSingleton("Model_Jichu_Employ");
		$params = array();
		$params['formItems'] = array(
			array(
				'type'      =>'comp-text',
				'name'      =>'userName',
				'title'     =>'用户名',
				'clearable' =>true,
				'value'     =>'',
		    ),
		    array(
				'type'      =>'comp-text',
				'name'      =>'realName',
				'title'     =>'姓名',
				'clearable' =>true,
				'value'     =>'',
		    ),
		    array(
				'type'      =>'comp-text',
				'name'      =>'phone',
				'title'     =>'手机号',
				'clearable' =>true,
				'value'     =>'',
		    ),
			array(
				'type'      =>'comp-input',
				'name'      =>'passwd',
				'inputType' =>'password',
				'title'     =>'输入密码',
				'clearable' =>true,
				'value'     =>'',
		    ),
		    array(
				'type'      =>'comp-input',
				'name'      =>'passwdConfirm',
				'inputType' =>'password',
				'title'     =>'确认密码',
				'clearable' =>true,
				'value'     =>'',
		    ),
		    // 关联信息
		    array(
				'type'       =>'comp-select',
				'name'       =>'roles',
				'filterable' =>true,
				'multiple'   =>true,
				'title'      =>'选择角色',
				'clearable'  =>true,
				'value'      =>'',
				'options'    =>$this->_modelRole->getOptions(),
		    ),
		    array(
				'type'       =>'comp-message-alert',
				'alertType'  =>'info',
				'alertTitle'      =>'关联业务员，可以查其看对应的数据',
				// 'close-text' =>'我知道了',
				'closable' =>false,
		    ),
		    array(
				'type'       =>'comp-select',
				'name'       =>'traders',
				'filterable' =>true,
				'multiple'   =>true,
				'title'      =>'选择业务员',
				'clearable'  =>true,
				'value'      =>'',
				'options'    =>$mEmploy->getSelect(),
		    ),
        );

        $params['rules'] = array(
        	'userName'=>array(
		        array(
					'required' =>true,
					'message'  =>'用户名必须',
		        )
		    ),
		    'realName'=>array(
		        array(
					'required' =>true,
					'message'  =>'姓名必须',
		        )
		    ),
		    'phone'=>array(
		        array(
					'required' =>true,
					'message'  =>'手机号必须',
		        )
		    ),
			/*'passwd'=>array(
		        array(
					'required' =>true,
					'message'  =>'请输入新密码',
		        )
		    ),*/
		    'passwdConfirm'=>array(
		        array(
		        	'validator'=>'passwdConfirm',
		          // 'message'=>'两次密码必须一致',
		          // 'trigger'=>'blur'
		        )
		    ),
		    'roles'=>array(
		        array(
					'required' =>true,
					'message'  =>'角色必须',
					'trigger'=>'blur'
		        )
		    ),
		);

		return $params;
	}

	function _edit($Arr) {
		//查找所有角色
		$formParams = $this->buildHtml();
		if(!$Arr['id']){
			$formParams['rules']['passwd'] = array(
		        array(
					'required' =>true,
					'message'  =>'请输入新密码',
		        )
		    );
		}
		// dump($formParams);die;
		$Arr['passwd'] = '';
		$Arr['roles'] = array_col_values($Arr['roles'],'id');
		$Arr['traders'] = array_col_values($Arr['traders'],'id');
		// dump($Arr);die;

		$smarty = & $this->_getView();

	    $smarty->assign('formItems',$formParams['formItems']);
	    $smarty->assign('rules',$formParams['rules']);
	    $smarty->assign('title','用户基本信息');
	    $smarty->assign('row',$Arr);
	    $smarty->assign('action',$this->_url('Save'));
	    $smarty->assign('sonTpl','Acm/EditUser.js');
	    $smarty->display('MainForm.tpl');

	}

	//是否验证身份
	function actionQrCodeVerify(){
		$post = $this->axiosPost();
		$id = intval($post['id']);

		$row = $this->_modelUser->find($id);
		$qrCodeVerify = 1;
		if($row['qrCodeVerify'] == 1){
			$qrCodeVerify = 0;
		}

		$data = array(
			'id'           =>$id,
			'qrCodeVerify' =>$qrCodeVerify,
		);
		$res = $this->_modelUser->update($data);
		echo json_encode(array('msg'=>'操作完成','success'=>true));
	}
}
?>