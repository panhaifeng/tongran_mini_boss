<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <!-- import CSS -->
  <link rel="stylesheet" href="index.css">
  <script src="Resource/Script/vue/vue.js"></script> 
  <script src="index.js"></script> 
  <script src="axios.min.js"></script> 
  <script src="components.js"></script> 
  <style>
    .el-dialog__body {
        padding: 0 10px 10px 10px;        
    }
    .el-pagination {
      margin-top: 5px;
    }
  </style>
</head>
<body>
  <div id='div1'>   
    <el-container>
      <el-header style="z-index:100;width:100%;height:60px;position:fixed;top:0;left:0;background-color:#ccc;">
         <span>数据源:{{row}}</span>
         <span><a href="<{url controller=$smarty.get.controller action='TestForm'}>">普通动态表单控件</a></span>
         <span><a href="">主从表动态表单控件</a></span>
      </el-header>
    
      <el-main style="margin-top: 30px;">      
        <span style="color:red;">以下是动态表单的显示</span>
        <li v-for="fld,i in fldDefine">          
          {{fld.title}}
          <component 
          v-if="fld['type']=='comp-file'"
          :is="fld.type"
          :fld="fld" 
          @on-success="handleSuccess"
          @on-remove="handleRemove"
          @on-preview="handlePreview"
          :ref="fld.name"
          ></component>
          <component 
            v-else
            :is="fld.type" 
            :fld="fld" 
            :value="row[fld['bindfield']]"
            :displaytext="row[fld['displayKey']]"
            @input="row[fld['bindfield']]=arguments[0]" 
            @select="handleSelect"
            @change="handleChange"
            @open="handleOpen"
            :ref="fld.name"
          ></component>
          <!-- 以上写法可以简写为 <component :is="fld.type" :fld="fld" v-model="row[fld['bindfield']]">-->
        </li>        
      </el-main>
    </el-container>
  </div>
</body>
  
  
<script>
  var title='页面标题';
  var row = <{$row|@json_encode}>;
  //单一表单的数据
  
  var fldDefine = <{$fields|@json_encode}>;   
  var app = new Vue({
    el: '#div1',
    data: {
      'fldDefine':fldDefine,
      'title':title,
      'row':row
    },
    methods : {
      //autocomplete的onselect事件
      //pop-select弹出选择的选中回调,
      //comp-pop-multi-select 确认选择后回调,item为选中的记录集
      'handleSelect' : function(funcName,item){
        // this.$emit("event-test", funcName);
        __Callback.trigger.apply(this,arguments);
      },
      //文件列表项点击时触发
      //fldConfig为控件的配置信息,从自组件中作为参数抛出,用来从接口仓库中匹配对应的接口
      'handlePreview' : function(funcName,file) {
        //将this作为上下文进行函数调用
        __Callback.trigger.apply(this,arguments);
      },
      //文件上传列表的删除的回调
      'handleRemove' : function(funcName,file, fileList) {
        __Callback.trigger.apply(this,arguments);
      },
      //file上传成功的回调 
      'handleSuccess' : function(funcName,response, file, fileList) {
        __Callback.trigger.apply(this,arguments);
      },
      //select和checkbox的change回调
      'handleChange' : function(funcName,val) {
        __Callback.trigger.apply(this,arguments);
      },
      //dialog弹开前回调
      'handleOpen' : function(funcName,dialog) {
        __Callback.trigger.apply(this,arguments);
      },
      // 'handleClear' : function(funcName,dialog) {
      //   var key = this.fld.rowKey;
      //   this.row[] = '';
      //   var key = this.fld.displayKey;
      //   this.row[] = '';
      //   __Callback.trigger.apply(this,arguments);
      // },
    },

    //后期考虑将form封装成一个组件,传入fld,
    // mounted : function() {
    //   this.$on('event-test',function(){
    //     console.log('on event fired');
    //   });
    // }
  }); 
  

  //回调接口集合,注意下面的回调接口中,this都是指向app根元素,
  var __Callback = {};
  __Callback.trigger = function() {
    var funcName = arguments[0]; 
    var args = [];
    for(var i=1;arguments.hasOwnProperty(i);i++)  {
      args.push(arguments[i]);
    }
    if(!__Callback[funcName]) {
      return ;
    }
    __Callback[funcName].apply(this,args);
  }

  //以下代码在子模版中实现
  __Callback['combox1:select'] = function(val){
    console.log('combox选中触发,参数为',val);
    alert('select');
  }
  __Callback['file1:on-preview'] = function(file){
    console.log(this);
    alert('on-preview');
  }
  __Callback['file1:on-success'] = function(file){
    console.log(this);
    alert('on-success');
  }
  __Callback['file1:on-remove'] = function(file){
    console.log(this);
    alert('on-remove');
  }
  //select的选中回调
  __Callback['sel1:change'] = function(item){
    console.log(item);
    // alert('on-remove');
  }
  //checkbox的改变回调
  __Callback['chk1:change'] = function(val){
    console.log('checkbox回调,改变后的值为:',val);
    // alert('on-remove');
  }

  //弹出选择前回调,可在url中增加参数等
  //自组件作为参数传入,可调用自组件中的方法或改变自组件中的属性
  __Callback['clientId:open'] = function(dialog){
    // this.$refs.clientId[0].fld.action='aaa';
    // console.log(this.$refs.clientId[0]);
    // console.log(this.fldDefine);
    console.log('弹出选择前触发:');
    
    //可以在 dialog.action 中加入 get参数,
    dialog.action += '&key=aaa';
    //也可以直接在dialog.params中加入参数
    dialog.params.key='aaa';
    console.log(dialog);
    // alert('on-remove');
  }
  //弹出选择的回调
  __Callback['clientId:select'] = function(row){
    console.log('弹出选择选定触发,选择的值为:',row);
    // alert('on-remove');
  }
  //弹出多选的回调
  __Callback['colors:select'] = function(rows){
    console.log('colors弹出多选选定触发,选择的值为:',rows);
    // alert('on-remove');
  }
  __Callback['colors1:select'] = function(rows){
    console.log('colors1弹出多选选定触发,选择的值为:',rows);
    // alert('on-remove');
  }
</script>  
  
</html>