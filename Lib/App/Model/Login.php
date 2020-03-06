<?php
load_class('FLEA_Db_TableDataGateway');
class Model_Login extends FLEA_Db_TableDataGateway {
	var $primaryKey = 'id';
	var $tableName = 'acm_userdb';
	var $usernameField = 'userName';
	var $passwordField = 'passwd';

	var $needCreateLog = true;// 需要打日志则加此参数
    var $moduleName = '用户表';// 模块名称

	function FLEA_Com_RBAC_UsersManager() {
		log_message('Construction FLEA_Com_RBAC_UsersManager', 'debug');
		parent::FLEA_Db_TableDataGateway();
		$this->meta[strtoupper($this->emailField)]['complexType'] = 'EMAIL';
	}

	/**
	 * 返回指定 ID 的用户
	 *
	 * @param mixed $id
	 *
	 * @return array
	 */
	function findByUserId($id) {
		return $this->findByField($this->fullTableName . '.' . $this->primaryKey, $id);
	}

	/**
	 * 返回指定用户名的用户
	 *
	 * @param string $username
	 *
	 * @return array
	 */
	function findByUsername($username) {
		return $this->findByField($this->usernameField, $username);
	}

	/**
	 * 检查指定的用户名是否已经存在
	 *
	 * @param string $username
	 *
	 * @return boolean
	 */
	function existsUsername($username) {
		return $this->findCount($this->usernameField . ' = ' .
			$this->dbo->qstr($username)) > 0;
	}



	/**
	 * 验证指定的用户名和密码是否正确
	 *
	 * @param string $username 用户名
	 * @param string $password 密码
	 *
	 * @return boolean
	 *
	 * @access public
	 */
	function validateUser($username, $password) {
		$user = $this->findByField($this->usernameField, $username, null,
			$this->passwordField);
		//die($user);
		if (!$user) { return false; }
		//die($this->checkPassword($password, $user[$this->passwordField]));
		return $this->checkPassword($password, $user[$this->passwordField]);
	}

	/**
	 * 检查密码的明文和密文是否符合
	 *
	 * @param string $cleartext 密码的明文
	 * @param string $cryptograph 密文
	 *
	 * @return boolean
	 *
	 * @access public
	 */
	function checkPassword($cleartext, $cryptograph) {
	/**
	 * 取消加密
	 switch ($this->encodeMethod) {
	 case PWD_MD5:
	 return (md5($cleartext) == rtrim($cryptograph));
	 case PWD_CRYPT:
	 return (crypt($cleartext, $cryptograph) == rtrim($cryptograph));
	 case PWD_CLEARTEXT:
	 return ($cleartext == rtrim($cryptograph));
	 case PWD_SHA1:
	 return (sha1($cleartext) == rtrim($cryptograph));
	 case PWD_SHA2:
	 return (hash('sha512', $cleartext) == rtrim($cryptograph));

	 default:
	 return false;
	 */
		return ($cleartext == $cryptograph);
	}

	//	function checkPasswordSn($cleartext, $cryptograph, $username) {
	//		//把用户输入的密码分成两段
	//		$password = substr($cleartext,0,strlen($cleartext)-6);
	//		$snPassword = substr($cleartext,strlen($cleartext)-6);
	//
	//		//调用判断动态密码口令函数
	//		$result = $this->getSn($username, $snPassword);
	//
	//		//echo($password.'--'.$cryptograph.'--'.$result); exit;
	//		if (($password == $cryptograph) && $result){
	//			return true;
	//		}else{
	//			return false;
	//		}
	//    }


	//判断动态口令是否正确，正确返回true结果
	function checkSn($userId, $snPassword) {
		$sql = "select * from acm_userdb where id='{$userId}'";

		$re=mysql_fetch_assoc(mysql_query($sql));
		$snInfo = $re['snInfo'];
		$sn = $re['sn'];
		//dump($re);exit;
		//如果取sninfo值有误，则直接退出。
		if (!$snInfo) {
			return false; exit;
		}

		$b=new COM("SeaMoonDLL.ClassKeys");//调用Com组件
		//调用验证接口
		$a=$b->CheckITSecurityPassWord($snInfo,$snPassword);//调用验证方法，第一个参数为动态令牌SN号对应的字符串，第二个参数为动态密码
		//dump($a);exit;
		if (strlen($a)>3) {
		//echo "动态密码验证通过"; //此时你需要把$a的值更新到你的数据库，下次调用时取出此字符串作为参数
		//把新的sninfo写入acm_sninfo的sninfo字段，如果写入失败，直接返回FALSE结果，禁止登入。
			$update = "update acm_userdb set snInfo = '$a' where id = '{$userId}'";
			if (!mysql_query($update)) {
				return false;
			}
			return true;
		}elseif($a=="-2") {
		//echo "系统内部错误";
			return false;
		}else {
		//echo "动态密码错误";
			return false;
		}
	}

