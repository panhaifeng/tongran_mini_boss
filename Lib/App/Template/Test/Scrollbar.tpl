<!DOCTYPE html>
<html>
<title><{$title}></title>
<head>
  <meta charset="UTF-8">
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/index.css"}>
  <style type="text/css">
    #app {
      height: 300px;
      overflow: hidden;
      width:600px;
    }

    /*展示列表的区域，超过200px出现滚动条*/
    .list {
      max-height: 200px;
    }
  </style>
</head>
<body>
  <p>1,将元素用 el-scrollbar 框住即可</p>
  <p>2,wrap-class中的类必须指定max-height,表示显示区域的高度</p>
  <p>3,如果需要横向滚动条,需要指定 el-scrollbar父元素(app)的宽度,或者直接指定 el-scrollbar的宽度</p>
  <div id="app">
    <h2>list:</h2>
    <el-scrollbar wrap-class="list"  :native="false">
      <div v-for="value in num" style="width:2000px;">
        bbb
      </div>
    </el-scrollbar>
  </div>
</body>
<script src="Resource/Script/vue/vue.js"></script>
<script src="Resource/Script/vue/element/index.js"></script>
<script src="Resource/Script/vue/element/axios.min.js"></script>
<script src="Resource/Script/vue/element/components.js"></script>
<script>
  new Vue({
  el: "#app",
  data: {
    num: 30
  }
})
</script>

</html>