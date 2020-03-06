<?php
/*********************************************************************\
*  Copyright (c) 1998-2013, TH. All Rights Reserved.
*  Author :Jeff
*  FName  :Option.php
*  Time   :2014/05/13 18:31:40
*  Remark :获取option的类,根据隐射数组来自动获得options
*  使用说明:
* $this->fldMain = array(
*     'kind'=>array('title'=>'设备分类','type'=>'select','optionType'=>'设备分类')
*    )
* 在编辑界面显示出来时会根据指定的optionType，获取相应的options
\*********************************************************************/
class TMIS_Option{
  var $_map = array(
      '币种'=>"function:getBizhong",
      '单位'=>"function:getUnit",
      'bankId'=>"model:Model_Jichu_Bank",
      '银行账号'=>'select bankname as text,id as value from caiwu_jichu_bank',
      '项目类型'=>'select collection_name as text,id as value from caiwu_jichu_incomeItem',
      '付款项目'=>'select payment_name as text,id as value from caiwu_jichu_expenseitem',
  );
  function __construct() {
    $this->_model = FLEA::getSingleton('Model_Acm_User');
  }

  /**
   * 得到options
   * @withHead:是否带空option(第一个提示项)
   */
  function getOptions($key,$withHead=true) {

    if(!$this->_map[$key]) {
      js_alert("在TMIS_Option中未发现{$key}所对应的选项!");
      exit;
    }

    //初始空选项
    if($withHead) {
      $ret[] = array('text'=>$key,'value'=>'');
    }
    $sql = $this->_map[$key];

    //如果是函数或model
    $temp = explode(':', $sql);
    if(strtolower($temp[0])=='function') {
      return $this->$temp[1]();
    }elseif(strtolower($temp[0])=='model'){
      return $this->getDataByModel($temp[1]);
    }
    //如果是静态数组，直接返回
    if(is_array($sql)) {
      foreach($sql as & $v) {
        $ret[] = $v;
      }
      return $ret;
    }
    $rows = $this->_model->findBySql($sql);
    foreach($rows as & $v) {
      $ret[] = $v;
    }
    return $ret;
  }

  /**
   * 通过model获取整个数据表的信息
   * @author li
   * @param String
   * @return Array
  */
  function getDataByModel($model){
    $_model = & FLEA::getSingleton($model);
    //排序信息
    $arr = $_model->findAll(null,$_model->sortByKey);

    $row=array();
    foreach ($arr as $key => $v) {
       $row[]=array('text'=>$v[$_model->primaryName],'value'=>$v[$_model->primaryKey]);
    }

    return $row;
  }

  /**
   * 获取币种
   * Time：2015/10/29 13:34:18
   * @author li
  */
  function getBizhong(){
    require "Config/Bizhong_config.php";
    //默认显示的币种
    $_bizhong = array('CNY','USD','HKD','EUR','JPY');
    //查找其他需要显示的币种信息：下单中有其他币种需要添加
    $sql="SELECT DISTINCT currency from trade_order
      where currency not in ('CNY','USD','HKD','EUR') and currency<>''";
    $tmp_curr = $this->_model->findBySql($sql);
    $tmp_curr = array_col_values($tmp_curr,'currency');
    $tmp_curr = array_filter(array_unique($tmp_curr));

    //合并
    $_bizhong = array_merge($_bizhong,$tmp_curr);

    foreach ($_bizhong as $key => & $v) {
      $_arr[] = array(
        'text'=>isset($bizhong_config[$v]) ? $bizhong_config[$v] : $v,
        'value'=>$v
      );
    }

    // dump($_arr);exit;
    return $_arr;
  }

  /**
   * 获取单位
   */
  function getUnit(){
    require "Config/Unit_config.php";
    // $_unit = array('M', 'Y', 'KG','piece','package');
    $_arr = array();
    foreach ($unit_config as $key=>$v){
      $_arr[] = array(
        'text' =>isset($unit_config[$v]) ? $unit_config[$v] : $v,
        'value'=>$key
      );
    }
    return $_arr;
  }
}
?>
