<?php
FLEA::loadClass('TMIS_TableDataGateway');
class Model_Jichu_Bank extends TMIS_TableDataGateway {
    var $tableName = "jichu_bank";
    var $primaryKey = "id";
    var $primaryName = "bankName";
    var $needCreateLog = true;
    var $moduleName = '银行账号';// 模块名称

    //加工户选项
    function getOptions(){
        $row = $this->findAll();
        foreach($row as & $v){
            $arr[]=array('value'=>$v[$this->primaryKey],'text'=>$v[$this->primaryName]);
        }
        return $arr;
    }
}
?>