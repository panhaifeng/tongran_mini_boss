<?php
FLEA::loadClass('TMIS_Controller');
class Controller_Sys_Log extends TMIS_Controller {
    function __construct() {
        $this->_modelExample = & FLEA::getSingleton('Model_Sys_Log');
    }

    // 订单查询
    function actionRight(){
        $this->authCheck(0);

        $searchItems = array(
            'dateRange'  =>array(date('Y-m-01'),date('Y-m-d')),
            'primaryKey' =>'',
            'logcontent' =>'',
            'userName'   =>'',
            'key'        =>'',
        );
        $smarty = & $this->_getView();
        $arrFieldInfo = array(
            'time'         =>array('text'=>'时间','width'=>'160'),
            'id'           =>array('text'=>'#id','width'=>'100'),
            'userName'     =>array('text'=>'用户名','width'=>''),
            'realName'     =>array('text'=>'用户姓名','width'=>''),
            "model"        =>array('text'=>'model模型','width'=>'200'),
            "mdlName"      =>array('text'=>'操作说明','width'=>'150'),
            "ip"           =>array('text'=>'ip','width'=>''),
            // "pcName"       =>array('text'=>'电脑名称','width'=>''),
            'primaryKey'   =>array('text'=>'主键字段','width'=>''),
            'primaryValue' =>array('text'=>'主键值','width'=>''),
            // 'log'          =>array('text'=>'查看详细','isHtml'=>true)
        );
        $smarty->assign('title', '系统日志列表');
        $smarty->assign('arr_field_info', $arrFieldInfo);
        $smarty->assign('action', $this->_url('GetRows'));
        $smarty->assign('searchItems', $searchItems);
        $smarty->assign('colsForKey', array(
              array('text'=>'关键字','col'=>'key'),
              array('text'=>'用户名','col'=>'userName'),
              array('text'=>'主键值','col'=>'primaryKey'),
              array('text'=>'日志内容','col'=>'logcontent'),
        ));

        //定义详细信息展开自定义模版
        $smarty->assign('optExpand',array(
          //展开面板type,可以是
          //comp-expand-form 普通表单形式的面板
          //comp-expand-tabs 带tab效果的展开面板
          'type'=>'comp-expand-tabs',
          //每个tab中组件参数
          'options'=>array(
            //form参数
            array(
              'type'=>'form',
              'title'=>'日志详细',
              'options'=>array(
                'formItems'=>array(
                  'log'  => array('text'=>'','isHtml'=>true)
                ),
              )
            ),
          ),
        ));

        //右上角高级功能菜单
        if(defined('NEED_DB_LOG_TIME') && NEED_DB_LOG_TIME > 0){
            $timeDay = NEED_DB_LOG_TIME;
        }else{
            $timeDay = 30;
        }

        $rightMenu = array(
            array('text'=>"删除".NEED_DB_LOG_TIME."天前日志",'name'=>'btnClearLog')
        );
        $smarty->assign('menuRightTop', $rightMenu);
        $smarty->assign('sonTpl', 'Sys/Log.js');

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

        $pagesize    = $_POST['pagesize'];
        $currentPage = $_POST['currentPage'];
        $keyField    = isset($_POST['colForKey']) ? $_POST['colForKey'] : 'key';
        //处理搜搜
        $_POST[$keyField] = $_POST['key'];
        if($keyField!='key') $_POST['key'] = '';

        $arr = $_POST;

        $sql = "select id from sys_log where 1";
        if($arr['userName']){
            $sql .= " and userName like '%{$arr['userName']}%'";
        }

        if($arr['logcontent']){
            $sql .= " and log like '%{$arr['logcontent']}%'";
        }
        if($arr['dateRange']){
            list($datefrom ,$dateTo) = $arr['dateRange'];
            $datefrom = strtotime($datefrom);
            $dateTo = strtotime($dateTo.' 23:59:59');
            $sql .= " and time >= '{$datefrom}' and time <= '{$dateTo}'";
        }
        if($arr['primaryKey']){
            $sql .= " and primaryValue = '{$arr['primaryKey']}'";
        }
        if($arr['key']){
            $sql .= " and (realName like '%{$arr['key']}%' or model like '%{$arr['key']}%' or mdlName like '%{$arr['key']}%' or log like '%{$arr['key']}%')";
        }
        $sql .= " order by time desc,id desc";
        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = & new TMIS_Pager($sql,null,null,$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();
        // dump($rowset);exit;

        foreach($rowset as & $v){
            //再次查询分页后的数据
            $tmp = $this->_modelExample->find($v['id']);
            $v = array_merge($v ,$tmp);

            $v['time'] = date('Y-m-d H:i:s' ,$v['time']);
            $v['log'] = "<pre style='line-height:1.2;'>".print_r(unserialize($v['log']) ,1)."</pre>";
        }

        $ret = array(
          'total'   =>$pager->totalCount,
          'columns' =>array(),
          'rows'    =>$rowset,
        );
        echo json_encode($ret);exit;
    }

    //查询日志
    function actionDetial(){
        $this->authCheck();
        $row = $this->_modelExample->find($_GET['id']);
        $log = unserialize($row['log']);
        dump($log);
    }

    //删除日志
    function actionClearLog(){
        //删除
        $this->_modelExample->clearLog();

        //判断是否跳转
        if(!isset($_GET['auto'])){
            js_alert('操作完成','',$this->_url('Right'));
            exit;
        }else{
            echo json_encode(array('status'=>"SUCCESS"));
            exit;
        }
    }


}
?>