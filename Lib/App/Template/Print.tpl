<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><{$title}></title>
<script language="javascript" src="Resource/Script/Print/LodopFuncs.js"></script>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0 pluginspage="install_lodop.exe"></embed>
<param name="CompanyName" value="常州易奇信息科技有限公司">
<param name="License" value="664717080837475919278901905623">
</object>

<script language="javascript" type="text/javascript">
//(Top,Left,Width,Height,strHtml)
//in(英寸)、cm(厘米) 、mm(毫米) 、pt(磅)、px(1/96英寸) 、%(百分比)，如"10mm"表示10毫米。
    var cnt=<{$arr_main_value|@count}>;
    var height=Math.ceil(cnt/3)*5;
    if(height==0)height=22;
    else height+=22;
    var LODOP; //声明为全局变量
    function PrintTable(){
        //加载打印控件
        LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
        //LODOP.SET_PRINT_PAGESIZE(0,'203mm','290mm','A4')
        LODOP.PRINT_INIT("易奇科技报表打印");
        LODOP.ADD_PRINT_HTM(10,5,"99%",400,document.getElementById("compName").innerHTML);
        LODOP.SET_PRINT_STYLEA(0,"ItemType",1);
        //0--普通项 1--页眉页脚 2--页号项 3--页数项 4--多页项
        LODOP.ADD_PRINT_TABLE(height+'mm',5,"99%",(280-height)+'mm',document.getElementById("main").innerHTML);
        LODOP.ADD_PRINT_TABLE(5,0,"99%",100,document.getElementById("div3").innerHTML);
        LODOP.SET_PRINT_STYLEA(0,"LinkedItem",2);


        LODOP.ADD_PRINT_TEXT(3,653,135,20,"第#页/共&页");
        LODOP.SET_PRINT_STYLEA(0,"ItemType",2);

        LODOP.PREVIEW();
    };


function prnbutt_onclick() {
    window.print();
    return true;
}

function window_onbeforeprint() {
    prn.style.visibility ="hidden";
    return true;
}

function window_onafterprint() {
    prn.style.visibility = "visible";
    return true;
}
</script>

</head>

<body style="text-align:center" onafterprint="return window_onafterprint()" onbeforeprint="return window_onbeforeprint()">
<div id="compName">
<div style="text-align:center;">
<font style="font-weight:bold;font-size:24px;"><{webcontrol type='GetAppInf' varName='compName'}></font>
<br>
<font style="font-weight:bold;font-size:19px;border-bottom:2pt double #000000;"><{$title}></font>
</div>
<{*主表信息,每行显示3个字段*}>
<{if $arr_main_value}>
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center" style="margin:6px; font-size:14px;">
   <tr id="tr">
    <{assign var=i value=0}>
    <{assign var=index value=0}>
    <{assign var=countMain value=$arr_main_value|@count}>
    <{foreach from=$arr_main_value item=item key=key}>

    <{if $countMain<=4 && $countMain>1}>
        <{assign var=align value='left'}>
        <{assign var=j value=$j+1}>
        <{if $j%3==0 || $j==$countMain && $j<4}>
        <{assign var=align value='right'}>
        <{elseif $j%3==2}>
        <{assign var=align value='center'}>
        <{/if}>
    <{/if}>
    <td align="<{$align|default:'left'}>"><{$key}>：<{$item}></td>
    <{math equation="(x+1)%3" x=$i assign=i}>
    <{math equation="x+1" x=$index assign=index}>
    <{if $i==0 && $index>0 && $index<$countMain}>
    </tr>
    <tr id="tr">
    <{/if}>
    <{/foreach}>
  </tr>
</table>
<{/if}>
</div>
<div id="main">
<style type="text/css">
#dataList{
    border-top:2px solid #000;
    border-bottom:2px solid #000;
    border-left:0px;
    border-right:0px;
    border-collapse:collapse;
    font-size:14px;
}
.ptd{font-weight:bold;}
#hr td{
    border-bottom:1px solid #000;
    white-space:nowrap;
    padding-left:3px;
    padding-right:2px;
    height:25px;
}
#trmain td{
    border-bottom:1px dotted #000;
    padding-left:3px;
    padding-right:2px;
    height:22px;
}
a,font,span{
    color:#000;
    text-decoration:none;
}

</style>

<{*明细列表*}>
<table id="dataList" width="98%" align="center" cellpadding="2" cellspacing="0">
<thead>
  <{*字段名称*}>
    <tr id="hr" style="height:{$hr_height|default:'30px'};">
        <{foreach from=$arr_field_info key=key item=item}>
        <{if $key!='_edit'}>
     <td class="ptd" <{if $item.width}>style='width:<{$item.width}>px;'<{/if}>><{if $item|@is_string==1}><{$item}><{else}><{$item.text}><{/if}></td>
        <{/if}>
        <{/foreach}>
  </tr>
  <{*字段的值*}>
</thead>
 <{foreach from=$arr_field_value item=field_value}>
  <tr id="trmain">
    <{foreach from=$arr_field_info key=key item=item}>
        <{if $key!='_edit'}>
            <{assign var=foo value="."|explode:$key}>
            <{assign var=key1 value=$foo.0}>
            <{assign var=key2 value=$foo.1}>
            <{assign var=key3 value=$foo.2}>
            <td <{if $field_value.mark=='heji'}>class="ptd"<{/if}>>
                <{if $key2==''}><{$field_value.$key|default:'&nbsp;'}>
                <{elseif $key3==''}><{$field_value.$key1.$key2|default:'&nbsp;'}>
                <{else}><{$field_value.$key1.$key2.$key3|default:'&nbsp;'}><{/if}>
            </td>
         <{/if}>
    <{/foreach}>
  </tr>
  <{/foreach}>
</table>
</div>
<div id="div3">
<div style="margin:10px 50px 0 50px; white-space:nowrap;font-size:14px;">
<table border="0px" width="90%">
<tr>
<td align="left" width="33%">制单人：<font style="border-bottom:1px solid #000000; padding-left:15px; padding-right:15px;"><{$smarty.session.REALNAME}></font></td>
<td width="33%" align="center">审核：___________</td>
<td width="33%" align="right" style="padding-right:30px;">日期：<font style="border-bottom:1px solid #000000;padding-left:10px; padding-right:10px;"><{$smarty.now|date_format:'%Y-%m-%d'}></font></td>
</tr>
</table>
</div>
</div>
<br />
<div id="prn" align="center">
<input type="button" name="button" id="button" value="打印" onclick="PrintTable()"/>
    <{$other_button}>
</div>
</body>
</html>
