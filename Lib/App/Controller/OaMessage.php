<?php
FLEA::loadClass('TMIS_Controller');
class Controller_OaMessage extends TMIS_Controller {
	var $_modelExample;
	var $_modelAcmOa;
	var $title = "通知";

	function Controller_OaMessage() {
		/**
		 * if(!//$this->authCheck()) die("禁止访问!");
		 */
		$this->_modelExample = &FLEA::getSingleton('Model_OaMessage');
		$this->_modelAcmOa = &FLEA::getSingleton('Model_Acm_User2message');
		$this->_modelClass = &FLEA::getSingleton('Model_OaMessageClass'); 
		// $this->_modelEmploy = & FLEA::getSingleton('Model_Jichu_Employ');

		$this->fldMain = array(
			'id'=>array('title'=>'','type'=>'hidden','value'=>''),
        	'title'=>array('title'=>'标题','type'=>'text','value'=>''),
        	'kindName'=>array('title'=>'类别','type'=>'select','options'=>$this->_modelClass->getOptions()),
        	'buildDate' => array('title' => '发布日期', 'type' => 'calendar', 'value' => date('Y-m-d')),
        	'content'=>array('title'=>'内容','type'=>'textarea','value'=>''),
        );

        $this->rules = array(
			'title'=>'required',
			'kindName'=>'required',
			'buildDate'=>'required'
		);

	}
	function actionRight() {
		$this->authCheck();
		// dump($_GET['kind']);exit;
		$title = '通知列表'; 
		// /////////////////////////////模板文件
		$tpl = 'TblList.tpl'; 
		// /////////////////////////////表头
		$arr_field_info = array('_edit' => '操作',
			"kindName" => '类别',
			'buildDate' => '日期',
			"title" => array('text' => '标题', 'width' => 200), 
			// "content" =>'内容',
			"creater" => '发布人'
			); 
		// /////////////////////////////模块定义
		// $this->authCheck(42);
		FLEA::loadClass('TMIS_Pager');
		$arr = TMIS_Pager::getParamArray(array('dateFrom' => date('Y-m-01'),
				'dateTo' => date('Y-m-d'),
				'key' => ''
				));
		$condition = array(
			array('kindName', "订单变动通知", '<>')
			);
		if ($arr['key'] != '') {
			$condition[] = array('title', "%{$arr['key']}%", 'like');
		}
		if ($arr['dateFrom'] != '') $condition[] = array('buildDate', $arr['dateFrom'], '>=');
		if ($arr['dateTo'] != '') $condition[] = array('buildDate', $arr['dateTo'], '<=');
		$pager = &new TMIS_Pager($this->_modelExample, $condition, 'id desc');
		$rowset = $pager->findAll(); 
		// dump($rowset);
		if (count($rowset) > 0) foreach($rowset as &$v) {
			$content = strip_tags($v['content']);
			$v['content'] = $this->cSubstr($content, 0, 20) . "...";
			$v['_edit'] = $this->getEditHtml($v['id']) . '&nbsp;&nbsp;' . $this->getRemoveHtml($v['id']);
			if ($v['kindName'] != '订单变动通知') {
				$str = "SELECT count(*) as cnt FROM `acm_user2message` x
					left join oa_message y on y.id=x.messageId
					where x.messageId='{$v['id']}' and x.userId='{$_SESSION['USERID']}' and y.kindName!='订单变动通知' and x.kind=0"; 
				// echo $str;exit;
				$re = $this->_modelAcmOa->findBySql($str);

				$v['title'] = "<a href='" . url('main', 'tzViewDetails', array('id' => $v['id'],
						'TB_iframe' => 1,
						'no_edit' => 1
						)) . "' class='thickbox' title='查看{$v['kindName']}'>{$v['title']}</a>";
				if ($re[0]['cnt'] == 0) {
					$v['title'] = $v['title'] . "<font color='red'>(未查看)</font>";
				}
			}
		}
		$smarty = &$this->_getView();
		$smarty->assign('title', $title);
		$smarty->assign('arr_field_info', $arr_field_info);
		$smarty->assign('arr_field_value', $rowset);
		$smarty->assign('arr_condition', $arr);

		$smarty->assign('arr_js_css', $this->makeArrayJsCss(array('calendar')));
		$smarty->assign('page_info', $pager->getNavBar($this->_url($_GET['action'], $arr)));
		$smarty->display($tpl);
	}

