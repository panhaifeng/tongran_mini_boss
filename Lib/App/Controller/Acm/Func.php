<?php
FLEA::loadClass('TMIS_Controller');
class Controller_Acm_Func extends TMIS_Controller {
    var $_modelFunc;
   // var $funcId=22;
    function Controller_Acm_Func() {
        $this->_modelFunc = FLEA::getSingleton('Model_Acm_Func');

    }

    //设置权限
    function actionSetQx(){
	    $this->authCheck('99-3');
	    $ret = $this->_modelFunc->getAuthTreeData(array(
			'children'=>$this->_modelFunc->menu
		));

	    //取得所有的组
	    $mRole = FLEA::getSingleton('Model_Acm_Role');
	    $mRole->clearLinks();
	    $rowRole = $mRole->findAll();
	    // dump($ret);exit;

	    $smarty = & $this->_getView();
	    $smarty->assign('title','设置权限');
	    $smarty->assign('action',$this->_url('AssignFuncByAjax'));
	    $smarty->assign('actionGetRole',$this->_url('GetRoleQx'));
	    $smarty->assign('treeData',json_encode($ret));
	    $smarty->assign('rowRole',json_encode($rowRole));
	    $smarty->display("Acm/ShowFunc.tpl");
    }

	//分配权限时，点击某个role,需要获得该role所有的funcId
	function actionGetRoleQx() {
		$auth = $this->authCheck('99-3' ,true);
		//获取post数据
		$post = $this->axiosPost();

		if(!$auth){
			echo json_encode(array('success'=>false,'msg'=>'无权限设置权限'));
			exit;
		}

		$m = & FLEA::getSingleton('Model_Acm_Role');
		$sql = "select * from acm_func2role where roleId='{$post['roleData']}'";
		$rowset = $m->findBySql($sql);
		$rows = array_col_values($rowset,'menuId');
		echo json_encode(array('success'=>true,'treeData'=>$rows));exit;
	}

	//检查acm_func2role表中是否存在无效的menuId,发现则删除.
	function checkQx() {

	}

	//从树形结构页面中以ajax方式提交过来的处理函数
	function actionAssignFuncByAjax(){
		$auth = $this->authCheck('99-3' ,true);
		//获取post数据
		$post = $this->axiosPost();

		if(!$auth){
			echo json_encode(array('success'=>false,'msg'=>'无权限设置权限'));
			exit;
		}

		if(!$post['roleData']){
			echo json_encode(array('success'=>false,'msg'=>'请选择角色'));
			exit;
		}

		$m = FLEA::getSingleton('Model_Acm_Func2Role');
		//先删除相关角色下所有已经分配的权限关系
		$sql = "delete from acm_func2role where roleId='{$post['roleData']}'";
		$m->execute($sql);
		$arr = array();
		foreach($post['treeData'] as & $v) {
			$arr[] = array(
				'menuId'=>$v,
				'roleId'=>$post['roleData']
			);
		}
		$m->createRowset($arr);

		sysDbLog(array('param'=>$arr,'post'=>$post),'Model_Acm_Role','设置权限');

		echo json_encode(array('success'=>true,'msg'=>'设置成功'));
		exit;
	}
}
?>