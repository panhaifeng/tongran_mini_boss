<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <!-- import CSS -->
  <link rel="stylesheet" href="Resource/Script/vue/element/index.css">
  <script src="Resource/Script/vue/vue.js"></script>
  <script src="Resource/Script/vue/element/index.js"></script>
  <script src="Resource/Script/vue/element/axios.min.js"></script>
  <script src="Resource/Script/vue/element/components.js"></script>
  <style>
    .el-dialog__body {
        padding: 0 10px 10px 10px;
    }
    .el-pagination {
      margin-top: 5px;
    }
    .el-header, .el-footer {
      background-color: #B3C0D1;
      color: #333;
      text-align: center;
      /*line-height: 60px;*/
    }
    .el-table .cell {
      white-space: nowrap;
    }
    .el-table td,.el-table th {
      padding: 3px 3px 4px 5px;
      font-size:13px;
      /*line-height: 20px;*/
    }
  </style>
</head>
<body>
  <div id='div1'>   
    <p>
      this.$message(msg,'success');
      <el-button @click="success('aaa')">success</el-button>  
    </p>
    <p>
      this.$message(msg,'error');
      <el-button @click="error('bbb')">error</el-button>  
    </p>
    <p>
      this.$message(msg,'info');
      <el-button @click="info('ccc')">info</el-button>  
    </p>
    <p>
      this.$message(msg,'warning');
      <el-button @click="warning('ddd')">warning</el-button>  
    </p><p>
      app.warning(msg);
      <el-button onclick="test('调用app.success方法')">vue外部调用vue中的方法</el-button>  
    </p><p>
      app.$message(msg,'success');
      <el-button onclick="test1('直接调用app.$message')">vue外部直接调用message</el-button>  
    </p>   
    
    
    
  </div>
</body>


<script>

  var app = new Vue({
    el: '#div1',
    methods: {
      success: function(msg) {
        this.$message(msg,'success');
      },
      error: function(msg) {
        this.$message(msg,'error');
      },
      info: function(msg) {
        this.$message(msg,'info');
      },
      warning: function(msg) {
        this.$message(msg,'warning');
      },
    },
    
  });

  // app.error('asdfasd');

  //在vue外部调用方法
  var test = function(msg) {
    app.warning(msg);
  }
  var test1 = function(msg) {
    app.$message(msg,'success');
  }
  // var error = function() {
  //   console.log(this);
  //   window.parent.showMsg('error','error');
  // }
</script>

</html>