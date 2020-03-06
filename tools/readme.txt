##1.index.php：常用erp系统访问入口文件

##2.open.php：如微信，支付回调等功能入口文件，特点，url中不能有"?"生成url方式可以看open.php中的说明
    a) 在index.php 入口访问的代码中生成open.php入口的url可以使用如下代码：
       url('main','welcome',array(),null,array('mode'=>'URL_PATHINFO','bootstrap'=>'open.php'));

    b) 在open.php入口进入程序中生成的url，默认就是上面的方式，所以只要使用url('ctl','action')就可以了，如果期望生成index.php入口的url,则使用url('main','welcome',array(),null,array('mode'=>'URL_STANDARD','bootstrap'=>'index.php'));

##3.crontab.php：把Config/crontab_list.php
  配置中的计划任务待执行任务update到mysql中【把待执行任务罗列在计划列表中】
  queue.php：执行待执行任务罗列在计划列表中的任务

  配合的文件还有Controller下的Crontab.php    ：逻辑处理class
                              CrontabLog.php ：待处理任务列表和已执行任务列表
                Model     下的Crontab.php   ：表crontab的model，并且处理任务到表的逻辑代码也在该文件

  在windows下配置计划任务，建议使用
        php -f D:/phpStudy/WWW/demo/crontab.php
        php -f D:/phpStudy/WWW/demo/queue.php
  可以借助windows的计划任务或辅助工具tools/planWork【该工具支持按照秒运行计划任务】