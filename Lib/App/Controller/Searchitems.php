<?php
FLEA::loadClass('TMIS_Controller');
class Controller_Searchitems extends TMIS_Controller {

  function __construct() {
    // parent::__construct();
  }

  //根据传入的参数,返回搜索条件的组件参数
  // http://localhost/vue-element/index.php?controller=searchitems&action=getcomps
  function actionGetComps() {
    include('Config/Searchitems_config.php');
    $requestParam = file_get_contents('php://input');
    $_POST = json_decode($requestParam,true);
    // dump($_POST);exit;
    $items = $_POST['items'];

    $ret = array();
    foreach($items as $key=>&$v) {
      if(!isset($search_items_config[$v])) continue;
      $tmpItem = $search_items_config[$v];
      /*if(is_array($v) && $v){
        $tmpItem = array_merge($tmpItem ,$v);
      }else{
        $tmpItem['value'] = $tmpItem;
      }*/
      $ret[] = $tmpItem;
    }

    //option处理
    foreach($ret as &$v){
      //处理需要获取数据的方法
      if(isset($v['funcName']) && $v['funcName'] != ''){
        list($className ,$method) = explode('@',$v['funcName']);
        $classObject = FLEA::getSingleton($className);
        $v['options'] = $classObject->$method();
      }
    }

    echo json_encode(array(
      'success'=>true,
      'items'=>$ret
    ));
    exit;
  }


}
?>