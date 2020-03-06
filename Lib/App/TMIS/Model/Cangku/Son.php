<?php
/*
*需要形成收发存报表的子表模型需要从此类继承
*主要就是在子表crud时关联修改库存表的记录
*变化点：需要复制到库存表中字段有变化
		库存汇总的字段有变化
*要求:
1，出入库记录必须是主从表结构,且主键都为id
1,库存表为必须字段：、
	rukuId
	chukuId
	dateFasheng,发生日期
  `cntFasheng` decimal(15,2) NOT NULL COMMENT '发生数量,入库为+，出库为-',
  `moneyFasheng` decimal(15,2) NOT NULL COMMENT '金额'
2，库存汇总字段在出入库表和库存表中必须一致，不能有的是chanpinId,有的是productId等
3，库存Model不需要定义belongTo属性，减少程序员工作量，但是需要在子表类定义时需要制定外键字段
4, 库存表中的索引必须建立完善，大数据下必须保证速度
5, 收发存报表必须先调用
*
*/
load_class('FLEA_Db_TableDataGateway');
class TMIS_Model_Cangku_Son extends FLEA_Db_TableDataGateway {
	//库存model标识符
	var $modelKucun = 'Model_Cangku_Kucun';
	//是否出库，如果是出库，记录生成时数量需要*-1
	var $isChuku = false;
	//子表记录中的主表映射名,用来取得主表记录
	var $mappingName = 'Ruku';
	//日期字段,必须在主表中
	var $fldDate = 'rukuDate';
	//数量,金额字段,必须在子表中,且数量字段可以是多个,键值表示入库、出库表中的字段,value值表示库存表中的字段
	var $fldCnt = array('cnt'=>'cnt');
	var $fldMoney = array('money'=>'money');
	//其他需要复制到库存表中的字段，主要是汇总字段(分主从表,字段名必须一致)，也可能是数量字段（不参与库存计算)
	var $fldCopyMain = array('kuweiId','supplierId');
	var $fldCopySon = array('productId','pihao');
	var $sumKind=array('Init','Ruku','Chuku');
	var $force_master = true;

	//记录新增后在库存表中插入记录
	function _afterCreateDb(&$row) {
		$this->_createIntoKucun($row);
	}

	//修改之后,删掉原来的数据，重新生成即可
	function _afterUpdateDb(&$row) {
		$m = & FLEA::getSingleton($this->modelKucun);
		//找到匹配的库存记录
		$con = array($this->isChuku?'chukuId':'rukuId'=>$row['id']);
		$r = $m->find($con);
		$pkv = $r['id'];
		//删除原来的库存数据
		$m->removeByPkv($pkv);
		//重新生成库存数据
		$this->_createIntoKucun($row);
	}

	/**
	 * 在库存表里添加记录
	 * 原来里面的代码写在_afterCreateDb中
	 * 新增本方法目的：原来_afterUpdateDb重新生成库存直接调用_afterCreateDb，
	 	发现会出现bug，这里避免更新的时候会再次调用create，所以单独写了一个方法
	 * Time：2015/09/22 13:37:14
	 * @author li
	*/
	function _createIntoKucun(&$row){
		$son = $this->find(array('id'=>$row['id']));

		$main = $son[$this->mappingName];
		$mKucun = & FLEA::getSingleton($this->modelKucun);
		//如果是出库
		//形成需要插入库存表中的记录
		$_row = array(
			'dateFasheng'=>$main[$this->fldDate],
		);
		if($this->isChuku) {
			$_row['chukuId'] = $row['id'];
			// $_row['cntFasheng'] = $son[$this->fldCnt]*-1;
			// $_row['moneyFasheng'] =$son[$this->fldMoney] *-1;
			foreach ($this->fldCnt as $key => & $v) {
				$_row[$v]=$son[$key]*-1;
			}

			foreach ($this->fldMoney as $key => & $v) {
				$_row[$v]=$son[$key]*-1;
			}
		} else {
			$_row['rukuId'] = $row['id'];
			// $_row['cntFasheng'] = $son[$this->fldCnt];
			// $_row['moneyFasheng'] =$son[$this->fldMoney];
			foreach ($this->fldCnt as $key => & $v) {
				$_row[$v]=$son[$key]+0;
			}

			foreach ($this->fldMoney as $key => & $v) {
				$_row[$v]=$son[$key]+0;
			}
		}
		//先取得主表中的相关字段,再取得从表中的相关记录
		foreach($this->fldCopyMain as & $v) {
			$_row[$v]=$main[$v].'';
		}
		foreach($this->fldCopySon as & $v) {
			$_row[$v]=$son[$v].'';
		}
		$mKucun->create($_row);
	}

	//删除,关联删除库存记录即可
	function _afterRemoveDbByPkv ($id){
		$m = & FLEA::getSingleton($this->modelKucun);
		//找到匹配的库存记录
		$con = array($this->isChuku?'chukuId':'rukuId'=>$id);
		$r = $m->find($con);
		$pkv = $r['id'];
		$m->removeByPkv($pkv);
	}

