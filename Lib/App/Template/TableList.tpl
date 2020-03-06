<!DOCTYPE html>
<html>
<title><{$title}></title>
<head>
  <meta charset="UTF-8">
  <!-- import CSS -->
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/index.css"}>

  <style>
    body{background-color: #f5f5f5;}
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
    #loading-mask{
      position:absolute;
      left:0;
      top:0;
      width:100%;
      height:100%;
      z-index:20000;
      background-color:#fff;
    }
    #loading{
      position:absolute;
      left:47%;
      top:45%;
      padding:2px;
      z-index:20001;
      height:auto;
    }
    #loading .loading-indicator{
      background:white;
      color:#555;
      font:bold 13px tahoma,arial,helvetica;
      padding:10px;
      margin:0;
      text-align:center;
      height:auto;
      font-size:40px;
    }
    table tr td a{text-decoration: none;}
    .el-table__expanded-cell {
      z-index: 999;
      padding: 0;
    }

    .el-table__expanded-cell .expand-area-wrapper {
      padding: 20px 50px;
      overflow-x: hidden;
      border: 1px solid #dfe6ec;
    }
    .el-tab-pane {
      height:200px;
    }
    .el-table__body-wrapper, .el-table__footer-wrapper, .el-table__header-wrapper{
      width: 100%;
    }
    /*解决缩放表头错位的问题*/
    body .el-table th.gutter{
      display: table-cell!important;
    }
  </style>
