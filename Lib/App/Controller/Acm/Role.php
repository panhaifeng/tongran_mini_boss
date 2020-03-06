<?php
FLEA::loadClass('TMIS_Controller');
class Controller_Acm_Role extends TMIS_Controller {
	var $_modelRole;
	var $funcId = 22;
	function __construct() {
		$this->_modelRole = FLEA::getSingleton('Model_Acm_Role');
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
				'name'      =>'roleName',
				'title'     =>'角色名称',
				'clearable' =>true,
				'value'     =>'',
		    )
        );

        $params['rules'] = array(
			'roleName'=>array(
		        array(
		        	'required'=>true,
		        	'message'=>'角色名称必须',
		          // 'trigger'=>'blur'
		        )
		    ),
		);

		return $params;
	}


	function actionRight() {
		$this->authCheck('99-2');

		$searchItems = array(
			'key'        =>''
        );
        $smarty = & $this->_getView();
        $arrFieldInfo = array(
			"id"       =>array('text'=>"编号",'width'=>''),
			"roleName" =>array('text'=>"角色名",'width'=>''),
        );

        $smarty->assign('title', '角色列表');
        $smarty->assign('arr_field_info', $arrFieldInfo);
        $smarty->assign('action', $this->_url('GetRows'));
        $smarty->assign('searchItems', $searchItems);
        $smarty->assign('colsForKey', array(
				array('text' =>'关键字','col'=>'key')
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
		));

        // $rightMenu = array(
        //     // array('text'=>"新增角色",'name'=>'btnAddNewRole','url'=>$this->_url('Add'))
        // );
        // $smarty->assign('menuRightTop', $rightMenu);
        $smarty->assign('addUrl',$this->_url('Add'));

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

        $arr = $_POST;

        $condition=array();
        if($arr['key']!='') {
            $condition[] = array('roleName',"%{$arr['key']}%",'like');
        }

        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = & new TMIS_Pager($this->_modelRole,$condition,'id asc',$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();
        // dump($rowset);exit;
        /*foreach($rowset as & $v){
            #code
        }*/

        $ret = array(
          'total'   =>$pager->totalCount,
          'columns' =>array(),
          'rows'    =>$rowset,
        );
        echo json_encode($ret);exit;
    }


	function actionAdd() {
		$this->authCheck('99-2');
		$this->_edit(array('roleName'=>''));
	}
	function actionEdit() {
		$this->authCheck('99-2');
		$aRole = $this->_modelRole->find($_GET['id']);
		$this->_edit($aRole);
	}

	function actionSave() {
		$this->authCheck('99-2');
		$requestParam = file_get_contents('php://input');
		$_POST = json_decode($requestParam,true);

		//验证数据
	    FLEA::loadClass('TMIS_Input');
	    $_POST = TMIS_Input::check_input($_POST);

		if(empty($_POST['id'])) {
			$sql = "SELECT count(*) as cnt FROM `acm_roledb` where roleName='".$_POST['roleName']."'";
			$rr = mysql_fetch_assoc(mysql_query($sql));
			//dump($rr);exit;
			if($rr['cnt']>0) {
				$ret = array('msg'=>'角色名称重复','success'=>false);
            	echo json_encode($ret);exit;
			}
		} else {
		//修改时判断是否重复
			$str1="SELECT count(*) as cnt FROM `acm_roledb` where id!=".$_POST['id']." and (roleName='".$_POST['roleName']."')";
			$ret=mysql_fetch_assoc(mysql_query($str1));
			if($ret['cnt']>0) {
				$ret = array('msg'=>'角色名称重复','success'=>false);
            	echo json_encode($ret);exit;
			}
		}

		$this->_modelRole->save($_POST);

		$ret = array(
			'success' =>true,
			'msg'     =>'用户操作成功',
	    );
	    echo json_encode($ret);exit;
	}
	function actionRemove() {
		$this->authCheck('99-2');
		$this->_modelRole->removeByPkv($_GET[id]);
		redirect($this->_url('right'));
	}

	function actionRemoveAjax() {
		$requestParam = file_get_contents('php://input');
		$_POST = json_decode($requestParam,true);
		$id = intval($_POST['row']['id']);
		$res = $this->_modelRole->removeByPkv($id);

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

	function _edit($Arr = array()) {
		$formParams = $this->buildHtml();
		// dump($formParams);die;

		$smarty = & $this->_getView();

	    $smarty->assign('formItems',$formParams['formItems']);
	    $smarty->assign('rules',$formParams['rules']);
	    $smarty->assign('title','基本信息');
	    $smarty->assign('row',$Arr);
	    $smarty->assign('action',$this->_url('Save'));
	    $smarty->display('MainForm.tpl');
	}

	function actionAssignFunc() {
		//为角色分配权限
		////$this->authCheck($this->funcId);
		$aRole = $this->_modelRole->find($_GET[id]);
		$smarty = $this->_getView();
		$smarty->assign("aRole",$aRole);
		$smarty->display("Acm/AssignFunc.tpl");
	}

	//分配权限时，点击某个role,需要获得该role所有的funcId
	function actionGetJsonRole(){
		$row = $this->_modelRole->find(array('id'=>$_GET['roleId']));
		//每个节点的path
		$mFunc = FLEA::getSingleton('Model_Acm_Func');
		if($row['funcs']) foreach($row['funcs']  as & $v) {
			$path = $mFunc->getPath($v);
			$v['path'] = array_col_values($path,'id');
		}
		//dump($row);exit;
		echo json_encode($row['funcs']);
	}
	/**
	*从关联表中删除指定的关联纪录
	*/
	function actionRemoveAssign() {
		#取得关联对象
		$link = $this->_modelRole->getLink('funcs');
		//dump($link); exit;
		#生成sql语句
		$sql = "delete from {$link->joinTable}
			where {$link->foreignKey}='{$_GET[$link->foreignKey]}'
			and {$link->assocForeignKey} = '{$_GET[$link->assocForeignKey]}'";
		//echo $sql;exit;
		if (!$link->dbo->execute($sql)) {
			js_alert('','',$this->_getBack());
		}
		redirect($this->_url('assignfunc',array('id'=>$_GET[roleId])));
		#执行sql语句
	}

	/**
	*保存分配结果
	*/
	function actionSaveAssign() {
		//check the existence of the $_POST[funcId];
		$modelFunc = FLEA::getSingleton('Model_Acm_Func');
		$aFunc = $modelFunc->find($_POST[funcId]);

		if (!$aFunc) {
			js_alert('权限不存在!请核实后重新输入!',
				'',
				$this->_url('assignfunc',array('id'=>$_POST[roleId]))
			);
		}

		//if the parentId have been assigned, then cancel
		if ($modelFunc->isAssigned($_POST[funcId],$_POST[roleId])) {
			js_alert('父权限已经被分配过!您不需要再进行分配!',
				'',
				$this->_url('assignfunc',array('id'=>$_POST[roleId]))
			);
		}

		//begin assign 1,get the funcs that were assigned befor ,then merge with new func
		$aRole = $this->_modelRole->find($_POST[roleId]);

		$arr = count($aRole[funcs])>0 ? array_col_values($aRole[funcs],'id') : array();
		$arr = array_unique(array_merge($arr,array($_POST[funcId])));
		//begin save
		$link = & $this->_modelRole->getLink('funcs');
		$link->saveAssocData($arr,$_POST[roleId]);

		/*$str = "insert into  {$link->joinTable} (
			{$link->foreignKey},{$link->assocForeignKey}
			) values(
			'$_POST[roleId]','$_POST[funcId]'
		)";
		$this->_modelRole->execute($str);
		*/
		redirect($this->_url('assignfunc',array('id'=>$_POST[roleId])));
	}
}
?>