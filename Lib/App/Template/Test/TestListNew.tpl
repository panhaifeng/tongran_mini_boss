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
    .dialog-search-box-card{
      border:0px;
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
      width: 33.3%;
      padding-left: 50px;
    }

  </style>
</head>
<body>
  <div id='div1'>
    <comp-table
      :action="action"
      :cols="columns"
      ref="tbl"
      @sort-change="handleSortChange"
      >

      <!-- 左上角 -->
      <template slot="leftTopSlot">
        <div style="float:left;margin-bottom:7px;">
          <!-- 高级搜索弹框 -->
          <comp-advsearch-dialog
            ref="advSearchDialog"
            @select="handleAdvSearchOk"
            ></comp-advsearch-dialog>
          <!-- 搜索输入框 -->
          <el-autocomplete
            :trigger-on-focus="true"
            size="small"
            placeholder="输入关键字"
            prefix-icon="el-icon-search"
            v-model="key"
            :fetch-suggestions="querySearch"
            @select="handleKeywordSelect"
            >
            <i
              class="el-icon-more el-input__icon"
              slot="suffix"
              @click="openAdvanceSearch">
            </i>
            <template slot-scope="{ item }">
              <span class="addr"><font color="red">{{ item.text }}</font> 包含 <font color="blue">{{key}}</font></span>
            </template>
          </el-autocomplete>
        </div>
      </template>

      <!-- 右上角 -->
      <div style="float:right;margin:7px 15px 0 0;" slot="rightTopSlot">
        <el-dropdown @command="handleCommand" trigger='click'>
          <span class="el-dropdown-link el-icon-menu">
            高级功能<i class="el-icon-arrow-down el-icon--right"></i>
          </span>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item
              v-for="(item,index) in menuRightTop"
              :key="index"
              :command="item.name"
              :divided="item.divided"
              >
              {{item.text}}
            </el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>

      <!-- 左下角 -->
      <template slot="leftBottomSlot">
        <div style="float:left;" >
          <div style="float:left;">
            <el-pagination
              @size-change="handleSizeChange"
              @current-change="handleCurrentChange"
              :current-page="currentPage"
              :page-sizes="[20,50, 100, 200]"
              :page-size="pagesize"
              layout="total, sizes, prev, pager, next"
              :total="total"
              background
            ></el-pagination>
          </div>
          <!-- 分页后的文字说明 -->
          <span style="float:left;margin-top:9px;" v-html="textAfterPage"></span>
        </div>
      </template>

      <!-- 右下角 -->
      <div style="float:right;margin-top:7px;" slot="rightBottomSlot">
        <el-button-group>
          <el-button circle icon="el-icon-refresh" size="small" @click="handleRefresh"></el-button>
        </el-button-group>
      </div>

      <!-- 表格详细展开后的效果 -->
      <template slot="expandSlot" slot-scope="props">
        <comp :is="optExpand.type" :row="props.row" :options="optExpand.options"></comp>
      </template>

      <!-- 操作栏的效果-下拉菜单或者平铺按钮 -->
      <template slot="rowButtonSlot" slot-scope="props">
        <el-dropdown size='small' @command="handleRowCommand" trigger="click">
          <el-button size='mini'>
            更多<i class="el-icon-arrow-down el-icon--right"></i>
          </el-button>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item
              v-for="(btn,index) in editButtons"
              :key="index"
              :command="{btn:btn,row:props.row}"
              :disabled="props.row.__btnsDisabled && props.row.__btnsDisabled[btn.text]"
              >{{btn.text}}</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </template>

    </comp-table>

    <!-- 为其他操作预留的组件位置 -->
    <!-- 比如某个订单需要在弹窗中设置备注信息 -->
    <!-- 比如在弹窗中设置某个客户的联系人等 -->
    <componet v-for="(comp,index) in otherComps" key="index" :is="comp.type" :ref="comp.name" v-bind="comp"></componet>

  </div>
</body>

