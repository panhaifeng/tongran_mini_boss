<?php
FLEA::loadClass('TMIS_Controller');
class Controller_Mail extends TMIS_Controller {
	var $_modelExample;
	var $title = "邮件管理";

	function Controller_Mail() {
		$this->_modelExample = & FLEA::getSingleton('Model_Mail');
	}
	function actionRight() {
		$title = '收件箱列表';
		///////////////////////////////模板文件
		$sonTpl="MailRemove.tpl";
		$tpl = 'TblList.tpl';
		///////////////////////////////表头
		$arr_field_info = array(
			'_edit'=>array('text'=>"<input type='checkbox' id='sel' ext:qtip='全选/反选'>",'width'=>40),
			//'isRead'=>'状态',
			"Sender.realName" =>'发送人',
			"title" =>array('text'=>'标题','width'=>300),
			//"content" =>'内容',
			"dt" =>array('text'=>'发送日期','width'=>160),
		);

		///////////////////////////////模块定义
		 $this->authCheck('8-2');
		FLEA::loadClass('TMIS_Pager');
		$arr = TMIS_Pager::getParamArray(array(
			'dateFrom'=>date('Y-m-01'),
			'dateTo'=>date('Y-m-d'),
			'key'=>''
		));
		$condition=array();
		if($arr['key']!='') {
			$condition[] = array('title',"%{$arr['key']}%",'like');
		}
		if($arr['dateFrom'] != '') $condition[] = array('dt',$arr['dateFrom']." 00:00:00",'>=');
		if($arr['dateTo'] != '') $condition[] = array('dt',$arr['dateTo']." 23:59:59",'<=');
		$condition[]=array('accepterId',$_SESSION['USERID'],'=');
		$pager = new TMIS_Pager($this->_modelExample,$condition,'id desc');
		$rowset =$pager->findAll();
		//dump($rowset);exit;
		if(count($rowset)>0) foreach($rowset as & $v) {
			///////////////////////////////

				$v['content']=$this->cSubstr($v['content'],0,20).".........";
				$v['_edit']="<input type='checkbox' name='editBtn[]' value='{$v['id']}'>";
				// $v['_edit'] = $this->getRemoveHtml($v['id']);
				//$v['isRead']=$v['time_read']=='0000-00-00 00:00:00'? '<font color="red">未读</font>':'已读';
				//dump($v['timeRead']);exit;
				if($v['title']=='') $v['title']='无标题';
				$v['title']="<a href='".$this->_url('ViewDetails',array(
					'id'=>$v['id'],
					'TB_iframe'=>1,
					'no_edit'=>1
					))."' class='thickbox' title='查看邮件内容'>{$v['title']}</a>";

				if($v['timeRead']=='0000-00-00 00:00:00' || $v['timeRead']==''){
				    $v['title']=$v['title']."<font color='red'>(未查看)</font>";

				}
			}
		$smarty = & $this->_getView();
		$smarty->assign('title', $title);
		$smarty->assign('sonTpl',$sonTpl);
		$smarty->assign('arr_field_info',$arr_field_info);
		$smarty->assign('arr_field_value',$rowset);
		$smarty->assign('arr_condition', $arr);
		$smarty->assign('other_url',"<a href='javascript:void(0);' id='remove'><span class='glyphicon glyphicon-trash'></span> 删除</a>");
		// $smarty->assign('arr_js_css',$this->makeArrayJsCss(array('calendar')));
		$smarty->assign('page_info',$pager->getNavBar($this->_url($_GET['action'],$arr)));
		$smarty->display($tpl);
	}

