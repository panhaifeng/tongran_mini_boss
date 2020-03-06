<?php
/*
 * 进行数据库改动的提交和浏览等功能
 */
FLEA::loadClass('TMIS_Controller');
class Controller_Dbchange extends TMIS_Controller {
    var $m;
	var $patchPath = "_Patchs/Files";
	var $logFile = "_Patchs/_log.txt";
	var $preFile = "_Patchs/_developer.txt";
	var $dateFrom = '';//360天之前的补丁不再执行
    function Controller_Dbchange() {
		$this->dateFrom = date('Y-m-d',mktime(0,0,0,date('m'),date('d')-360,date('Y')));
		$this->m= & FLEA::getSingleton('Model_Dbchange');
    //echo 1;exit;
    }

	//每次有新版本发布，必须执行此方法，用来清空所有的数据补丁和清空_log.txt文件，表示从头开始。
	//每个分支的数据补丁都应从创建之日起开始生成，不能和主版本有继承关系。
	function actionInit() {
	}

	//显示数据库改动语句提交窗口,由程序员在main.tpl中ctrl+shift+alt+a弹出
	function actionAdd() {
		$smarty= $this->_getView();
		//得到前缀
		$fileName= $this->preFile;
		//dump($fileName);exit;
		if(!file_exists($fileName)) {
			//js_alert('_developer.txt不存在！');exit;
			//$arr = array('success'=>false,'msg'=>'');
			//echo json_encode($arr);exit;
		}

		$content = $this->removeBOM(file_get_contents($fileName));
		if($content=='') {
			//js_alert('_developer.txt中没写前缀！');exit;
		}
		$smarty->assign('prefix',$content);
		$smarty->display('Tool/Dbchange/Edit.tpl');
	}

	//保存程序员提交的数据库改动
	function actionSave() {
		$datePatch = substr(join('',explode('-',$_POST['datePatch'])),2);
		//$datePatch .= join('',explode(':',$_POST['timePatch']));
		$fileName = $_POST['fileName'];
		$prefix = $_POST['prefix'];
		$sql = $_POST['sql'];

		if($fileName=='') {//新建文件
			//搜索最大的文件名
			$initFile = "{$prefix}_{$datePatch}_0.txt";
			if(!file_exists($this->patchPath.'/'.$initFile)) {
				$fileName = $initFile;
			} else {//如果文件名存在，检索最后创建的那个文件，在此基础上+1;
				//echo 1;exit;
				$maxIndex = 0;
				if ($dh = opendir($this->patchPath)) {
					while (false !== ($file = readdir($dh))) {
						//不是文件跳过
						if(!is_file($this->patchPath.'/'.$file)) continue;
						//日期不匹配跳过
						if(strpos($file,$datePatch)===false) continue;
						//前缀不匹配跳过
						$_arr = explode('_',$file);
						if($_arr[0]!=$prefix) continue;

						$_arr = explode(".",$_arr[2]);
						if($_arr[0]+0>$maxIndex) {
							$maxIndex = $_arr[0];
							continue;
						}

					};
					$fileName = "{$prefix}_{$datePatch}_".($maxIndex+1).".txt";
					closedir($dh);
				}
			}
			//创建文件名
		}
		//dump($fileName);exit;
		$file = $this->patchPath.'/'.$fileName;
		$this->rewrite($file, $sql);

		//修改数据库标记为已执行
		$arr = $this->m->find(array('fileName'=>$fileName));
		$arr['fileName'] = $fileName;
		$arr['content'] = $sql;
		$arr['memo']="程序员提交，不需执行";
		$this->m->save($arr);

		$arr = array('success'=>true,'curFile'=>$fileName);
		echo json_encode($arr);exit;
	}


	//删除某个补丁文件
	function actionRemove() {
		$f = $this->patchPath.'/'.$_POST['fileName'];
		if(!file_exists($f)) {
			$arr = array('success'=>false,'msg'=>'文件不存在');
			echo json_encode($arr);exit;
		}

		$ret = unlink($f);
		if(!$ret) {
			$arr = array('success'=>false,'msg'=>'文件删除失败');
			echo json_encode($arr);exit;
		}
		$arr = array('success'=>true);
		echo json_encode($arr);exit;
	}

