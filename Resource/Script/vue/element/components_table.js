
//列表组件
Vue.component('comp-table',{
  //editColumnWidth,showIndex
  // props:['cols','data','action','height','rowKey'],
  props : {
    cols :{required:false},
    data :{required:false},
    action :{required:false,},
    height :{required:false,default:500},
    rowKey:{required:false,default:'id'},
  },
  data : function() {
    return {
      // 'searchItem':[],
      'columns':this.cols,
      // 'params':{},
      'multiSelect':false,
      //
      'rows':this.data||[],
      //记录的key字段
      // 'rowKey':'id',
      //是否显示载入
      'isLoading':false,
      //表格高度
      'myHeight':this.height || 500,
      //搜索关键字
      // 'key':'',
      //高级搜索关键字
      // 'advParams':{},
      //关键字可匹配字段
      // 'colsForKey':[],
      //关键字输入后选中的匹配字段
      // 'colForKey':'',
      //选中记录
      //'multipleSelection':[],
      //是否显示操作列
      'showEditColumn':false,
      //是否显示展开详细列
      'showExpand':false,
      //是否显示合计行
      'showSummary':false,
      //是否显示操作栏的文字,
      'showHeaderText':true,
      //是否显示序号列
      'showIndex':false,
      //是否有边线:
      'border':true,
      //是否在序号列表头显示新增按钮
      'showHeaderIndexButton':false
    }
  },
  template: `
    <div>
      <slot name="leftTopSlot"></slot>
      <slot name="rightTopSlot"></slot>
      <!-- 数据表格 -->
      <el-table
        :data="rows"
        :row-key="rowKey"
        stripe
        :border="border"
        :height="myHeight"
        size="mini"
        ref="table"
        v-loading="isLoading"        
        :cell-style="setCellStyle"        
        :show-summary="showSummary"
        highlight-current-row
        v-on="$listeners"
        @sort-change="handleSortChange"        
        >
        <!-- 使用了v-on后,以下代码都省略了 
        @cell-mouse-enter="$emit('cell-mouse-enter',arguments[0],arguments[1],arguments[2],arguments[3])"
        @cell-mouse-leave="$emit('cell-mouse-leave',arguments[0],arguments[1],arguments[2],arguments[3])"
        @select="$emit('select',arguments[0],arguments[1])"
        @select-all="$emit('select-all',arguments[0],arguments[1])"
        @row-dblclick="$emit('row-dblclick',arguments[0],arguments[1])"
        @row-click="$emit('row-click',arguments[0],arguments[1])"
         -->

        <!-- 展开详细 -->

        <el-table-column 
          type="expand" 
          fixed="left"
          label="..."
          v-if="showExpand">
          <template slot-scope="scope">
            <slot :row="scope.row" name="expandSlot"></slot>
          </template>
        </el-table-column>  

        <!-- index列 -->
        <el-table-column
          type="index"
          v-if="showIndex"
          fixed="left"
          width="50">
          <template slot-scope="scope">#{{(scope.$index)+1}}</template>
          <!-- 该列是固定列,不能设置slot,否则会报错,-->
          <!-- 目前定死了只能显示按钮,后期可以考虑改为使用动态组件-->
          <template 
            slot="header"
            slot-scope="scope">                       
            <el-button               
              v-if="showHeaderIndexButton"
              size='mini' 
              type='primary'
              title='新增'
              circle
              plain
              style="padding:3.5px"
              @click="$emit('index-header-click',arguments[0])"
              icon="el-icon-plus" ></el-button>
            <span v-else>#</span>
          </template>

        </el-table-column>

        <!-- 多选 -->
        <el-table-column
          v-if="multiSelect"
          type="selection"
          width="55"
          ></el-table-column>

        <!-- 数据列 -->
        <template v-for="(col,index) in columns">

          <!-- html数据列 -->
          <el-table-column
            v-if="col.show && (col.isHtml===true || col.isHtml=='html')"
            :key="index"
            :property="index"
            :label="col.text"
            :width="col.width"
            :sortable="col.sortable"
            >
            <template slot-scope="scope">
              <template v-if="scope.row.__showButton && col.showButton">
                <slot name="rowButtonSlot" :row="scope.row" :index="scope.$index"></slot>
              </template>
              <span v-html="scope.row[index]" v-else></span>               
            </template>
          </el-table-column>

          <!-- 自定义组件 -->
          <el-table-column
            v-else-if="col.show && col.isHtml=='component'"
            :key="index"
            :property="index"
            :label="col.text"
            :width="col.width"
            :sortable="col.sortable"
            >
            <template slot-scope="scope">
              <template v-if="scope.row.__showButton && col.showButton">
                <slot name="rowButtonSlot" :row="scope.row" :index="scope.$index"></slot>
              </template>
              <component v-else :is="col.componentType" :row="scope.row" :index="scope.$index"></component>
            </template>
          </el-table-column>

          <!-- 非html数据列 -->
          <el-table-column
            v-else-if="col.show==true || col.show==undefined"
            :key="index"
            :property="index"
            :label="col.text"
            :width="col.width===undefined ? 80 : (col.width===''?'':col.width)"
            :sortable="col.sortable"
            show-overflow-tooltip
            :summation="col.summation"
            >
            <template slot-scope="scope">
              <div v-show="scope.row.__showButton && col.showButton">
              <slot name="rowButtonSlot" :row="scope.row" :index="scope.$index"></slot>
              </div>
              <span v-show="!(scope.row.__showButton && col.showButton)">{{getObjectValue(scope.row,col,index)}}</span>
            </template>
          </el-table-column>          
        </template>
        
      </el-table>

      <slot name="leftBottomSlot"></slot>
      <slot name="rightBottomSlot"></slot>
    </div>
  `,
  methods: {
    //根据col的定义,返回应该显示的字段值
    //row,当前记录行
    //col,字段定义,其中包含displayKey(其值如 Client.name,表示显示Client对象中的name属性)
    //index,字段名
    getObjectValue : function(row,col,index) {
      if(!col.displayKey) return row[index];
      // var arr = col.displayKey.split('.');
      // if(!row[arr[0]]) return row[index];
      // dump(col);dump(row[arr[0]][arr[1]]);
      return row[col.displayKey];
    },
    handleSortChange : function({column, prop, order}) {
      // dump(prop);return;
      this.$emit('sort-change',column, prop, order);
    },
    //改变行背景色
    setCellStyle({row, column, rowIndex, columnIndex}) {
      if(row.__bgColor) {
        return `backgroundColor:${row.__bgColor}`;
      }
      return '';
    },
    // //设置表格宽度
    setHeight : function(h) {
      this.myHeight=h;
    },

    //高级搜索点击
    openAdvanceSearch : function() {
      //显示高级搜索弹框
      this.$refs.advSearchDialog.show();
    },
    //高级搜索弹框点击确认后搜索
    handleAdvSearchOk : function(params) {
      this.advParams = params;
      this.key = '';
      this.currentPage=1;
      this._getRows();
    },
    //右上角菜单点击后触发
    handleCommand : function(itemName) {
      var key = `${itemName}:click`;
      if(!this.$root.callbacks[key]) return;
      this.$root.callbacks[key].apply(this,arguments);
    },
    

    //用户手动点击选择某条记录后触发
    // handleUserSelect : function(selection, row) {
    //   this.multipleSelection = selection;
    // },

    // //用户手动点击选择全部记录后触发
    // handleUserSelectAll : function(selection) {
    //   this.multipleSelection = selection;
    // },

    // handleRowDblClick :function(row,e) {
    //   this.$emit('row-dblclick',row,e);
    // },
  },
  mounted: function() {
    // dump(this.columns);
    //格式化columns的格式
    for(var key in this.columns) {
      item = this.columns[key];
      if(typeof(item)=='string') {
        this.columns[key] = {'text':item,'width':90};
      }
    }

    //处理需要 formatter 的列
    // var cols = this.$refs.table.columns;
    // var i=-1;
    // for(var k in this.columns) {
    //   i++;
    //   var col = this.columns[k];
    //   if(!col.formatter) continue;
    //   var funcName = col.formatter;
    //   if(!this.$root.callbacks[funcName]) {
    //     continue;
    //   }
    //   dump(cols[i]);
    //   cols[i].formatter = (row, column, cellValue, index)=> {
    //     dump(111,this.$root.callbacks[funcName]);
    //     return this.$root.callbacks[funcName].apply(this,[row, column, cellValue, index]);
    //     // return this.$root.callbacks[funcName](row, column, cellValue, index);
    //   }
    // }
  }
});

