<?php
FLEA::loadClass('FLEA_Helper_Pager');
class TMIS_Pager extends FLEA_Helper_Pager {
	//是否跳过搜索已定义的session;true为跳过
	var $skipSession = false;
	var $pageList = array('25','50','75','100');

	function TMIS_Pager(& $source, $conditions = null, $sortby = null, $pageSize=0, $currentPage=-1) {
		//以get方式提交page,正常模式
		//如果session中的搜索条件存有page,改变页数
		$cGet = strtolower($_GET['controller']);
		$aGet = strtolower($_GET['action']);
		$_k = $cGet.','.$aGet;
		if(intval($currentPage) < 0) $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : (
			isset($_SESSION['SEARCH'][$_k]['page'])? $_SESSION['SEARCH'][$_k]['page'] : 0
		);

		if(isset($_SESSION['SEARCH'][$_k]['pageSize'])) $pageSize = $_SESSION['SEARCH'][$_k]['pageSize'];
		if(isset($_GET['pageSize'])) $pageSize = $_GET['pageSize'];

		if (empty($pageSize)) $pageSize = FLEA::getAppInf('pageSize');

		if ($sortby==null) $sortby = is_object($source)? ($source->primaryKey.' desc') : 'id desc';
		//dump($sortby);exit;
		//$currentPage = isset($_POST[start]) ? (int)($_POST[start]/$pageSize) : 0;

		//在extjs中以$_POST[start],$_POST[limit],$_POST[sort],$_POST[dir]=asc||desc提交
		//$currentPage = isset($_POST[start]) ? (int)($_POST[start]/$pageSize) : 0;
		//下面为extjs使用时的语句，暂时去掉。
		//$sortby = (isset($_POST[dir]) && isset($_POST[sort])) ? ("$_POST[sort] $_POST[dir]") : ($source->primaryKey . " desc");
		parent::FLEA_Helper_Pager($source, $currentPage, $pageSize, $conditions, $sortby);
	}

