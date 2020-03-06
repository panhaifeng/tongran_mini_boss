<!DOCTYPE html>
<html>
<title><{$title}></title>
<head>
  <meta charset="UTF-8">
  <style>
    .el-dialog__body {
        padding: 0 10px 40px 10px  !important;
    }
    .grid-content{text-align: center;margin-top: 17%;}
    .grid-content h1{font-size: 26px;}
    .font-blue{color: #12afe3;}
  </style>
  <!-- import CSS -->
  <{webcontrol type='LoadJsCss' src="Resource/Script/layui/layui/css/layui.css"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/layui/style/admin.css"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/index.css"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/vue.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/index.js"}>

</head>
<body>
  <div id="div1">
    <el-container>
        <el-main>
          <el-row>
            <el-col :span="24"><div class="grid-content"><h1>{{welcomeMsg}}</h1></div></el-col>
          </el-row>
        </el-main>
      </el-container>
  </div>
</body>


<script>
  var app = new Vue({
    el: '#div1',
    data: function() {
      return {
        'welcomeMsg':'<{$welcomeMsg}>'
      }
    },
    mounted : function() {

    }
  });
</script>
</html>