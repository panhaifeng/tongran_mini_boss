<?php
FLEA::loadClass('TMIS_Controller');
class Controller_Main extends TMIS_Controller {

	function Controller_Main() {
		$this->_modelExample = &FLEA::getSingleton('Model_OaMessage');
		$this->_modelAcmOa = &FLEA::getSingleton('Model_Acm_User2message');

	}

	function actionIndex() {
		$this->authCheck(0);

		if (!$_SESSION['REALNAME']||!$_SESSION['USERID']) {
			redirect(url("Login"));	exit;
		}

		//判断浏览器类型：如果浏览器类型不对，给予提示
		// FLEA::loadClass('TMIS_Common');
		// TMIS_Common::doBrowser();

		$menu = $this->getMenu();
		// dump($menu);exit;
		$smarty = & $this->_getView();
		$smarty->assign("Menu", $menu);
		$smarty->display('Main.tpl');
	}

	function actionGetMenu() {
		$ret = $this->getMenu();
		echo json_encode($ret);
	}

	//获取menu
	function getMenu(){
		// error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
		$f = FLEA::getAppInf('menu');
		include $f;
		$m = FLEA::getSingleton('Model_Acm_Func');
		$ret = array();

		foreach($_sysMenu as &$v) {
			$a = $m->changeVisible($v, array('userName' => $_SESSION['USERNAME']));
			if(!$a) continue;

			//没有产品档案的情况下，不显示产品档案菜单
			if($a['text']=='基础档案'){
				$key = '';
				foreach ($a['children'] as $k=>& $vv) {
					if($vv['text']=='产品档案'){
						$vv['leaf'] = false;
						$modelKind = FLEA::getSingleton('Model_Jichu_ProKind');
						$kinds = $modelKind->findAll();

						foreach ($kinds as $key => $kind) {
							$vv['children'][] = array(
								'text'     =>$kind['kindName'],
								'expanded' => false,
								'src'      =>$vv['src'].'&kindId='.$kind['id'],
								'leaf'     =>true,
								'id'       =>$vv['id'].'-'.$kind['id'],
								'iconCls'  =>'x-tree-icon-hide',
					    	);
						}

						unset($vv['src']);
					}
				}
			}

			$ret[] = $a;
		}

		//处理图标问题
		/*foreach($ret as & $v) {
			$this->setIconTree($v);
		}*/

		return $ret;
	}


	/**
	 * 处理icon，优先使用spanIcon
	 * Time：2015/07/22 14:52:55
	 * @author li
	 * @param array
	 * @return array
	*/
	function setIconTree(&$node){
		//处理图标问题
		if($node['iconSpan']!=''){
			$node['text']="<span class='glyphicon-tree glyphicon {$node['iconSpan']}'></span> ".$node['text'];
			$node['iconCls']='x-tree-icon-hide';
			unset($node['iconSpan']);
		}

		foreach ($node['children'] as & $v) {
			$this->setIconTree($v);
		}
	}

	function actionTzViewDetails() {
		//dump($_GET);exit;
		$row=$this->_modelExample->findAll(array('id'=>$_GET['id']));
		if($_SESSION['USERID']!='') {
			if($row[0]['kindName']!='订单变动通知') {
				$sql="SELECT count(*) as cnt,kind,id FROM `acm_user2message` where messageId='{$_GET['id']}' and userId='{$_SESSION['USERID']}'";
				$rr=mysql_fetch_assoc(mysql_query($sql));

				if($rr['cnt']==0) {
					$arr=array(
						'userId'=>$_SESSION['USERID'],
						'messageId'=>$_GET['id'],
						'kind'=>0,
					);
				}else if($rr['kind']==1){
					$arr=array(
						'id'=>$rr['id'],
						'kind'=>0,
					);
				}

				if($arr && $_SESSION['USERID']!='')$this->_modelAcmOa->save($arr);
			}
		}
		$smarty = & $this->_getView();
		$smarty->assign('title','查看通知');
		$smarty->assign("row", $row[0]);
		$smarty->display('OaViewDetails.tpl');
	}

	//处理弹出窗口后下次不在弹出消息的问题
	function actionTzViewDetailsByAjax(){
		// dump(1);exit;
		if($_SESSION['USERID']=='')exit;
		$userId=$_SESSION['USERID'];
		$sql="SELECT x.* FROM `oa_message` x
		left join oa_message_class y on y.className=x.kindName
		where y.isWindow=0
		and not exists(select * from acm_user2message z where z.messageId=x.id and z.userId='{$userId}')";
		$rr=$this->_modelExample->findBySql($sql);
		foreach($rr as & $v){
			// if($v['kindName']=='行政通知') {
					$arr[]=array(
						'userId'=>$_SESSION['USERID'],
						'messageId'=>$v['id'],
						'kind'=>1,
					);
			// }
		}
		if($arr)$this->_modelAcmOa->saveRowset($arr);
		echo json_encode(array('success'=>true));exit;
	}

