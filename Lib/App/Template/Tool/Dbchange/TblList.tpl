<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<{webcontrol type='LoadJsCss' src="Resource/Script/jquery.1.9.1.js"}>
<{webcontrol type='LoadJsCss' src="Resource/Script/layer/layer.js"}>
<{webcontrol type='LoadJsCss' src="Resource/Script/Calendar/WdatePicker.js"}>
<{webcontrol type='LoadJsCss' src="Resource/Script/layui/style/admin.css"}>
<{webcontrol type='LoadJsCss' src="Resource/Script/layui/layui/css/layui.css"}>

<title>数据库修改提交</title>

<script language="javascript">
$(function(){


});
</script>
<style type="text/css">
    body{padding-top: 5px;}
    /*---------分页--------------*/
    #p_bar {
        font-size:12px;
    }
    .p_bar {
        /*clear: both;  */
    }
    .p_bar a {
        float: left;
        padding: 6px 10px 6px 10px;
        font-size: 12px;
        text-decoration: none;
        color: #428bca;
        background-color: #ffffff;
        /*border: 1px solid #ddd;*/
    }
    .p_input {
        border: 0px;
        padding: 0px;
        width: 40px;
        height: 26px !important;
        /*height: 15px;*/
        margin: 0px;
        background: #FFFFFF;
        border: 1px solid #86B9D6;
        margin-top: 1px;
    }
    .p_total {
        background-color: #fff !important;
        /*border: 1px solid #86B9D6;*/
        /*border-right: 0px solid #86B9D6;*/
        font-weight: bold;
    }
    .p_pages {
        background-color: #fff !important;
        /*border: 1px solid #86B9D6;*/
        margin-right:1px;
        vertical-align: middle;
        font-weight: bold;
    }
    .p_num {
        background-color: #FFFFFF;
        /*border: 1px solid #DEDEB8;*/
        margin-right:1px;
        vertical-align: middle;
    }
    a:hover.p_num  {
        background-color: #F5FBFF;
        border: 1px solid #86B9D6;
        text-decoration: none;
    }
    .p_redirect {
        background-color: #FFFFFF;
        border: 1px solid #DEDEB8;
        margin-right:1px;
        font-size: 12px !important;
    }
    a:hover.p_redirect {
        background-color: #F5FBFF;
        border: 1px solid #86B9D6;
        text-decoration: none;
    }
    .p_curpage {
        margin-right:1px;
        /*border: 1px solid #DEDEB8;*/
        vertical-align: middle;
        background-color: #428BCA !important;
        color: #ffffff !important;
        font-weight: bold;
    }
    .p_pager_list{
        border: 1px solid #efefef;
        height: 26px !important;
        margin-top: 1px;
        padding: 0px 0px;
        outline: none;
        /*line-height: 19px !important;*/
    }
    .p_pager_list option{border-color: #efefef;}
    .bottom-div{width: 100%;position: fixed;bottom: 0px;left: 0px;background-color: #fff;padding:10px 20px 10px 20px;}
    .layui-card-body{margin-bottom: 48px;}
</style>

</head>
<body>
    <div class="layui-fluid">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md12">
                <div class="layui-card">
                  <div class="layui-card-header">历史记录</div>
                  <div class="layui-card-body">
                    <table class="layui-table" lay-skin="line">
                        <thead>
                            <tr>
                                <{foreach from=$arr_field_info item=item key=key}>
                                <th><{if $item|@is_string == 1}>
                                        <{$item}>
                                    <{else}>
                                        <{$item.text}>
                                    <{/if}></th>
                                <{/foreach}>
                            </tr>
                        </thead>
                        <tbody>

                            <{foreach from=$arr_field_value item=r}>
                                <tr>
                                    <{foreach from=$arr_field_info item=item key=key}>
                                    <td><{$r[$key]}></td>
                                    <{/foreach}>
                                </tr>
                            <{/foreach}>
                        </tbody>
                    </table>
                  </div>
                </div>
            </div>
        </div>
        <div class="bottom-div"><{$page_info}></div>
    </div>
</body>
</html>