	//实施人员执行的自动更新
	//如果是开发人员执行，必须保证不执行自己提交的更新
	function actionAutoUpdate() {
		//得到所有未执行的文件，和_log进行比对

		$smarty= $this->_getView();
		$smarty->assign('dateFrom',$this->dateFrom);
		//$content = $this->removeBOM(file_get_contents($fileName));
		//$smarty->assign('prefix',$content);
		$smarty->display('Tool/Dbchange/AutoUpdate.tpl');
	}
	//显示已更新日志
	function actionShowLog() {
		// $title = '色纱回收列表';
		///////////////////////////////模板文件
		$tpl = 'TableList.tpl';
		///////////////////////////////模块定义
		FLEA::loadClass('TMIS_Pager');

		$arr = TMIS_Pager::getParamArray(array(
			'dateFrom'=>'',
			'dateTo'=>''
		));
		$sql = "select * from sys_dbchange_log where 1";
		if($arr['dateFrom']!='') {
			$sql .= " and dt>='{$dateFrom} 00:00:00'";
		}
		if($arr['dateTo']!='') {
			$sql .= " and dt<='{$dateTo} 23:59:59'";
		}
		$sql .= " order by dt desc";
		$pager = new TMIS_Pager($sql);
		$rowset =$pager->findAll();
		if(count($rowset)>0) {
			foreach ($rowset as & $v) {
				$v['sql'] = $v['content'];
			}
		}

		$arr_field_info=array(
			'fileName'=>'文件名',
			"dt" =>array('text'=>'执行时间','width'=>150),
			"memo" =>"备注",
			"sql" =>"详细",
		);


		$smarty = & $this->_getView();
		$smarty->assign('add_display','none');
		$smarty->assign('arr_field_info',$arr_field_info);
		$smarty->assign('arr_field_value',$rowset);
		$smarty->assign('page_info',$pager->getNavBar($this->_url($_GET['action'])));
        $smarty->display('Tool/Dbchange/TblList.tpl');
	}

	//清空之前的补丁文件，防止文件过多。不清空日志文件_log.txt
	function actionClearPatchs() {
	}

	//根据日期获得当天某个前缀开头的所有补丁名
	function actionGetPatchsByAjax() {
		$prefix = $_POST['prefix'];
		$d = substr(join('',explode('-',$_POST['datePatch'])),2);

		$len = strlen($prefix);
		$arrFile = array();
		if ($dh = opendir($this->patchPath)) {
			while (false !== ($file = readdir($dh))) {
				//不是文件跳过
				if(!is_file($this->patchPath.'/'.$file)) continue;
				//日期不匹配跳过
				if(strpos($file,$d)===false) continue;
				//前缀不匹配跳过
				$_arr = explode('_',$file);
				if($_arr[0]!=$prefix) continue;
				$index = $_arr[count($_arr)-1];
				$temp = explode('.',$index);
				$index=$temp[0];
				$arrFile[$index] = $file;
			};
			// dump($arrFile);
			closedir($dh);
		}

		//arrFile可能会被解析成对象，所以需要强制转换为数组
		ksort($arrFile);
		$temp = array();
		foreach($arrFile as & $v) $temp[] = $v;
		echo json_encode($temp);exit;
	}

	//得到所有需执行的patch,和sys_dbchange_log表进行比对
	function actionGetPatchs4Up() {
		//从数据中取得所有已执行的文件名,一年前的不执行
		// $dateFrom= $this->dateFrom;
		// //$dateTo= date('Y-m-d');
		// $sql = "select * from sys_dbchange_log where dt>'{$dateFrom}'";
		// $rowset = $this->m->findBySql($sql);
		// $arrLog = array_col_values($rowset,'fileName');

		//取得所有的补丁文件
		if ($dh = opendir($this->patchPath)) {
			while (false !== ($file = readdir($dh))) {
				//不是文件跳过
				if(!is_file($this->patchPath.'/'.$file)) continue;
				$temp = explode('_',$file);
				$temp1 = explode('.',$temp[2]);//将后缀名分割开

				//判断文件是否执行过
				$sql = "select * from sys_dbchange_log where fileName='{$file}'";
				$_temp = $this->m->findBySql($sql);
				if(count($_temp)>0) continue;
				// $dt = filemtime($this->patchPath.'/'.$file);
				// dump($temp[1].'_'.$temp1[0].'_'.$temp[0]);
				$arrFile[$temp[1].'_'.$temp1[0].'_'.$temp[0]] = $file;
			};
			closedir($dh);
		}
		//按创建时间排序
		ksort($arrFile);
		 // dump($arrFile);exit;

		$arrNeed = array();
		foreach ($arrFile as $key=>&$v){
			if(in_array($v,$arrLog)) continue;
			$arrNeed[] = $v;
		}
		//dump($arrNeed);exit;
		echo json_encode($arrNeed);
		exit;
	}