	//重新生成库存表数据
	function refreshKucun() {
		$fld = $this->isChuku?'chukuId':'rukuId';
		$mKucun = & FLEA::getSingleton($this->modelKucun);

		//删除库存表中数据
		$sql = "delete from {$mKucun->qtableName} where $fld>0";
		$this->execute($sql);

		//重新生成数据
		$sql = "select * from {$this->qtableName} where 1";
		$rowset = $this->findBySql($sql);
		foreach($rowset as & $v) $this->_afterCreateDb($v);
	}

	//生成收发存报表的sql语句
	function getSfcSql() {
		//得到库存表名
		$mKucun = & FLEA::getSingleton($this->modelKucun);
		$tblKucun = $mKucun->qtableName;

		$strDateFrom = '{$arr[\'dateFrom\']}';
		$strDateTo = '{$arr[\'dateTo\']}';
		$strCon = '{$strCon}';
		$strGroup = '{$strGroup}';

		//汇总字段
		$strG = join(',',array_merge($this->fldCopyMain,$this->fldCopySon));

		//期初
		$sqlInit = "select {$strGroup},".$this->getStrSql('Init')."
		from {$tblKucun} where dateFasheng<'{$strDateFrom}'
		 {$strCon}";
		$sqlInit .= " group by {$strGroup}";
		//dump($sqlInit);

		//本期入库
		$sqlRuku = "select {$strGroup},
		".$this->getStrSql('Ruku')."
		from {$tblKucun} where
		dateFasheng>='{$strDateFrom}' and dateFasheng<='{$strDateTo}'
		and rukuId>0  {$strCon}";
		$sqlRuku .= " group by {$strGroup}";
		//dump($sqlRuku);

		//本期出库
		$sqlChuku = "select {$strGroup},
		".$this->getStrSql('Chuku','*-1')."
		from {$tblKucun} where
		dateFasheng>='{$strDateFrom}' and dateFasheng<='{$strDateTo}'
		and chukuId>0  {$strCon}";
		$sqlChuku .= " group by {$strGroup}";
		//dump($sqlChuku);

		//对sql进行加工
		$sqlUnion = "{$sqlInit}
		union
		{$sqlRuku}
		union
		{$sqlChuku}";

		$sql = "select
		{$strGroup},".$this->_getField()."
		from (".'{$sqlUnion}'.") as x
		group by {$strGroup}
		having ".$this->_getHaving();

		$str = '请将以下代码复制到你的控制器中进行二次修改:
		$strGroup="'.$strG.'";
		';
		$str.= '$sqlUnion="'.$sqlUnion.'";
		';
		$str.='$sql="'.$sql.'";';
		dump($str);exit;
		//return $str;
	}

	/**
	* @return string
	* 自动生成需要查询的数量与金额信息，可以西东分析fldCnt与fldMoney中定义的数组
	* 用于生成sql语句
	*/
	function getStrSql($str,$operation){
		//需要查找并sum的数据信息
		$tempInit=array();
		//数量信息
		foreach ($this->sumKind as & $vv) {
			foreach ($this->fldCnt as $key => & $v) {
				if($str==$vv)$tempInit[]="sum({$v}{$operation}) as {$v}{$vv}";
				else $tempInit[]="0 as {$v}{$vv}";
			}
		}
		//金额信息
		foreach ($this->sumKind as & $vv) {
			foreach ($this->fldMoney as $key => & $v) {
				if($str==$vv)$tempInit[]="sum({$v}{$operation}) as {$v}{$vv}";
				else $tempInit[]="0 as {$v}{$vv}";
			}
		}

		// $temp=array_merge($tempInit);
		$selField = join(',',$tempInit);
		return $selField;
	}

	/**
	* @return string
	* 用于生成sql语句
	*/
	function _getField(){
		//需要查找并sum的数据信息
		$temp=array();
		//数量信息
		foreach ($this->sumKind as & $vv) {
			foreach ($this->fldCnt as $key => & $v) {
				$temp[]="sum({$v}{$vv}) as {$v}{$vv}";
			}
		}
		//金额信息
		foreach ($this->sumKind as & $vv) {
			foreach ($this->fldMoney as $key => & $v) {
				$temp[]="sum({$v}{$vv}) as {$v}{$vv}";
			}
		}

		$selField = join(',',$temp);
		return $selField;
	}

	/**
	* @return string
	* 用于生成sql语句 having sum()<>0
	*/
	function _getHaving(){
		//需要查找并sum的数据信息
		$temp=array();
		//数量信息
		foreach ($this->sumKind as & $vv) {
			foreach ($this->fldCnt as $key => & $v) {
				$temp[]=" sum({$v}{$vv})<>0 ";
			}
		}
		//金额信息
		foreach ($this->sumKind as & $vv) {
			foreach ($this->fldMoney as $key => & $v) {
				$temp[]=" sum({$v}{$vv})<>0 ";
			}
		}

		$selField = join('or',$temp);
		return $selField;
	}
}
?>