<?php
FLEA::loadClass('TMIS_Controller');
class Controller_Api_Logs extends TMIS_Controller {
    var $funcId = '95-3';
    function __construct() {
        $this->_modelExample = FLEA::getSingleton('Model_Api_Logs');
    }


    /**
     * @desc ：员工档案查询
     * Time：2017/07/31 15:45:28
     * @author li
    */
    function actionRight() {
        $this->authCheck(0);

        $searchItems = array(
            'api_type'   =>'response',
            'log_status' =>'',
            'rpc_id'     =>'',
            'ip'         =>'',
            'key'        =>'',
        );
        $smarty = & $this->_getView();
        $arrFieldInfo = array(
            "apilog_id"     =>array('text'=>"apilog_id",'width'=>''),
            "rpc_id"        =>array('text'=>"rpc_id",'width'=>''),
            "version"       =>array('text'=>"版本号",'width'=>''),
            "status"        =>array('text'=>"状态",'width'=>''),
            "calltime"      =>array('text'=>"请求时间",'width'=>''),
            "createtime"    =>array('text'=>"结束时间",'width'=>''),
            "title"         =>array('text'=>"标题",'width'=>''),
            "last_modified" =>array('text'=>"最后重试时间",'width'=>''),
            "next_modified" =>array('text'=>"下次重试时间",'width'=>''),
            "retry"         =>array('text'=>"重试次数",'width'=>''),
            "ip"            =>array('text'=>"ip",'width'=>''),
        );

        $smarty->assign('title', 'API日志列表');
        $smarty->assign('arr_field_info', $arrFieldInfo);
        $smarty->assign('action', $this->_url('GetRows'));
        $smarty->assign('searchItems', $searchItems);
        $smarty->assign('colsForKey', array(
                array('text' =>'关键字','col'=>'key')
        ));

        // $smarty->assign('editButtons',array(
        //     // array('text'=>'编辑'),
        //     // array('text'=>'删除','isRemove'=>true,'removeUrl'=>$this->_url('RemoveAjax'))
        // ));

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
                  'params'        => array('text'=>'参数','isHtml'=>true),
                  'response_json' => array('text'=>'返回值','isHtml'=>true)
                ),
              )
            ),
          ),
        ));

        // $smarty->assign('addUrl',$this->_url('Add'));

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

        $post = $this->axiosPost();

        $pagesize    = $post['pagesize'];
        $currentPage = $post['currentPage'];

        $keyField    = isset($post['colForKey']) ? $post['colForKey'] : 'key';
        //处理搜搜
        $post[$keyField] = $post['key'];
        if($keyField!='key') $post['key'] = '';

        $arr = $post;

        $sql = "select apilog_id from api_logs where 1";
        if($arr['api_type']   !='') $sql .=" and api_type = '{$arr['api_type']}'";
        if($arr['log_status'] !='') $sql .=" and status = '{$arr['log_status']}'";
        if($arr['rpc_id']     !='') $sql .=" and rpc_id like '%{$arr['rpc_id']}%'";
        if($arr['ip']         !='') $sql .=" and ip like '%{$arr['ip']}%'";
        if($arr['key']        !='') $sql .=" and (params like '%{$arr['key']}%' or title like '%{$arr['key']}%')";
        $sql .= " order by calltime desc,apilog_id desc";
        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = & new TMIS_Pager($sql ,null,null,$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();
        // dump($rowset);exit;
        foreach($rowset as & $v){
            $tmp = $this->_modelExample->find($v['apilog_id']);
            $v = array_merge($v ,$tmp);

            // $v['id'] = $v['apilog_id'];
            // $v['__url']['编辑'] = $this->_url('Edit',array('id'=>$v['id']));
            $v['calltime']                             = date('Y-m-d H:i:s' ,$v['calltime']);
            $v['last_modified'] && $v['last_modified'] = date('Y-m-d H:i:s' ,$v['last_modified']);
            $v['createtime'] && $v['createtime']       = date('Y-m-d H:i:s' ,$v['createtime']);
            $v['next_modified'] && $v['next_modified'] = date('Y-m-d H:i:s' ,$v['next_modified']);

            $v['params'] = "<pre style='line-height:1.2;'>".print_r(unserialize($v['params']),1)."</pre>";
            $v['response_json'] = "<pre style='line-height:1.2;'>".print_r(json_decode($v['response_json'] ,1),1)."</pre>";
        }

        $ret = array(
          'total'   =>$pager->totalCount,
          'columns' =>array(),
          'rows'    =>$rowset,
        );
        echo json_encode($ret);exit;
    }


    /**
     * @desc ：重新调用日志
     * @author li 2015/09/21 09:52:41
     * @param 参数类型
     * @return 返回值类型
    */
    function actionRetrycall() {
        $apilog_id = intval($_GET['apilog_id']);
        $info = $this->_modelExample->find($apilog_id);
        $info['params'] = unserialize($info['params']);

        $service = FLEA::getSingleton('Api_Request');
        $result = $service->re_api_caller($info['params'] ,$info['rpc_id']);

        js_alert('','window.parent.showMsg("重试结束")',$this->_url('Right'));
    }



    //计划任务删除请求日之后的时间长的记录
    function clearLog(){
        $this->_modelExample->deleteLogs();
        return 'finish';
    }
}
?>