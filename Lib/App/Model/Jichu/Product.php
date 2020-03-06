<?php
load_class('TMIS_TableDataGateway');
class Model_Jichu_Product extends TMIS_TableDataGateway {
    var $tableName = 'jichu_product';
    var $primaryKey = 'id';
    var $primaryName = 'proName';

    var $needCreateLog = false;// 需要打日志则加此参数
    var $codeField = 'proCode';// 编号字段
    var $moduleName = '产品档案';// 模块名称

    function isUnit($kindId){
      if($kindId == 1){
        return false;
      }else{
        return true;
      }
    }

    function isUnitPid($pid){
      $row = $this->find($pid);

      return $this->isUnit($row['kindId']);
    }

    function getOptions($filed = 'proName'){
        $row = $this->findAll();
        foreach($row as & $v){
          $fileds = explode(',',$filed);
          $tmp = array();
          foreach ($fileds as & $f) {
              $tmp[] = $v[$f] ;
          }
          $text = join('-',$tmp);
          $arr[]=array('value'=>$v[$this->primaryKey],'text'=>$text);
        }
        return $arr;
    }

    function getUnit($kindId = ''){
      if($kindId != 1 && $kindId != ''){
        $sql = "SELECT distinct unit from jichu_product where unit <> '' ";
        $row = $this->findBySql($sql);
        foreach ($row as $key => & $v) {
          $arr[]=array('value'=>$v['unit'],'text'=>$v['unit']);
        }
        return $arr;

      }else{
        $unit = array('公斤','平方米');
        foreach($unit as & $v){
            $arr[]=array('value'=>$v,'text'=>$v);
        }
        return $arr;
      }
    }

    /**
     * 大类对应的字段映射关系
     * Time：2019/05/06 10:43:32
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function filed2kind($kind){
        $kindFiled = array();
        $kindFiled[1] = array(
           'type'        =>'型号' ,
           'xianweiJing' =>'纤维种类经向' ,
           'xianweiWei'  =>'纤维种类纬向' ,
           'fukuan'      =>'幅宽' ,
           'zuzhi'       =>'组织结构' ,
           'kezhong'     =>'克重' ,
           'houdu'       =>'厚度mm' ,
           'miduJing'    =>'经纬密度经向' ,
           'miduWei'     =>'经纬密度纬向' ,
           'chehao'      =>'车号' ,
        );

        $kindFiled[2] = array(
            'kind'       =>'种类' ,
            'miduJing'   =>'经纬密度经向' ,
            'miduWei'    =>'经纬密度纬向' ,
            'length'     =>'长度' ,
            'width'      =>'宽度' ,
            'height'     =>'高度' ,
            'houdu'      =>'厚度' ,
            'neijing'    =>'(上)内径' ,
            'waijing'    =>'(上)外径' ,
            'neijingxia' =>'下内径' ,
            'waijingxia' =>'下外径' ,
        );

        $kindFiled[3] = array(
           'type'        =>'型号' ,
           'xianweiJing' =>'纤维种类经向' ,
           'xianweiWei'  =>'纤维种类纬向' ,
           'fukuan'      =>'幅宽' ,
           'zuzhi'       =>'组织结构' ,
           'kezhong'     =>'克重' ,
           'houdu'       =>'厚度mm' ,
           'miduJing'    =>'经纬密度经向' ,
           'miduWei'     =>'经纬密度纬向' ,
           'weight'      =>'预浸料总重量' ,
           'shuzhi'      =>'树脂含量' ,
        );

        $kindFiled[4] = array(
           'kind'          =>'种类' ,
           'pucengNumber'  =>'铺层循环数' ,
           'unitRate'      =>'单纱/碳布/网胎比例' ,
           'neijing'       =>'内径' ,
           'waijing'       =>'外径' ,
           'dikongjing'    =>'底孔径' ,
           'dikongwaijing' =>'底孔外径' ,
           'height'        =>'高度' ,
           'dihou'         =>'组织结构' ,
           'kezhong'       =>'底厚' ,
           'neiyuan'       =>'内圆弧比靠' ,
           'waiyuan'       =>'外圆弧比靠' ,
           'weight'        =>'重量' ,
           'tiji'          =>'体积' ,
           'midu'          =>'密度' ,
           'yuanhoudu'     =>'圆台厚度' ,
           'yuanwaijing'   =>'圆台外径' ,
        );

        $kindFiled[5] = array(
           'xianweiJing' =>'纤维种类经向' ,
           'xianweiWei'  =>'纤维种类纬向' ,
           'houdu'       =>'厚度mm' ,
           'fukuan'      =>'幅宽' ,
           'kezhong'     =>'克重' ,
           'zhijing'     =>'编织直径' ,
        );

        if(isset($kindFiled[$kind])){
            return $kindFiled[$kind];
        }

        return array();
    }


    //组织成通用的展示字段
    function filedOtherFormat($row ,$format='string'){
        $fileds = $this->filed2kind($row['kindId']);
        $tmp = array();
        foreach ($fileds as $key => & $v) {
            if($row[$key])$tmp[] = $v.':'.$row[$key];
        }

        if($format == 'string'){
            $result = join(',',$tmp);
        }elseif($format == 'html'){
            foreach($tmp as & $v){
              $v = '<span class="el-tag el-tag--mini">'.$v.'</span>';
            }
            $result = join(' ',$tmp);
        }

        return $result;
    }
}
?>