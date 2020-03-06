<?php
class TMIS_Controller extends FLEA_Controller_Action {
    /**
    *代表模块代码的变量
    *默认为0,表示不需要进行权限判断
    */
	var $funcId=0;
	var $_modelExample;
	//显示左右分割的iframe框架
	function actionIndex() {
		$smarty = & $this->_getView();
		$smarty->assign('arr_left_list', $this->arrLeftHref);
		$smarty->assign('caption', $this->leftCaption);
		$smarty->assign('controller', $this->_controllerName);
		$smarty->assign('action', 'right');
		$smarty->display('MainContent.tpl');
	}

	#根据主键删除,并返回到action=right
	function actionRemove() {
		$from = $_GET['fromAction']?$_GET['fromAction']:$_GET['from'];
		if ($this->_modelExample->removeByPkv($_GET['id'])) {
			if($from=='') redirect($this->_url("right"));
			else js_alert(null,"window.parent.showMsg('成功删除')",$this->_url($from));
		}
		else js_alert('出错，不允许删除!',null,$this->_url($from));

	}
	//利用ajax删除订单明细，在订单编辑界面中使用,需要定义subModel
	function actionRemoveByAjax() {
		$id=$_REQUEST['id'];
		$m = $this->_subModel;
		if($m->removeByPkv($id)) {
			echo json_encode(array('success'=>true));
			exit;
		}
	}