//数据列表弹出选择对话框 comp-dialog-tablelist
//包括搜索条件,分页等
Vue.component('comp-dialog-tablelist',{
  // props : ['title','action','name'],
  props : {
    title :{default: '双击选择'},
    action :{required:true},//必须参数
    // 如果要在sontpl中对弹框的open事件进行自定义,需要定义name属性,然后对callback['mydialog:open']进行定义即可
    name :{required:false,},
    //弹窗选择列表记录中的字段名,表单提交时该字段的值会提交到后台,并作为外键进行保存
    rowKey:{required:false,default:'id'}
  },
  data : function() {
    return {
      dialogTableVisible : false,
      //远程地址,有时需要更改,
      urlRemote : this.action,
      //列
      columns : {},
      //参数:其中可能包括从open方法中传入的参数,搜索参数,分页参数
      params:{},
      //搜索关键字
      key:'',
      //分页参数
      currentPage:1,
      pagesize:20,
      total:400,//记录总数
    };
  },
  // extends:Vue.component('comp-table'),//从comp-image继承
  template : `
    <el-dialog 
      :title="title" 
      :visible.sync="dialogTableVisible" 
      @open="handleOpen">       

      <comp-table
        :cols="columns"
        :action="action"
        ref="table"
        height="290"
        @row-dblclick="handleSelect"
        >
        <!-- 左上角插槽-搜索 -->
        <template slot="leftTopSlot">
          <div style="width:180px;float:left;margin-bottom:5px;">
            <el-input placeholder="关键字"  size="mini" v-model="key">
              <el-button slot="append" icon="el-icon-search" @click="handleSearch"></el-button>
            </el-input>
          </div>
        </template>
        
        <!-- 左下角插槽-分页 -->
        <template slot="leftBottomSlot">
          <div style="float:left; margin-top:5px;" >
            <div style="float:left;">
              <el-pagination
                @current-change="handleCurrentChange"
                :current-page="currentPage"
                :page-sizes="[20,50, 100, 200]"
                :page-size="pagesize"
                layout="total, prev, pager, next"
                small
                :total="total"
                background             
              ></el-pagination>
            </div>        
            <!-- 分页后的文字说明 -->
            <!--<span style="float:left;margin-top:9px;" v-html="textAfterPage"></span> -->
          </div>
        </template>          
      </comp-table>

      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleSelect">确 定</el-button>
        <el-button @click="dialogTableVisible = false">取 消</el-button>
      </div>
    </el-dialog>    
  `,
  methods : {
    //显示对话框
    show : function () {
      this.dialogTableVisible = true;
    },
    //隐藏对话框
    hide : function() {
      this.dialogTableVisible = false;
    },
    //获得地址
    getAction : function(url) {
      return this.urlRemote;
    },
    //修改地址
    setAction : function(url) {
      this.urlRemote = url;
    },
    //dialog打开前的回调
    handleOpen :function(){      
      // var key = `${this.name}:open`;
      // if(this.$root.callbacks[key]) {
      //   this.$root.callbacks[key].apply(this,[this]);
      // }
      //调用父组件的open事件
      this.$emit('open');
      //dom元素生成后,从服务器获取数据
      this.$nextTick(()=>{
        this.$refs.table.multiSelect = this.multiSelect;
        this._getRows();
      });
    },
    //通过ajax请求从服务器获得数据
    _getRows : function() {
      var url = this.urlRemote;
      if(url=='' || url==undefined) {
        alert('popup-select 组件未定义action参数');
        return false;
      }
      this.params.pagesize = this.pagesize;
      this.params.currentPage = this.currentPage;
      this.params.key = this.key;
      this.$http.post(url, this.params)
      .then((response)=>{
        var cols = this._formatColumns(response.data.columns);
        var rows = this._formatRows(response.data.rows);
        this.total = response.data.total;
        this.$refs.table.columns = response.data.columns;
        this.$refs.table.rows = rows;
      })
      .catch(function (error) {
        console.error(error);
      });
    },
    //格式化表头定义
    _formatColumns : function(columns) {
      for(var key in columns) {
        if(typeof(columns[key])=='string') {
          columns[key] = {'text':columns[key],'width':80};
        }
      }
      return columns;
    },
    //格式化数据集
    _formatRows : function(rows) {
      //判断返回的记录集中是否存在rowKey字段,
      //如果存在构造虚拟的rowKey,否则返回时有错.
      if(rows.length>0) {
        if(rows[0][this.rowKey]==undefined) {
          console.warn(`后台返回的数据集中未包含${this.rowKey}字段,这可能导致确认回调时的错误!`);
          //构造虚拟的rowkey
          for(var i=0;rows[i];i++) {
            rows[i][this.rowKey] = i+1;
          }
        }
      }
      return rows;
    },
    handleCurrentChange : function(page) {
      this.currentPage=page;
      this._getRows();
    },

    handleSelect : function(row,e){
      var key = this.rowKey;
      if(!row.hasOwnProperty(key)) {
        alert(`选中的记录中未包含 ${key} 字段`);
        return false;
      }

      this.dialogTableVisible = false;
      this.$emit('select',row);
      

      // var key = `${this.fld.name}:select`;
      // if(this.$root.callbacks[key]) {
      //   this.$root.callbacks[key].apply(this,[row,e]);
      // }
    },

    //点击搜索
    handleSearch : function() {
      this.currentPage=1;
      this._getRows();
    },

    //取消已选择结果
    handleClear : function() {
      this.displayText='';
      this.$emit('input','');
    }
  },
  mounted : function() {
    // this._getRows();
  }
});