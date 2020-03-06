/*
tablelist中每行记录的操作按钮点击后的事件
*/
var RowEditButtonFuncs = {
  //跳转
  redirect : function(row,options) {
    var paramColumns = options.paramColumns;
    var urlColumn = options.urlColumn;
    var url = row[urlColumn] ? row[urlColumn] : options.url;
    //将url中的模版变量进行替换          
    var arrParms = url.match(/\{.+?\}/g) || [];
    for(var i=0;arrParms[i];i++) {
      var len = arrParms[i].length;
      var pName = arrParms[i].substr(1,len-2);
      if(!row[pName]) {              
        console.warn(`当前记录中未发现${pName}字段,url可能有错`);
      } else {              
        url=url.replace(new RegExp(arrParms[i],'g'),row[pName]);
      }
    }
    window.location.href=url;
    return;
  },
  //删除
  remove : function(row,options) {
    this.$confirm('确认删除吗?', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {      
      var url = options.url;
      var params = {row:row};
      this.$http.post(url,params).then((res)=>{
        if(!res.data.success) {
          this.$message(res.data.msg||'删除失败','error');
          // console.log(res);
          return false;
        }
        this.$message(res.data.msg||'删除成功','success');
        //刷新grid
        this._getRows();
        return;
      }).catch((error)=>{
        //服务器端返回success=false
        console.error(error);
      });
    }).catch(() => {
      //请求失败      
      return false;
    });
  },
  //用户自定义功能
  func : function(row,options) {
    var key = options.funcName;
    if(!this.$root.callbacks[key]) {
      console.warn(`未发现callback[${key}]方法`);
      return;
    }
    this.$root.callbacks[key].apply(this,[row]);
  },
  //用户自定义组件
  //在sontpl中定义组件
  comp : function(row,options) {
    if(!options.name) {
      console.warn(`自定义组件${options.type}配置项目中未发现name属性`);
      return;
    }
    //定义组件的option
    var opt = {
      type : options.type,
      //必须提供name参数,方便返回实例
      //使用this.$refs.myDialog[0]获得实例
      name : options.name,
    };    

    var found = false;
    for(var i=0;this.otherComps[i];i++) {
      if(this.otherComps[i].type==opt.type) {
        found = true;
        break;
      }
    }
    if(!found) {
      this.otherComps.push(opt);
    }
    
    //调用组件中的某个方法,
    //比如显示弹窗,设置弹窗的绑定记录为当前行
    //等dom元素创建后才能得到组件实例
    this.$nextTick(()=>{      
      var comp = this.$refs[options.name][0];
      //检查comp的row属性是否存在
      if(comp.row==undefined) {
        consol.warn(`自定义组件${options.type}中未发现row属性`);
        return;
      }
      comp[options.onclickButton](row);
      // dump(comp);
      // dialog.title = `为${row.compName}设置联系人,这是动态创建的弹窗组件`;
      // dialog.show();
    });
  }
};