	function actionEdit(){
        $row = $this->_modelExample->find($_GET['id']);
		if ($row['kindName'] == '行政通知') {
			$sql = "SELECT count(*) as cnt FROM `acm_user2message` where messageId='{$_GET['id']}'";
			$rr = mysql_fetch_assoc(mysql_query($sql));
			if ($rr['cnt'] == 0) {
				$arr = array('userId' => $_SESSION['USERID'],
					'messageId' => $_GET['id'],
					); 
				// dump($arr);exit;
				$this->_modelAcmOa->save($arr);
			}
		} 

        $this->fldMain = $this->getValueFromRow($this->fldMain,$row);

        $smarty = & $this->_getView();
        $smarty->assign('fldMain',$this->fldMain);
        $smarty->assign('rules',$this->rules);
        $smarty->assign('title','发布通知');
        $smarty->assign('aRow',$row);
        $smarty->assign("sonTpl", 'OaMessageSonTpl.tpl');
        $smarty->display('Main/A1.tpl');
    }

    function actionAdd($Arr){
        $smarty = & $this->_getView();
        $smarty->assign('fldMain',$this->fldMain);
        $smarty->assign('title','发布通知');
        $smarty->assign('rules',$this->rules);
         $smarty->assign("sonTpl", 'OaMessageSonTpl.tpl');
        $smarty->display('Main/A1.tpl');
    }

	/*function _edit($Arr) {
		// dump($Arr);exit;
		// $this->authCheck('8');
		$tpl = 'OaMessageEdit.tpl';
		$smarty = &$this->_getView();
		$messageClass = $this->_modelClass->findAll();
		$smarty->assign('messageClass', $messageClass);
		$smarty->assign('aRow', $Arr);
		$smarty->display($tpl);
	}
	function actionEdit() {
		// dump($_GET);exit;
		// 如果是查看状态并且是行政通知类型，判断acm_user2message表中是否有这条数据
		$row = $this->_modelExample->find($_GET['id']);
		if ($row['kindName'] == '行政通知') {
			$sql = "SELECT count(*) as cnt FROM `acm_user2message` where messageId='{$_GET['id']}'";
			$rr = mysql_fetch_assoc(mysql_query($sql));
			if ($rr['cnt'] == 0) {
				$arr = array('userId' => $_SESSION['USERID'],
					'messageId' => $_GET['id'],
					); 
				// dump($arr);exit;
				$this->_modelAcmOa->save($arr);
			}
		} 
		// dump($row);exit;
		$this->_edit($row);
	}*/

	function actionSave() {
		$_POST['creater'] = $_SESSION['REALNAME'];
		$id = $this->_modelExample->save($_POST);
		if ($_POST['id'] == '')
			js_alert(null, 'window.parent.showMsg("保存成功！")', $this->_url('add'));
		else
			js_alert(null, 'window.parent.showMsg("保存成功！")', $this->_url('right'));
	}

	function changToHtml($val) { // 将特殊字元转成 HTML 格式
		$val = htmlspecialchars($val);
		$val = str_replace("\011", ' &nbsp;&nbsp;&nbsp;', str_replace('  ', ' &nbsp;', $val));
		$val = ereg_replace("((\015\012)|(\015)|(\012))", '<br />', $val);
		return $val;
	}
	function cSubstr($str, $start, $len) { // 截取中文字符串
		$temp = "<span title='" . $str . "'>" . mb_substr($str, $start, $len, 'utf-8') . "</span>";
		return $temp;
	}
}

?>