</head>
<body>
  <div id='div1'>
    <!-- 遮罩 -->
    <transition v-if="showLoading" name="el-fade-in-linear">
      <div id="loading-mask"></div>
    </transition>
    <transition v-if="showLoading" name="el-fade-in-linear">
      <div id="loading">
        <div class="loading-indicator">
          <i class='el-icon-loading' ></i>
        </div>
      </div>
    </transition>
    <!-- end -->
    <comp-table
      :action="action"
      :cols="columns"
      :row-key="rowKey"
      ref="tbl"
      @sort-change="handleSortChange"
      @select="handleUserSelect"
      @select-all="handleUserSelectAll"
      @cell-mouse-enter="handelMouseOverRow"
      @cell-mouse-leave="handelMouseLeaveRow"
      @row-click = "handleRowClick"
      show-index
      >

      <!-- 左上角 -->
      <template slot="leftTopSlot">
        <div style="float:left;margin-bottom:7px;">
          <!-- 高级搜索弹框 -->
          <comp-advsearch-dialog
            ref="advSearchDialog"
            @select="handleAdvSearchOk"
            @open="colForKey='';searchDialogOpened=true;"
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
            select-when-unmatched
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
        <el-tooltip
          placement="right"
          effect="light"
          v-if="hasAdvParams()"
          style="margin-top: 10px;margin-left: 10px;">
          <div slot="content" v-html="$refs.advSearchDialog.descAdvParams"></div>
          <i class="el-icon-info" style="color:#ccc"></i>
        </el-tooltip>
      </template>

      <!-- 右上角 -->
      <div v-if="buttonRightTop || menuRightTop.length>0" style="float:right;margin:0px 15px 7px 0;" slot="rightTopSlot">
        <!-- 高级功能 -->
        <el-dropdown
          @command="handleCommand"
          @click="handleCommand(buttonRightTop)"
          :split-button="true"
          size="small"
          trigger='click'>
          <span :class="`el-dropdown-link ${buttonRightTop.icon||menuRightTopIcon[0]}`"> {{buttonRightTop.text}}</span>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item
              v-for="(item,index) in menuRightTop"
              :key="index"
              :command="item"
              :divided="item.divided"
              >
              <i :class="item.icon?item.icon:menuRightTopIcon[index+1]"> {{item.text}}</i>
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
        <!-- <el-button-group> -->
        <el-popover
          placement="top"
          trigger="click">
          <el-progress type="line" :percentage="exportPercentag" :width="50"></el-progress>
          <el-button circle icon="el-icon-goods" size="small" @click="handleExport" slot="reference" title="导出本页"></el-button>
        </el-popover>
        <el-popover
          v-if="actionExportAll"
          placement="top"
          trigger="click">
          <el-progress type="line" :percentage="exportPercentag" :width="50"></el-progress>
          <el-button circle icon="el-icon-sold-out" size="small" @click="handleExportAll" slot="reference" title="导出全部"></el-button>
        </el-popover>
        <el-button circle icon="el-icon-refresh" size="small" @click="handleRefresh" title="刷新"></el-button>
          <!-- 可显示列 -->
        <el-popover
          placement="bottom-start"
          :visible-arrow="false"
          trigger="click">
          <div class="el-table-filter__content">
            <el-scrollbar wrap-class="el-table-filter__wrap">
              <el-checkbox-group class="el-table-filter__checkbox-group" v-model="colNamesShow">
                <el-checkbox
                  v-for="(item,index) in columns"
                  :key="index"
                  :label='index'
                  v-if="item.text"
                  :disabled="item.showButton"
                  >{{item.text}}
                </el-checkbox>
              </el-checkbox-group>
            </el-scrollbar>
          </div>
          <el-button slot="reference" type="plain" icon="el-icon-setting" circle size="small"></el-button>
        </el-popover>
        <!-- </el-button-group> -->
      </div>

      <!-- 表格详细展开后的效果 -->
      <template slot="expandSlot" slot-scope="props">
          <comp
            :is="optExpand.type"
            :row="props.row"
            :options="optExpand.options">
          </comp>
      </template>

      <!-- 操作栏的效果-下拉菜单或者平铺按钮 -->
      <template slot="rowButtonSlot" slot-scope="props" >
        <!-- 前三个按钮组作为默认显示的按钮 -->
        <el-button
          v-for="(btn,index) in defaultEditButtons"
          :key="index"
          type="primary"
          plain
          :icon="btn.icon?btn.icon:defaultEditButtonsIcons[index]"
          circle
          size="mini"
          style="padding:3.5px;margin-left:3px;"
          @click="handleRowCommand({btn:btn,row:props.row})"
          :title="btn.text"
          :disabled="props.row[btn.options.disabledColumn]"
          ></el-button>
        <!-- 后面的按钮以dropDown呈现 -->
        <el-dropdown
          @visible-change="showDropdown=arguments[0];showDropdownIndex=props.index;"
          size='small'
          @command="handleRowCommand"
          trigger="click"
          v-if="moreEditButtons.length>0">
          <el-button type="primary" plain icon="el-icon-more" circle size="mini" style="padding:3.5px;margin-left:3px;"></el-button>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item
              v-for="(btn,index) in moreEditButtons"
              :key="index"
              :command="{btn:btn,row:props.row}"
              :disabled="props.row[btn.options.disabledColumn]"
              >{{btn.text}}</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </template>

    </comp-table>

    <!-- 为其他操作预留的组件位置 -->
    <!-- 比如某个订单需要在弹窗中设置备注信息 -->
    <!-- 比如在弹窗中设置某个客户的联系人等 -->
    <componet v-for="(comp,index) in otherComps" key="index" :is="comp.type" :ref="comp.name" v-bind="comp" ></componet>

  </div>
