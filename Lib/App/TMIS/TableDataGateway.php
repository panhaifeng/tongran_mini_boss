<?php
FLEA::loadFile('FLEA_Helper_Array.php');
FLEA::loadClass('FLEA_Db_TableDataGateway');
class TMIS_TableDataGateway extends FLEA_Db_TableDataGateway {
	/**
	*最能表达某个记录意义的字段名,比如用户表中的用户姓名字段，客户表中的客户姓名字段等。
	*当select控件中显示时有用。
	*/
	var $primaryName;

	function findCountBySql($querystring) {
		#有limit或者有union的直接执行mysql_num_rows(mysql_query($sqlstr));
		#如果是简单的browsing，用count(*),能大大加快速度
		#其他的用select  SQL_CALC_FOUND_ROWS .....
		$querystring=preg_replace("/[\n\r	]/"," ",trim($querystring));
		$sqlstr=strtolower($querystring);

		$pos_select=strpos($sqlstr,"select ");
		$pos_limit=strpos($sqlstr," limit ");
		$pos_union=strpos($sqlstr," union ");
		$str="explain ".$sqlstr;
		$query=mysql_query($str);
		$cnt_table=mysql_num_rows($query);
		$re=mysql_fetch_array($query);

		if ($cnt_table==1&&$re['Extra']=="") {
			#第一个select位置,第一个 from 位置之间插入count(*) cnt, return $re[cnt];
			$FPos = strpos($sqlstr," from");
			$str=substr_replace($querystring,"select count(*) rows",0,$FPos);
			//echo $str;
			$re=mysql_fetch_array(mysql_query($str));
			return $re['rows'];
		}
		if ($pos_limit!==false||$pos_union!==false) {
			return mysql_num_rows(mysql_query($querystring));
		}
		if ($pos!==false) {
			$str=substr_replace($sqlstr," SQL_CALC_FOUND_ROWS",$pos+6,0). " limit 1";
			mysql_query($str);
			$re=mysql_fetch_array(mysql_query("SELECT FOUND_ROWS() as count"));
			return $re[count];
		}
	}


	function save( &$row,$saveLinks = true) {
		// dump($row);exit;
		if ($this->autoLink && $saveLinks) {
             foreach ($this->links as $link) {
				//echo($link->mappingName);

                 /* @var $link FLEA_Db_TableLink */
                 // 跳过不需要处理的关联
                 if (!$link->enabled || !$link->linkUpdate
                     || !isset($row[$link->mappingName])
                     || !is_array($row[$link->mappingName]))
                 {
                    continue;
                }

				foreach($row[$link->mappingName] as & $v) {
					if (is_array($v) && $v[ifRemove]==='1') {
						//删除该记录
						$_m = & FLEA::getSingleton($link->tableClass);
						$_m->removeByPkv($v[id]);
						//从row中删除
						$v = false;
					}
				}

				$row[$link->mappingName] = array_filter($row[$link->mappingName]);
             }
			//exit;
         }
		//dump($row);exit;
		return parent::save( $row,$saveLinks = true);
	}

	function changeYlKucun($ylId) {
		$sql = "select * from cangku_yl_init where ylId='$ylId'";
		$re = mysql_fetch_assoc(mysql_query($sql));

		$sql = "select sum(cnt) as cnt,sum(cnt*danjia) as money from cangku_yl_ruku2yl where ylId='$ylId'";
		$re1 = mysql_fetch_assoc(mysql_query($sql));

		$sql = "select sum(cnt) as cnt,sum(cnt*danjia) as money from cangku_yl_chuku2yl where ylId='$ylId'";
		$re2 = mysql_fetch_assoc(mysql_query($sql));

		$cnt = $re['initCnt'] + $re1['cnt'] - $re2['cnt'];
		$money = $re['initMoney'] + $re1['money'] - $re2['money'];
		//dump($re);dump($re1);dump($re2);exit;
		if($re['id']>0) $sql = "update cangku_yl_init set kucunCnt='$cnt',kucunMoney='$money' where ylId='$ylId'";
		else $sql = "insert into cangku_yl_init(ylId,kucunCnt,kucunMoney) values('$ylId','$cnt','$money')";
		mysql_query($sql) or die(mysql_error());
		return true;
	}

	function changeKucun($proId) {
		$sql = "select * from cangku_init where productId='$proId'";
		$re = mysql_fetch_assoc(mysql_query($sql));

		$sql = "select sum(cnt) as cnt,sum(cnt*danjia) as money from cangku_ruku2product where productId='$proId'";
		$re1 = mysql_fetch_assoc(mysql_query($sql));

		$sql = "select sum(cnt) as cnt,sum(cnt*danjia) as money from cangku_chuku2product where productId='$proId'";
		$re2 = mysql_fetch_assoc(mysql_query($sql));

		$cnt = $re['initCnt'] + $re1['cnt'] - $re2['cnt'];
		$money = $re['initMoney'] + $re1['money'] - $re2['money'];
		//dump($re);dump($re1);dump($re2);exit;
		if($re['id']>0) $sql = "update cangku_init set kucunCnt='$cnt',kucunMoney='$money' where productId='$proId'";
		else $sql = "insert into cangku_init(productId,kucunCnt,kucunMoney) values('$proId','$cnt','$money')";
		mysql_query($sql) or die(mysql_error());
		return true;
	}

	//修改当前应收款
	function changeAr($clientId) {
		$sql = "select * from Caiwu_Ar_Zhengli_Init where clientId='$clientId'";
		$re = mysql_fetch_assoc(mysql_query($sql));

		$sql = "select sum(money) as money from caiwu_ar_Zhengli_guozhang where clientId='$clientId'";
		$re1 = mysql_fetch_assoc(mysql_query($sql));

		$sql = "select sum(money) as money from caiwu_income where clientId='$clientId'";
		$re2 = mysql_fetch_assoc(mysql_query($sql));

		$money = $re['initMoney'] + $re1['money'] - $re2['money'];
		//dump($re);dump($re1);dump($re2);exit;
		if($re['id']>0) $sql = "update Caiwu_Ar_Zhengli_Init set kucunMoney='$money' where clientId='$clientId'";
		else $sql = "insert into Caiwu_Ar_Zhengli_Init(clientId,kucunMoney) values('$clientId','$money')";
		mysql_query($sql) or die(mysql_error());
		return true;
	}

	//修改当前应收款
	function changeYf($supplierId) {
		$sql = "select * from caiwu_yf_init where supplierId='$supplierId'";
		$re = mysql_fetch_assoc(mysql_query($sql));

		$sql = "select sum(money) as money from caiwu_yf_guozhang where supplierId='$supplierId'";
		$re1 = mysql_fetch_assoc(mysql_query($sql));

		$sql = "select sum(money) as money from caiwu_expense where supplierId='$supplierId'";
		$re2 = mysql_fetch_assoc(mysql_query($sql));

		$money = $re['initMoney'] + $re1['money'] - $re2['money'];
		//dump($re);dump($re1);dump($re2);exit;
		if($re['id']>0) $sql = "update caiwu_yf_init set kucunMoney='$money' where supplierId='$supplierId'";
		else $sql = "insert into caiwu_yf_init(supplierId,kucunMoney) values('$supplierId','$money')";
		mysql_query($sql) or die(mysql_error());
		return true;
	}

	//取得记录的第一行的第一个字段的值
	function getOne($sql) {
		$dbo = $this->getDBO();
		return $dbo->getOne($sql);
	}	
}
?>