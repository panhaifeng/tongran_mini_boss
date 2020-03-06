<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <!-- import CSS -->
  <{webcontrol type='LoadJsCss' src="Resource/Script/layui/layui/css/layui.css"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/layui/style/admin.css"}>
  <link rel="stylesheet" href="Resource/Script/vue/element/index.css">
  <script src="Resource/Script/vue/vue.js"></script>
  <script src="Resource/Script/vue/element/index.js"></script>
  <script src="Resource/Script/vue/element/axios.min.js"></script>
  <script src="Resource/Script/vue/element/components.js?v=1"></script>
  <script src="Resource/Script/vue/element/components_card.js"></script>
</head>
<body>
  <div class="" id="div1">
      <el-container>
        <el-main>
          <comp-form 
            :fields="fldDefine" 
            :row="row" 
            :rules="rules" 
            :action="action" 
            form-width-col="8" 
            form-height="100%" 
            form-title="编辑-Tab"
            >
          </comp-form>
        </el-main>
      </el-container>
  </div>
</body>


<script>
  var title='页面标题';
  var row = <{$row|@json_encode}>;
  var fldDefine = <{$fields|@json_encode}>;
  var rules = <{$rules|@json_encode}>;
  var action = "<{$action}>";
  var callbacks = [];

  //对数据集进行预处理
  fldDefine.forEach(function(item,i){
    //如果自动完成控件,数据集中必须存在对应字段,否则控件有问题
    if(!row.hasOwnProperty(item.name)) {
      if(item.type=='comp-checkbox-group' ||
        item.type=='comp-pop-multi-select' ||
        item.type=='comp-image' ||
        item.type=='comp-file'
        ) {
        row[item.name] = [];
      } else {
        row[item.name] = '';
      }
    }
  });

  //以下代码在sontpl中实现
  callbacks['compName:change'] = function(val) {
    this.$notify.success("客户编码改变事件触发");
    // console.log("this,arguments",this,arguments);
  }
  callbacks['people:select'] = function(val) {
    this.$notify.warning("联系人选中,onselect事件触发");
    // console.log("this,arguments",this,arguments);
  }
  callbacks['createDate:change'] = function(val) {
    this.$notify.warning("创建日期改变事件触发");
    // console.log("this,arguments",this,arguments);
  }
  callbacks['traderId:change'] = function(val) {
    this.$notify.warning("联系人改变事件触发");
    // console.log("this,arguments",this,arguments);
  }
  callbacks['isStop:change'] = function(val) {
    this.$notify.warning("联系人改变事件触发");
    console.log("this,arguments",arguments);
  }
  callbacks['associateClientId:open'] = function(dialog) {
    this.$notify.warning("弹出选择上家客户,open事件触发,增加参数");
    //可以在 dialog.action 中加入 get参数,
    //dialog.action += '&key=aaa';
    //也可以直接在dialog.params中加入参数
    //dialog.params.key='bbb';
    console.log("this,arguments",arguments[0]);
  }
  callbacks['associateClientId:select'] = function(row,e) {
    this.$notify.warning("弹出选择上家客户,选中回调事件触发");
    console.log("this,arguments",arguments);
  }
  callbacks['xiajia:select'] = function(row,e) {
    this.$notify.warning("弹出选择下家客户,选中回调事件触发");
    console.log("this,arguments",arguments);
  }
  callbacks['pic:remove'] = function(row,e) {
    this.$notify.warning("文件删除触发");
    // console.log("this,arguments",arguments);
  }
  callbacks['pic:success'] = function(row,e) {
    this.$notify.warning("文件上传成功触发");
    // console.log("this,arguments",arguments);
  }

  var app = new Vue({
    el: '#div1',
    data: {
      'fldDefine':fldDefine,
      'row':row,
      'rules':rules,
      'action':action,
      'callbacks':callbacks,//所有表单元素的自定义事件的回调集合
    },
  });



</script>

</html>