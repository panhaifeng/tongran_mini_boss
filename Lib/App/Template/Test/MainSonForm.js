//以下代码在sontpl中写
var callbacks = [];
//列渲染函数,
//row:当前行
//index:第几行
callbacks['isHanshuiFormatter'] = function(row, column, cellValue, index) {
  // dump('formatter fired');
  dump(cellValue);
  var ret = cellValue==true?'是':'否';
  //html不支持,待完善
  // this.columns.isHanshui.isHtml=true;
  // ret = "<el-tag type='primary'>"+ret+"</el-tag>";
  return ret;
}
//自定义验证函数,订单编号中必须包含a
callbacks['checkOrderCode'] = function(rule, value, callback) {
  // console.log(this,rule,value);
  if(value.indexOf('a')==-1) {
    //抛出错误
    return callback(new Error('订单编号中必须包含a'));
  }
  //正常返回
  return callback();
}

//客户选择弹窗显示
callbacks['clientId:open'] = function() {
  //拿到traderId,并作为url附加参数
  dump('traderId is:',app.row.traderId); 
}

//traderId改变后,改变traderId1的options
callbacks['traderId:change'] = function(newValue) {  
  dump('traderId:change fired'); 
  //以下两个写法都可以成功,推荐第一个写法,
  //因为traderId1为动态组件,所以app.$refs.traderId1是一个数组
  var options = app.$refs.traderId1[0].fld.options;
  var options = app.mainFormItems[3].options;
  // var options = app.mainFormItems.traderId1.options;
  options.splice(0,1);//删除第一个选项
  dump("traderId1删除一个选项后的结果",options);
}

//表单提交前验证接口
// callbacks['beforeSubmit'] = function(row) {    
//   //如果错误,return false;
//   this.$message('在callbacks["beforeSubmit"]中定义的错误信息','error');
//   return false;    
//   return true;
// }
//子表记录编辑弹窗open时触发
//row为当前记录
callbacks['dialogSon:open'] = function(row) {
  //可能需要增加参数
  dump('dialogSon:open fired'); 
}
//子表记录编辑弹窗open时触发
//rowSon为当前窗口编辑的子表记录
//row为修改对象
callbacks['dialogSon:close'] = function(rowSon,row) {
  dump('dialogSon:close fired',arguments);
}

//子表记录的更改触发,
//比如子表金额=子表数量*子表单价
//监听子表记录集的变化,
callbacks['__sonWatcher'] = function(son,oldSon) {
  console.log('监听到子表记录变化,自动计算金额');
  son.money=(son.danjia*son.number).toFixed(2);
}

//设置工艺按钮点击后触发
callbacks['setGongyi'] = function(row,index) {
  //调用自定义组件user-comp
  var opt = {
    type : 'user-comp',
    //必须提供name参数,方便下面的获得实例
    //使用this.$refs.comp1[0]获得实例
    name : 'comp1',
  };
  //使用makeNewComp注册组件,并使组件显示
  this.makeNewComp(opt);

  // console.log(arguments);
  // console.log('setGongyi fired',this,arguments);
  // son.money=(son.danjia*son.number).toFixed(2);
}

//用户自定义组件,子表记录中的自定义组件按钮点击后触发
Vue.component('user-comp',{
  props:[],
  data : function(){
    return {
      'row':{},
    };
  },
  template : `
    <div>这是在sontpl中自定义的组件,实际应用中可能会使用弹窗等</div>
  `,
  methods : {
    //每次按钮点击时触发
    show :function(row) {
      this.row=row;
      this.$message(`user-comp@show fired`);
      dump(row);
    }
  }
});

//产品弹出选择控件选中后触发
//场景:选中的产品的品名需要回显在表单的品名栏目中,
callbacks['productId:select'] = function(row,e) {
  //可能需要增加参数
  dump('fired in productId:select');
  dump(row,e);
}

//子表记录的自定义动作按钮点击后触发
callbacks['userFunc'] = function(row,e) {
  //可能需要增加参数
  dump('fired in userFunc');
  dump(arguments);
  // dump(row,e);
}

//设置行操作按钮的图标
//可以直接写成一个数组,也可以写成一个函数返回形式
//后台已经直接指定了icon,这里不需要了. 
// callbacks['EditButtonsIcon'] = function() {
//   return ['el-icon-delete','el-icon-edit','el-icon-check'];
// }