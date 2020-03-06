<?php
class TMIS_ErrorSys{
	/**
	* 判断是否需要显示错误信息，如果定义为不显示错误信息，则显示一个已经定义的有好界面，否则显示错误信息
	* @author li
	* _page:需要跳转的界面
	* _msg: 需要显示的提示信息
	*/
	
	function TMIS_ErrorSys($view = '' , $msg = '捕获到异常…' , $page = 'Building.php'){
		$this->_page = $page;
		$this->_msg = $msg;
		$this->_view = $view;
	}

	//定义处理方法
	function jumpToPage(){
		if(!ERROR_SYS){
			TMIS_ErrorSys::setUrl();
			//转向
			header('location:'.$this->_page);exit;
		}
	}

	//处理特殊情况的问题
	function _die($view,$msg,$isDie=ERROR_SYS){
		if($isDie){
			die($view);
		}else{
			$error=new TMIS_ErrorSys($view,$msg);
			$error->jumpToPage();
		}
	}

	//处理跳转页面
	/*function setPage($page){
		$this->_page = $page;
	}

	function setMsg($msg){
		$this->_msg = $msg;
	}

	function setView($view){
		$this->_view = $view;
	}*/

	function setUrl(){
		$url=$this->_page."?msg=".rawurlencode($this->_msg)."&view=".rawurlencode($this->_view);
		// echo $url;exit;
		$this->_page=$url;
	}
}
?>