<?php
FLEA::loadClass('TMIS_TableDataGateway');
class Model_Jichu_Supplier extends TMIS_TableDataGateway {
    var $tableName = "jichu_supplier";
    var $primaryKey = "id";
    var $primaryName = "compName";
    var $needCreateLog = false;// 需要打日志则加此参数
    var $moduleName = '供应商档案';// 模块名称
    var $mark = 's';// 类型标记

    //供应商选项
    function getSuppliers(){
        $typeMark = $this->mark;
        $str = "SELECT * from jichu_supplier where find_in_set('{$typeMark}', typeMark)";
        $row = $this->findBySql($str);
        foreach($row as & $v){
            $arr[]=array('value'=>$v[$this->primaryKey],'text'=>$v[$this->primaryName]);
        }
        return $arr;
    }

    //获取供应商和加工户
    function getOptionsAll(){
        $typeMark = $this->mark;
        $str = "SELECT id,compName from jichu_supplier ";
        $row = $this->findBySql($str);
        foreach($row as & $v){
            $arr[]=array('value'=>$v[$this->primaryKey],'text'=>$v[$this->primaryName]);
        }
        return $arr;
    }
}
?>