    function actionRemoveAjax() {
        $requestParam = file_get_contents('php://input');
        $_POST = json_decode($requestParam,true);
        $id = intval($_POST['row']['id']);

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

    function actionRemoveSonAjax() {
        $requestParam = file_get_contents('php://input');
        $_POST = json_decode($requestParam,true);
        $id = intval($_POST['id']);

        $res = $this->_subModel->removeByPkv($id);

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

	//新增
	function actionAdd() {
		$this->_edit(array());
	}
	//修改
	function actionEdit() {
		$aRow=$this->_modelExample->find($_GET[id]);
		// dump($aRow);exit;
		$this->_edit($aRow);
	}

	function _edit($arr) {
		$smarty = & $this->_getView();
		$smarty->assign('aRow',$arr);
		$smarty->display($this->_tplEdit);
	}
	//保存
	function actionSave() {
		$id = $this->_modelExample->save($_POST);
		redirect($this->_url($_POST['Submit']=='保存并新增下一个' ? 'add':"Right"));
	}

	//调用通用的编辑模板进行编辑
	function actionCommonEdit() {
		if($_POST['modelName']) {
			$m = & FLEA::getSingleton($_GET['modelName']);
		} else $m= $this->_modelExample;

		$arrF = $this->_editField();
		$aRow=$m->find($_GET[id]);
		$tpl = '_edit.tpl';
		$smarty = & $this->_getView();
		$smarty->assign('field',$arrF);
		$smarty->assign('aRow',$aRow);
		$smarty->display($tpl);
	}
	//修改保存
	function actionCommonSave() {
		if($_POST['modelName']) {
			$m = & FLEA::getSingleton($_GET['modelName']);
		} else $m= $this->_modelExample;
		$m->update($_POST);
		js_alert(null,'window.parent?(window.parent.location.href=window.parent.location.href):(window.location.href="'.$this->_url('right').'")');
	}
	/**
	 *get the arr data from $pager(TMIS_pager),  and conver to the json data which can be used in extjs datgrid
	 */
	function getJsonDataOfExt(& $pager) {
		$rowset = $pager->findAll();
		return '{"totalCount": ' . $pager->totalCount . ',"rows": ' . json_encode($rowset) . '}';
	}

	/**
	 *根据纪录总数和纪录数组构造Ext中的records数据格式
	 */
	function buildExtRecords($cnt,& $arr) {
		return '{"totalCount": ' . $cnt . ',"rows": ' . json_encode($arr) . '}';
	}

	function authCheck($funcId = 0,$isReturn=false) {
		$warning = "<div align=center style='font-size:12px; color=#cc0000'><img src='Resource/Image/error.gif' style='vertical-align:middle;'>&nbsp&nbsp您没有登录或没有当前模块访问权限!</img></div>";

        //如果跨项目访问，则清掉对应的session
        if($_SESSION['PHP_SELF'] != $_SERVER['PHP_SELF']){
            unset($_SESSION['USERID']);
            unset($_SESSION['USERNAME']);
            unset($_SESSION['REALNAME']);
        }

        $logout_url = url('Login','logout');

		if ($funcId === 0) {//检查是否登录
			if($_SESSION['USERID']>0) return true;
			if ($isReturn) return false;
			$_SESSION['TARGETURL'] = $_SERVER['REQUEST_URI'];

			redirect($logout_url);
			exit;
		}

		//处理$funcId>0的情况
		if(empty($_SESSION['USERID'])) {//没有登录,显示登录界面
		//保存当前地址到session
			$_SESSION['TARGETURL'] = $_SERVER['REQUEST_URI'];
			if ($isReturn) return false;

			redirect($logout_url);
			exit;
		}

		$mUser = FLEA::getSingleton('Model_Acm_User');
		$user = $mUser->find($_SESSION['USERID']);//dump($user);exit;
		if($user['userName']=='admin') {//管理员直接跳过
			return true;
		}

        /*$mFunc = FLEA::getSingleton('Model_Acm_Func');
        if ($funcId == -1) {//从Acm_FuncDb中搜索controller和action匹配的记录
            $sql = "select count(*) cnt,id from acm_funcdb where LOWER(controller)='".strtolower($_GET['controller'])."' and LOWER(action)='".strtolower($_GET['action'])."'";
            $r = mysql_fetch_assoc(mysql_query($sql));
            if($r['cnt']==0) {
                if(!$isReturn) die('没有定义该功能模块！请在模块定义中定义该功能！点击自动增加 [增加]');
                return false;
            }
            $funcId=$r['id'];
        }*/

		$userRoles = $mUser->getRoles($_SESSION['USERID']);
		if(count($userRoles)==0) {
			if (!$isReturn) die($warning);
			return false;
		}
		$roles = join(',',array_col_values($userRoles,'id'));
		//各个组是否享有对当前节点的访问权限
		$sql = "select count(*) cnt from acm_func2role where (menuId like '{$funcId}-%' or menuId='{$funcId}') and roleId in({$roles})";
		$_r = $mUser->findBySql($sql);
		if($_r[0]['cnt']==0) {
			if (!$isReturn) die($warning);
			return false;
		}
		return true;
	}

    // 检查登录状态
    function checkLogin($isReturn = false){
        if(empty($_SESSION['USERID'])) {//没有登录,显示登录界面
            //保存当前地址到session
            $_SESSION['TARGETURL'] = $_SERVER['REQUEST_URI'];
            if ($isReturn) return false;
            js_alert("登录信息已过期!",'parent.location.reload();');
            redirect(url('Login','logout'));
            exit;
        }
        return true;
    }



	//显示登陆界面
	function showLogin() {
		$smarty = & $this->_getView();
		$smarty->assign("aProduct",$Arr);
		$pk=$this->_modelExample->primaryKey;
		$primary_key=(isset($_GET[$pk])?$pk:"");
		$smarty->assign("pk",$primary_key);
		$smarty->display('JiChu/ProductEdit.tpl');
	}


	//可编辑的grid进行修改动作中，控件失去焦点时通过ajax调用.
	//传入id,fieldName,value3个值
	//返回json数据对象{success:true,msg:'成功'}
	//注意$this->_modelExample必须为相应的model.
	function actionAjaxEdit() {
		$row['id'] = $_GET['id'];
		$row[$_GET['fieldName']] = $_GET['value'];
		if ($this->_modelExample->update($row)) {
			$arr = array('success'=>true,'msg'=>'成功!');
		//echo "{'success':true,'msg':'成功!'}";
		} else {
			$arr = array('success'=>false,'msg'=>'出错!');
		//echo "{'success':false,'msg':'出错!'}";
		}
		echo json_encode($arr);
		exit;
	}

	//将普通显示的字段以可编辑的形式显示出来
	function makeEditable(& $arr,$fieldName) {
		$title = $arr[$fieldName]=='' ? '无' : $arr[$fieldName] ;
		$arr[$fieldName] = '<span onclick="gridEdit(this,\''.$fieldName.'\','.$arr[id].')" title="点击修改" onmouseover="this.style.cssText = \'background: #278296;\'" onmouseout="this.style.cssText = \'background:#efefef\'" style="background:#efefef;">'.$title.'</span>';
	}

	function getEditHtml($pkv,$action='Edit') {
        if(!is_array($pkv)) return "<a href='".$this->_url($action,array(id=>$pkv,'fromAction'=>$_GET['action']))."'><span class='glyphicon glyphicon-pencil' title='修改'></span></a>";
		if(!$pkv['fromAction']) $pkv['fromAction']= $_GET['action'];
        return "<a href='".$this->_url($action,$pkv)."'><span class='glyphicon glyphicon-pencil' title='修改'></span></a>";
    }

    //返回"删除"操作按钮
    function getRemoveHtml($pkv,$action='Remove') {
        if(!is_array($pkv)) return "<a href='".$this->_url($action,array(
			'id'=>$pkv,
			'from'=>$_GET['action'],
			'fromAction'=>$_GET['action']
		))."' onclick='return confirm(\"您确认要删除吗?\")'><span class='glyphicon glyphicon-trash text-danger' title='删除'></span></a>";
		if(!$pkv['text']) $pkv['text']="<span class='glyphicon glyphicon-trash text-danger' title='删除'></span>";
		if(!$pkv['msg']) $pkv['msg']='您确认要删除吗';
		if(!$pkv['fromAction']) $pkv['fromAction']=$_GET['action'];
        return "<a href='".$this->_url($action,$pkv)."' onclick='return confirm(\"{$pkv['msg']}?\")'>{$pkv['text']}</a>";
    }


    /**
     * ps ：显示审核状态：已通过，未审核，未完成，拒绝
     * Time：2015/09/23 11:15:21
     * @author jiang
    */
    function stateShenhe($_shenheName,$pkv){
        if(!$_shenheName)return "参数信息不能为空";

        //实例化model 获取审核对应的配置信息
        $_modelShenhe = &FLEA::getSingleton('Model_Shenhe_Shenhe2Node');
        $_shenheInfo = $_modelShenhe->_config_shenhe();

        //获取对应的节点审核信息
        $nodeInfo = $_shenheInfo[$_shenheName];
        if(!$nodeInfo)return "不需审核";
        $_modeldb = &FLEA::getSingleton('Model_Shenhe_ShenheDb');
        foreach ($nodeInfo as $key => & $v) {
            //显示审核状态
            $v['showShenhe']=$_modeldb->_shenhe_status($_shenheName,$v,$pkv);

            //一级审核通过才需要判断二级审核状态
           	if($key==0&$v['showShenhe']!='通过'){
                if($v['showShenhe']=='未审核') return '未审核';
                else return '拒绝';
            }else if(count($nodeInfo)==1){//如果只有一级审核 则返回一级审核的状态
            	if($v['showShenhe']=='通过') return '已通过';
            }else if(count($nodeInfo)==2 && $key==1){
                if($v['showShenhe']=='未审核') return '未完成';
                else if($v['showShenhe']=='通过') return '已通过';
                else return '拒绝';
            }else if(count($nodeInfo)==3 && $key==2){
                if($v['showShenhe']=='未审核') return '未完成';
                else if($v['showShenhe']=='通过') return '已通过';
                else return '拒绝';
            }
        }
    }

    /**
     * ps ：显示审核信息
     * Time：2015/09/23 13:40:49
     * @author jiang
     *@return string 显示审核信息
    */
    function showShenhe($_shenheName,$pkv){
        $_modeShenhe2Node = FLEA::getSingleton('Model_Shenhe_Shenhe2Node');
        // $_model_ShenheDb = FLEA::getSingleton('Model_Shenhe_ShenheDb');
        $sql="SELECT x.*,z.realname
                  from shenhe_db x
                  left join acm_userdb z on x.userId=z.id
                  where x.tableId='{$pkv}' and x.nodename='{$_shenheName}'
                  order by x.nodeId";
        $memo=$_modeShenhe2Node->findBySql($sql);
        $_detial=array();
        foreach ($memo as & $vv) {
            $v['state'] = $vv['status']==yes ? "通过":"未通过";
            $v['realname'] = $vv['realname'];
            $v['memo'] = $vv['memo'];
            $v['dt'] = $vv['dt'];
            $v['node'] = $_modeShenhe2Node->getNodeById($vv['nodeId']);
            $v['shen'] = $v['node']['text'];

            $_detial[] = "<span class='text-danger'>".$v['shen']."</span>：".$v['realname'].'('.$v['state'].')';
            $_detial[] = "<span class='text-success'>备注</span>：".$v['memo'];
            $_detial[] = "<span class='text-primary'>时间</span>：".$v['dt'];
        }
        $_detial = join('<br>',$_detial);
        return $_detial;
    }

    /**
	 * ps ：判断是否有审核权限
	 * Time：2015/09/01 14:51:11
	 * @author jiang
	*/
	function authShenhe($funcId) {
        //没有session
        if(!$_SESSION['REALNAME']){
            echo "你还没有登录，赶快登录吧";exit;
        }
		//管理员跳过判断
		if($_SESSION['REALNAME']!='管理员'){
			$_modelShenhe = &FLEA::getSingleton('Model_Shenhe_Shenhe2Node');
			$shenheNode = $_modelShenhe->find(array('nodeId'=>$funcId,'userId'=>$_SESSION['USERID']));
			if(!$shenheNode) return false;
		}
		return true;
	}


    /**
     * 添加审核按钮 审核按钮分为两级
     * 首先要判断该用户是否有审核权限  如果没有者不显示按钮
     * 存在多级审核，一级审核未审核通过，二级审核不允许操作
     * Time：2015/09/01 14:31:12
     * @author jiang
     * @return string 表示审核操作的html，显示到界面后就是操作按钮信息
    */
    function getShenheHtml($_shenheName,$pkv,$pcd=null) {
    	if(!$_shenheName)return "参数信息不能为空";

    	//实例化model 获取审核对应的配置信息
        $_modelShenhe = &FLEA::getSingleton('Model_Shenhe_Shenhe2Node');
    	$_shenheInfo = $_modelShenhe->_config_shenhe();

    	//获取对应的节点审核信息
    	$nodeInfo = $_shenheInfo[$_shenheName];
    	if(!$nodeInfo)return "不需审核";

    	//处理数组，判断哪些节点按钮允许操作，哪些节点不允许操作，并且生成title提示信息
    	$_modeldb = &FLEA::getSingleton('Model_Shenhe_ShenheDb');
    	foreach ($nodeInfo as $key => & $v) {
    		//显示审核状态
    		$v['showShenhe']=$_modeldb->_shenhe_status($_shenheName,$v,$pkv);

            //添加当前审核的备注信息:审核人，时间，备注
            // dump($v);exit;
            if($v['showShenhe']!='未审核'){
                $res = $_modeldb->find(array(
                        'nodeId'=>$v['id'],
                        'tableId'=>$pkv
                ));
                $v['title'] = "审核人：{$res['User']['realName']}";
                $v['title'] .= "<br/>时间：{$res['dt']}";
                $v['title'] .= "<br/>说明：{$res['memo']}";
                // dump($res);exit;
            }

    		//权限判断:没有权限不能操作
    		if(!$this->authShenhe($v['id'])){
    			//没有权限 不能操作
    			$v['disabled'] = true;
    			$v['title'] .= '<br/><font color="red">没有该审核节点的权限，无法操作</font>';
    			continue;
    		}

    		//前面审核节点是否已经审核成功
    		$_nodeId_prev = $key == 0 ? '通过' : $nodeInfo[$key-1]['showShenhe'];
    		if($_nodeId_prev!='通过'){
				$v['disabled'] = true;
    			$v['title'] .= '<br/><font color="red">上一级审核未通过，无法操作</font>';
    			continue;
    		}

    		//后面审核节点是否已经审核
    		$_nodeId_prev='未审核';
    		if($key!=(count($nodeInfo)-1)){
                $_nodeId_prev=$_modeldb->_shenhe_status($_shenheName,$nodeInfo[$key+1],$pkv);
            }
    		if($_nodeId_prev!='未审核'){
				$v['disabled'] = true;
    			$v['title'] .= '<br/><font color="red">下一级已审核，无法操作</font>';
    			continue;
    		}
    	}

    	//开始生成html 按钮操作信息
    	$_htmlArr = array();
    	foreach ($nodeInfo as $key => &$v) {
    		$_url = $v['disabled'] == true ? 'javascript:;' : url('Shenhe_Shenhe','EditShenhe',array(
                'id'=>$pkv,
    			'proCode'=>$pcd,
    			'nodeId'=>$v['id'],
    			'isLast'=>$v['isLast'],
    			'nodeName'=>$_shenheName,
    			'TB_iframe'=>1,
    		));
    		//按钮颜色
    		if($v['showShenhe']=='通过'){
                $v['btn'] = 'btn-success';
                $v['ico_cls'] = 'glyphicon glyphicon-ok';
                $v['color'] = '#fff';
            }
    		else if($v['showShenhe']=='未通过'){
                $v['btn'] = 'btn-warning';
                $v['ico_cls'] = 'glyphicon glyphicon-remove';
                $v['color'] = '#fff';
            }
    		else{
                $v['btn'] = 'btn-default';
                $v['ico_cls'] = 'glyphicon glyphicon-link';
                if($v['disabled'] == false){
                    $v['color'] = '#222';
                }else{
                    $v['color'] = '#999';
                }
            }

    		//弹框
    		$v['box']=$v['disabled'] == true ? '' :'thickbox';

    		$_htmlArr[]="<a class='btn {$v['btn']} btn-xs {$v['box']}' style='color:{$v['color']};' href='{$_url}' ext:qtip='{$v['title']}'>{$v['text']}(<span class='{$v['ico_cls']}'></span>)</a>";
    	}

    	return join(" <span class='glyphicon glyphicon-chevron-right'></span> ",$_htmlArr);
    }

     /**
     * ps ：重新申购：就是把审核那边改为未审核，审核次数加1
     * Time：2015/09/23 16:32:15
     * @author jiang
    */
    function actionAgainShenhe(){
        //加载配置文件
        require "Config/Config_Shenhe.php";
        //修改审核状态
        $sql="update shenhe_db set status='' where tableId='{$_GET['id']}' and nodeName='{$_GET['shenheName']}'";
        $this->_modelExample->findBySql($sql);
        //修改主表的审核状态次数加1
        $dbTable=$_node_table[$_GET['shenheName']];
        $sql="update {$dbTable} set shenhe='',shTime=shTime+1 where id='{$_GET['id']}'";
        $this->_modelExample->findBySql($sql);

        js_alert(null,"window.parent.showMsg('成功')",$this->_url('Right'));
    }

	#清空搜索条件
	function clearCondition() {
		FLEA::loadClass('TMIS_Pager');
		TMIS_Pager::clearCondition();
	}


	#对rowset的某几个字段进行合计,
	#firstField表示需要显示为"合计"字样的字段
	#返回合计行的数据
	function getHeji(&$rowset,$arrField,$firstField='') {
		$str = "\$newRow[\"" . join('"]["',explode('.',$firstField)) . '"]="合计";';
		eval($str);
		foreach($rowset as & $v) {
			foreach($arrField as & $f) {
				$newRow[$f] += $v[$f];
				$newRow[$f] = $newRow[$f];
			}
		}
		$newRow['_bgColor']='#F2F4F6';
		$newRow['mark']='heji';
		return $newRow;
	}

	//取得系统配置数组
	function getSysSet() {
		FLEA::loadClass('TMIS_Common');
		return TMIS_Common::getSysSet();
	}

	//根据前缀，自动从表中产生$head.yymmddxxx的流水号
	function _getNewCode($head,$tblName,$fieldName) {
		$m = FLEA::getSingleton('Model_Acm_User');
		$sql = "select {$fieldName} from {$tblName} where {$fieldName} like '{$head}_________' order by {$fieldName} desc limit 0,1";

		$_r = $m->findBySql($sql);
		$row = $_r[0];

		$init = $head .date('ymd').'001';
		if(empty($row[$fieldName])) return $init;
		if($init>$row[$fieldName]) return $init;

		//自增1
		$max = substr($row[$fieldName],-3);
		$pre = substr($row[$fieldName],0,-3);
		return $pre .substr(1001+$max,1);
	}

    //根据前缀，自动从表中产生$head.yymmxxx的流水号
    function _getNewGydCode($head) {
        $m = & FLEA::getSingleton('Model_Acm_User');
        $sql = "select orderCode from gongyidan where orderCode like '{$head}_______' and createrId='{$_SESSION['USERID']}' order by orderCode desc limit 0,1";

        $_r = $m->findBySql($sql);
        $row = $_r[0];

        $init = $head .date('ym').'001';
        if(empty($row['orderCode'])) return $init;
        if($init>$row['orderCode']) return $init;

        //自增1
        $max = substr($row['orderCode'],-3);
        $pre = substr($row['orderCode'],0,-3);
        return $pre .substr(1001+$max,1);
    }

	function _autoCode($head,$next,$tblName,$fieldName){
		$m = & FLEA::getSingleton('Model_Acm_User');
		$sql = "select {$fieldName} from {$tblName} where {$fieldName} like '{$head}{$next}____' order by {$fieldName} desc limit 0,1";

		$_r = $m->findBySql($sql);
		$row = $_r[0];

		$init = $head.$next.'0001';
		if(empty($row[$fieldName])) return $init;
		if($init>$row[$fieldName]) return $init;
		// dump($init);exit;
		//自增1
		$max = substr($row[$fieldName],-4);
		$pre = substr($row[$fieldName],0,-4);
		return $pre .substr(10001+$max,1);
	}

	//使用通用编辑模板进行修改前，需要用数据库中的记录覆盖表中的value字段
	//返回的标签中增加了value属性
	function getValueFromRow($fldMain,$row) {
		foreach($fldMain as $key=>&$v) {
			$v['value'] = $row[$key];
		}
		return $fldMain;
	}

    //初始化部分有值的参数，其他的默认原来的
    function initValueFromParams($fldMain,$row) {
        foreach($row as $key=>&$v) {
            $fldMain[$key]['value'] = $v;
        }
        return $fldMain;
    }

	/*********************************************************************\
	*  Copyright (c) 1998-2013, TH. All Rights Reserved.
	*  Author :li
	*  FName  :Controller.php
	*  Time   :2014/05/13 17:00:30
	*  Remark :用于有效性验证，验证重复问题
	\*********************************************************************/
	function actionRepeat(){
		if($_GET['field']=='' || $_GET['fieldValue']==''){
			exit;
		}
		//查找是否存在
		$con=array();
		$con[]=array($_GET['field'],$_GET['fieldValue'],'=');
		if($_GET['id']>0)$con[]=array('id',$_GET['id'],'<>');
		$temp=$this->_modelExample->findAll($con);
		$success = count($temp)>0?false:true;

		echo json_encode(array('success'=>$success));
	}

	/**
   * ps ：清空某些字段的value值
   * 对二维数组进行操作
   * Time：2014/09/15 09:53:25
   * @author li
   * @param $arr二维数组
   * @param $keys,数组,也支持字符串的形式
  */
  function array_values_empty(& $arr , $keys){
	    foreach ($arr as $k => & $v) {
	    	//字符串的形式
	    	if(is_string($keys)){
	    		if(isset($v[$keys])){
			      		$v[$keys]='';
		      	}
	    	}elseif(is_array($keys)){
	    		//数组的形式
	    		foreach ($keys as $key => & $c) {
			      	if(isset($v[$c])){
			      		$v[$c]='';
			      	}
			    }
	    	}
	    }
  }

  /**
   * 导出操作
   * Time：2014/09/28 16:25:51
   * @author li
  */
  function _export($arr){
  		$arr['fileName']=='' &&  $arr['fileName']=date('y-m-d').$SESSION['REALNAME'];
  		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=".iconv('utf-8',"gb2312", $arr['fileName'].".xls"));
		// header("Content-Disposition: attachment;filename=List HL.xls");
		header("Content-Transfer-Encoding: binary");
  }

  /**
   * 导出明细列表
   * Time：2014/09/28 16:34:25
   * @author li
  */
  function _exportList($arr,& $smarty){
  	$this->_export($arr);
  	// dump($smarty);exit;
  	$field = & $smarty->_tpl_vars['arr_field_info'];
  	// dump($field);exit;
  	//键值为下面这些的，输出类型默认未数字类型
  	$col = array('cnt','cntJian','cntM','cntYaohuo','danjia','money','tuiCnt','rkCnt');
  	foreach ($col as & $v) {
  		if(array_key_exists($v, $field)){
	  		if(is_array($field[$v])){
	  			$field[$v]['type']="Number";
	  		}else{
	  			$field[$v] = array(
	  					'text'=>$field[$v],
	  					'type'=>'Number'
	  			);
	  		}
	  	}
  	}
  	// dump($field);
  	// dump($smarty->_tpl_vars['arr_field_info']);
  	$smarty->display('Export2Excel.tpl');
  	exit;
  }



  //下载附件
  function getFile($file_add){
  	if($file_add!=''){
  		$fn=iconv('utf-8',"gb2312",$file_add);
  		$index=strpos($fn,' ');
  		$name=substr($fn,$index);//处理文件名
  		//dump($name);exit;
  		$file = $fn;
  		header('Content-Description: File Transfer');
  		header('Content-Type: application/octet-stream');
  		header('Content-Disposition: attachment; filename="'.$name.'"');
  		header('Content-Transfer-Encoding: binary');
  		header('Expires: 0');
  		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  		header('Pragma: public');
  		header('Content-Length: ' . filesize($file));
  		readfile($file);
  	}
  }


    /**
     * 读取某个excel文件的某个sheet数据
    */
    function _readExcel($filePath,$sheetIndex=0) {
        set_time_limit(0);
        include "Lib/PhpExcel/PHPExcel.php";

        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize'=>'16MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $PHPExcel = new PHPExcel();
        //如果是2007,需要$PHPReader = new PHPExcel_Reader_Excel2007();
        $PHPReader = new PHPExcel_Reader_Excel5();
        // dump($PhpExcel);die;
        if(!$PHPReader->canRead($filePath)){
            js_alert('不能读取文件！','window.history.go(-1)');
        }
        $PHPExcel = $PHPReader->load($filePath);
        /**读取excel文件中的第一个工作表*/
        $currentSheet = $PHPExcel->getSheet($sheetIndex);
        /**取得共有多少列,若不使用此静态方法，获得的$col是文件列的最大的英文大写字母*/
        $allColumn = PHPExcel_Cell::columnIndexFromString($currentSheet->getHighestColumn());

        /**取得一共有多少行*/
        $allRow = $currentSheet->getHighestRow();
        //输出
        $ret = array();
        for($currow=1;$currow<=$allRow;$currow++){
          $_row=array();
          for($curcol=0;$curcol<$allColumn;$curcol++){
               $result=$currentSheet->getCellByColumnAndRow($curcol,$currow)->getValue();
               $result=(string)$result;
               $_row[] = $result;
          }
          $ret[] = $_row;
        }
        return $ret;
    }

    /**
     * $dizhi 数组 ['path']['name'] 必填
    */
    function _importAttac($dizhi,$path,$uploadFile){ //$uploadFile为上传附件标识
        //上传路径
        if(!$path){
        	$path="upload/cache";
        }
        $targetFile='';
        $tt = false;//是否上传文件成功
        //禁止上传的文件类型
        $upBitType = array(
            'application/x-msdownload',//exe,dll
            'application/octet-stream',//bat
            'application/javascript',//js
            'application/msword',//word
        );
        //处理上传代码
        if($dizhi['path']['name']!=''){
            //附件大小不能超过10M
            $max_size=10;//M
            $max_size2=$max_size*1024*1024;
            if($dizhi['path']['size']>$max_size2){
                return array('success'=>false,'msg'=>"附件上传失败，请上传小于{$max_size}M的附件");
            }

            //限制类型
            if(in_array($dizhi['path']['type'],$upBitType)){
                $_msg = $dizhi['path']['type']."文件类型不允许上传";
                js_alert($_msg,null);
                // return array('success'=>false,'msg'=>"该文件类型不允许上传");
            }

            //上传附件信息
            if ($dizhi['path']['name']!="") {
                $tempFile = $dizhi['path']['tmp_name'];
                //处理文件名
                $pinfo=pathinfo($dizhi['path']['name']);
                $ftype=$pinfo['extension'];
                // $fileName=md5(uniqid(rand(), true)).' '.$dizhi['path']['name'];
                // $fileName=$dizhi['path']['name'];
                $fileName=md5(uniqid(rand(), true));

                if($uploadFile){ //如果是上传附件 则加上后缀文件类型
	                $targetFile=$path.$fileName.'.'.$ftype;//目标路径
                }else{
	                $targetFile=$path.$fileName;//目标路径
                }
                $tt=move_uploaded_file($tempFile,iconv('UTF-8','gb2312',$targetFile));
                if($tt==false && $targetFile!='')$msg="上传失败，请重新上传附件";
            }
        }
        return array('filePath'=>$targetFile,'success'=>$tt,'msg'=>$msg);
    }

    function __toStringR($result){
      return (string) $result;
    }

    /**
     * 上传excel
     */
    function importFile($filepath){
    	set_time_limit(90);
		ini_set("memory_limit","1024M");
		include "Lib/PhpExcel/PHPExcel/IOFactory.php";
		//设定缓存模式为经gzip压缩后存入cache（还有多种方式请百度）
		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
		$cacheSettings = array();
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings);
		$objPHPExcel = new PHPExcel();
		//读入上传文件
		$objPHPExcel = PHPExcel_IOFactory::load($filepath);
		//内容转换为数组
		$indata = $objPHPExcel->getActiveSheet()->toArray();
		unset($indata[0]);
		return $indata;
	}


    /**
	 * 根据HTML代码获取word文档内容
	 * 创建一个本质为mht的文档，该函数会分析文件内容并从远程下载页面中的图片资源
	 * 该函数依赖于类MhtFileMaker
	 * 该函数会分析img标签，提取src的属性值。但是，src的属性值必须被引号包围，否则不能提取
	 *
	 * @param string $content HTML内容
	 * @param string $absolutePath 网页的绝对路径。如果HTML内容里的图片路径为相对路径，那么就需要填写这个参数，来让该函数自动填补成绝对路径。这个参数最后需要以/结束
	 * @param bool $isEraseLink 是否去掉HTML内容中的链接
	 */
	function getWordDocument( $content , $absolutePath = "" , $isEraseLink = true )
	{
		FLEA::loadClass('TMIS_MhtFileMaker');
		$mht = new TMIS_MhtFileMaker();
	    if ($isEraseLink){
	        $content = preg_replace('/<a\s*.*?\s*>(\s*.*?\s*)<\/a>/i' , '$1' , $content);   //去掉链接
	    }

	    $images = array();
	    $files = array();
	    $matches = array();
	    //这个算法要求src后的属性值必须使用引号括起来
	    if ( preg_match_all('/<img[.\n]*?src\s*?=\s*?[\"\'](.*?)[\"\'](.*?)\/>/i',$content ,$matches ) )
	    {
	        $arrPath = $matches[1];
	        for ( $i=0;$i<count($arrPath);$i++)
	        {
	            $path = $arrPath[$i];
	            $imgPath = trim( $path );
	            if ( $imgPath != "" )
	            {
	                $files[] = $imgPath;
	                if( substr($imgPath,0,7) == 'http://')
	                {
	                    //绝对链接，不加前缀
	                }
	                else
	                {
	                    $imgPath = $absolutePath.$imgPath;
	                }
	                $images[] = $imgPath;
	            }
	        }
	    }
	    $mht->AddContents("tmp.html",$mht->GetMimeType("tmp.html"),$content);

	    for ( $i=0;$i<count($images);$i++)
	    {
	        $image = $images[$i];
	        if ( @fopen($image , 'r') )
	        {
	            $imgcontent = @file_get_contents( $image );
	            if ( $content )
	                $mht->AddContents($files[$i],$mht->GetMimeType($image),$imgcontent);
	        }
	        else
	        {
	            echo "file:".$image." not exist!<br />";
	        }
	    }

	    return $mht->GetFile();
	}

	function _exportWord($wordStr){
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		$wordStr = '导出为doc文件';
		$fileContent = $this->getWordDocument($wordStr);
		$time = time('y-m-d');
		$fileName = iconv("utf-8", "GBK", $time);
		header("Content-Type: application/doc");
		header("Content-Disposition: attachment; filename=" . $fileName . ".doc");
		echo $fileContent;
	}

	/**
	 * object 转 array
	 */
	function object_to_array($obj){
	    $_arr=is_object($obj)?get_object_vars($obj):$obj;
	    foreach($_arr as $key=>$val){
	        $val=(is_array($val))||is_object($val)?object_to_array($val):$val;
	        $arr[$key]=$val;
	    }
	    return $arr;
	}


    //图片预览功能
    function actionShowImg(){
        // dump($_GET);die;
        $pics = explode(',',rtrim($_GET['pic'],','));

        $smarty = &$this->_getView();
        $smarty->assign('rowset', $pics);
        $_from = $_GET['fromAction']==''?'add':$_GET['fromAction'];
        $smarty->assign('fromAction', $_from);
        // $smarty->assign('sonTpl', $this->sonTpl);
        $smarty->display('Popup/showImgs.tpl');
    }
    /**
     * 有时数组中存在null，会导致保存时报错，这个函数自动将所有null改为空格
     */
    function notNull($row) {
        if($row===null) {
            return $row.'';
        }
        if(!is_array($row) && $row!==null) return $row;
        if(is_array($row)) {
            foreach($row as & $v) $v=$this->notNull($v);
        }
        return $row;
    }
    /**
     * 数据集传递到修改编辑界面前的处理操作
     * Time：2016年12月14日 08:57:20
     * @author shen   (zcc搬运)
    */
    function _beforeDisplayEdit(& $smarty){
        return true;
    }

    /**
     * 数据集传递到修改编辑界面前的处理操作
     * Time：2016年12月14日 08:57:20
     * @author shen   (zcc搬运)
    */
    function _beforeDisplayAdd(& $smarty){
        return true;
    }

    function _beforeDisplayRight(& $rowset){
        return true;
    }

    /**
     * 获取axios 传递的post数据
     * Time：2018/12/14 11:10:17
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    public function axiosPost()
    {
        $requestParam = file_get_contents('php://input');
        $_POST = json_decode($requestParam,true);

        //验证数据
        FLEA::loadClass('TMIS_Input');
        $_POST = TMIS_Input::check_input($_POST);

        return $_POST;
    }

    /**
     *
     * Time：2018/12/26 12:54:37
     * @author li
    */
    function _imageHtml($src = '',$cssText='' ,$class='' ,$async = false ,$asyncName='data-src'){
        FLEA::loadClass('TMIS_Common');
        return TMIS_Common::_imageHtml($src ,$cssText ,$class ,$async ,$asyncName);
    }

    //查找图片信息
    function getImagePath($imageId){
        FLEA::loadClass('TMIS_Common');
        $sql = "SELECT * from jichu_image where id='{$imageId}'";
        $rows = $this->_modelExample->findBySql($sql);
        //图片信息
        $path = TMIS_Common::_imageSrc($rows[0]['path']);
        return $path;
    }

    //上传图片
    function actionUploadImage(){
        $result = $this->_uploadImage();
        echo json_encode($result);
    }

    //上传图片
    function _uploadImage($fileImage ,$limitSize = 12 ,$basePhp = ''){
        if(!$fileImage){
            $key = key($_FILES);
            $fileImage = $_FILES[$key];
        }

        if(!$fileImage){
            return array('success' => false ,'msg'=>'ERROR_DATA : Files_Image is null');
        }

        //上传文件的类型
        $uploadType = array('jpg','jpeg','png');
        $arrDot = strstr($fileImage['name'] , '.');
        $extType = ltrim($arrDot , '.');
        $extType = strtolower($extType);
        if(!in_array($extType , $uploadType)){
            return array('success' => false ,'msg'=>'ERROR_DATA : Files_Image type is error');
        }
        //上传文件的最大值
        $max_size = $limitSize * 1024 * 1024; //12M
        if($fileImage['size'] > $max_size){
            return array('success' => false ,'msg'=>'ERROR_DATA : Files_Image size > '.$limitSize.'M');
        }

        //获取图片信息
        $size = getimagesize($fileImage['tmp_name']);
        $width  = $size[0];
        $height = $size[1];

        if($width > 0 && $height > 0){
            //计算图片需要的内存，防止内存溢出
            $memoryLimit = ceil($width*$height/131072);
            if($memoryLimit > 128 && $memoryLimit <= 1024){
                 @ini_set('memory_limit', $memoryLimit.'M');
            }else if($memoryLimit > 1024){
                return array('success' => false ,'msg'=>'ERROR_DATA : 图片分辨率过大');
            }
        }

        $pathRoot = 'upload/Images/';
        $randName = date('ymdHis') . rand(10000, 99999).'.'.$extType;
        $imagePath = $pathRoot.$randName;

        FLEA::loadClass('FLEA_Helper_Image');
        $img = & FLEA_Helper_Image::createFromFile($fileImage['tmp_name'], $extType);
        $img->resampled($width , $height);
        $img->saveAsJpeg($imagePath,100);

        //保存到表里
        $arr = array(
            'path'      => $imagePath,
            'smallPath' => '',
            'width'     => $width,
            'height'    => $height,
            'time'      => time(),
        );

        $_model = FLEA::getSingleton('Model_Jichu_Image');
        $id = $_model->save($arr);

        //根域名
        $serverUrl = '';
        if($basePhp != ''){
            //basePhp = 'index.php';
            $baseUrlstr = detect_uri_base();
            $serverUrl = substr($baseUrlstr, 0,strrpos(strtolower($baseUrlstr), $basePhp));
        }

        return array('success' => true ,'imgPath'=>$serverUrl.$imagePath ,'imageId'=>$id,'msg'=>'上传成功');
    }

    //删除图片
    function actionRemoveImage(){
        //未删除，没有移除实际图片和表数据
        $requestParam = file_get_contents('php://input');
        $_POST = json_decode($requestParam,true);

        $url = $_POST['url'];
        //根据url提取文件名,进行删除
        $ret = array(
          'success' =>true,
          'msg'     =>'图片删除成功',
          'url'     =>$url,
        );
        echo json_encode($ret);exit;
    }

    //审核显示的说明
    function shenheFormat($string){
        $arr = array(
            ''    =>'未审核',
            'yes' =>'审核通过',
            'no'  =>'审核未通过',
            'ing' =>'审核中',
        );

        return $arr[$string];
    }

    function getCompName(){
        $sys = $this->getSysSet();
        $_compName = FLEA::getAppInf('compName');
        $compName = $sys['company_name'] ? $sys['company_name'] : $_compName;
        return $compName;
    }
}
?>
