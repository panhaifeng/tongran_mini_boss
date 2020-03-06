<?php
/*
 * 实施人员用的后台配置程序，
 * 可进行动态密码卡的设置，
 * 可进行功能权限的定义。
 * 可查看db_change
 */
FLEA::loadClass('TMIS_Controller');
class Controller_Tool extends TMIS_Controller {
    var $m;
    function Controller_Tool() {
		$this->m= & FLEA::getSingleton('Model_Jichu_Client');
		$this->_guozhang = & FLEA::getSingleton('Model_Caiwu_Ar_Guozhang');
    	//echo 1;exit;
    }
    function actionIndex() {
		if($_SESSION['SN']==1||$_GET['_debug']==1){
			$smarty = & $this->_getView();
			$smarty->display('Tool/Index.tpl');
		}else{
			js_alert('没有通过动态密码卡验证，禁止操作',null,url('Login','Index'));
		}
	}

    //利用ajax获得工具栏的操作目录
    function actionGetToolMenu() {
		$menu = array(
			// array('text'=>'开关管理','leaf'=>true,'src'=>'?controller=Tool&action=Kaiguan'),
			array('text'=>'动态密码卡管理','expanded'=> false,'leaf'=>true,'src'=>'?controller=Tool&action=dongtai'),
			array('text'=>'设置弹窗信息','expanded'=> false,'leaf'=>true,'src'=>'?controller=Tool&action=setTanchuang'),
			array('text'=>'测试数据自动生成工具','expanded'=> false,'leaf'=>true,'src'=>'?controller=Tool&action=TestDataInsert'),
			array('text'=>'短信日志列表','expanded'=> false,'leaf'=>true,'src'=>'?controller=SMS_SMS&action=ReportLog'),
		);
		echo json_encode($menu);
    }

    /**
    	* @author li
    	* @return null
    	*/
    function actionBuilding(){
    	FLEA::loadClass('TMIS_Common');
    	$m = FLEA::getSingleton('Model_Jichu_Client');
    	///客户的首字母自动填充
    	$sql="select id,compName from yixiang_client where 1";
    	$res=$m->findBySql($sql);
    	foreach($res as & $v){
    		$letters=strtoupper(TMIS_Common::getPinyin($v['compName']));
    		$sql="update yixiang_client set letters='{$letters}' where id='{$v['id']}'";
    		$m->execute($sql);
    	}

    	///员工档案的首字母
    	$sql="select id,compName from jichu_jiagonghu where 1";
    	$res=$m->findBySql($sql);
    	foreach($res as & $v){
    		$letters=strtoupper(TMIS_Common::getPinyin($v['compName']));
    		$sql="update jichu_jiagonghu set letters='{$letters}' where id='{$v['id']}'";
    		$m->execute($sql);
    	}
    	///加工户的首字母
    	$sql="select id,employName from jichu_employ where 1";
    	$res=$m->findBySql($sql);
    	foreach($res as & $v){
    		$letters=strtoupper(TMIS_Common::getPinyin($v['employName']));
    		$sql="update jichu_employ set letters='{$letters}' where id='{$v['id']}'";
    		$m->execute($sql);
    	}
    	echo '补丁完成';exit;
    }

    //开关设置
    function actionKaiguan() {
    	if(count($_POST)>0) {
    		$ret = array();
    		$m = FLEA::getSingleton('Model_Acm_SetParamters');
    		foreach($_POST as $k=>&$v) {
    			if($k=='Submit') continue;
    			//找到相关的记录，取得相对应的id
    			$sql = "select id from sys_set where item='{$k}'";
    			$_rows = $this->m->findBySql($sql);
    			$ret[] = array(
    				'id'=>$_rows[0]['id'],
    				'item'=>$k,
    				'value'=>$v
    			);
    		}
    		$m->saveRowset($ret);
    		js_alert(null,"window.parent.showMsg('保存成功')",$this->_url('kaiguan'));
    	}
    	FLEA::loadClass('TMIS_Common');
    	$row = TMIS_Common::getSysSet();
    	// dump($row);
    	$smarty = & $this->_getView();  
    	$smarty->assign('aRow',$row);  	
    	$smarty->display('Tool/Kaiguan.tpl');
    }

