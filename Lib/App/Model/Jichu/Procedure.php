<?php
load_class('TMIS_TableDataGateway');
class Model_Jichu_Procedure extends TMIS_TableDataGateway {
    var $tableName = 'jichu_procedure';
    var $primaryKey = 'id';
    var $primaryName = 'itemName';

    var $needCreateLog = true;// 需要打日志则加此参数
    var $moduleName = '工序档案';// 模块名称

    function getOptions(){
        $row = $this->findAll();
        foreach($row as & $v){
            $arr[]=array('value'=>$v[$this->primaryKey],'text'=>$v['itemName']);
        }
        return $arr;
    }

    function getProcedureKind(){
        $row = $this->findAll();
        $kindName = array();
        foreach($row as & $v){
            //查找产品分类名称
            if(!isset($kindName[$v['kid']])){
                $sql = "select * from jichu_prokind where id='{$v['kid']}'";
                $tmp = $this->findBySql($sql);
                $kindName[$v['kid']] = $tmp[0]['kindName'];
            }

            $arr[]=array('value'=>$v[$this->primaryKey],'text'=>$v['itemName']." ({$kindName[$v['kid']]})");
        }
        return $arr;
    }
}
?>