	function actionMailNoRead() {
		$title = '收件箱列表';
		$tpl = 'TblList.tpl';
		$arr_field_info = array(
			// '_edit'=>'操作',
			//'isRead'=>'状态',
			"Sender.realName" =>'发送人',
			"title" =>array('text'=>'标题','width'=>300),
			//"content" =>'内容',
			"dt" =>array('text'=>'发送日期','width'=>120),
		);
		FLEA::loadClass('TMIS_Pager');
		$arr = TMIS_Pager::getParamArray(array(
			'key'=>''
		));
		$condition=array();
		if($arr['key']!='') {
			$condition[] = array('title',"%{$arr['key']}%",'like');
		}
		$condition[]=array('accepterId',$_SESSION['USERID'],'=');
		$condition[]=array('timeRead','0000-00-00 00:00:00','=');
		$pager = new TMIS_Pager($this->_modelExample,$condition,'id desc');
		$rowset =$pager->findAll();
		//dump($rowset);exit;
		if(count($rowset)>0) foreach($rowset as & $v) {
			///////////////////////////////

				$v['content']=$this->cSubstr($v['content'],0,20).".........";
				$v['_edit'] = $this->getRemoveHtml($v['id']);
				
				if($v['title']=='') $v['title']='无标题';
				$v['title']="<a href='".$this->_url('ViewDetails',array(
					'id'=>$v['id'],
					'TB_iframe'=>1,
					'no_edit'=>1
					))."' class='thickbox' title='查看邮件内容'>{$v['title']}</a>";

				if($v['timeRead']=='0000-00-00 00:00:00' || $v['timeRead']==''){
				    $v['title']=$v['title']."<font color='red'>(未查看)</font>";

				}
			}
		$smarty = & $this->_getView();
		$smarty->assign('title', $title);
		$smarty->assign('arr_field_info',$arr_field_info);
		$smarty->assign('arr_field_value',$rowset);
		$smarty->assign('arr_condition', $arr);

		$smarty->assign('arr_js_css',$this->makeArrayJsCss(array('calendar')));
		$smarty->assign('page_info',$pager->getNavBar($this->_url($_GET['action'],$arr)));
		$smarty->display($tpl);
	}

	function Add(){
	    $this->authCheck('8-1');
	}

	function _edit($Arr) {
		//dump($_GET);exit;
		//邮件回复功能
		if($_GET['isBack']==1){
			$res=$this->_modelExample->find(array('id'=>$_GET['mailId']));
			$accepterId="<input type='hidden' name='userId[]' id='userId[]' value='{$res['senderId']}'>";
			$Arr['title']='[回复]：'.$res['title'];
			$Arr['content']="<br/><br/><br/><br/><br/><br/><br/><br/><hr><span style='color:#e53333;'>".$res['dt'].' '.$res['Sender']['realName'].' '.$res['title']."中写到：</span><br>".$res['content'];
			$Arr['accepter']="<div class='parDiv'><div class='userDiv'>".$res['Sender']['realName']."</div><div class='colDiv'></div></div>";

			//全部回复
			if($_GET['isAll']==1){
				$temp=$this->_modelExample->findAll(array('mailCode'=>$res['mailCode']));
				foreach($temp as & $v){
					if($_SESSION['USERID']!=$v['accepterId']){
						$accepterId.="<input type='hidden' name='userId[]' id='userId[]' value='{$v['accepterId']}'>";
						$Arr['accepter'].="<div class='parDiv'><div class='userDiv'>".$v['Accepter']['realName']."</div><div class='colDiv'></div></div>";
					}
				}
			}
			
		}
		// dump($Arr);exit;
		$tpl = 'MailEdit.tpl';
		$smarty = & $this->_getView();
		$smarty->assign('aRow',$Arr);
		$smarty->assign('hidden',$accepterId);
		$smarty->display($tpl);
	}

