<?php
/**
 * 自动计划任务
 *
 * @copyright  eqinfo lwj
 */

//自动化计划任务列表
$crontab_list = array(
    // array(
    //     'schedule'=>'*/3 * * * *',//时间
    //     'enabled'=>'1',//0不开启，1开启
    //     'action'=>'Controller_Test@Test',//controller@function
    //     'description'=>'合同审核2小时未审核自动提醒',
    //     'param'=>array()//可以带参数
    // ),
    array(
        'schedule'=>'45 3 */1 * *',//时间
        'enabled'=>'1',//0不开启，1开启
        'action'=>'Controller_Crontab@clearLog',//controller@function
        'description'=>'删除过期crontab数据表的数据',
        'param'=>array(),//可以带参数
    )
);

?>

