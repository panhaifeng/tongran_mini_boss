<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  Author :wuyou
*  FName  :Employ.php
*  Time   :2017/07/31 15:38:24
*  Remark :员工档案
\*********************************************************************/
FLEA::loadClass('TMIS_Controller');
class Controller_Jichu_Employ extends TMIS_Controller {
	var $_modelExample;
	var $sonTpl;
    var $funcId = '90-3';
	function __construct() {
		$this->_modelExample = FLEA::getSingleton('Model_Jichu_Employ');
		$this->_modelDepart = FLEA::getSingleton('Model_Jichu_Department');
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
                'name'      =>'employCode',
                'title'     =>'员工代码',
                'clearable' =>true,
                'value'     =>'',
                'addonEnd'  =>'留空系统自动生成',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'employName',
                'title'     =>'员工姓名',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-select',
                'name'      =>'sex',
                'title'     =>'性别',
                'clearable' =>true,
                'value'     =>'0',
                'options'   =>array(
                    array('value'=>'0','text'=>'男'),
                    array('value'=>'1','text'=>'女'),
                    // array('value'=>2,'text'=>'未知'),
                ),
            ),
            array(
                'type'      =>'comp-select',
                'name'      =>'depId',
                'title'     =>'部门',
                'clearable' =>true,
                'value'     =>'',
                'options'   =>$this->_modelDepart->getDepartment(),
            ),
            array(
                'type'      =>'comp-select',
                'name'      =>'isFire',
                'title'     =>'员工状态',
                'clearable' =>true,
                'value'     =>'0',
                'options'   =>array(
                    array('text'=>'正式','value'=>'0'),
                    array('text'=>'离职','value'=>'1'),
                ),
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'mobile',
                'title'     =>'手机号',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'shenfenNo',
                'title'     =>'身份证号',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-text',
                'name'      =>'address',
                'title'     =>'地址',
                'clearable' =>true,
                'value'     =>'',
            ),
            array(
                'type'      =>'comp-calendar',
                'name'      =>'dateEnter',
                'title'     =>'入职日期',
                'clearable' =>true,
                'value'     =>date('Y-m-d'),
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
            'employName'=>array(
                array(
                    'required'=>true,
                    'message'=>'员工名字必须',
                )
            ),
            'depId'=>array(
                array(
                    'required'=>true,
                    'message'=>'部门必须',
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
            'depId'  =>'',
            'isFire' =>'',
        );
        $smarty = & $this->_getView();
        $arrFieldInfo = array(
            "depName"    =>array('text'=>"部门",'width'=>''),
            "employName" =>array('text'=>"姓名",'width'=>'100'),
            "employCode" =>array('text'=>"员工代码",'width'=>'100'),
            "depName"    =>array('text'=>"部门名称",'width'=>'100'),
            'dateEnter'  =>array('text'=>"入职日期",'width'=>'100'),
            "isFire"     =>array('text'=>"员工状态",'width'=>'100'),
            "sexName"    =>array('text'=>"性别",'width'=>'100'),
            "mobile"     =>array('text'=>"手机",'width'=>'110'),
            "address"    =>array('text'=>"地址",'width'=>''),
            "shenfenNo"  =>array('text'=>"身份证号",'width'=>''),
            "memo"       =>array('text'=>"备注",'width'=>''),
        );

        $smarty->assign('title', '员工列表');
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

        $smarty->assign('addUrl',$this->_url('Add'));
        $smarty->assign('textAfterPage', "<font color='red'>灰色表示已离职</font>");

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

        $sql="select x.*,y.depName from jichu_employ x
                left join jichu_department y on x.depId=y.id
                where 1";

        if($arr['key']!='') {
            $sql.=" and (x.employCode like '%{$arr['key']}%' or x.employName like '%{$arr['key']}%')";
        }
        if($arr['depId']!='') {
            $sql.=" and x.depId = '{$arr['depId']}'";
        }
        if($arr['isFire']!='') {
            $sql.=" and x.isFire = '{$arr['isFire']}'";
        }
        $sql .=" order by id desc";
        // dump($sql);exit;
        FLEA::loadClass('TMIS_Pager');
        $pager = new TMIS_Pager($sql,null,null,$pagesize ,($currentPage - 1));
        $rowset = $pager->findAll();
        // dump($rowset);exit;
        foreach($rowset as & $v){
            if($v['isFire']=='1') {
                $v['__bgColor'] = '#dedede';
                $v['isFire'] = '已离职';
            }else{
                $v['isFire'] = '正式';
            }
            $v['sexName'] = $v['sex']==0 ?'男':'女';
            if($v['dateEnter']=='0000-00-00') $v['dateEnter']="";
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
        $row['dateEnter']=$row['dateEnter']=='0000-00-00'?'':$row['dateEnter'];
        $row['dateLeave']=$row['dateLeave']=='0000-00-00'?'':$row['dateLeave'];

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


    //处理上传的图片
   	function actionAddPics(){
		// dump($_FILES);die;
		if($_FILES['pics']['name']!=''){
			// $path = "upload/Order/";//图片保存路径
			$path="upload/jichu/";
			//定义大图和小图的尺寸
			$size=GetImageSize($_FILES['pics']['tmp_name']);
			// dump($size);exit;
			//2寸图片尺寸
			$h_default=413;
			$w_width=626;

			if($size[1]>$h_default){
			    $width=$w_width;
			    $height=$h_default;
			}else{
			    $width=$size[0];
			    $height=$size[1];
			}
			$size = array(
				'big'=>array($width,$height),
				'small'=>array(60,60)
			);
			FLEA::loadClass('FLEA_Helper_Image');
			$ext = pathinfo($_FILES['pics']['name'], PATHINFO_EXTENSION);
			$bfileName = $path.'b'.date('ymdHis').'.'.$ext;//创建大图文件名和路径
			$sfileName = $path.'s'.date('ymdHis').'.'.$ext;	//创建小图文件名和路径
			$img =& FLEA_Helper_Image::createFromFile($_FILES['pics']['tmp_name'], $ext);//不能使用getSingleton创建对象，必须有图像实体
			$img->resampled($size['big'][0],$size['big'][1]);

			$img->saveAsJpeg($bfileName,100);

			$img->resize($size['small'][0],$size['small'][1]);
			$img->saveAsJpeg($sfileName,100);
		}

		//如果需要删除原来的图片，则删除
		// if($_FILES['pics']['name']!='' || $_POST['isRemove']==1){
		// 	//删除原来的图片，
		// 	$o = $this->_modelExample->find(array('id'=>$_POST['id']));
		// 	// dump($o);exit;
		// 	if($o['bpic']) unlink($o['bpic']);
		// 	if($o['spic']) unlink($o['spic']);
		// 	// return false;
		// }
		// return array(
		// 	'bpic'=>$bfileName,
		// 	'spic'=>$sfileName,
		// );

		echo json_encode(array('success'=>true,'bpic'=>$bfileName,'spic'=>$sfileName,'msg'=>'上传成功！'));exit;

	}

    /**
     * @desc ：员工档案保存
     * Time：2017/07/31 15:48:32
     * @author Wuyou
    */
	function actionSave() {
        $this->authCheck($this->funcId);
        $post = $this->axiosPost();

        if(trim($post['employCode'])=='' || trim($post['employCode']) == '自动生成'){
            $post['employCode'] = $this->getEmployCode();
        }

        $post['employCode'] = str_pad($post['employCode'],4,'0',STR_PAD_LEFT);
        // dump($post);exit;
        if(empty($post['id'])) {
            if($post['employCode']){
                $condition = array();
                $condition[] = array('employCode',$post['employCode'],'=');
                $count = $this->_modelExample->findCount($condition);

                if($count > 0) {
                    $ret = array('msg'=>'员工编码重复','success'=>false);
                    echo json_encode($ret);exit;
                }
            }


            $condition = array();
            $condition[] = array('employName',$post['employName'],'=');
            $count = $this->_modelExample->findCount($condition);

            if($count > 0) {
                $ret = array('msg'=>'员工姓名重复','success'=>false);
                echo json_encode($ret);exit;
            }
        } else {
            //修改时判断是否重复
            if($post['employCode']){
                $condition = array();
                $condition[] = array('employCode',$post['employCode'],'=');
                $condition[] = array('id',$post['id'],'<>');
                $count = $this->_modelExample->findCount($condition);

                if($count > 0) {
                    $ret = array('msg'=>'员工编码重复','success'=>false);
                    echo json_encode($ret);exit;
                }
            }

            $condition = array();
            $condition[] = array('employName',$post['employName'],'=');
            $condition[] = array('id',$post['id'],'<>');
            $count = $this->_modelExample->findCount($condition);

            if($count > 0) {
                $ret = array('msg'=>'员工姓名重复','success'=>false);
                echo json_encode($ret);exit;
            }
        }

        //数据保存
        // dump($post);exit;
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
     * @desc ：员工档案删除 验证是否已经被使用
     * Time：2017/07/31 15:49:06
     * @author lwj
    */
	function actionRemoveAjax() {
        $requestParam = file_get_contents('php://input');
        $_POST = json_decode($requestParam,true);
        $id = intval($_POST['row']['id']);

        //查找是否已经被使用
        $array = array(
            'Model_Trade_Order'           => 'traderId',
            'Model_Cangku_Chengpin_Chuku' => 'employId',
            'Model_Cangku_Yuanliao_Chuku' => 'employId',
            'Model_Caigou_Plan'           => 'employId',
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

    /**
     * @desc ：获得员工代码
     * Time：2017/07/31 15:55:37
     * @author Wuyou
    */
	function getEmployCode($letters){
		$begin="0001";
		$pre = date('y');
		$str="SELECT right(employCode,4)as employCode FROM `jichu_employ` where 1 and bmLetters='{$letters}' order by right(employCode,4) desc limit 0,1";
		// dump($str);die;
		$re=mysql_fetch_assoc(mysql_query($str));
		// dump($re);die;
		$i = $re['employCode'];

		if($i!='')
		{
			$i = substr($i,0);
			$i++;
			if(9999 == $i){
				$i=$begin;
			}
			$next = str_pad($i,4,'0',STR_PAD_LEFT);
			// dump($next);die;
			return $letters.$pre.$next;
		}else{
			return $letters.$pre.$begin;
		}

	}

	function actionRoute(){
   		// dump($_FILES);die;
   	    // $dizhi['memo'] = $_FILES['memo'];
   	    $temp=array();$arr=array();
        foreach ($_FILES as $k=> &$v){
            for($i=0;$i<count($v['name']);$i++){
                foreach ($v as $key=> &$value){
                    $temp[$key]=$value;
                }
                $arr[][$k]=$temp;
            }
        }
        foreach ($arr as &$v){
            $temp1='';
            foreach ($v as $kk=>&$vv){
                $temp1=$kk;
            }
            $dizhi['path']=$v[$kk];
            $filePath[$kk][]= $this->_importAttac($dizhi,"upload/jichu/",true);
        }

   		if($filePath['memoRoute'][0]['success']==true){
   			echo json_encode(array('success'=>true,'filePath'=>$filePath['memoRoute'][0]['filePath'],'msg'=>'上传成功！'));exit;
   		}else{
   			echo json_encode(array('success'=>false,'filePath'=>$filePath['memoRoute'][0]['filePath'],'msg'=>'上传失败！'));exit;
   		}

   }

   function actionShowPics(){
		// dump($_GET);die;
		$pics = explode(',',$_GET['pics']);
		// dump($pics);die;
		if(is_array($pics)){
			foreach ($pics as $key =>& $value) {
				echo "<img src='{$value}' width='150px;' height='150px;' />";
			}
		}
		// echo '<img src="upload/order/images.bmp" />';
	}


	 function actionShowPic(){
		// dump($_GET);die;

		$pics = explode(',',$_GET['memoRoute']);

		$smarty = &$this->_getView();
        $smarty->assign('rowset', $pics);
        $_from = $_GET['fromAction']==''?'add':$_GET['fromAction'];
        $smarty->assign('fromAction', $_from);
        $smarty->assign('sonTpl', $this->sonTpl);
    	$smarty->display('Jichu/ViewMemo.tpl');

		// echo '<img src="upload/order/images.bmp" />';
	}
	/*
	*功能：php实现下载远程图片保存到本地
	*参数：文件url,保存文件目录,保存文件名称，使用的下载方式
	*当保存文件名称为空时则使用远程文件原来的名称
	*/
	function actiongetImage(){
		$filename = $_REQUEST['picsUrl'];

		$a = $this->getFile($filename);
		exit;
		// header('content-disposition:attachment;filename='. basename($filename));
		// header('content-length:'. filesize($filename));
		// readfile($filename);
	}

}
?>