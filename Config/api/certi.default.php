<?php
    $certKey = array(
        'token'  => '7pa72bA2hMmNn39d027frG4f34o71gb0c6U21ca4PQ35gb5zf4',
        'url'     => '{domain}/api.php',
        'method' =>array(
            'get.BarMonth.data'=>array(
                'title'=>'返回合同汇总数据接口(柱形图)',
            ),
            'get.Month.data'=>array(
                'title'=>'返回合同按月份汇总数据',
            ),
            'get.Client.data'=>array(
                'title'=>'返回客户汇总数据',
            ),
            'get.Saler.data'=>array(
                'title'=>'返回业务员汇总数据',
            ),
            'get.Profit.data'=>array(
                'title'=>'返回利润汇总数据',
            ),
            'get.Sales.data'=>array(
                'title'=>'获取销售额汇总数据',
            ),
        ),
        'next_call_time'=>array(1,2,5,10,15,30,30,60,60,60,180,180,180,360,360,360,720,720,720,1440)
    );

?>