	//利用ajax获得某个文件的内容
	function actionGetSqlByAjax() {
		$fileName= $this->patchPath.'/'.$_POST['fileName'];
		if(!file_exists($fileName)) {
			$arr = array('success'=>false,'msg'=>'文件不存在');
			echo json_encode($arr);exit;
		}
		$content = file_get_contents($fileName);
		//去掉bom头
		$content = $this->removeBOM($content);

		$arr = array('success'=>true,'content'=>$content);
		echo json_encode($arr);exit;

	}


	//重写入文件,不存在则新建
	function rewrite($filename, $data) {
		$filenum = fopen($filename, "w");
		flock($filenum, LOCK_EX);
		fwrite($filenum, $data);
		fclose($filenum);
	}
	function removeBOM($content) {
		$charset[1] = substr($content, 0, 1);
		$charset[2] = substr($content, 1, 1);
		$charset[3] = substr($content, 2, 1);
		if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
			$content = substr($content, 3);
		}
		return $content;
	}

	//执行某个文件
	function actionExecute() {
		//文件不存在返回错误
		$fileName= $this->patchPath.'/'.$_POST['fileName'];
		if(!file_exists($fileName)) {
			$arr = array('success'=>false,'msg'=>'文件不存在');
			echo json_encode($arr);exit;
		}
		//已经执行过了，返回错误
		$arr = $this->m->find(array('fileName'=>$fileName));
		if($arr) {
			echo json_encode(array(
				'success'=>false,
				'msg'=>'此文件已经执行过，不需要标记'
			));
			exit;
		}
		//取得文件内容
		$content = file_get_contents($fileName);
		//去掉bom头
		$content = $this->removeBOM($content);
		//作为sql语句执行,
		$arrSql = split(';',$content);
		foreach($arrSql as & $v) {
			$v = trim($v);
		}
		$arrSql = array_filter($arrSql);
		//如果sql语句执行错误，返回错误信息
		foreach($arrSql as & $v) {
			$q=mysql_query($v);
			$error = mysql_error();
			if($error!='') {
				echo json_encode(array(
					'success'=>false,
					'msg'=>"{$_POST['fileName']}执行失败,错误如下:<br /><font color='red'>{$error}</font><br/>sql语句:<pre>{$v}</pre>",
					'fileName'=>$_POST['fileName']
				));
				exit;
			}
		}

		//如果sql语句执行成功,插入日志表中
		$ret = array('fileName'=>$_POST['fileName'],'content'=>$content);
		$this->m->create($ret);
		$arr = array(
			'success'=>true,
			'msg'=>$_POST['fileName']."执行成功",
			'fileName'=>$_POST['fileName']
		);
		echo json_encode($arr);
		exit;
	}

	//将某个补丁文件标记为已执行
	function actionDoMark() {
		// dump($_POST);
		$f = $_POST['fileName'];
		//查找数据库中是否存在该记录,不存在则新增
		$arr = $this->m->find(array('fileName'=>$f));
		if($arr) {
			echo json_encode(array(
				'success'=>false,
				'msg'=>'此文件已经执行过，不需要标记'
			));
			exit;
		}
		//开始插入
		$arr = array('fileName'=>$f,'memo'=>'人工标记为已执行');
		$this->m->create($arr);
		echo json_encode(array(
			'success'=>true,
			'fileName'=>$f
		));
		exit;
	}

	//修改程序员前缀
	function actionChangePrefix() {
		$pre = $_POST['prefix'];
		$fileName = $this->preFile;
		//如果文件不存在，创建文件
		//存在的话修改
		$this->rewrite($fileName, $pre);
		echo json_encode(array(
			'success'=>true
		));
		exit;
	}

	//自动svn update
	function actionSvnUpdate() {
		// $cmd = "TortoiseProc.exe /command:update /path:E:/phpstudy/WWW/ranzheng_donghui1 /closeonend:0";
		$cmd = "svn update";
		// $cmd = "dir";
		//ret2状态码,0成功
		//ret1结果输出
		exec($cmd,$ret1,$ret2);
		// exec('netstat -ano|findstr "80"',$ret1,$ret2);
		if($ret2===0) {
			echo "执行结果：{$ret2}-success\n";
		} else {
			echo "执行结果：{$ret2}-fail,可能原因：\n
				1,项目根目录下必须有svn命令和相关dll文件，从tools目录获得,\n
				2,检查php.ini中的disable_function中去掉exec,exec_shell)\n";
		}
		foreach ($ret1 as $v){
		    echo "<pre>".htmlentities($v)."</pre>";
		}
	}
}
?>