<script src="Resource/Script/vue/vue.js"></script>
<script src="Resource/Script/vue/element/index.js"></script>
<script src="Resource/Script/vue/element/axios.min.js"></script>
<script src="Resource/Script/vue/element/components.js"></script>
<script src="Resource/Script/vue/element/components_table.js"></script>
<script>
  var title='页面标题';
  var columns = <{$arr_field_info|@json_encode}>;
  var action = "<{$action}>";
  var textAfterPage = <{$textAfterPage|@json_encode}>||'';
  var searchItems = <{$searchItems|@json_encode}>;
  var menuRightTop = <{$menuRightTop|@json_encode}>;
  //关键自匹配字段
  var colsForKey = <{$colsForKey|@json_encode}>;
  var multiSelect = <{$multiSelect|@json_encode}>;
  var editButtons = <{$editButtons|@json_encode}>||[];
  var optExpand = <{$optExpand|@json_encode}>;

  //回调函数的集合
  var callbacks = [];

  // var columnsExpand = <{$arr_field_expand|@json_encode}>||null;

  //载入用户自定义js文件,一般包括回调和自定义组件
  <{include file=$sonTpl}>

  var app = new Vue({
    el: '#div1',
    data : function() {
      return {
        //当前页码
        'currentPage':1,
        //每页记录数
        'pagesize':20,
        //记录总数
        'total':100,
        //排序字段
        sortBy:'',
        //升序或者降序
        sortOrder:'',
        //分页导航后的文字
        'textAfterPage':'asdfas',
        //右上角按钮
        'menuRightTop':[],
        //搜索关键字
        'key':'',
        //高级搜索关键字
        'advParams':{},
        //关键字可匹配的字段集合
        'colsForKey':colsForKey,
        //关键字输入后选中的匹配字段
        'colForKey':'',
        //高级搜索弹框需要的formItem
        'searchItems':searchItems,
        //是否显示载入
        'isLoading':true,
        //数据集合
        'rows':[],
        //是否需要选择
        'multiSelect':multiSelect,

        //获取数据的远程地址
        'action':action,
        //从后台获取数据时需要带入的参数
        'params':{},
        //数据表对应的字段
        'columns':columns,
        //详细展开面板配置
        'optExpand':optExpand,
        //展开面板对应的组件,决定展开面板的展示效果,可设置为用户自定义的comp todo
        // 'compExpand':compExpand || 'comp-expand-form',
        'textAfterPage':textAfterPage,
        'menuRightTop':menuRightTop,

        'editButtons':editButtons,
        //所有表单元素的自定义事件的回调集合
        'callbacks':callbacks,
        //用户自定义插槽
        'otherComps':[],
      }
    },
    methods: {
      //-------------------数据相关-------------------
      //通过ajax请求从服务器获得数据
      //p:搜索带入的参数,
      _getRows : function() {
        // dump(this.advParams);
        this.params.key = this.key;
        for(var k in this.advParams) {
          var item = this.advParams[k];
          this.params[k] = item;
        }
        this.params.colForKey = this.colForKey.col;
        console.log('_getRow fired,params:',this.params);
        var url = this.action;
        this.params.pagesize = this.pagesize;
        this.params.currentPage = this.currentPage;
        this.params.sortBy = this.sortBy;
        this.params.sortOrder = this.sortOrder;

        this.isLoading = true;
        this.$http.post(url, this.params)
        .then((response)=> {
          var rows = response.data.rows;
          this.total = response.data.total;
          this.rows = rows;
          this.isLoading = false;
          this.$refs.tbl.rows=this.rows;
        })
        .catch((error)=>{
          this.$notify.warning('数据获取出错');
          this.isLoading = false;
          console.log(error);
        });
      },
      //排序触发
      //如果为空函数,只对本页排序,不发起服务器请求,
      handleSortChange: function(column, prop, order) {
        if(column.sortable=='custom') {
          this.sortBy = prop;
          this.sortOrder = order;
          this._getRows();
        }
      },
      //每行的编辑菜单项选中
      handleRowCommand(command) {
        var btn = command.btn;
        var row = command.row;
        // console.log(btn,row);
        //如果存在url属性,跳转
        if(btn.url) {
          window.location.href=btn.url;
          return;
        }
        //删除的处理
        if(btn.isRemove) {
          this.$confirm('确认删除吗?', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
          }).then(() => {
            var url = btn.removeUrl;
            var params = {row:row};
            this.$http.post(url,params).then((res)=>{
              console.log(res);
              if(!res.data.success) {
                this.$message('删除失败','error');
                // console.log(res);
                return false;
              }
              this.$message('删除成功','success');
              //刷新grid
              this._getRows();
              return;
            }).catch((error)=>{
              console.error(error);
            });
          }).catch(() => {
            // this.$message('已取消删除');
            return false;
          });
        }

        //其他处理,比如弹窗交互或者其他ajax调用,可考虑采用slot或者其他
        var key = `${btn.name}:click`;
        if(!this.$root.callbacks[key]) return;
        //回调函数调用.将当前记录作为参数传入
        this.$root.callbacks[key].apply(this,[row]);
      },

      //-------------------高级搜索相关-----------------------
      //高级搜索弹框点击确认后搜索
      handleAdvSearchOk : function(params) {
        this.advParams = params;
        this.key = '';
        this.currentPage=1;
        this._getRows();
      },
      //关键字录入后的显示待匹配的字段
      querySearch(queryString, cb) {
        var ret = this.colsForKey;
        ret.map((item,i)=>{
          item.value=queryString;
        });
        cb(ret);
      },
      //关键字输入后选中匹配字段后触发
      handleKeywordSelect : function(val) {
        // console.log('handleKeywordSelect',val);
        this.currentPage=1;
        this.advParams={};
        // this.params={};
        this.colForKey = val;
        this._getRows();
      },
      //高级搜索点击
      openAdvanceSearch : function() {
        //显示高级搜索弹框
        this.$refs.advSearchDialog.show();
      },

      //刷新
      handleRefresh : function() {
        this._getRows();
      },

      //---------------------分页相关-----------------
      //页数改变时重新载入数据
      handleSizeChange :function(size) {
        this.currentPage=1;
        this._getRows();
      },
      //点击分页导航时重新载入数据
      handleCurrentChange : function(page) {
        this.currentPage=page;
        this._getRows();
      },

      //-------------------高级功能相关---------------
      //右上角菜单点击后触发
      handleCommand : function(itemName) {
        var key = `${itemName}:click`;
        if(!this.$root.callbacks[key]) return;
        this.$root.callbacks[key].apply(this,arguments);
      },

      //注册用户自定义组件
      makeNewComp : function(opt) {
        //避免重复插入
        var found = false;
        for(var i=0;this.otherComps[i];i++) {
          if(this.otherComps[i].type==opt.type) {
            found = true;
            break;
          }
        }
        if(!found) {
          this.otherComps.push(opt);
        }

        // this.$nextTick(()=>{
        //   return this.$refs[opt.name][0];
        // });
        // console.log(this.$refs[opt.name]);
      }

    },
    mounted: function() {
      //得到body高度
      var bodyHeight = document.documentElement.clientHeight || document.body.clientHeight;
      this.$refs.tbl.setHeight(bodyHeight-90);

      this.$refs.tbl.multiSelect=this.multiSelect;
      //设置是否显示操作列
      if(this.editButtons.length>0) {
        this.$refs.tbl.showEditColumn=true;
      }
      if(this.optExpand!=null) {
        this.$refs.tbl.showExpand=true;
      }

      //设置高级搜索默认值
      for(var k in this.searchItems) {
        if(this.searchItems[k]!='') {
          this.advParams[k] = this.searchItems[k];
        }
      }


      //获得数据
      this._getRows();

      //设置高级弹窗的form-item,
      this.$refs.advSearchDialog.setSearchItems(this.searchItems);
    }
  });

</script>

</html>