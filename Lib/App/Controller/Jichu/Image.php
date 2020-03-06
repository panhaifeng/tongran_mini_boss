<?php
/*********************************************************************\
*  Copyright (c) 2007-2015, TH. All Rights Reserved.
*  Author :li
*  FName  :Dingagent.php
*  Time   :2018/12/25 10:56:58
*  Remark :基础档案
\*********************************************************************/
FLEA::loadClass('TMIS_Controller');
class Controller_Jichu_Image extends TMIS_Controller {
    var $funcId = '95-1';
    /**
     * 构造函数
     * @var 参数类型
    */
    function __construct(){
        $this->_modelExample = FLEA::getSingleton('Model_Jichu_Image');
    }


    /**
     * 角色html配置form
     * Time：2018/12/13 15:49:40
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    public function buildHtml() {
        $params = array();
        $params['formItems'] = array(
            array(
                'type'      =>'comp-text',
                'name'      =>'proCode',
                'title'     =>'产品编号',
                'clearable' =>true,
                'value'     =>'',
                // 'addonEnd'  =>'留空系统自动生成',
            ),
            array(
                'type'      =>'comp-textarea',
                'name'      =>'memo',
                'title'     =>'其他说明',
                'clearable' =>true,
                'value'     =>'',
            ),
        );

        $params['rules'] = array(
            'proCode'=>array(
                array(
                    'required'=>true,
                    'message'=>'编号必须',
                )
            ),
        );

        return $params;
    }


    /**
     * @desc ：员工档案查询
     * Time：2017/07/31 15:45:28
     * @author li
    */
    function actionRight() {
        $this->authCheck($this->funcId);

        $searchItems = array(
            'key'    =>'',
        );
        $smarty = & $this->_getView();
        $arrFieldInfo = array(
            "id"        =>array('text'=>"#ID",'width'=>'60'),
            "path"      =>array('text'=>"图片路径",'width'=>''),
            "smallPath" =>array('text'=>"缩略图路径",'width'=>''),
            'preview'   =>array('text'=>"预览",'width'=>'70','isHtml'=>'component','componentType'=>'tip-preview-image'),
            "width"     =>array('text'=>"宽度width(px)",'width'=>'130'),
            "height"    =>array('text'=>"高度height(px)",'width'=>'130'),
            'time'      =>array('text'=>"时间",'width'=>''),

        );

        $smarty->assign('title', '图片列表');
        $smarty->assign('arr_field_info', $arrFieldInfo);
        $smarty->assign('action', $this->_url('GetRows'));
        $smarty->assign('searchItems', $searchItems);
        $smarty->assign('colsForKey', array(
                array('text' =>'关键字','col'=>'key')
        ));

        $smarty->assign('editButtons',array(
            array('text'=>'删除','type'=>'remove','icon'=>'el-icon-delete','options'=>array(
                'url'            =>$this->_url('RemoveAjax'),
                'disabledColumn' =>'__disabledRemove',
            )),
        ));

        // $smarty->assign('addUrl',$this->_url('Add'));
        $smarty->assign('sonTpl','Jichu/ImageTable.js');
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

        $sql="select x.* from jichu_image x where 1";

        if($arr['key']!='') {
            $sql.=" and (x.path like '%{$arr['key']}%' or x.smallPath like '%{$arr['key']}%')";
        }
        $sql .=" order by x.id desc";
        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        FLEA::loadClass('TMIS_Common');
        $pager = new TMIS_Pager($sql,null,null,$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();
        // dump($rowset);exit;
        foreach($rowset as & $v){
            $v['time'] = date('Y-m-d H:i:s' ,$v['time']);
            $v['imageSrc'] = $path = TMIS_Common::_imageSrc($v['path']);
        }

        $ret = array(
          'total'   =>$pager->totalCount,
          'columns' =>array(),
          'rows'    =>$rowset,
        );
        echo json_encode($ret);exit;
    }

    /**
     * 新增
     * Time：2018/12/17 13:35:37
     * @author li
    */
    function actionAdd() {
        $this->authCheck($this->funcId);
        $this->_edit();
    }

    /**
     * 编辑修改
     * Time：2018/12/17 13:36:35
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function actionEdit() {
        $this->authCheck($this->funcId);
        $row = $this->_modelExample->find($_GET['id']);

        $this->_edit($row);
    }

    function _edit($Arr = array()) {
        $formParams = $this->buildHtml();
        if(!$Arr){
            foreach ($formParams['formItems'] as $key => $v) {
                $Arr[$v['name']] = $v['value'].'';
            }
        }

        // dump($Arr);exit;

        $smarty = & $this->_getView();

        $smarty->assign('formItems',$formParams['formItems']);
        $smarty->assign('rules',$formParams['rules']);
        $smarty->assign('title','基本信息');
        $smarty->assign('row',$Arr);
        $smarty->assign('action',$this->_url('Save'));
        $smarty->display('MainForm.tpl');
    }

    /**
     * 档案保存
     * Time：2017/07/31 15:48:32
     * @author li
    */
    function actionSave() {
        $auth = $this->authCheck($this->funcId ,true);
        if(!$auth){
            $ret = array('msg'=>'登录权限过期','success'=>false);
            echo json_encode($ret);exit;
        }
        $post = $this->axiosPost();
        // dump($post);exit;

        //数据保存
        $result = $this->_modelExample->save($post);
        if($result){
            $ret = array(
                'success' =>true,
                'msg'     =>'操作成功',
            );
        }else{
            $ret = array(
                'success' =>false,
                'msg'     =>'操作失败',
            );
        }

        echo json_encode($ret);exit;
    }


    /**
     * 删除图片的时候需要判断图片是否被使用
     * Time：2019/01/11 12:44:11
     * @author li
    */
    function actionRemoveAjax(){
        $auth = $this->authCheck($this->funcId ,true);
        if(!$auth){
            $ret = array('msg'=>'登录权限过期或无权限','success'=>false);
            echo json_encode($ret);exit;
        }

        $post = $this->axiosPost();
        $id = intval($post['row']['id']);

        $res = $this->_removeImg($id ,$msg);

        if($res){
            $ret = array(
                'success' =>true,
                'msg'     =>'删除成功',
            );
        }else{
            $ret = array(
                'success' =>false,
                'msg'     =>'删除失败:'.$msg,
            );
        }
        echo json_encode($ret);exit;
    }


    /**
     * 删除某个图片
     * Time：2019/01/11 12:54:15
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function _removeImg($imageId ,& $msg){
        $imageId = intval($imageId);
        //查找是否已经被使用
        $array = array(
            'jichu_product'       => 'pic',
            'trade_order2product' => 'imageId',
        );

        $isOccupy = false;

        foreach ($array as $key =>  & $v) {
            $sql = "SELECT count({$v}) as cnt from {$key} where find_in_set({$imageId} ,{$v})";
            $tmp = $this->_modelExample->findBySql($sql);

            //如果某个表中存在，则不能删除
            if($tmp[0]['cnt'] > 0){
                $isOccupy = true;
                $msg = "图片被占用";
                break;
            }
        }

        //如果没有被占用，则删除
        if($isOccupy == false){
            $row = $this->_modelExample->find($imageId);
            $res = $this->_modelExample->removeByPkv($imageId);
            //表数据删除成功,删除实际图片
            if($res){
                if($row['path'] != '' && file_exists($row['path'])){
                    unlink(iconv('UTF-8','gb2312',$row['path']));
                }

                if($row['smallPath'] != '' && file_exists($row['smallPath'])){
                    unlink(iconv('UTF-8','gb2312',$row['smallPath']));
                }

                return true;
            }

            $msg = "数据表操作错误";
        }

        return false;
    }
}
?>