    //管理动态密码卡
    function actionDongtai() {
		$sql = "select * from acm_sninfo";
		$rowset = $this->m->findBySql($sql);
		$rowset[] = array();
		$smarty = & $this->_getView();
		$smarty->assign('rowset',$rowset);
		$smarty->display('Tool/Dongtai.tpl');
    }
    function actionSaveDongtai() {
		$m = & FLEA::getSingleton('Model_Acm_Sninfo');
		if($m->save($_POST)) {
			js_alert(null,'window.parent.showMsg("保存成功")',$this->_url('dongtai'));
		}
    }

	

	//导出菜单目录
    function actionExport() {
		echo("<a href='".$this->_url('View')."'>导出</a>");
    }
    function actionView() {
		include('Config/menu.php');
		$smarty = & $this->_getView();
		$smarty -> assign('row',$_sysMenu);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=test.xls");
		header("Content-Transfer-Encoding: binary");
		$smarty -> display('Tool/MenuView.tpl');
    }

	//设置弹窗内容，如果这里设置了，登录成功后，会弹出一个对话框，强制用户观看。
	function actionSetTanchuang() {
		//$this->authCheck('8');
		$sql = "select * from sys_pop";
		$row = mysql_fetch_assoc(mysql_query($sql));
		$tpl = 'Tool/PopEdit.tpl';
		$smarty = & $this->_getView();
		$smarty->assign('aRow',$row);
		$smarty->display($tpl);
	}
	function actionSavePop() {
		$m = & FLEA::getSingleton('Model_Sys_Pop');
		$id = $m->save($_POST);
		js_alert('保存成功,提交的信息将会在用户登录的第一时间弹出显示，客户必须关闭弹窗才可继续操作！','',$this->_url('SetTanchuang'));
	}
	//利用ajax取得弹窗的内容
	function actionGetPopByAjax() {
		$d = date('Y-m-d');
		$sql = "select * from sys_pop where dateFrom<='{$d}' and dateTo>='{$d}'";
		//dump($sql);exit;
		$row = mysql_fetch_assoc(mysql_query($sql));
		if(!$row) {
			$arr = array(
				'success'=>false
			);
		} else {
			$arr = array(
				'success'=>true,
				'data'=>$row
			);
		}
		echo json_encode($arr);
	}


	/****************************读取excel文件********************************************/
	function actionReadExcel() {
		$filePath='a.xls';
		$arr = $this->_readExcel($filePath);
		//以下为数据处理过程
		//$ret = array();
		foreach($arr as $k=> & $v) {
			if($k==0) continue;
			$row = array(
				'proCode'=>$v[1].'',
				'proName'=>$v[2].'',
				'unit'=>'只',
				'priceRetail'=>$v[4].'',
				'barCode'=>$v[6]
			);
			//dump($row);exit;
			$sql = "insert into jxc_jianzhong.jichu_product(
				proCode,
				proName,
				unit,
				priceRetail,
				barCode
			) values(
				'{$row['proCode']}',
				'{$row['proName']}',
				'{$row['unit']}',
				'{$row['priceRetail']}',
				'{$row['barCode']}'
			)";
			mysql_query($sql) or die(mysql_error());
		}
		// dump($ret[0]);exit;
		
