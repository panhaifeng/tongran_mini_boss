<?php
//FLEA::loadClass('TMIS_Nodes');
class Model_Acm_Func {
	var $menu;
	function Model_Acm_Func() {
		$mUser = FLEA::getSingleton('Model_Acm_User');
		$m = & FLEA::getAppInf('menu');
		require $m;
		$arr = array('children'=>$_sysMenu);
		$this->menu=$this->_getChildren($arr);
		$arr = array('children'=>$this->menu);
		//dump(tree_to_array($arr),'children');exit;
		$c = count($_sysMenu);

		//载入附加的功能点
		require "Config/menu_memo.php";
		if($_sysMenuMemo) {
			foreach($_sysMenuMemo as $key =>&$v) {
				$node = &$this->getNodeByPath($key);
				if($node==null) $node['id'] = $key;
				$temp = explode('-',$node['id']);
				$node['nodeIndex']=$temp[count($temp)-1];
				$this->addChildren($node,$v);
			}
		}
		//dump($this->menu);exit;
		$this->menu= $this->reOrder($this->menu);
		//dump($this->menu);exit;
	}

	function reOrder(& $menu) {
		//if(!$menu['children']) return $menu;
		$menu = array_column_sort($menu,'nodeIndex');
		$i=0;
		$arr = array();
		foreach($menu as &$v) {
			$v['nodeIndex'] = $i;
			if(isset($v['children']) && $v['children']) {
				$v['children'] =$this->reOrder($v['children']);
			}
			$arr[$v['id']] = & $v;
			$i++;
		}

		return $arr;
	}

	function addChildren(& $node,$v) {
		if(isset($node['children']) && count($node['children'])>0) {
			echo "{$node['id']}({$node['text']})节点下已有子节点，不可再增加节点";
			exit;
		}
		if(count($v['children'])==0) {//如果是叶子，直接增加后返回
			$node['text'] =$v['text'];
			//根据node['id']取得nodeIndex
			$temp = explode('-',$node['id']);
			$node['nodeIndex']=$temp[count($temp)-1];
			return true;
		}
		$node['text'] = $v['text'] ? $v['text'] : $node['text'];
		$node['nodeIndex'] = isset($node['nodeIndex']) ? $node['nodeIndex']:0;
		foreach($v['children'] as $key =>& $vv) {
			$n = &$this->getNodeByPath($key);
			if($n==null) $n['id'] = $key;
			$temp = explode('-',$n['id']);
			$n['nodeIndex']=$temp[count($temp)-1];
			$this->addChildren($n,$vv);
		}
	}

	function _getChildren($node) {
		if(isset($node['leaf']) && $node['leaf']) return null;
		$arr = array();
		//$i=0;
		foreach($node['children'] as & $v) {
			if(!$v['id']) continue;
			$temp = explode('-',$v['id']);
			$arr[$v['id']] = array(
				'id'=>$v['id'],
				'text'=>$v['text'],
				'nodeIndex'=>$temp[count($temp)-1]//此属性代表了同级节点中的位置信息，用来映射出树节点位置
			);
			if(!isset($v['leaf']) || !$v['leaf']) $arr[$v['id']]['children'] = $this->_getChildren($v);
			//$i++;
		}
		return $arr;
	}

	function getTreeJson($node) {
		if(!$node['children']) return null;
		$arr = array();
		foreach($node['children'] as & $v) {
			$temp = array(
				"id"=>$v['id'],//节点id
				"text"=> $v['text'],//标签文本
				"value"=> $v['id'].'',//值
				"showcheck"=> true,//是否显示checkbox
				//"isexpand"=> $v['leftId']+1==$v['rightId'] ? false : true,//是否展开,
				"isexpand"=>false,
				"checkstate"=> 0,//是否被选中
				"hasChildren"=> $v['children'] ? true : false,//是否有子节点
				//"ChildNodes"=> null,//子节点,仅当complete为true时起作用，如果使用ajax获得子节点，这里定义的将不起作用
				//"complete"=>false//是否已经完成，if true:不再进行递归检索，否则将搜索子项并展开,如果是ajax进行检索，childNodes属性将不再起作用
				"complete"=> true,
				'nodeIndex'=>$v['nodeIndex']
			);
			if(count($v['children'])>0) {
				$temp['ChildNodes'] = $this->getTreeJson($v);
			}
			$arr[] = $temp;
		}
		$arr = array_column_sort($arr,'nodeIndex');
		foreach($arr as $key=>& $v) {
			$v['nodeIndex'] = $key;
		}
		return $arr;
	}