</body>
<{webcontrol type='LoadJsCss' src="Resource/Script/vue/vue.js"}>
<{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/index.js"}>
<{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/axios.min.js"}>
<{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/components.js"}>
<{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/components_popup_select.js"}>
<{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/components_table.js"}>
<{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/edit_button.js"}>
<{webcontrol type='LoadJsCss' src="Resource/Script/xlsx.full.min.js"}>
<script>
  var title='<{$title|default:"列表"}>';
  var columns = <{$arr_field_info|@json_encode}>;
  var action = "<{$action}>";
  var textAfterPage = <{$textAfterPage|@json_encode}>||'';
  var searchItems = <{$searchItems|@json_encode}>;
  var menuRightTop = <{$menuRightTop|@json_encode}>||[];
  //关键自匹配字段
  var colsForKey = <{$colsForKey|@json_encode}>||[];
  var multiSelect = <{$multiSelect|@json_encode}>;
  var editButtons = <{$editButtons|@json_encode}>||[];
  var optExpand = <{$optExpand|@json_encode}>;

  //回调函数的集合
  var callbacks = [];

  //载入用户自定义js文件,一般包括回调和自定义组件
<{if $sonTpl}>
  <{if $sonTpl|@is_string==1}>
    <{include file=$sonTpl}>
  <{else}>
    <{foreach from=$sonTpl item=js_item}>
      <{include file=$js_item}>

    <{/foreach}>
  <{/if}>
<{/if}>

  var app = new Vue({
    el: '#div1',
    data : function() {
      //如果 colsForKey 为空,从columns中获取
      if(colsForKey.length==0) {
        for(var k in columns) {
          var item=columns[k];
          if(item.forKeySearch) {
            colsForKey.push({'text':item.text,'col':k});
          }
        }
      }
      //默认显示所有列
      var colNamesShow=[];
      for(var k in columns) {
        columns[k].show=true;
        colNamesShow.push(k);
        //如果显示操作按钮,保证最小宽度为130
        if(columns[k].showButton && columns[k].width!=='') columns[k].width = columns[k].width>130 ? columns[k].width : 130;
      }

      //将 editButtons 分解成两个部分,前3个一组默认显示,后面的以dropdown呈现
      var defaultEditButtons = [];
      var moreEditButtons = [];
      editButtons.forEach((item,i)=>{
        if(i<3) defaultEditButtons.push(item);
        else {
          moreEditButtons.push(item);
        }
      });

      //将 menuRightTop 分解,第一个单独形成按钮
      var addUrl = <{$addUrl|@json_encode}> ||'';
      var buttonRightTop = addUrl!='' ? {text:'新增',url:addUrl,icon:'el-icon-circle-plus'} : menuRightTop.splice(0,1)[0];

      return {
        //当前页码
        'currentPage':1,
        //每页记录数
        'pagesize':20,
        //记录总数
        'total':0,
        //排序字段
        sortBy:'',
        //升序或者降序
        sortOrder:'',
        //分页导航后的文字
        'textAfterPage':'asdfas',

        //右上角的更多菜单
        'menuRightTop':menuRightTop,
        //右上角第一个按钮
        'buttonRightTop':buttonRightTop,
        //右上角按钮图标集合
        'menuRightTopIcon':['el-icon-tickets','el-icon-document','el-icon-goods','el-icon-sold-out','el-icon-news','el-icon-message','el-icon-date','el-icon-printer','el-icon-time','el-icon-bell'],
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
        //高级搜索弹窗是否弹开过
        'searchDialogOpened':false,
        //是否显示载入
        'isLoading':true,
        //数据集合
        'rows':[],
        //是否需要选择
        'multiSelect':multiSelect,
        //本页选中记录
        'multipleSelection':[],
        //获取数据的远程地址
        'action':action,
        //从后台获取数据时需要带入的参数
        'params':{},
        //数据表对应的字段
        'columns':columns,
        //需要显示的列
        'colNamesShow':colNamesShow,
        //详细展开面板配置
        'optExpand':optExpand,
        //展开面板对应的组件,决定展开面板的展示效果,可设置为用户自定义的comp todo
        // 'compExpand':compExpand || 'comp-expand-form',
        'textAfterPage':textAfterPage,

        //每行中的高级功能按钮
        'editButtons':editButtons,
        'defaultEditButtons':defaultEditButtons,
        'moreEditButtons':moreEditButtons,
        //dropdown-men是否显示,dropdown显示时,不隐藏菜单
        'showDropdown':false,
        //当前显示dropdown的是第几行
        'showDropdownIndex':-1,
        //每行功能按钮的可用图标
        'defaultEditButtonsIcons':['el-icon-edit','el-icon-delete','el-icon-edit-outline'],

        //所有表单元素的自定义事件的回调集合
        'callbacks':callbacks,
        //用户自定义插槽
        'otherComps':[],
        //是否载入状态
        'showLoading':true,
        //新增记录的url
        //'addUrl' : addUrl,
        //新增按钮图标
        //'iconAdd': addUrl=='' ? "el-icon-menu":"el-icon-circle-plus",
        //导出进度百分比
        'exportPercentag':0,
        //全部导出时获取数据地址
        'actionExportAll':<{$actionExportAll|@json_encode}>,
        'rowKey':<{$rowKey|@json_encode}>||'id',
      }
    },
    methods: {
      //测试方法
      test : function() {
        dump(this);
      },

      //新增记录按钮点击后触发
      // 'handleAdd' : function() {
      //   if(this.addUrl=='') return;
      //   window.location.href=this.addUrl;
      // },
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

        // this.showLoading = true;
        this.$http.post(url, this.params)
        .then((response)=> {
          var rows = response.data.rows;
          this.total = response.data.total;
          this.rows = rows;
          //如果没有rowkey(id),补充rowkey字段,
          var temp=[];
          for(var i=0;rows[i];i++) {
            temp.push(rows[i][this.rowKey]);
            if(rows[i][this.rowKey]==undefined) {
              if(i==0) {
                console.warn('返回的数据集中未包含id字段,系统自动填充id字段,这可能会导致修改删除操作异常!');
              }
              rows[i][this.rowKey]=i+1;
            }
          }
          //以下是es6判断数组是否有重复值的方法,可能有些浏览器不一定兼容
          if((new Set(temp)).size != temp.length) {
            console.warn('rowKey重复,数据集中发现rowkey重复的记录,可能需要指定其他的字段为rowkey');
          }
          //检查rowkey字段是否有重复
          this.showLoading = false;
          this.$refs.tbl.rows=this.rows;
        })
        .catch((error)=>{
          this.$notify.warning({
            title : '错误',
            message:'数据获取出错'
          });
          this.showLoading = false;
          console.log(error);
        });
      },
      //点击选中select
      handleUserSelect(selection,row) {
        this.multipleSelection = selection;
      },
      //点击全选select
      handleUserSelectAll(selection) {
        this.multipleSelection = selection;
      },
      //排序触发
      //如果为空函数,只对本页排序,不发起服务器请求,
      handleSortChange: function(column, prop, order) {
        if(column.sortable=='custom') {
          this.sortBy = prop;
          this.sortOrder = order;
          this.showLoading = true;
          this._getRows();
        }
      },
      //行点击时,隐藏其他行的dropdown,然后显示当前行的dropdow
      handleRowClick : function(row, event, column) {
        if(!this.showDropdown) return;
        //隐藏之前显示的dropdown-menu
        if(this.rows[this.showDropdownIndex]) {
          this.rows[this.showDropdownIndex]['__showButton'] = false;
        }
        // dump(this,this.showDropdownIndex);
        if(!row.hasOwnProperty('__showButton')) {
          Vue.set(row,'__showButton',true);
        } else {
          row.__showButton = true;
        }
      },
      //鼠标移上显示操作按钮
      handelMouseOverRow : function(row, column, cell, event) {
        //如果有dropDown打开,直接返回
        if(this.showDropdown) return;
        //隐藏之前显示的dropdown-menu
        if(this.rows[this.showDropdownIndex]) {
          this.rows[this.showDropdownIndex]['__showButton'] = false;
        }
        if(!row.hasOwnProperty('__showButton')) {
          Vue.set(row,'__showButton',true);
        } else {
          row.__showButton = true;
        }
      },
      //鼠标移出隐藏操作按钮
      handelMouseLeaveRow : function(row, column, cell, event) {
        if(this.showDropdown) return;
        row.__showButton = false;
      },
      //每行的编辑菜单项选中
      handleRowCommand(command) {
        var btn = command.btn;
        var row = command.row;
        RowEditButtonFuncs[btn.type].apply(this,[row,btn.options||{}]);
        return;
      },

      //-------------------高级搜索相关-----------------------
      //高级搜索内容是否都为空
      hasAdvParams : function() {
        //如果高级搜索弹框没有初始化，不显示
        if(this.searchDialogOpened===false) return false;
        for (var k in this.advParams) {
          if(this.advParams[k]!='') return true;
        }
        return false;
      },
      //高级搜索项的文字描述
      // getDescAdvParams : function() {
      //   var text=[];
      //   this.$refs.advSearchDialog.searchItems.forEach((item,index)=>{
      //     var textJoin = item.type=='comp-text' ? "包含" : "等于";
      //     if(this.advParams[item.name]===undefined) return;
      //     if(this.advParams[item.name]=='') return;
      //     text.push(`${item.title} ${textJoin} ${this.advParams[item.name]}`)
      //   });
      //   return text.join("<br/>");
      // },
      //高级搜索弹框点击确认后搜索
      handleAdvSearchOk : function(params) {
        this.advParams = params;
        this.key = '';
        this.currentPage=1;
        this._getRows();
      },
      //关键字录入后的显示待匹配的字段
      querySearch(queryString, cb) {
        var ret = this.colsForKey || [];
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

      //导出本页
      //如果使用 FileSaver.js 就不要同时使用以下函数
      _saveAs:function(obj, fileName) {//当然可以自定义简单的下载文件实现方式
          var tmpa = document.createElement("a");
          tmpa.download = fileName || "下载";
          tmpa.href = URL.createObjectURL(obj); //绑定a标签
          tmpa.click(); //模拟点击实现下载
          setTimeout(function () { //延时释放
              URL.revokeObjectURL(obj); //用URL.revokeObjectURL()来释放这个object URL
          }, 100);
      },
      //导出位excel的通用方法
      //rows为数据集,字段和this.columns不一定匹配
      _exportExcel : function(rows,fileName) {
        var fName = fileName || "data.xlsx";
        var data = [];
        //表头处理
        var temp = [];
        for(var k in this.columns) {
          var col = this.columns[k];
          temp.push(col.text);
        }
        data.push(temp);

        //内容
        rows.forEach((item,i)=>{
          var temp = [];
          for(var k in this.columns) {
            var _tmpCellTxt = item[k] ? this.delHtmlTag(item[k]) : item[k];
            temp.push(_tmpCellTxt);
          }
          data.push(temp);
        });
        var ws = XLSX.utils.aoa_to_sheet(data);
        var wb=XLSX.utils.book_new();
        wb.SheetNames.push('sheet1');
        wb.Sheets['sheet1'] = ws;
        var wopts = { bookType:'xlsx', bookSST:false, type:'array' };
        var wbout = XLSX.write(wb,wopts);
        this._saveAs(new Blob([wbout],{type:"application/octet-stream"}), fName);
      },
      delHtmlTag : function(str){
        return typeof(str)=='string' ? str.replace(/<[^>]+>/g,"") : str;
      },
      //导出当前页,
      handleExport : function(){
        this.exportPercentag=0;
        setTimeout(()=>{
          this.exportPercentag=100;
          this._exportExcel(this.rows,`${title}${this.currentPage}.xlsx`);
        },500);

      },
      //导出全部
      handleExportAll: function(){
        //记录总数
        var total=this.total;
        // var total=1000;
        //每次请求条数
        var pageSize=200;
        //当前页
        var page=1;
        //访问地址
        var url=this.actionExportAll;
        if(!url) {
          console.error("未发现smarty模版变量$actionExportAll,无法使用导出全部功能!");
          return false;
        }
        //成功请求次数
        var completeCount=0;
        //设置当前进度百分比为0
        this.exportPercentag=0;
        //Promise.all要用到的参数, 存放每次请求的Promise对象
        var funcs=[];
        //this指针
        var _this = this;
        //需要请求的次数
        var times = Math.ceil(total/pageSize);
        for(var i=0;i<times;i++) {
          var param = {
            page:page++,
            pageSize:pageSize,
          };
          var func=new Promise(function(resolve, reject){
            _this.$http.post(url,param).then(function(response){
              var rows = response.data.rows;
              //完成百分比
              completeCount++;
              _this.exportPercentag=100*completeCount/times;
              // console.log(_this.exportPercentag);
              return resolve(rows);
            });
          });
          funcs.push(func);
        }

        //ajax请求全部完毕后进行导出
        Promise.all(funcs).then(function(values){
          var rows=[];
          //将数据合并
          for (var i=0; i<values.length; i++) {
              for (var j=0; j<values[i].length; j++) {
                  rows.push(values[i][j]);
              }
          }
          _this._exportExcel(rows,'all.xlsx');
        });
      },
      //刷新
      handleRefresh : function() {
        this.showLoading = true;
        this._getRows();
      },

      //---------------------分页相关-----------------
      //页数改变时重新载入数据
      handleSizeChange :function(size) {
        this.currentPage=1;
        this.pagesize=size;
        this.showLoading = true;
        this._getRows();
      },
      //点击分页导航时重新载入数据
      handleCurrentChange : function(page) {
        this.currentPage=page;
        this.showLoading = true;
        this._getRows();
      },

      //-------------------高级功能相关---------------
      //右上角菜单点击后触发
      handleCommand : function(item) {
        var key = `${item.name}:click`;
        if(this.$root.callbacks[key]){
          this.$root.callbacks[key].apply(this,arguments);
        }else if(item.url){
          window.location.href = item.url;
        }
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

        this.$nextTick(()=>{
          return this.$refs[opt.name][0];
        });
        // console.log(this.$refs[opt.name]);
      },

      //对localstorage的读写
      saveColsToLocal: function(cols) {
        var reg = new RegExp("(^|&)" + "controller" + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        var controller = unescape(r[2]);

        var reg = new RegExp("(^|&)" + "action" + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        var action = unescape(r[2]);

        var json = JSON.stringify(cols);
        var stor = window.localStorage;
        stor.setItem(`${controller}-${action}`,json);
        // dump(controller,action,json);
      },
      //从localstorage中读取可见列
      getColsFromLocal: function() {
        var reg = new RegExp("(^|&)" + "controller" + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        var controller = unescape(r[2]);

        var reg = new RegExp("(^|&)" + "action" + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        var action = unescape(r[2]);
        var json = window.localStorage.getItem(`${controller}-${action}`);
        var cols = JSON.parse(json);
        return cols;
      }

    },
    watch : {
      //可显示列变化
      'colNamesShow' : function(val,oldVal) {
        this.saveColsToLocal(val);
        //将需要显示的列存入localstorage
        for(var i=0;val[i];i++) {
          this.columns[val[i]].show = true;
        }
        for(var k in this.columns) {
          if(val.indexOf(k)==-1) {
            this.columns[k].show = false;
          }
        }
      }
    },
    mounted: function() {
      //得到body高度
      this.$nextTick(()=>{
        var bodyHeight = document.documentElement.clientHeight || document.body.clientHeight;
        // console.log("bodyHeight",bodyHeight);
        this.$refs.tbl.setHeight(bodyHeight-90);
      });

      this.$refs.tbl.multiSelect=this.multiSelect;
      // this.$refs.tbl.rowKey = this.rowKey;
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

      //隐藏遮罩
      this.showLoading = false;

      //更换新增图标
      // if(this.addUrl!='') {
      //   this.iconAdd="el-icon-circle-plus";
      // }

      var cols = this.getColsFromLocal();
      if(cols) {
        this.colNamesShow = cols;
      }

      //是否设置了操作按钮列
      var hasShowButton = false;
      for(var k in columns) {
        if(columns[k].showButton) {
          hasShowButton=true;
          break;
        }
      }
      //如果没有在后台定义操作按钮显示列,默认第一列显示操作按钮
      if(!hasShowButton) {
        if(this.editButtons.length>0) {
          console.warn('存在行操作按钮,但是未在列定义中发现可显示操作按钮的列,您需要将某列的showButton设置为true');
          this.columns[this.colNamesShow[0]].showButton=true;
        }
      }
    }
  });

</script>

</html>