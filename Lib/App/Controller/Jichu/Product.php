<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  Author :wuyou
*  FName  :Product.php
*  Time   :2019/02/14 16:30:13
*  Remark :产品档案
\*********************************************************************/
FLEA::loadClass('TMIS_Controller');
class Controller_Jichu_Product extends TMIS_Controller {
    var $funcId = '90-4';
    /**
     * 构造函数
     * @var 参数类型
    */
    function __construct(){
        $this->_modelExample = FLEA::getSingleton('Model_Jichu_Product');
        $this->_modelKind = FLEA::getSingleton('Model_Jichu_ProKind');
    }

    /**
     * 导航进入产品大类
     * Time：2019/05/06 10:04:41
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function actionIndex(){
        $proKinds = $this->_modelKind->findAll();
        $smarty = & $this->_getView();
        $smarty->assign('title','产品档案');
        $smarty->assign('proKinds',$proKinds);
        $smarty->display('Jichu/ProductIndex.tpl');
    }


    /**
     * 角色html配置form
     * Time：2018/12/13 15:49:40
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    public function buildHtml($kind) {
        $fileds = $this->_modelExample->filed2kind($kind);
        // dump($fileds);
        $params = array();
        $params['formItems'] = array(
            array(
                'type'      =>'comp-select',
                'name'      =>'kindId',
                'title'     =>'产品大类',
                'clearable' =>true,
                'disabled'  =>true,
                'options'   =>$this->_modelKind->getKinds(),
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'proCode',
                'title'     =>'产品编号',
                'clearable' =>true,
                'value'     =>'',
                'addonEnd'  =>'工艺号',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'proName',
                'title'     =>'产品名称',
                'clearable' =>true,
                'value'     =>'',
            ),
        );

        if($this->_modelExample->isUnit($kind)){
            $params['formItems'][] = array(
                'type'       =>'comp-autocomplete',
                'name'       =>'unit',
                'title'      =>'单位',
                'value'      =>'',
                'clearable'  =>false,
                'options'    =>$this->_modelExample->getUnit($kind),
                'placeholder' =>'可输入可选择历史记录',
            );
        }

        //加载个性的部分
        if($fileds){
            foreach ($fileds as $key => $v) {
                $params['formItems'][] = array(
                    'type'      =>'comp-text',
                    'name'      =>$key,
                    'title'     =>$v,
                    'clearable' =>true,
                    'value'     =>'',
                );
            }
        }
        // dump($params);exit;
        $params['formItems'][] = array(
                'type'         =>'comp-image',
                'name'         =>'pic',
                'title'        =>'产品图片',
                'action'       =>$this->_url('saveFile'),//上传地址
                'actionRemove' =>$this->_url('removeFile'),//删除图片时需要从服务器删除,可以不定义
                'accept'       =>'.jpg,.bmp,.PNG',//接受上传的文件类型
                'limit'        =>5,//最大允许上传个数
                'multiple'     =>true,//是否允许多选,
            );

        $params['formItems'][] = array(
                'type'      =>'comp-textarea',
                'name'      =>'memo',
                'title'     =>'备注',
                'clearable' =>true,
                'value'     =>'',
            );

        $params['rules'] = array(
            'proCode'=>array(
                array(
                    'required'=>true,
                    'message'=>'产品编号必须',
                )
            ),
            'unit'=>array(
                array(
                    'required'=>true,
                    'message'=>'单位必须',
                )
            ),
            'proName'=>array(
                array(
                    'required'=>true,
                    'message'=>'品名必须',
                )
            )
        );

        return $params;
    }


    /**
     * @desc ：产品查询
     * Time：2019/02/14 16:49:06
     * @author Wuyou
    */
    function actionRight() {
        $this->authCheck($this->funcId);
        $initKindid = $_GET['kindId'];

        $searchItems = array(
            'key'    =>'',
            'kindId' =>$initKindid,
        );
        $smarty = & $this->_getView();
        $arrFieldInfo = array(
            "kindName"    =>array('text'=>"产品类别",'width'=>'100'),
            "proCode"     =>array('text'=>"产品编号",'width'=>'100'),
            "proName"     =>array('text'=>"产品名称",'width'=>'100'),
            "unit"        =>array('text'=>"单位",'width'=>'100'),
            "memo"        =>array('text'=>"备注",'width'=>'150'),
            // "otherDesc"        =>array('text'=>"备注",'width'=>'150','isHtml'=>true),
        );

        if(!$this->_modelExample->isUnit($initKindid)){
            unset($arrFieldInfo['unit']);
        }

        $fileds = $this->_modelExample->filed2kind($initKindid);
        foreach ($fileds as $key => & $v) {
            $width = 100;
            if(mb_strlen($v) > 10){
                $width = 120;
            }
            if(mb_strlen($v) > 15){
                $width = 150;
            }
            $arrFieldInfo[$key] = array('text'=>$v,'width'=>$width);
        }

        $smarty->assign('title', '产品列表');
        $smarty->assign('arr_field_info', $arrFieldInfo);
        $smarty->assign('action', $this->_url('GetRows'));
        $smarty->assign('searchItems', $searchItems);
        $smarty->assign('colsForKey', array(
                array('text' =>'关键字','col'=>'key')
        ));

        $smarty->assign('editButtons',array(
            array('text'=>'编辑','type'=>'redirect','icon'=>'el-icon-edit','options'=>array(
                //点击后跳转的地址
                'url'            =>$this->_url('Edit').'&id={id}',
                'disabledColumn' =>'__disabledEdit',
            )),
            array('text'=>'删除','type'=>'remove','options'=>array(
                'url'            =>$this->_url('RemoveAjax'),
                'disabledColumn' =>'__disabledRemove',
            )),
        ));

        $smarty->assign('addUrl',$this->_url('Add',array('kindId'=>$initKindid)));
        // $smarty->assign('sonTpl','Jichu/ProductList.js');

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
        //处理搜索
        $post[$keyField] = $post['key'];
        if($keyField!='key') $post['key'] = '';

        $arr = $post;

        $sql = "SELECT x.*,y.kindName
                from jichu_product x
                left join jichu_prokind y on x.kindId=y.id
                where 1";

        if($arr['key']!='') {
            $sql.=" and (x.proCode like '%{$arr['key']}%' or x.proName like '%{$arr['key']}%')";
        }
        if($arr['kindId']!='') {
            $sql.=" and x.kindId = '{$arr['kindId']}'";
        }

        $sql .=" order by id desc";
        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = new TMIS_Pager($sql,null,null,$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();
        // dump($rowset);exit;
        // foreach($rowset as & $v){
        //     $v['otherDesc'] = $this->_modelExample->filedOtherFormat($v ,'html');
        // }

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
        $this->_edit(array('kindId'=>$_GET['kindId']));
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
        $pic = explode(',', $row['pic']);
        $imgs = array();
        foreach ($pic as & $v) {
            if(!$v)continue;
            $sql = "SELECT * FROM jichu_image WHERE id='{$v}'";
            $temp = $this->_modelExample->findBySql($sql);
            $imgs[] = array(
				'imageId' => $v,
                'name' => 'image_'.$v,
                'url' => $temp[0]['path']
            );
        }
        $row['pic'] = $imgs;
        // dump($pic);exit;
        $this->_edit($row);
    }

    function _edit($Arr = array()) {
        $formParams = $this->buildHtml($Arr['kindId']);
        if(!$Arr){
            foreach ($formParams['formItems'] as $key => $v) {
                $Arr[$v['name']] = $v['value'].'';
            }
        }
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

        if(empty($post['id'])) {
            $condition = array();
            $condition[] = array('proCode',$post['proCode'],'=');
            $count = $this->_modelExample->findCount($condition);

            if($count > 0) {
                $ret = array('msg'=>'产品编号不能重复','success'=>false);
                echo json_encode($ret);exit;
            }

            // $condition = array();
            // $condition[] = array('proName',$post['proName'],'=');
            // $count = $this->_modelExample->findCount($condition);

            // if($count > 0) {
            //     $ret = array('msg'=>'产品名称不能重复','success'=>false);
            //     echo json_encode($ret);exit;
            // }
        } else {
            //修改时判断是否重复
            $condition = array();
            $condition[] = array('proCode',$post['proCode'],'=');
            $condition[] = array('id',$post['id'],'<>');
            $count = $this->_modelExample->findCount($condition);

            if($count > 0) {
                $ret = array('msg'=>'产品编号不能重复','success'=>false);
                echo json_encode($ret);exit;
            }

            // $condition = array();
            // $condition[] = array('proName',$post['proName'],'=');
            // $condition[] = array('id',$post['id'],'<>');
            // $count = $this->_modelExample->findCount($condition);

            // if($count > 0) {
            //     $ret = array('msg'=>'品名不能重复','success'=>false);
            //     echo json_encode($ret);exit;
            // }
        }
        // 选择的图片
        if(count($post['pic']) > 0){
            $post['pic'] = join(',', array_col_values($post['pic'],'imageId'));
        }
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

    //保存上传图片或者文件action
    function actionSaveFile() {
        $mImage = FLEA::getSingleton('Model_Jichu_Image');
        $path = "upload/Images/";//图片保存路径

        FLEA::loadClass('FLEA_Helper_Image');
        $ext = pathinfo($_FILES['pic']['name'], PATHINFO_EXTENSION);
        $bfileName = $path.'b'.date('ymdHis').'.'.$ext;//创建大图文件名和路径
        $imgInfo = getimagesize($_FILES['pic']['tmp_name']);// 获得图片宽高

        move_uploaded_file($_FILES['pic']['tmp_name'],$bfileName);

        //保存图片档案信息
        $arr = array(
            'path'   => $bfileName,
            'width'  => $imgInfo[0],
            'height' => $imgInfo[1],
            'time'   =>time()
        );
        // dump($arr);exit;
        $imageId = $mImage->save($arr);
        $ret = array(
            'success'=>true,
            'msg'=>'保存成功',
            'imageId'=>$imageId,
            'imgPath'=>$bfileName,
        );
        echo json_encode($ret);exit;
    }

    //删除图片或者文件 TODO
    function actionRemoveFile() {
        $requestParam = file_get_contents('php://input');
        $_POST = json_decode($requestParam,true);

        $url = $_POST['url'];
        //根据url提取文件名,进行删除
        $ret = array(
          'success'=>true,
          'msg'=>'图片删除成功',
          'url'=>$url,
        );
        echo json_encode($ret);exit;
    }

    //获得产品列表页面：选择框
    function actionListPro() {
        $post = $this->axiosPost();

        $pagesize = $post['pagesize'];
        $currentPage = $post['currentPage'];

        $arr = $post;

        $sql="SELECT x.* ,y.kindName
                from jichu_product x
                left join jichu_prokind y on x.kindId=y.id
                where 1";

        if($arr['key']!='') {
            $sql.=" and (x.proCode like '%{$arr['key']}%' or x.proName like '%{$arr['key']}%')";
        }
        $sql .=" order by id desc";
        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = new TMIS_Pager($sql,null,null,$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();

        foreach ($rowset as $key => & $v) {
            $v['otherDesc'] = $this->_modelExample->filedOtherFormat($v);
            $v['productDesc'] = $v['kindName'].' - '.$v['proCode'].' - '. $v['proName'];

            $v['_unit'] = $this->_modelExample->isUnit($v['kindId']);
        }

        //表头信息
        $arr_field_info = array(
          'kindName'  => array('text'=>'大类','width'=>'100'),
          'proCode'   => array('text'=>'编号','width'=>'100'),
          'proName'   => array('text'=>'名称','width'=>'100'),
          'memo'      => array('text'=>'备注','width'=>'100'),
          'otherDesc' => array('text'=>'其他描述','width'=>''),
        );

        $ret = array(
          'total'   =>$pager->totalCount,
          'columns' =>$arr_field_info,
          'rows'    =>$rowset,
        );
        echo json_encode($ret);exit;
    }

    /**
     * 判断是否需要单位，单位有哪些
     * @var 参数类型
    */
    function actionUnit(){
        $post = $this->axiosPost();

        //查找产品id
        $productId = $post['productId'];
        $row = $this->_modelExample->find($productId);
        $res = $this->_modelExample->isUnit($row['kindId']);

        echo json_encode(array('_unit'=>$res,'unit'=>$row['unit']));
    }

    /**
     * @desc ：员工档案删除 验证是否已经被使用
     * Time：2017/07/31 15:49:06
     * @author lwj
    */
    function actionRemoveAjax() {
        $post = $this->axiosPost();
        $id = intval($post['row']['id']);

        //查找是否已经被使用
        $array = array(
            'Model_Trade_Order2Product'           => 'productId',
            'Model_Shengchan_Plan_Main'           => 'productId',
            'Model_Cangku_Chengpin_Ruku2Product'  => 'productId',
            'Model_Cangku_Chengpin_Chuku2Product' => 'productId',
        );

        $isOccupy = false;

        foreach ($array as $key =>  & $v) {
            $tmpModel = FLEA::getSingleton($key);
            $condition = array();
            $condition[$v] = $id;
            $count = $tmpModel->findCount($condition);

            //如果某个表中存在，则不能删除
            if($count > 0){
                $isOccupy = true;
                $msg = $tmpModel->moduleName;
                break;
            }
        }

        if($isOccupy){
            $ret = array(
                'success' =>false,
                'msg'     =>'被'.$msg.'使用，禁止删除',
            );
            echo json_encode($ret);exit;
        }


        $res = $this->_modelExample->removeByPkv($id);

        if($res){
            $ret = array(
                'success' =>true,
                'msg'     =>'删除成功',
            );
        }else{
            $ret = array(
                'success' =>false,
                'msg'     =>'删除失败',
            );
        }
        echo json_encode($ret);exit;
    }
}
?>