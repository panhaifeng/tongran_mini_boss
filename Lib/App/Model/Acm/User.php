<?php
FLEA::loadClass('TMIS_TableDataGateway');
class Model_Acm_User extends TMIS_TableDataGateway {
	var $tableName = 'acm_userdb';
	var $primaryKey = 'id';
	var $primaryName = 'realName';
	var $manyToMany = array(
		array(
			'tableClass' => 'Model_Acm_Role' ,
			'mappingName' => 'roles',
			'joinTable' => 'acm_user2role',
			'foreignKey' => 'userId',
			'assocForeignKey' => 'roleId'
		),
		array(
			'tableClass' => 'Model_Jichu_Employ' ,
			'mappingName' => 'traders',
			'joinTable' => 'acm_user2trader',
			'foreignKey' => 'userId',
			'assocForeignKey' => 'traderId'
		),
	);

	/*var $hasMany = array(
		array(
			'tableClass' => 'Model_Acm_User2trader',
			'foreignKey' => 'userId',
			'mappingName' => 'TraderId'
		)
	);*/

	function getRoles($userId) {
		$arr = $this->find($userId);
		return $arr['roles'];
	}

	//一个用户可能对应多个业务员，所以在搜索的时候会用到in ******************
    //是否允许查看所有订单
	//如果是admin或者组名为'管理员'，返回true;
	//模块中有个功能叫'浏览所有订单',如果能访问该权限则返回true;
	function canSeeAllOrder($userId) {
		// dump($userId);exit;
		$user = $this->find($userId);
		// dump($user);exit;
		if ($user[userName]=='admin') return true;
		$funcId= '100-1';
		$roles = $this->getRoles($userId);
		// dump($roles);exit;
		foreach( $roles as & $v) {
		    $sql = "select count(*) cnt from acm_func2role where roleId='{$v['id']}' and menuId='{$funcId}'";
		    $r=$this->findBySql($sql);
		    // dump($r);exit;
		    if($r[0]['cnt']>0) return true;
		}
		return false;
		//dump($roles);exit;
//		//得到"浏览所有订单"的相关funcId
//		$mFunc = & FLEA::getSingleton('Model_Acm_Func');
//		$condition = array(
//			array('funcName','浏览所有订单','=','or'),
//			array('funcName','查看所有订单','=','or'),
//		);
//		$func = $mFunc->find($condition);
//		$funcId = $func['id'];
//
//		$path = $mFunc->getPath($func);
//		$nodes = array_col_values($path,'id');
//		$nodes[] = $funcId;
//		//dump($user);
//		$roles = $user['roles'];
//		//dump($roles);exit;
//		if($roles) {
//			$ids = join(',',$nodes);
//			foreach($roles as & $v) {
//				$sql = "select count(*) cnt from acm_func2role where roleId='{$v['id']}' and funcId in({$ids})";
//				//dump($sql);
//				$rr = $this->findBySql($sql);
//				if($rr[0]['cnt']>0) return true;
//			}
//		}
//		return false;

	}

	#得到当前用户被允许查看哪个业务员的顶单,一般是姓名匹配的单个业务员
	function getTraderIdOfCurUser($userId){
		$user = $this->find($userId);
		$mTrader = & FLEA::getSingleton('Model_JiChu_Employ');
		$trader = $mTrader->find(array(employName=>$user[realName]));
		return $trader[id];
		//$roles = array_col_values($user[roles],'roleName');
		/*if (count($roles)>0) foreach ($roles as $v) {
			//刘果的秘书只能看到刘果的单子, 刘果的userId是23
			if ($v=="业务经理秘书") {
				$userId = 23;
				return $userId;
			}
		}*/
		//return $users;
	}
	//一个用户可能对应多个业务员，所以在搜索的时候会用到in
	//查找当前匹配的业务员有哪些
	function getTraderIdByUser($userId,$type=false){
		$allTrader = $this->canSeeAllOrder($userId);

		//表示可以查看所属有的
		if($allTrader){
			return array('_ALL_'=>true);
		}

		$user = $this->find($userId);
		$traders = array_col_values($user['traders'],'id');
		if(!$traders){
			$traders[] = -1;
		}
		// dump($users);exit;
		return array('_ALL_'=>false ,'Traders'=>$traders);
	}

	/**
	 * 查找所有的用户信息，除了管理员信息除外
	 * Time：2015/08/28 17:51:12
	 * @author li
	 * @param $_admin = true 表示同时获取管理员信息
	 * @return Array
	*/
	function getUser($_admin = false){
		//筛选条件
		if(!$_admin)$_condition[]=array('userName','admin','<>');

		//查询字段
		$field = 'id,userName,realName,shenfenzheng';

		//去除关联信息
		$this->clearLinks();

		//查找所有数据
		$rows = $this->findAll($_condition,'convert(trim(realName) USING gbk),userName',null,$field);

		return $rows;
	}

	function getOptions(){
        $row = $this->findAll();
        foreach($row as & $v){
            $arr[]=array('value'=>$v[$this->primaryKey],'text'=>$v[$this->primaryName]);
        }
        return $arr;
    }
}
?>