	function actionSave() {
		// dump($_POST);exit;
	    $sender=$_SESSION['USERID'];
	    $dt=date("Y-m-d H-i-s");
	     $url=$_POST['fromAction']==''?'Add':'right';
		//用于标记同一次发送的邮件，2012-11-6 by li
		$code=$this->getNewCode();
	    foreach($_POST['userId'] as $key=>$v){
			$rows[]=array(
				'id'=>$_POST['id'],
				'senderId'=>$sender,
				'accepterId'=>$_POST['userId'][$key],
				'title'=>$_POST['title'],
				'content'=>$_POST['content'],
				'dt'=>$dt,
				'mailCode'=>$code,
		    );
	    }
	    //dump($rows);exit;
	    if($rows){
		$this->_modelExample->saveRowset($rows);
	    }
	   
	    js_alert('','window.parent.showMsg("发送成功")',$this->_url($url));
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

	function actionViewDetails(){
	    $row=$this->_modelExample->findAll(array('id'=>$_GET['id']));
	    //dump($row);exit;
		//查找所有收件人
		$temp=$this->_modelExample->findAll(array('mailCode'=>$row[0]['mailCode']));
		$accepter=array();
		foreach($temp as & $v){
			//标记本人
			if($_SESSION['USERID']==$v['Accepter']['id'])$v['Accepter']['mark']='1';
			$accepter[$v['Accepter']['id']]=$v['Accepter'];
		}
		//dump($accepter);exit;
	    if($row){
			$dt=date("Y-m-d H-i-s");
			$arr=array(
				'id'=>$_GET['id'],
				'timeRead'=>$dt
			);
			if($arr)
			//dump($arr);exit;
			$this->_modelExample->save($arr);
	    }


	    $smarty = & $this->_getView();
	    $smarty->assign('title','查看邮件');
	    $smarty->assign("row", $row[0]);
		$smarty->assign("accepter", $accepter);
	    $smarty->display('mailViewDetails.tpl');
	}
	function actionRight1() {
		$title = '发件箱列表';
		$tpl = 'TblList.tpl';
		$sonTpl="MailRemove.tpl";
		$arr_field_info = array(
			'_edit'=>array('text'=>"<input type='checkbox' id='sel' ext:qtip='全选/反选'>",'width'=>40),
			"Accepter.realName" =>'收件人',
			"title" =>array('text'=>'标题','width'=>300),
			//"content" =>'内容',
			"dt" =>array('text'=>'发送日期','width'=>160),
		);

		///////////////////////////////模块定义
		 $this->authCheck('8-3');
		FLEA::loadClass('TMIS_Pager');
		$arr = TMIS_Pager::getParamArray(array(
			'dateFrom'=>date('Y-m-01'),
			'dateTo'=>date('Y-m-d'),
			'key'=>''
		));
		$condition=array();
		if($arr['key']!='') {
			$condition[] = array('title',"%{$arr['key']}%",'like');
		}
		if($arr['dateFrom'] != '') $condition[] = array('dt',$arr['dateFrom']." 00:00:00",'>=');
		if($arr['dateTo'] != '') $condition[] = array('dt',$arr['dateTo']." 23:59:59",'<=');
		$condition[]=array('senderId',$_SESSION['USERID'],'=');
		$pager = new TMIS_Pager($this->_modelExample,$condition,'id desc');
		$rowset =$pager->findAll();
		//dump($rowset);exit;
		if(count($rowset)>0) foreach($rowset as & $v) {
			///////////////////////////////
				$v['content']=$this->cSubstr($v['content'],0,20).".........";
				$v['_edit']="<input type='checkbox' name='editBtn[]' value='{$v['id']}'>";
				// $v['_edit'] = $this->getRemoveHtml($v['id']);
				$v['title']="<a href='".$this->_url('ViewDetails',array(
					'id'=>$v['id'],
					'TB_iframe'=>1,
					'no_edit'=>1
					))."' class='thickbox' title='查看邮件内容'>{$v['title']}</a>";
			}
		$smarty = & $this->_getView();
		$smarty->assign('title', $title);
		$smarty->assign('arr_field_info',$arr_field_info);
		$smarty->assign('arr_field_value',$rowset);
		$smarty->assign('arr_condition', $arr);
		$smarty->assign('sonTpl',$sonTpl);
		$smarty->assign('other_url',"<a href='javascript:void(0);' id='remove'><span class='glyphicon glyphicon-trash'></span> 删除</a>");
		/*///////////////grid,thickbox,///////////////*/
		// $smarty->assign('arr_js_css',$this->makeArrayJsCss(array('calendar')));
		$smarty->assign('page_info',$pager->getNavBar($this->_url($_GET['action'],$arr)));
		$smarty->display($tpl);
	}

	function getNewCode(){
			$sql="select * from mail_db order by mailCode desc limit 0,1";
			$res=mysql_fetch_assoc(mysql_query($sql));
			return $res['mailCode']+1;
	}

	function actionRemoveByAjax(){
		if($_GET['id']=='')$_GET['id']='null';
		$sql="delete from mail_db where id in({$_GET['id']})";
		$success=$this->_modelExample->execute($sql);
		echo json_encode(array('success'=>$success,'msg'=>'删除失败'));exit;
	}

}
?>