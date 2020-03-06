<?php
FLEA::loadClass('TMIS_Controller');
class Controller_Acm_Log extends Tmis_Controller  {

    function Controller_Acm_Log() {
        $this->_modelLog = FLEA::getSingleton('Model_Acm_Log');
    }

    /**
     * @desc ：操作日志查询
     * Time：2016/04/06 14:17:25
     * @author Wuyou
    */
    function actionRight(){
        // dump(1);exit;
        $tpl = 'TblList.tpl';
        FLEA::loadClass('TMIS_Pager');
        $arr = TMIS_Pager::getParamArray(array(
            'dateFrom'   => date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")-1)),
            'dateTo'     =>date('Y-m-d'),
            'realName'   =>'',
            'moduleName' =>'',
        ));
        $sql = "SELECT *
                from sys_log
                where 1";
        if($arr['dateFrom'] != ''){
            $sql .= " and left(dt,10) >= '{$arr['dateFrom']}' and left(dt,10) <= '{$arr['dateTo']}'";
        }
        if($arr['moduleName'] != 0){
            $sql .= " and kind like '%{$arr['moduleName']}%'";
        }
        if($arr['realName'] != ''){
            $sql .= " and realName like '%{$arr['realName']}%'";
        }
        $sql .= " order by dt DESC";
        // dump($sql);exit;
        $pager =& new TMIS_Pager($sql);
        $rowset =$pager->findAll();

        $arrFieldInfo = array(
            "realName"     =>"操作人",
            "ip"           =>"ip",
            "pcName"       =>"电脑名称",
            "kind"         =>"模块类型",
            "memo"         =>array('text'=>"操作内容",'width'=>220),
            "primaryValue" =>"主键值",
            "dt"           =>array('text'=>"操作时间",'width'=>150),
        );

        $smarty = & $this->_getView();
        $smarty->assign("title","日志查看");
        $smarty->assign('arr_condition', $arr);
        $smarty->assign("arr_field_info",$arrFieldInfo);
        $smarty->assign("arr_field_value",$rowset);
        $smarty->assign("add_display",'none');
        $smarty->assign("page_info",$pager->getNavBar($this->_url($_GET['action'])));
        $smarty->display($tpl);
    }



}

?>