
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <!-- import CSS -->
  <link rel="stylesheet" type="text/css" href="Resource/Script/layui/layui/css/layui.css?v=1542876904" />
  <link rel="stylesheet" type="text/css" href="Resource/Script/layui/style/admin.css?v=1545382172" />
  <link rel="stylesheet" href="Resource/Script/vue/element/index.css">
  <style type="text/css">
    .el-dialog__body {
        padding: 0 10px 40px 10px  !important;
    }
    .el-table__header th {
      border-top: 1px solid #ebeef5;
    }
  </style>
  <script src="Resource/Script/vue/vue.js"></script>
  <script src="Resource/Script/vue/element/index.js"></script>
  <script src="Resource/Script/vue/element/axios.min.js"></script>
  <script src="Resource/Script/vue/element/components.js?v=1"></script>
  <script src="Resource/Script/vue/element/components_card.js"></script>
  <script src="Resource/Script/vue/element/components_popup_select.js"></script>
  <script src="Resource/Script/vue/element/components_table.js"></script>
  <title>主从表单New</title>
</head>
<body>
  <div class="" id="div1">
    <div style="width:500px;margin-left: 200px;margin-top: 50px;">
      <el-table
        :data="tableData"
        stripe
        style="width: 100%"
        size="small"
        height="300"
        highlight-current-row
        border
        @cell-mouse-enter="handleCellOver"
        @cell-mouse-leave="handleCellLeave"
        >
        <el-table-column
          prop="date"
          label="日期"
          show-overflow-tooltip
          width="180">
          <template slot-scope="scope">
            <span v-if="!scope.row.__showButton">
              {{scope.row.date}}
            </span>
            <template v-else >
              <el-button type="primary" icon="el-icon-edit" size="mini" circle></el-button>
              <el-button type="primary" icon="el-icon-share" size="mini" circle></el-button>
              <el-button type="primary" icon="el-icon-delete" size="mini" circle></el-button>
              <el-dropdown type="primary" trigger="click" style="margin-left: 10px;">
                <el-button type="primary" icon="el-icon-more" size="mini" circle @click=></el-button>
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item>黄金糕</el-dropdown-item>
                  <el-dropdown-item>狮子头</el-dropdown-item>
                  <el-dropdown-item>螺蛳粉</el-dropdown-item>
                  <el-dropdown-item>双皮奶</el-dropdown-item>
                  <el-dropdown-item>蚵仔煎</el-dropdown-item>
                </el-dropdown-menu>
              </el-dropdown>
            </template>
          </template>
        </el-table-column>
        <el-table-column
          prop="name"
          label="姓名"
          show-overflow-tooltip
          width="180">
        </el-table-column>
        <el-table-column
          prop="address"
          show-overflow-tooltip
          label="地址"
          width="150">
        </el-table-column>
      </el-table>
    </div>

  </div>
</body>


<script>
  var app = new Vue({
    el: '#div1',
    data: function() {
      var rows = [{
          date: '2016-05-02',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1518 弄',          
        }, {
          date: '2016-05-04',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1517 弄'
        }, {
          date: '2016-05-01',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1519 弄'
        }, {
          date: '2016-05-02',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1518 弄'
        }, {
          date: '2016-05-04',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1517 弄'
        }, {
          date: '2016-05-01',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1519 弄'
        },{
          date: '2016-05-02',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1518 弄'
        }, {
          date: '2016-05-04',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1517 弄'
        }, {
          date: '2016-05-01',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1519 弄'
        },{
          date: '2016-05-03',
          name: '王小虎',
          address: '上海市普陀区金沙江路 1516 弄'
      }];
      rows.forEach((item,i)=>{
        item.__showButton=false;
      });
      dump(rows);
      return {
        tableData: rows
      }
    },
    methods : {
      handleCellOver : function(row, column, cell, event){
        row.__showButton=true;
      },
      handleCellLeave : function(row, column, cell, event){
        row.__showButton=false;
      },
    }  
  });
</script>

</html>