// JavaScript Document
//设置方向键的方法
function direct(def){
			//参数空时
			if(def==undefined)return false;
			//定义默认值
			var defaults={
				cellname:[],
				selectedorfocus:false,
				optionfocus:false
			};
			//处理参数值
			for(var key in def){
				defaults[key]=def[key];
			}
			
			var tabName=defaults.cellname;
			var _select=defaults.selectedorfocus;
			var optionfocus=defaults.optionfocus;

			//设置方向键的问题
			$(tabName).each(function(pos){
				$('[name="'+this+'"]').live('keydown',function(e){
					var objs = document.getElementsByName(this.name);
					var p = $(objs).index(this);
		
					//处理键盘事件
					if(e.keyCode==38 && !e.ctrlKey){
						//上
						//如果是select的向下，需要屏蔽						
						if(objs[p].type=='select-one' && optionfocus==false) {
							return true;
						}
						//如果是聚焦到上一行，则需要判断是否存在上一行
						if(p<1)return false;
						if(objs[p-1]){
							selectorfocus(objs[p-1],_select);
						}
						return false;
					}else if((e.keyCode==40 || e.keyCode==13) && !e.ctrlKey){
						//下，回车
						//如果是select的向下，需要屏蔽
						if(objs[p].type=='select-one' && optionfocus==false) {
							return true;
						}
						//如果是聚焦到下一行，则需要判断是否存在下一行
						if(p+2>objs.length)return false;
						if(objs[p+1]){
							selectorfocus(objs[p+1],_select);
						}
						return false;
		
					}else if(e.keyCode==37){
						//左
						if(pos==0) return false;
						var _objs = document.getElementsByName(tabName[pos-1]);
						if(_objs.length==0) {
							alert('未发现'+tabName[pos-1]+'元素');
							return false;
						}
						if(_objs[p])selectorfocus(_objs[p],_select);
						else if(_objs[0])selectorfocus(_objs[0],_select);
						return false;
		
					}else if(e.keyCode==39){
						//右
						if(pos==tabName.length-1) return false;
						var _objs = document.getElementsByName(tabName[pos+1]);
						if(_objs.length==0) {
							alert('未发现'+tabName[pos+1]+'元素');
							return false;
						}
						if(_objs[p])selectorfocus(_objs[p],_select);
						else if(_objs[0])selectorfocus(_objs[0],_select);
						return false;
					}else if(e.ctrlKey&&e.keyCode==40){
						return true;
					}
					else if(e.ctrlKey&&e.keyCode==38){
						return true;
					}
				});
			});
}

//判断是选中还是聚焦
function selectorfocus(o,_select){
	if(_select==false)o.focus();
	else{
		if(o.type=='text' || o.type=='textarea')o.select();
		else o.focus();
	}
}
