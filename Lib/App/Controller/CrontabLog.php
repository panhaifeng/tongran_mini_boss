<?php
FLEA::loadClass('TMIS_Controller');
class Controller_CrontabLog extends TMIS_Controller{
    function __construct() {
        $this->_modelExample = FLEA::getSingleton('Model_Crontab');
    }

    /**
     * 日志查看工具
     * Time：2015/10/27 09:44:25
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function actionRight(){
        $this->authCheck(0);

        $searchItems = array(
            'key'    =>'',
            'isOver' =>'',
        );
        $smarty = & $this->_getView();
        $arrFieldInfo = array(
            "runtime"     => array('text'=>"计划执行时间",'width'=>'160'),
            'createtime'  =>array('text'=>"任务创建时间",'width'=>'160'),
            "result"      => array('text'=>"执行结果",'width'=>'100'),
            // "id"          => "id",
            "description" => array('text'=>"任务描述",'width'=>'160'),
            "action"      => array('text'=>"执行地址",'width'=>''),
            "enabled"     => array('text'=>"是否启用",'width'=>''),
            "schedule"    => array('text'=>"时间规则",'width'=>'120'),
            "updatetime"  => array('text'=>"实际执行时间",'width'=>''),
            'param'       =>array('text'=>"参数",'width'=>''),
            'response'    =>array('text'=>"执行结果描述",'width'=>''),
        );
        $smarty->assign('title', '计划查询');
        $smarty->assign('arr_field_info', $arrFieldInfo);
        $smarty->assign('action', $this->_url('GetRows'));
        $smarty->assign('searchItems', $searchItems);
        $smarty->assign('colsForKey', array(
              array('text'=>'关键字','col'=>'key')
        ));
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

        $_POST['sortOrder'] = $_POST['sortOrder']=='descending' ? 'desc' : 'asc';
        $_POST['sortBy']    = $_POST['sortBy']=='' ? 'runtime' : $_POST['sortBy'];

        $pagesize           = $_POST['pagesize'];
        $currentPage        = $_POST['currentPage'];
        $key                = $_POST['key'];
        $isOver             = $_POST['isOver'];
        $keyField           = isset($_POST['colForKey'])?$_POST['colForKey']:'compName';
        $from               = ($currentPage-1)*$pagesize;

        $arr = array();

        $key && $condition[]=array('description',"%{$key}%",'like');
        $orderby = " {$_POST['sortBy']} {$_POST['sortOrder']}";
        if(strval($isOver)=='0'){
            $condition[] = array('result',"未执行",'=');
        }elseif(strval($isOver)=='1'){
            $condition[] = array('result',"未执行",'<>');
        }


        // $rowset = $this->_modelExample->findAll($condition ,$orderby ,array($pagesize ,$from));
        FLEA::loadClass('TMIS_Pager');
        $pager = new TMIS_Pager($this->_modelExample,$condition,$orderby ,$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();
        // dump($rowset);exit;

        foreach($rowset as &$v) {
            $v['runtime'] = date('Y-m-d H:i:s',$v['runtime']);
            $v['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
            $v['updatetime'] = $v['updatetime'] ? date('Y-m-d H:i:s',$v['updatetime']) : '';

            $v['enabled'] = $v['enabled']==1?'是':'否';

            $v['param'] = print_r(unserialize($v['param']),1);
            $v['response'] = print_r(unserialize($v['response']),1);
        }

        $ret = array(
          'total'   =>$pager->totalCount,
          'columns' =>array(),
          'rows'    =>$rowset,
        );
        echo json_encode($ret);exit;
    }
}
?>