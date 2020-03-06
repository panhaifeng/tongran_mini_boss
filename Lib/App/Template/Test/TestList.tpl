<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <!-- import CSS -->
  <link rel="stylesheet" href="Resource/Script/vue/element/index.css">
  
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
    .el-dropdown-link {
      cursor: pointer;
    }
    .demo-table-expand {
      font-size: 0;
    }
    .demo-table-expand label {
      width: 90px;
      color: #99a9bf;
    }
    .demo-table-expand .el-form-item {
      margin-left: 0;
      margin-right: 0;
      margin-bottom: 0;
      width: 50%;
      padding-left: 100px;
    }
  </style>
</head>
<body>
  <div id='div1'>    
    <comp-table-list 
      :action="action"
      :cols="columns" 
      :multiselect="multiSelect"
      ref="tbl" 
      >        
      <!-- 每行的操作按钮的下拉菜单 -->
      <comp 
        v-for='(comp,index) in compsSlot'
        :key="index"
        :is="comp" 
        slot='userslot' 
        :ref='comp'
        ></comp>
      <!-- 每行详细信息展开面板的显示 数据集为props.row -->
      <template slot-scope="props" slot="expandslot">
        <!-- 指定展开面板的显示样式,自定义组件 -->
        <!-- 展开面板默认的展开样式 -->
        <comp :is="compExpand" :row="props.row" :fld="columnsExpand"></comp>
        
      </template>
    </comp-table-list>      
  </div>
</body>

<script src="Resource/Script/vue/vue.js"></script>
<script src="Resource/Script/vue/element/index.js"></script>
<script src="Resource/Script/vue/element/axios.min.js"></script>
<script src="Resource/Script/vue/element/components.js"></script>
<script>
  var title='页面标题';
  var columns = <{$arr_field_info|@json_encode}>;
  var action = "<{$action}>";
  var textmemo = <{$textmemo|@json_encode}>||'';
  var searchItems = <{$searchItems|@json_encode}>;
  var menuRightTop = <{$menuRightTop|@json_encode}>;
  //关键自匹配字段
  var colsForKey = <{$colsForKey|@json_encode}>;
  var multiSelect = <{$multiSelect|@json_encode}>;
  var editButtons = <{$editButtons|@json_encode}>||[];

  //回调函数的集合
  var callbacks = [];

  var columnsExpand = <{$arr_field_expand|@json_encode}>||[];

  //载入用户自定义js文件,一般包括回调和自定义组件
  <{include file=$sonTpl}>

  var app = new Vue({
    el: '#div1',
    data : function() {
      return {
        'action':action,
        //数据表对应的字段
        'columns':columns,
        //详细展开面板需要展示的数据
        'columnsExpand':columnsExpand,
        //展开面板对应的组件,决定展开面板的展示效果,可设置为用户自定义的comp todo
        'compExpand':'comp-expand-form',
        'textmemo':textmemo,
        'searchItems':searchItems,
        'menuRightTop':menuRightTop,
        'colsForKey':colsForKey,
        'multiSelect':multiSelect,
        'editButtons':editButtons,
        //所有表单元素的自定义事件的回调集合
        'callbacks':callbacks,
        //用户自定义插槽
        'compsSlot':[],
      }
    },
    methods: {
      
    },
    mounted: function() {
      //设置右上角菜单
      this.$refs.tbl.setMenuRightTop(this.menuRightTop);
      this.$refs.tbl.setColsForKey(this.colsForKey);
      //设置每行的编辑按钮组
      this.$refs.tbl.setEditButtons(this.editButtons);
      this.$refs.tbl.setTextmemo(this.textmemo);
      this.$refs.tbl.setSearchItems(this.searchItems);
      this.$refs.tbl.setMultiselect(this.multiSelect);
      // this.$refs.tbl.setAction(this.action);
      //设置关键字匹配字段,输入关键字时自动提示

      //得到body高度
      var bodyHeight = document.documentElement.clientHeight || document.body.clientHeight;
      this.$refs.tbl.setHeight(bodyHeight-90);
    }
  });
  
</script>

</html>