	function getAuthTreeData($node) {
		if(!$node['children']) return null;
		$arr = array();
		foreach($node['children'] as & $v) {
			$temp = array(
				"id"        =>$v['id'],//节点id
				"text"     => $v['text'],//标签文本
				"value"     => $v['id'].'',//值
				'nodeIndex' =>$v['nodeIndex']
			);
			if(count($v['children'])>0) {
				$temp['children'] = $this->getAuthTreeData($v);
			}
			$arr[] = $temp;
		}
		$arr = array_column_sort($arr,'nodeIndex');
		foreach($arr as $key=>& $v) {
			$v['nodeIndex'] = $key;
		}
		return $arr;
	}

	//根据userId判断哪些目录节点需要隐藏,
	function changeVisible(&$node,$arr) {
		FLEA::loadClass('Model_Acm_User');
		$m = & FLEA::getSingleton('Model_Acm_User');
		if($arr['userName']=='admin') return $node;
		//dump($arr);exit;
		//得到用户属于的组
		if(!$arr['roles']) {
			$sql = "select group_concat(roleId) as roles
				from acm_user2role x
				left join acm_userdb y on x.userId=y.id
				where y.userName='{$arr['userName']}'";
			//dump($sql);exit;
			$r = $m->findBySql($sql);
			if($r[0]['roles']=='') {
				$node['hidden']=true;
				return false;
			}
			$arr['roles'] = $r[0]['roles'];
		}
		if($node['leaf']) {
			//如果未定义id,可见
			if(!isset($node['id'])) return $node;
			//在数据库中查找是否可使用当前节点
			$sql = "select count(*) cnt from acm_func2role x
				where (x.menuId like '{$node['id']}-%' or x.menuId='{$node['id']}') and roleId in({$arr['roles']})";
			//dump($node);
			$r = $m->findBySql($sql);
			if($r[0]['cnt']==0) {
				$node['hidden']=true;
				return false;
			}
			return $node;
		} else {//如果是目录
			//在数据库中查找，如果没有子功能可以访问，目录消失
			$sql = "select count(*) cnt from acm_func2role x
				where (x.menuId like '{$node['id']}-%' or x.menuId='{$node['id']}') and roleId in({$arr['roles']})";
			$r = $m->findBySql($sql);
			if($r[0]['cnt']==0) {
				//子节点里是否有未定义id的节点，如果有,可见
				$f = $this->_noIdInChildren($node);
				if(!$f) {//如果不存在未定义id的子节点，隐藏
					$node['hidden']=true;
					return false;
				}

			}

			//构造返回值
			$r = array();
			// dump($node);exit;
			foreach($node as $k=>& $v) {
				if($k=='children') continue;
				$r[$k] = $v;
			}
			//循环处理children
			foreach($node['children'] as $k=>& $v) {
				$a = $this->changeVisible($v,$arr);
				if($a) $r['children'][] = $a;
			}
			return $r;
		}
	}

	//权限判断之后的第二次设置可见性
	function changeVisibleBySys(&$node,&$sys) {
		//2012-8-29 根据老车要求，进行并存处理
//		if($sys['Plan_Gongyi']==1 && $node['id']=='2-2-5') {//做工艺隐藏新增染纱计划
//			$node['hidden'] = true;
//			return true;
//		}
//		if($sys['Plan_Gongyi']==0 && $node['id']=='2-1') {//不做工艺隐藏工艺菜单
//			$node['hidden'] = true;
//			return true;
//		}
		if($node['leaf']) return true;
		foreach($node['children'] as & $v) {
			$this->changeVisibleBySys($v,$sys);
		}
	}

	//判断节点的所有子节点是否存在未定义id的节点
	function _noIdInChildren($node) {
		foreach($node['children'] as & $v) {
			if($v['leaf']) {
				if (!isset($v['id'])){
					return true;
				}
			} else {
				$t = $this->_noIdInChildren($v);
				if($t) {
					return true;
				} else {
					return false;
				}
			}
		}
		return false;
	}

	//根据路径获得节点
	function &getNodeByPath($path) {
		$arr = explode('-',$path);
		if(count($arr)==0) return null;
		$n = array('children'=>& $this->menu);
		$k = '';
		foreach($arr as $key =>& $v) {
			$k .= $v.'-';
			$n = & $n['children'][substr($k,0,-1)];
		}
		return $n;
	}



}