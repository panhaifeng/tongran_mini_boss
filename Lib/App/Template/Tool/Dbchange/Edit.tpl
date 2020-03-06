<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<{webcontrol type='LoadJsCss' src="Resource/Script/jquery.1.9.1.js"}>
<{webcontrol type='LoadJsCss' src="Resource/Script/layer/layer.js"}>
<{webcontrol type='LoadJsCss' src="Resource/Script/Calendar/WdatePicker.js"}>
<{webcontrol type='LoadJsCss' src="Resource/Script/common.js"}>
<{webcontrol type='LoadJsCss' src="Resource/Script/layui/layui/css/layui.css"}>
<{webcontrol type='LoadJsCss' src="Resource/Script/layui/style/admin.css"}>
<title>数据库修改提交</title>

<script language="javascript">
$(function(){
	//补丁文件列表内容初始化
	getPatchs();
	// layer.msg('start');
	//getPrefix();
	//l.options[0].click();

	//刷新
	$('#btnRfresh').click(function(){
		getPatchs();
		$('#sql').val('');
	});

	//删除补丁文件
	$('#btnRemove').click(function(){
		var l = $('#listPatch')[0];
		var f = l.options[l.selectedIndex].value;
		if(f=='') {
			layer.msg('请选择文件');return;
		}
		var url='?controller=Dbchange&action=remove';
		var param={fileName:f};
		$.post(url,param,function(json){
			if(!json) {
				layer.msg('服务器返回错误');
				return;
			}
			if(!json.success) {
				layer.msg(json.msg);
				return;
			}
			getPatchs();
			$('#sql').val('');
			layer.msg('删除完成');
		},'json');
	});

	//日期改变时，补丁文件内容刷新
	document.getElementById('datePatch').onclick=function(){
		WdatePicker({onpicked:getPatchs})
	}

	//前缀改变时刷新list
	$('#prefix').change(function(){
		if(this.value=='') {
			layer.msg('请输入程序员身份标记');
			$('#prefix').focus();
			return;
		}
		//ajax改变前缀文件，成功后刷新补丁列表
		var url='?controller=Dbchange&action=ChangePrefix';
		var param = {prefix:this.value};
		$.post(url,param,function(json){
			if(!json.success) {
				layer.msg(json.msg);
				retun;
			}
			getPatchs();
			$('#sql').val('');
		},'json');
	});

	//listPatch选中时获得文件内容
	$('#listPatch').change(function(){
		if(this.selectedIndex==0) {
			$('#sql').val('');
			return;
		}

		var l = $('#listPatch')[0];
		var f = l.options[l.selectedIndex].value;
		var url='?controller=Dbchange&action=GetSqlByAjax';
		var param={fileName:f};
		$.post(url,param,function(json){
			if(!json) {
				layer.msg('服务器返回错误');
				return;
			}
			if(!json.success) {
				layer.msg(json.msg);
				return;
			}
			$('#sql').val(json.content);
		},'json');
	});

	//提交前有效性判断
	$('#btnOk').click(function(){
		if($('#prefix').val()=='') {
			layer.msg('请输入前缀');
			return;
		}
		if($('#sql').val()=='') {
			layer.msg('请输入sql语句');
			return;
		}
		this.disabled=false;
		//开始提交
		var l = $('#listPatch')[0];
		var f = l.options[l.selectedIndex].value;
		var url='?controller=Dbchange&action=save';
		var param={fileName:f,prefix:$('#prefix').val(),datePatch:$('#datePatch').val(),timePatch:$('#timePatch').val(),sql:$('#sql').val()};
		$.post(url,param,function(json){
			if(!json) {
				layer.msg('服务器返回错误');
				return;
			}
			if(!json.success) {
				layer.msg(json.msg);
				return;
			}
			layer.msg('保存成功');
			getPatchs(function(){
				var f = json.curFile;

				var l = $('#listPatch')[0];
				for(var i=0;l.options[i];i++) {
					if(l.options[i].value==f) {
						l.selectedIndex = i;
						$(l).change();
						return;
					}
				}
			});
			//还原选中状态
			//$('#sql').val(json.content);
		},'json');
	});

	//根据日期获得所有patchs
	function getPatchs(fn) {
		var d = $('#datePatch').val();
		var prefix = $('#prefix').val();
		if(prefix=='') {
			layer.msg('程序员身份标记不存在,请输入');
			return;
		}
		var url='?controller=Dbchange&action=GetPatchsByAjax';
		var param={datePatch:d,prefix:prefix};
		$.post(url,param,function(json){
			if(!json) {
				layer.msg('获得补丁失败');
				return;
			}
			//清空list
			var l = document.getElementById('listPatch');
			while(l.options.length>1) {
				l.options[1]=null;
			}

			for(var i=0;json[i];i++) {
				//$("<option value='111'>UPS Ground</option>").appendTo($("select[@name=ISHIPTYPE]"));
				$("<option value='"+json[i]+"'>"+json[i]+"</option>").appendTo(l);
			}

			if(fn&&typeof(fn)=='function') fn();
		},'json');
	}

	//将最后一个置为选中
	var l = document.getElementById('listPatch');
	l.selectedIndex = 0;
	$(l).change();
});
</script>
<style type="text/css">
	#listPatch{min-height: 360px;}
	.layui-table tbody tr:hover{background: #fff;}
	.layui-form-label{width: auto;}
</style>

</head>

<body>

<table width="100%" border="0" cellspacing="1" cellpadding="1" class="layui-table">
  <tr>
  	<td>补丁提交(程序员用)</td>
  	<td align='right'>
  		<div class="layui-inline">
          <label class="layui-form-label">程序员身份标记：</label>
          <div class="layui-input-inline">
            <input name="prefix" style="width: 120px;" type="text" id="prefix" value="<{$prefix}>" class="layui-input"/>
          </div>
        </div>
    </td>
  </tr>
  <tr>
    <td valign="top">
    	<select name="listPatch" size="10" id="listPatch" class="layui-input layui-unselect">
	    	<option value=''>创建新补丁</option>
		</select>
    <br />
    <input type="button" name="btnRfresh" id="btnRfresh" value="刷新" class="layui-btn layui-btn-normal layui-btn-sm"/>
    <input type="button" name="btnRemove" id="btnRemove" value="删除" class="layui-btn layui-btn-primary layui-btn-sm"/></td>
    <td valign="top"><table width="100%" border="0" cellspacing="1" cellpadding="1" >
      <tr>
        <td align="right"></td>
        <td>
        	<div class="layui-inline">
	          <label class="layui-form-label">日期 - 时间：</label>
	          <div class="layui-input-inline">
	            <input name="datePatch" style="width: 160px;" type="text" id="datePatch" value="<{$smarty.now|date_format:'%Y-%m-%d'}>" class="layui-input"/>
	          </div>
	          -
	          <div class="layui-input-inline">
	            <input name="timePatch" style="width: 160px;" type="text" id="timePatch" value="<{$smarty.now|date_format:'%H:%M:%S'}>" class="layui-input"/>
	          </div>
	        </div>
    	</td>
    </tr>
      <tr>
        <td align="right" valign="top">sql语句：</td>
        <td valign="top"><label for="sql"></label>
          <textarea name="sql" id="sql" cols="70" rows="10" class="layui-textarea"></textarea><br />
          有多条sql语句以&quot;;&quot;隔开,sql语句中不能出现分号</td>
        </tr>
      <tr>
        <td valign="top">&nbsp;</td>
        <td valign="top"><input type="submit" name="btnOk" id="btnOk" class="layui-btn layui-btn-normal" value=" 提 交 " /></td>
        </tr>
    </table></td>
  </tr>

</table>
</body>
</html>