	function changToHtml($val) {//将特殊字元转成 HTML 格式
		$val=htmlspecialchars($val);
		$val= str_replace("\011", ' &nbsp;&nbsp;&nbsp;', str_replace('  ', ' &nbsp;', $val));
		$val= ereg_replace("((\015\012)|(\015)|(\012))", '<br />', $val);
		return $val;
	}
	function cSubstr($str,$start,$len) {//截取中文字符串
		$temp = "<span title='".$str."'>".mb_substr($str,$start,$len,'utf-8')."</span>";
		return $temp;
	}

	//
	function actionGetTongzhiByAjax() {
		$userId=$_SESSION['USERID'];
		$sql="SELECT x.*,count(*) as cnt FROM `oa_message`  x
		left join oa_message_class y on y.className=x.kindName
		where y.isWindow=0
		and not exists(select * from acm_user2message z where z.messageId=x.id and z.userId='{$userId}')";
		$rr=$this->_modelExample->findBySql($sql);
		//dump($rr);exit;
		//if($rr[0]['cnt']>0){
		echo json_encode($rr[0]);
		exit;
	//}

	}

	//
	function actionGetMailByAjax() {
		$userId=$_SESSION['USERID'];
		$sql="SELECT count(*) as cnt FROM mail_db where accepterId='{$userId}' and timeRead='0000-00-00 00:00:00'";
		//dump($sql);exit;
		$rr=$this->_modelExample->findBySql($sql);
		echo json_encode($rr[0]);
		exit;
	}

	//根据id取得通知内容，返回为json
	function actionGetContentByAjax() {
		$row=$this->_modelExample->findAll(array('id'=>$_GET['id']));
		if($_SESSION['USERID']!='') {
			if($row[0]['kindName']=='行政通知') {
				$sql="SELECT count(*) as cnt FROM `acm_user2message` where messageId='{$_GET['id']}' and userId='{$_SESSION['USERID']}'";
				$rr=mysql_fetch_assoc(mysql_query($sql));
				if($rr['cnt']==0) {
					$arr=array(
						'userId'=>$_SESSION['USERID'],
						'messageId'=>$_GET['id'],
					);
					if($arr && $arr['userId']!='')
						$this->_modelAcmOa->save($arr);
				//$dbo=FLEA::getDBO(false);dump($dbo->log);exit;
				}
			}
		}
		$row=$this->_modelExample->find(array('id'=>$_GET['id']));
		echo json_encode($row);exit;
	}
	//获取新订单通知
	function actionGetNewTrade(){
		$userId=$_SESSION['USERID'];
		$sql="SELECT x.*,count(*) as cnt FROM oa_message  x
			where 1 and kindName = '订单'
			and not exists(select z.* from acm_user2message z where z.messageId=x.id and z.userId='{$userId}')";
		$rr=$this->_modelExample->findBySql($sql);
		// dump($sql);exit;
		echo json_encode($rr['0']);
		exit;
	}

	//2016年3月28日12:22:09 获取新的退库通知by jiangxu
	function actionGetNewTkmsg(){
		$userId=$_SESSION['USERID'];
		$sql="select * from acm_roledb where roleName like '%仓库%' or roleName = '财务科'";
		$row=$this->_modelExample->findBySql($sql);
		foreach ($row as &$v) {
			$str="select * from acm_user2role where roleId = '{$v['id']}'";
			$arr=$this->_modelExample->findBySql($str);
			foreach ($arr as &$vv) {
				$sql = "select * from acm_userdb where id = '{$vv['userId']}'";
				$rowset=$this->_modelExample->findBySql($sql);
				foreach ($rowset as &$value) {
					// dump($value['id']);
					if($userId==$value['id']){
					$sql="SELECT x.*,count(*) as cnt FROM oa_message  x
					where 1 and kindName ='采购退库'
					and not exists(select z.* from acm_user2message z where z.messageId=x.id and z.userId='{$userId}')";
					$rr=$this->_modelExample->findBySql($sql);
					}
				}
			}
		}
				    echo json_encode($rr['0']);
				    exit;
	}

	//处理弹出窗口后下次不在弹出消息的问题
	function actionTzNext(){
		if($_SESSION['USERID']=='')exit;
		$userId=$_SESSION['USERID'];
		$sql="SELECT x.* FROM `oa_message` x
		where 1
		and not exists(select * from acm_user2message z where z.messageId=x.id and z.userId='{$userId}')";
		$rr=$this->_modelExample->findBySql($sql);
		foreach($rr as & $v){
			$arr[]=array(
				'userId'=>$_SESSION['USERID'],
				'messageId'=>$v['id'],
				'kind'=>1,
			);
		}
		if($arr)$this->_modelAcmOa->saveRowset($arr);
		echo json_encode(array('success'=>true));exit;
	}

	function actionWelcome() {
		$smarty = & $this->_getView();
		$compName = FLEA::getAppInf('compName');

		$smarty->assign('title','首页');
		$smarty->assign('welcomeMsg','欢迎使用 - '.$compName.'系统');
		$smarty->display('Welcome.tpl');
	}

}
?>