	//修改用户的最后登录日期和登录次数
	function changeLoginTime($userId) {
	//
		$dt=date('Y-m-d');
		$cnt=0;
		$str="SELECT * FROM acm_userdb where id='{$userId}' and lastLoginTime='{$dt}'";
		$re=mysql_fetch_assoc(mysql_query($str));
		$cnt+=$re['loginCnt'];
		//修改
		$newCnt=$cnt+1;
		$sql="UPDATE acm_userdb SET lastLoginTime='{$dt}' , loginCnt='{$newCnt}' where id='{$userId}'";
		if (!mysql_query($sql)) {
			return false;
		}
		return true;
	}

	//修改用户身份的经验和积分,传入的参数为数组，防止后期规则的改变
	function changeUser($arr) {
		//if($arr['userName']=='admin') return false;
		//登录一次经验值为1，积分为5
		$initJifen=5;
		$initJingyan=5;
		$dt=date('Y-m-d');
		$str="SELECT count(*) as cnt FROM jifen_user WHERE remoteUserId='{$arr['userId']}'";
		$re=mysql_fetch_assoc(mysql_query($str));
		//dump($str);exit;
		//admin用户登录不进行积分操作
		if($_SESSION['USERNAME']=='admin'){
		    return false;
		}
		//新用户登录
		if(!$re || $re['cnt']==0) {
			$str="SELECT * FROM acm_userdb where id='{$arr['userId']}'";
			$_r=mysql_fetch_assoc(mysql_query($str));
			$sql="INSERT INTO jifen_user (remoteUserId,shenfenzheng,realName,userCode,passwd,remoteJifen,jifen,remoteJingyan,jingyan)
		    VALUES ('{$arr['userId']}','{$_r['shenfenzheng']}','{$_r['realName']}','{$_r['userName']}','{$_r['passwd']}',{$initJifen},{$initJifen},{$initJingyan},{$initJingyan})";
			if (!mysql_query($sql)) {
				return false;
			}
		}
		//老用户登录
		else {
		//新判断是否符合规则
			$sql1="SELECT * FROM acm_userdb where id='{$arr['userId']}'";
			$r1=mysql_fetch_assoc(mysql_query($sql1));
			if(($r1['lastLoginTime']==$dt && $r1['loginCnt']<2) || ($r1['lastLoginTime']!=$dt)) {
			//改变用户表中的积分信息
				$str1="SELECT * FROM jifen_user where remoteUserId='{$arr['userId']}'";
				$rr=mysql_fetch_assoc(mysql_query($str1));
				$newJifen=$rr['remoteJifen']+$initJifen;
				$newJifen1=$rr['jifen']+$initJifen;
				$newJy=$rr['remoteJingyan']+$initJingyan;
				$newJy1=$rr['jingyan']+$initJingyan;
				$sql="UPDATE jifen_user set remoteJifen='{$newJifen}',jifen='{$newJifen1}',remoteJingyan='{$newJy}',jingyan='{$newJy1}' where remoteUserId='{$arr['userId']}'";
				if (!mysql_query($sql)) {
					return false;
				}
			}
			else return false;
		}

		//改变企业表中的经验信息
		$str2="SELECT * FROM `jifen_comp` where 1";
		$r2=mysql_fetch_assoc(mysql_query($str2));
		$j1=$r2['remoteJingyan']+$initJingyan;
		$j2=$r2['jingyan']+$initJingyan;
		$sql2="UPDATE jifen_comp SET remoteJingyan='{$j1}',jingyan='{$j2}'";
		//echo $sql2;exit;
		if (!mysql_query($sql2)) {
			return false;
		}
		return true;
	}

	//将用户好企业的积分上传至服务器，比下载到本地系统中



}
?>