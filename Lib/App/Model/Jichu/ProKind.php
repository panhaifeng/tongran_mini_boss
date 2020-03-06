<?php
FLEA::loadClass('TMIS_TableDataGateway');
class Model_Jichu_ProKind extends TMIS_TableDataGateway {
    var $tableName = "jichu_prokind";
    var $primaryKey = "id";
    var $primaryName = "kindName";
    var $needCreateLog = false;// 需要打日志则加此参数
    var $moduleName = '产品大类';// 模块名称

    //产品大类
    function getKinds(){
        $str="select * from jichu_prokind";
        $row = $this->findBySql($str);
        foreach($row as & $v){
            $arr[]=array('value'=>$v[$this->primaryKey],'text'=>$v[$this->primaryName]);
        }
        return $arr;
    }
}
?>