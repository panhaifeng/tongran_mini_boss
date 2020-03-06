<!DOCTYPE html>
<html>
<title><{$title}></title>
<head>
  <meta charset="UTF-8">
  <style>
  .el-col {
    margin-top: 20px;
  }
  .el-button{width: 200px;}
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
            <template>
              <div>
                <span>请选择产品大类</span>

              </div>
            </template>
            <el-col :span="24" v-for="(item,index) in proKind">
                <el-button type="primary" @click="toList(item.id)">{{item.kindName}}</el-button>
            </el-col>
          </el-row>
        </el-main>
      </el-container>
  </div>
</body>


<script>
  var _proKind = <{$proKinds|@json_encode}>;
  var app = new Vue({
    el: '#div1',
    data: function() {
      return {
        'baseUrl':'<{url controller=$smarty.get.controller action=Right}>',
        'proKind':_proKind
      }
    },
    methods :{
      toList :function(kind){
        console.log('kindid => ',kind);
        if(!kind){
          this.$message('参数有错误，请刷新重试');
          return false;
        }

        window.location.href = this.baseUrl + '&kindId='+kind;
      }
    }
  });
</script>
</html>