	/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms
	$RCSfile: global.func.php,v $
	$Revision: 1.83.2.7 $
	$Date: 2006/10/27 08:08:18 $
	$num记录总数,
	$perpage每页记录数,
	$curpage当前页码，
	$mpurl跳转url，
	$maxpages允许查看最大页码。
	$page每屏允许显示的页数
	$pages 实际页数
	$offset 偏移数
	*/
	function getNavBar($mpurl=null) {
		$multipage = '';
		$num = $this->count;
		if($num==0) return "未发现记录！";
		// dump($mpurl);exit;
		if($mpurl==null) {
			$mpurl=url($_GET['controller'],$_GET['action']);
			// dump($mpurl);exit;
		}
		$mpurl .= strpos($mpurl, '?') ? '&' : '?';

		$curpage = $this->currentPage;
		$perpage = $this->pageSize;

		$mpurl.= "pageSize=".$perpage;
		$mpurl .= strpos($mpurl, '?') ? '&' : '?';

		$maxpages = 0;
		if($num >0) {
			$page = 8; #每屏允许显示的页数
			$offset = 2;#偏移量
			$pages = $this->pageCount;
			if($page > $pages) {//总页数不足每屏显示个数
				$from = 1;
				$to = $pages;
			} else {//总页数多于每屏显示个数
				$from = $curpage - $offset + 1;
				$to = $from + $page - 1;
				if($from < 1) {
					$to = $curpage - $from + 1;
					$from = 1;
					if($to - $from < $page) {
						$to = $page;
					}
				} elseif($to > $pages) {
					$from = $pages - $page + 1;
					$to = $pages;
				}
			}
			$multipage = ($curpage > $offset && $pages > $page ? '<a class="p_redirect" href="'.$mpurl.'page='.$this->firstPage.'">|‹</a>' : '').
				($curpage > 0 ? '<a class="p_redirect" href="'.$mpurl.'page='.$this->prevPage.'">‹‹</a>' : '');
			for($i = $from; $i <= $to; $i++) {
				$multipage .= $i == $curpage + 1 ? '<a class="p_curpage">'.$i.'</a>' :
					'<a href="'.$mpurl.'page='.($i-1).'" class="p_num">'.$i.'</a>';
			}
			$multipage .= ($curpage+1 < $pages ? '<a class="p_redirect" href="'.$mpurl.'page='. $this->nextPage .'">››</a>' : '').
				($to < $pages ? '<a class="p_redirect" href="'.$mpurl.'page='. $this->lastPage .'">›|</a>' : '').
				($curpage+1 == $maxpages ? '<a class="p_redirect" href="misc.php?action=maxpages&pages='.$maxpages.'">›?</a>' : '').
				($pages > $page ? '<a class="p_pages" style="padding: 0px">
			   					<input class="p_input" type="text" name="custompage"
								onKeyDown="if(event.keyCode==13) {window.location=\''.$mpurl.'page=\'+(this.value-1); return false;}"></a>' : '');
			$multipage = $multipage ? '<div class="p_bar"><a class="p_total"> '.$num.' </a><a class="p_pages"> '.$this->currentPageNumber.'/'.$this->pageCount.' </a>'.$multipage.'</div>' : '';

			$selectList="<select class='p_pager_list' onchange=\"window.location='".$mpurl."pageSize='+(this.value); return false;\">";
			foreach ($this->pageList as & $v) {
				$selectList.="<option value='{$v}' ".($perpage==$v ? 'selected' :'').">{$v}</option>";
			}
			$selectList .= "</select>";

			$multipage .=$selectList;
		}
		return $multipage;
	}

	//处理查询参数,在查询结果分页时需要用到，
	//arr = array('key'=>'value','key1'=>'value1');
	//key需要传递的变量名称，
	//value是需要传递的变量的值
	//2012-12-21 update by jeff,
	//搜索条件形成关联数组，不在需要oldSearch,不用再调用keepcondition
	//为了防止session爆炸，默认只保留10个不同页面的搜索条件
	function getParamArray($arr) {
		//如果GET中传递的no_edit不为空，则跳过session
		if($_GET['no_edit']==1) TMIS_Pager::setSkipSession(true);
		$max = 10;//最多保留10个页面的搜索条件
		$ret = array();
		$cGet = strtolower($_GET['controller']);
		$aGet = strtolower($_GET['action']);
		$_k = $cGet.','.$aGet;
		if($_SESSION['SEARCH'][$_k] && !$this->skipSession) $con = & $_SESSION['SEARCH'][$_k];

		//将搜索条件保存在session中
		//如果session中原来没有,且第一次进入，不需要创建session
		if ($_SERVER['REQUEST_METHOD']=='POST') {//如果是提交，销毁后重建search
			$con = array(
				'controller'=>$cGet,
				'action'=>$aGet
			);

			foreach ($arr as $key=>$value) {
				//&& $_POST[$key]!=$arr[$key]去掉，因为form的action中可能存在相关get变量，会覆盖post过来的空变量
				//if (isset($_POST[$key]) && $_POST[$key]!=$arr[$key]) $con[$key] = $_POST[$key];
				if (isset($_POST[$key])) $con[$key] = $_POST[$key];
			}
			//dump($_POST);dump($arr);dump($con);exit;

		} else {//非提交：分页或者打开
			if(!$con) {
				$con = array(
					'controller'=>$cGet,
					'action'=>$aGet
				);
			}
			//保存分页信息
			if(isset($_GET['page'])) $con['page'] = (int)$_GET['page'];
			if(isset($_GET['pageSize'])) $con['pageSize'] = (int)$_GET['pageSize'];

			//因为有需要根据arr_condition设置搜索项目，所以必须所有的$arr都设置值
			foreach ($arr as $key=>$value) {
				if($this->skipSession) break;
				if (isset($_GET[$key])  && $_GET[$key]!=$arr[$key])	$con[$key] = $_GET[$key];
			}
		}

		//dump($_k);exit;
		//不跳过session才应该保存session
		if(!$_SESSION['SEARCH'][$_k] && $con && !$this->skipSession) {
			$_SESSION['SEARCH'][$_k]= & $con;
		}
		//unset($_SESSION['SEARCH']['caiwu_duizhang_pantouduizhang,setdanjia']);
		//

		//构造返回值
		foreach ($arr as $key=>& $value) {
			$ret[$key] = isset($con[$key]) ? $con[$key] : (isset($_GET[$key]) ? $_GET[$key] : rawurldecode($value));
		}

		//去掉首尾空格
		foreach ($ret as $key=> & $value) {
			$ret[$key] = trim($ret[$key]);
		}

		if(!$this->skipSession) return $ret;
		//如果session中保存的搜索条件超过$max,去掉多出的部分
		//要保证去掉的不是$con
		$cnt = count($_SESSION['SEARCH'])-$max;
		if($cnt<=0) return $ret;

		if($_SESSION['SEARCH']) {
			foreach($_SESSION['SEARCH'] as $key=>& $v) {
				if($cnt==0) break;
				if($key==$_k) continue;
				unset($_SESSION['SEARCH'][$key]);
				$cnt--;
			}
		}

		return $ret;
	}

	//设置是否跳过session。
	function setSkipSession($b) {
		$this->skipSession=$b;
	}

	//直接在session中开辟一个新空间保留前一个页面的搜索条件,当需要保留上一个页面的搜索条件时使用。
	//注意必须在getParamArray之前调用。
	function keepCondition() {
		return true;
		//2012-8-30 by jeff,调用keepCondition时，直接在session中开辟一个新空间保留前一个页面的搜索条件
		//只有当当前页面的controller和action<>session中的controller和action时才开辟，否则说明用户还是停留在子窗口进行操作，不应覆盖。
		if($_SESSION['SEARCH']['curController']==strtolower($_GET['controller']) && $_SESSION['SEARCH']['curAction']==strtolower($_GET['action'])) {
			return false;
		}
		$_SESSION['OLDSEARCH'] = $_SESSION['SEARCH'];
		return true;
	}

	#清空session中的搜索项目,
	function clearCondition() {
		session_unregister('SEARCH');
		$_SESSION['SEARCH'][curController]=$_GET[controller];
	}



	//除page外其他所有的参数形成queryString
	//paramArr为
	function getParamStr($paramArr) {
		$controller = $_GET[controller];
		$action = $_GET[action];
		$ret = "controller=".$controller."&action=".$action;
		if (count($paramArr)>0) {
			foreach($paramArr as $key=>$value) {
				if ($value!='') $ret .= "&$key=".rawurlencode($value);
			}
		};
		return $ret;
	}

	//使用时注意初始话pager必须以sql语句作为第一个参数
	function findAllBySql($sql) {
		/*FLEA::loadClass('TMIS_TableDataGateway');
		$count = TMIS_TableDataGateway::findCountBySql($sql);
		$dbo = FLEA::getDBO(false);
		$this->setDBO($dbo);
		$this->setCount($count);*/
		return $this->findAll();
	}
}
?>
