<?php
FLEA::loadClass('TMIS_TableDataGateway');
class Model_Jichu_Factory extends TMIS_TableDataGateway {
    var $tableName = "jichu_supplier";
    var $primaryKey = "id";
    var $primaryName = "compName";
    var $needCreateLog = false;// 需要打日志则加此参数
    var $moduleName = '加工户档案';// 模块名称
    var $mark = 'f';// 类型标记

    //加工户选项
    function getFactorys(){
        $typeMark = $this->mark;
        $str = "SELECT * from jichu_supplier where find_in_set('{$typeMark}', typeMark)";
        $row = $this->findBySql($str);
        foreach($row as & $v){
            $arr[]=array('value'=>$v[$this->primaryKey],'text'=>$v[$this->primaryName]);
        }
        return $arr;
    }
}
?>