<!DOCTYPE html>
<html>
<title><{$title}></title>
<head>
  <meta charset="UTF-8">
  <style>
    .el-dialog__body {
        padding: 0 10px 40px 10px  !important;
    }
  </style>
  <!-- import CSS -->
  <{webcontrol type='LoadJsCss' src="Resource/Script/layui/layui/css/layui.css"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/layui/style/admin.css"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/index.css"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/vue.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/index.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/axios.min.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/components.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/components_card.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/components_table.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/components_popup_select.js"}>
</head>
<body>
  <pre>
    有时需要点击文字或者链接弹出数据列表,选中后对当前行进行改动,
    类似弹出选择,
  </pre>
  <table border='1' width='200'>
    <tr> 
      <td>姓名</td>
      <td>id</td>
      <td>操作</td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td>
        <a href='#' onclick='app.clickBtn()'>弹出选择</a>
      </td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td>
        <a href='#' onclick='app.clickBtn()'>弹出选择</a>
      </td>
    </tr>
  </table>
  <div class="" id="div1">
    <!-- 
      注意url对应的地址应该提供columns和rows,而不仅仅是rows 
      如果要在sontpl中对弹框的open事件进行自定义,需要定义name属性,然后对callback['mydialog:open']进行定义即可
    -->
    <comp-dialog-tablelist
      title="提示"
      ref="dialog"
      name="mydialog"
      action="?controller=jichu_test&action=listClient"
      @open="handleOpen"
      @select="handleSelect"
      >
    </comp-dialog-tablelist>
  </div>
</body>


<script>

  var app = new Vue({
    el: '#div1',
    data: function() {      
      return {
        //第几行被点击
        btnIndex : -1,
      }
    },
    methods : {
      handleClose(done) {
        this.$confirm('确认关闭？')
          .then(_ => {
            done();
          })
          .catch(_ => {});
      },
      // handleRowDblClick : function(row) {
      //   dump(row);
      // },
      //弹窗弹开前触发
      handleOpen : function() {
        var url = this.$refs.dialog.action+'&aaa=aaa';
        this.$refs.dialog.setAction(url);
        dump('弹窗打开前触发',arguments);
      },
      //选中后回调
      handleSelect : function(ret){
        dump(`选中记录后触发,第${this.btnIndex}行的按钮被点击`,ret);
        alert(`将会修改第${this.btnIndex}行的数据`);
      },
      clickBtn : function() {
        //获得当前点击的元素的index-第几行
        var index=0;//假设是第一行
        this.btnIndex = index;
        this.$refs.dialog.show();
      }

    },
    mounted : function() {
    }
  });



</script>

</html>