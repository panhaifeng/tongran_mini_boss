<?php
load_class('TMIS_TableDataGateway');
class Model_Jichu_Material extends TMIS_TableDataGateway {
    var $tableName = 'jichu_material';
    var $primaryKey = 'id';
    var $primaryName = 'proName';

    var $needCreateLog = false;// 需要打日志则加此参数
    var $codeField = 'proCode';// 编号字段
    var $moduleName = '原料档案';// 模块名称


    //所有类别
    function getKinds(){
        $str="SELECT distinct kind from jichu_material";
        $row = $this->findBySql($str);
        foreach($row as & $v){
            $arr[]=array('value'=>$v['kind'],'text'=>$v['kind']);
        }
        return $arr;
    }

    //所有类别
    function getUnits(){
        $str="SELECT distinct unit from jichu_material";
        $row = $this->findBySql($str);
        foreach($row as & $v){
            $arr[]=array('value'=>$v['unit'],'text'=>$v['unit']);
        }
        return $arr;
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
}
?>