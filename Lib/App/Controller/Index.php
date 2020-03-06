<?php
FLEA::loadClass('TMIS_Controller');
class Controller_Index extends TMIS_Controller {
    var $_modelExample;
	
	function Controller_Index() {
	}
	
	function actionIndex() {
		redirect(url('Login')); exit;
	}
	
		

	//调试模板专用 2011.3.18 by shi
	function actionTest() {
		$model = FLEA::getSingleton('Model_Jichu_Client');
	}
		
}
?>