		//dump($arr[1]);dump($ret);exit;
		// $m = & FLEA::getSingleton('Model_Jichu_Client');
		// $m->createRowset($ret);
		echo "成功!";
	}
	//读取某个excel文件的某个sheet数据，
	function _readExcel($filePath,$sheetIndex=0) {
		set_time_limit(0);
		include "Lib/PhpExcel/PHPExcel.php";

		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array('memoryCacheSize'=>'16MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		$PHPExcel = new PHPExcel();
		//如果是2007,需要$PHPReader = new PHPExcel_Reader_Excel2007();
		$PHPReader = new PHPExcel_Reader_Excel5();
		if(!$PHPReader->canRead($filePath)){
			echo 'no Excel';
			return ;
		}
		$PHPExcel = $PHPReader->load($filePath);
		/**读取excel文件中的第一个工作表*/
		$currentSheet = $PHPExcel->getSheet($sheetIndex);
		/**取得共有多少列,若不使用此静态方法，获得的$col是文件列的最大的英文大写字母*/
		$allColumn = PHPExcel_Cell::columnIndexFromString($currentSheet->getHighestColumn());

		/**取得一共有多少行*/
		$allRow = $currentSheet->getHighestRow();
		//输出
		$ret = array();
		for($currow=1;$currow<=$allRow;$currow++){
		  $_row=array();
		  for($curcol=0;$curcol<$allColumn;$curcol++){
			   $result=$currentSheet->getCellByColumnAndRow($curcol,$currow)->getValue();
			   $_row[] = $result;
		  }
		  $ret[] = $_row;
		}
		return $ret;
	}

	//测试数据自动生成工具
	function actionTestDataInsert(){
		$clientIndex = 100;

		$actionIndex = 5000;

		for ($i=0; $i <$clientIndex ; $i++) { 
			$sql="insert into jichu_client (compName,compCode,traderId) values ('客户".$i."',00{$i},1)";
			mysql_query($sql);
		}

		for ($i=0; $i <$actionIndex ; $i++) { 
			$sql="insert into reschedule (clientId,scheduleDate,traderId,styleId) values ('1',now(),1,10)";
			mysql_query($sql);
		}
	}


	function actionTestRemoveData(){
		
		$sql="SELECT id,orderId,kind from caiwu_ar_guozhang where kind='运费过账' ";
		$rr = $this->_guozhang->findBySql($sql);
		foreach ($rr as  &$value) {
			$_rr[] = $value['id'];
		}
		$_res[] = join(',',$_rr);
		// dump($rr);die;

		$arr="SELECT min(id),kind,orderId from caiwu_ar_guozhang where kind='运费过账' group by orderId   ";
		$res = $this->_guozhang->findBySql($arr);
		foreach ($res as  &$v) {
			$_arr[] = $v['min(id)'];
		}
		$_row[] = join(',',$_arr);
		// dump($res);die;

		$str="DELETE from caiwu_ar_guozhang 
			where  id in ({$_res['0']}	) 
			and  id not in ({$_row['0']}) ";
		// dump($str);die;
		$this->_guozhang->execute($str);
	}

	function actionGx(){
		$this->pro= & FLEA::getSingleton('Model_Jichu_Product');
		$this->gx= & FLEA::getSingleton('Model_Jichu_Gongxu');
		$sql  = "SELECT * from jichu_product where 1 and proType='面料'";
		$res = $this->pro->findBySql($sql);

		$row_rs = $this->gx->find(array('gxName'=>'染色'));
		$rs = $row_rs['id']; //染色

		$row_mm = $this->gx->find(array('gxName'=>'磨毛'));
		$mm = $row_mm['id']; //磨毛

		$row_jy = $this->gx->find(array('gxName'=>'卷验'));
		$jy = $row_jy['id']; //卷验

		$combM = $rs.','.$mm.','.$jy;
		$comb_text='染色 磨毛 卷验';

		$combMM = $rs.','.$jy;
		$combMM_text='染色 卷验';

		foreach ($res as $key => &$value) {
			$value['milling'] = trim($value['milling']);
			if($value['milling']=='单磨'||$value['milling']=='双磨'){
				$sql="update jichu_product set gongxuIds='{$combM}',gongxu='{$comb_text}' where id='{$value['id']}'";
    			$this->pro->execute($sql);
			}elseif($value['milling']=='平布'){
				$sql="update jichu_product set gongxuIds='{$combMM}',gongxu='{$combMM_text}' where id='{$value['id']}'";
    			$this->pro->execute($sql);
			}
		}

		echo 'OK';
	}

	function actionRemovespace(){
		$this->pro= & FLEA::getSingleton('Model_Jichu_Product');
		
		$sql="UPDATE  jichu_product  SET  proCode= REPLACE(proCode, ' ', ''),proName= REPLACE(proName, ' ', ''),kezhong= REPLACE(kezhong, ' ', '');";
		$this->pro->execute($sql);

		$str="UPDATE  trade_order2product  SET  productId= REPLACE(productId, ' ', '');";
		$this->pro->execute($str);

		echo 'OK';
	}

	//清楚基础资料
	function actionCleanDB(){
		$str="TRUNCATE TABLE caigou_order";
		$this->m->execute($str);
		$str="TRUNCATE TABLE caigou_order2product";
		$this->m->execute($str);
		$str="TRUNCATE TABLE cangku_chuku";
		$this->m->execute($str);
		$str="TRUNCATE TABLE cangku_chuku2product";
		$this->m->execute($str);
		$str="TRUNCATE TABLE cangku_kucun";
		$this->m->execute($str);
		$str="TRUNCATE TABLE cangku_ruku";
		$this->m->execute($str);
		$str="TRUNCATE TABLE cangku_ruku2product";
		$this->m->execute($str);
		$str="TRUNCATE TABLE check_main";
		$this->m->execute($str);
		$str="TRUNCATE TABLE check_main2flaw";
		$this->m->execute($str);
		$str="TRUNCATE TABLE checkcdmx";
		$this->m->execute($str);
		$str="TRUNCATE TABLE checkinfo";
		$this->m->execute($str);
		$str="TRUNCATE TABLE checkcusprop";
		$this->m->execute($str);
		$str="TRUNCATE TABLE checkinfo_seq";
		$this->m->execute($str);
		$str="TRUNCATE TABLE chuku_plan";
		$this->m->execute($str);
		$str="TRUNCATE TABLE madan_db";
		$this->m->execute($str);
		$str="TRUNCATE TABLE madan_rc2madan";
		$this->m->execute($str);
		$str="TRUNCATE TABLE pl_plan";
		$this->m->execute($str);
		$str="TRUNCATE TABLE pl_plan2gongxu";
		$this->m->execute($str);
		$str="TRUNCATE TABLE pl_planitem";
		$this->m->execute($str);

		$str="TRUNCATE TABLE plan_finish";
		$this->m->execute($str);
		$str="TRUNCATE TABLE plan_finish2gx";
		$this->m->execute($str);
		$str="TRUNCATE TABLE plan_recover";
		$this->m->execute($str);
		$str="TRUNCATE TABLE plan_recover2pro";
		$this->m->execute($str);
		$str="TRUNCATE TABLE shenhe_db";
		$this->m->execute($str);
		$str="TRUNCATE TABLE shipping_db";
		$this->m->execute($str);
		
	}
	//删除规定时间内的订单数据
	function actionCleanOrder(){
		// $str="delete from trade_order where orderTime<'2017-02-09 00:00:00'";
		$time = '2017-02-09 00:00:00';
		$str="select id from trade_order where orderTime<'{$time}'";
		$rr = $this->m->findBySql($str);
		$modelOrder = &FLEA::getSingleton('Model_Trade_Order');
		foreach ($rr as $key => &$value) {
			if (!$modelOrder->removeByPkv($value['id'])) {
				echo "nonono";
			}		
		}
	}

	function actionDealGanghao(){
		$sql = "SELECT * FROM cangku_kucun  where rukuId>0";
		$rr = $this->m->findBySql($sql);
		// dump($rr);die;
		foreach ($rr as $key => &$value) {
			$sql = "select y.ganghao from cangku_ruku x 
				left join cangku_ruku2product y on x.id=y.rukuId
				where y.id ='{$value['rukuId']}'";
			$res_g = $this->m->findBySql($sql);
			// dump($res_g);
			if($res_g[0]['ganghao']){
				$str = "update cangku_kucun set ganghao='{$res_g[0]['ganghao']}' where id='{$value['id']}'";
    			$this->m->execute($str);
			}
		}

		$sql2 = "SELECT * FROM cangku_kucun  where chukuId>0";
		$rrCk = $this->m->findBySql($sql2);

		foreach ($rrCk as $key => &$value) {
			$sql = "select y.ganghao from cangku_chuku x 
				left join cangku_chuku2product y on x.id=y.chukuId
				where y.id ='{$value['chukuId']}'";
			$resCk_g = $this->m->findBySql($sql);
			// dump($resCk_g);
			if($resCk_g[0]['ganghao']){
				$str = "update cangku_kucun set ganghao='{$resCk_g[0]['ganghao']}' where id='{$value['id']}'";
    			$this->m->execute($str);
			}
		}
	}

	//处理plan_finish的nextPlan2gxIdx
	function actionDealNextPlan2gxId(){
		$sql = "SELECT x.planId,x.plan2gxId 
			from plan_finish x 
			left join plan_finish2gx y on x.id=y.finishId
			where 1 ";
		$rowset = $this->m->findBySql($sql);
		// dump($rowset);die;
		$_modelPlan= & FLEA::getSingleton('Model_Plan_Plan');
		$_modelPlan2Pro= & FLEA::getSingleton('Model_Plan_Plan2Pro');
		foreach ($rowset as $key => &$value) {
			$rr = $_modelPlan2Pro->find(array('id'=>$value['plan2gxId'],'planId'=>$value['planId']));
			// dump($rr);die;
			$nextOrder = $rr['order']+1;
			$str = "SELECT y.id from pl_plan x 
				left join pl_plan2gongxu y on x.id=y.planId
				where x.id='{$value['planId']}' and y.order ={$nextOrder}";
			$res = $this->m->findBySql($str);
			// dump($res);die;

			$sql2="update plan_finish set nextPlan2gxId='{$res[0]['id']}' where planId='{$value['planId']}' and plan2gxId='{$value['plan2gxId']}'";
    		$this->m->execute($sql2);

		}
	}

	//处理pl_plan2gongxu表beforeGxId
 	function actionDealBeforeGxId(){
        $sql = "SELECT x.id as planId,y.order,y.id as plan2gxId
			from pl_plan x 
			left join pl_plan2gongxu y on x.id=y.planId
			where 1 ";
		$rowset = $this->m->findBySql($sql);
		$_modelPlan2Pro= &FLEA::getSingleton('Model_Plan_Plan2Pro');
        foreach ($rowset as $key => &$value) {
            if($value['order']>1){
                $temp = $value['order']-1;
                $row = $_modelPlan2Pro->find(array('order' => $temp,'planId'=>$value['planId']));
                $sql = "update pl_plan2gongxu set beforeGxId= '{$row['id']}' where id='{$value['plan2gxId']}'";
                $_modelPlan2Pro->execute($sql);
            }
        }
    }

    function actionKcJiagonghuId(){
        $sql = "SELECT *
			from cangku_kucun
			where 1 and rukuId>0";
		$rowset = $this->m->findBySql($sql);
		// dump($rowset);die;

		$_modelRuk2= &FLEA::getSingleton('Model_Cangku_Ruku2Product');
        foreach ($rowset as $key => &$value) {
            if(!$value['jiagonghuId']){
            	$row = array();
                $row = $_modelRuk2->find(array('id' => $value['rukuId']));
				if($row['Rk']['jiagonghuId']>0){
	                $sql = "update cangku_kucun set jiagonghuId= '{$row['Rk']['jiagonghuId']}' where id='{$value['id']}'";
	                $_modelRuk2->execute($sql);
				}
            }
        }
    }

    function actionDealWithMadan(){
    	if($_SESSION['REALNAME']!='管理员') echo "no";exit;
    	$sql = "SELECT *
			from check_main
			where 1 and lengthUnit<>'m'";
		$rowset = $this->m->findBySql($sql);

		foreach ($rowset as $key => &$v) {
			$str ="SELECT x.*
	            from madan_db x 
	            left join madan_rc2madan y on x.id=y.madanId
	            left join cangku_ruku2product z on y.rukuId=z.id
	            left join cangku_ruku a on a.id=z.rukuId
	            where a.id = '{$v['cprkId']}' and x.rollNo ='{$v['checkId']}'";
			$rr = $this->m->findBySql($str);
			$cntL = $rr[0]['cntL']*0.9144;
			$strr = "update madan_db set cntL='{$cntL}' where id = '{$rr[0]['id']}'";
            $this->m->execute($strr);
		}
    }

     function actionKcBpCntPi(){
        $sql = "SELECT *
			from cangku_kucun
			where 1  and cangkuName='白坯仓库'";
		$rowset = $this->m->findBySql($sql);
		// dump($rowset);die;

		$_modelRuk2= &FLEA::getSingleton('Model_Cangku_Ruku2Product');
		$_modelCHuk2= &FLEA::getSingleton('Model_Cangku_Chuku2Product');
        foreach ($rowset as $key => &$value) {
        	$row = array();
        	if($value['rukuId']>0){
        		$row = $_modelRuk2->find(array('id' => $value['rukuId']));
				if($row['cntPi']>0){
	                $sql = "update cangku_kucun set cntPi= '{$row['cntPi']}' where id='{$value['id']}'";
	                $_modelRuk2->execute($sql);
				}
        	}
        	if($value['chukuId']>0){
            	$rowS = $_modelCHuk2->find(array('id' => $value['chukuId']));
				if($rowS['cntPi']){
					$cntPiCk = 0-$rowS['cntPi'];
	                $sql = "update cangku_kucun set cntPi= '{$cntPiCk}' where id='{$value['id']}'";
	                $_modelCHuk2->execute($sql);
				}
        	}
        }

        echo "OK";
    }

    function actionDealCaiwu(){
    	$this->Noguozhang = "'染色投坯','原料领用','面料领用','其他出库'";
    	$sql ="SELECT x.id 
    		FROM cangku_chuku x
    		LEFT JOIN cangku_chuku2product y on x.id=y.chukuId
    		where x.kind in ($this->Noguozhang) and x.isGuozhang='0'";
    	$rowset = $this->m->findBySql($sql);
    	// dump($rowset);die;
    	foreach ($rowset as $key => &$v) {
    		$str ="update cangku_chuku set isGuozhang='1' where id='{$v['id']}'";
    		$this->m->execute($str);
    	}
    	echo "OK";
    }

    /*
    *	2017年4月18日 08:46:39
    *	库存清理 
    */
    function actionClearKc(){
    	$str="TRUNCATE TABLE cangku_chuku";
		$this->m->execute($str);
		$str="TRUNCATE TABLE cangku_chuku2product";
		$this->m->execute($str);
		$str="TRUNCATE TABLE cangku_kucun";
		$this->m->execute($str);
		$str="TRUNCATE TABLE cangku_ruku";
		$this->m->execute($str);
		$str="TRUNCATE TABLE cangku_ruku2product";
		$this->m->execute($str);
		
		$str="TRUNCATE TABLE madan_db";
		$this->m->execute($str);
		$str="TRUNCATE TABLE madan_rc2madan";
		$this->m->execute($str);
    }

    function actionCkClientId(){
    	$sql = "SELECT x.*,z.clientId as tClientId,y.orderId as sid,a.compCode
    		from cangku_chuku x 
    		left join cangku_chuku2product y on x.id=y.chukuId
    		left join trade_order z on z.id=y.orderId
    		left join jichu_client a on a.id=z.clientId
    		where x.kind='销售出库'";
    	$rowset = $this->m->findBySql($sql);
    	foreach ($rowset as $key => &$v) {
    		if(!$v['clientId']){
    			$str ="update cangku_chuku set clientId='{$v['tClientId']}' where id='{$v['id']}'";
    			$this->m->execute($str);
    		}
    	}
    	echo "OK";
    }


    function actionDeleteMf(){
    	$sql_1="DELETE x.*,y.* 
	    	from cangku_chuku x
	    	left join cangku_chuku2product y on x.id=y.chukuId 
	    	where x.cangkuName ='面料仓库' or x.cangkuName ='辅料仓库'";
    	$res_1 = $this->m->execute($sql_1);

    	$sql_2="DELETE x.*,y.* 
	    	from cangku_ruku x
	    	left join cangku_ruku2product y on x.id=y.rukuId
	    	where x.cangkuName ='面料仓库' or x.cangkuName ='辅料仓库'";
    	$res_2 = $this->m->execute($sql_2);

    	$sql_3 = "DELETE from cangku_kucun where cangkuName='面料仓库' or cangkuName ='辅料仓库'";
    	$res_3 = $this->m->execute($sql_3);
    	if($res_1 && $res_2 && $res_3){
    		echo "OK";
    	}
    }

    function actionImportKc(){
		$this->Kc= & FLEA::getSingleton('Model_Import_Chuku');
		$this->Ck= & FLEA::getSingleton('Model_Cangku_Chuku');
		$_temp= array('面料仓库','辅料仓库');
        $condition['in()']=array('cangkuName'=>$_temp);
		$result  = $this->Kc->findAll($condition);
    	// dump($result);die;
    	foreach ($result as $key => &$v) {
    		if(!$v['id']) continue;
    		$temp = array();
    		unset($v['id']);
    		foreach ($v['Products'] as $k=>&$vv) {
    			unset($vv['id']);
    			unset($vv['chukuId']);
    		}
    		$temp = $v;
	    	$rowset = $this->Ck->save($temp);
    	}

    	$this->KcR= &FLEA::getSingleton('Model_Import_Ruku');
		$this->Rk= &FLEA::getSingleton('Model_Cangku_Ruku');
		$_tempR= array('面料仓库','辅料仓库');
        $conditionR['in()']=array('cangkuName'=>$_tempR);
		$resultR  = $this->KcR->findAll($conditionR);
    	// dump($resultR);die;
    	foreach ($resultR as $key => &$v) {
    		if(!$v['id']) continue;
    		$temp = array();
    		unset($v['id']);
    		foreach ($v['Products'] as $k=>&$vv) {
    			unset($vv['id']);
    			unset($vv['rukuId']);
    		}
    		$temp = $v;
	    	$rowset = $this->Rk->save($temp);
    	}
    }
}
?>