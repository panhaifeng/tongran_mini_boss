//弹出选择控件-new,
//使用了comp-table作为其中的一个组成部分的组件.
Vue.component('comp-popup-select',{
  props:['fld','value'],
  data:function(){
    return {
      columns: {
        'date':{'text':'日期','width':200},
        'name':{'text':'姓名','width':200},
        'address':{'text':'地址','width':300},
      },
      rows: [{
        date: '2016-05-02',
        name: '王小虎',
        address: '上海市普陀区金沙江路 1518 弄'
      }],
      multiSelect:this.fld.multiSelect,
      //获取数据的url
      action:this.fld.action,
      //参数:其中可能包括从open方法中传入的参数,搜索参数,分页参数
      params:{},
      //搜索关键字
      key:'',
      //弹窗选择列表记录中的字段名,表单提交时该字段的值会提交到后台,并作为外键进行保存
      rowKey:this.fld.rowKey ||'id',

      //需要回显在text中的弹窗列表字段,比如客户名称
      displayKey:this.fld.displayKey,
      //text中显示的默认值
      displayText:this.fld.displayText,
      //对话框是否显示
      dialogTableVisible:false,
      //分页参数
      currentPage:1,
      pagesize:20,
      total:400,//记录总数
    };
  },
  template: `
    <div>
      <el-input
        :placeholder="fld.placeholder ? fld.placeholder : '点击选择'"
        :value="displayText"
        :disabled="fld.disabled"
        clearable
        @clear="handleClear" >
        <el-button slot="append" :disabled="fld.disabled" icon="el-icon-more" @click="dialogTableVisible=true" ></el-button>
      </el-input>
      <input type="hidden" :name='fld.name' :value="value"></input>
      <el-dialog
        :title="fld.title"
        :visible.sync="dialogTableVisible"
        append-to-body
        @open="handleOpen"
        ref="dialog"
        >
        <comp-table
          :cols="columns"
          :action="fld.action"
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
              <!--<span style="float:left;margin-top:9px;" v-html="textAfterPage"></span> -->
            </div>
          </template>
        </comp-table>

        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogTableVisible = false">取 消</el-button>
        </div>
      </el-dialog>
    </div>
  `,
  methods:{
    //设置displayText的值

    //以下事件没用了,都是在handelselect事件中处理了
    // handleModelInput :function(val){
    //   this.$emit("input", val);
    // },
    //dialog打开前的回调
    handleOpen :function(){
      var key = `${this.fld.name}:open`;
      if(this.$root.callbacks[key]) {
        this.$root.callbacks[key].apply(this,[this]);
      }
      //dom元素生成后,从服务器获取数据
      this.$nextTick(()=>{
        this.$refs.table.multiSelect = this.multiSelect;
        this._getRows();
      });
    },
    //通过ajax请求从服务器获得数据
    _getRows : function() {
      var url = this.action;
      if(url=='' || url==undefined) {
        alert('popup-select 组件未定义action参数');
        return false;
      }
      this.params.pagesize = this.pagesize;
      this.params.currentPage = this.currentPage;
      this.params.key = this.key;
      // dump(this);
      // var param= {pagesize:this.pagesize,currentPage:this.currentPage};
      //var _this = this;
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

    //页数改变时触发
    handleSizeChange :function(size) {
      this.currentPage=1;
      this._getRows();
    },

    handleCurrentChange : function(page) {
      this.currentPage=page;
      this._getRows();
    },

    //双击回调
    handleSelect : function(row,e){
      // dump('asdfasd');
      var key = this.fld.rowKey;
      if(!row.hasOwnProperty(key)) {
        alert(`选中的记录中未包含 ${key} 字段`);
        return false;
      }
      //改变文本框中的值,文本框属于组建内的元素,这个元素的改变应该组件自己负责
      //而hidden控件绑定的是记录集中的字段,这个值的改变应该由父组件负责
      this.displayText = row[this.fld.displayKey];
      //向外抛出事件,改变displayText所代表的子表记录的字段值
      // console.log('开始抛出');
      this.$emit('displaytext-change',this.displayText);

      this.dialogTableVisible = false;
      this.$emit('input',row[key]);


      var key = `${this.fld.name}:select`;
      if(this.$root.callbacks[key]) {
        this.$root.callbacks[key].apply(this,[row,e]);
      }
    },

    //点击搜索
    handleSearch : function() {
      this.currentPage=1;
      this._getRows();
    },

    //取消已选择结果
    handleClear : function() {
      this.displayText='';
      // this.$emit('clear',`${this.fld.name}:clear`,this);
      this.$emit('input','');
    }
  },
  watch : {
    //mainSon的弹窗中点击上一条下一条时,value变化,
    //需要联动改变displayText
    //但是只有上级组件才知道displayText应该改成什么
    //上级组件中有个事件displaytext-set就是用来处理这个的
    'value' : {
      handler : function(newVal,oldVal){
        // dump('popup.value change',oldVal,newVal);
        //向外抛出,在上级组件中改变displayText的值
        //因为只有上级组件才知道应该改成什么值,
        this.$emit('displaytext-set',this);
      },
      immediate: true,
    }
  }
});

//弹出选择,多选,比如多个产品
//回显效果变化太多,不进行封装,故只做一个触发弹出的多选按钮,
Vue.component('comp-popup-multi-select',{
  extends: Vue.component('comp-popup-select'),
  // props:['fld','value'],
  data:function(){
    return {
      columns: {
        // 'date':{'text':'日期','width':200},
        // 'name':{'text':'姓名','width':200},
        // 'address':{'text':'地址','width':300},
      },
      // rows: [
      //   // {
      //   //   date: '2016-05-02',
      //   //   name: '王小虎',
      //   //   address: '上海市普陀区金沙江路 1518 弄'
      //   // }
      // ],
      //是否支持选中记录
      multiSelect:true,
      //本页选中记录
      multipleSelection:[],
      //其他页选中记录
      otherSelection:[],
      //所有选中记录,可能跨页
      allSelection:this.value,
      //获取数据的url
      action:this.fld.action,
      //参数:其中可能包括从open方法中传入的参数,搜索参数,分页参数
      params:{},
      //搜索关键字
      key:'',
      //记录的主键记录
      rowKey: this.fld.rowKey ? this.fld.rowKey : 'id',
      //displayKey,displayText
      //对话框是否显示
      dialogTableVisible:false,
      //分页参数
      currentPage:1,
      pagesize:20,
      total:400,//记录总数

      //远程载入次数
      loadTimes:0,
      //是否显示结果,如果存在用户自定义插槽,不显示
      showSelectionSlot:true,
      //选中结果中,用来进行回显选中结果的字段.
      displayKey:this.fld.displayKey,

    };
  },
  template: `
    <div>
      <el-button
        slot="append"
        icon="el-icon-more"
        @click="dialogTableVisible=true"
        >单击选择</el-button>
      <template v-if="showSelectionSlot">
        <el-popover
          v-if="allSelection.length>0"
          placement="right"
          width="400"
          trigger="hover">
          <el-tag
            v-for="(selection,index) in allSelection"
            :key="index"
            closable
            :disable-transitions="false"
            @close="handleRemoveTag(selection)">
            {{selection[displayKey]?selection[displayKey]:"未知"}}
          </el-tag>
          <el-button slot="reference" type="text">共选中{{allSelection.length}}条记录
            <i class="el-icon-error" type="error" @click="handleClearAll"></i>
          </el-button>
        </el-popover>
      </template>

      <!-- 选中结果回显插槽 -->
      <slot name="selectionSlot"></slot>

      <!-- dialog -->
      <el-dialog
        :title="fld.title"
        :visible.sync="dialogTableVisible"
        @open="handleOpen">

        <comp-table
          :cols="columns"
          :action="fld.action"
          ref="table"
          height="290"
          @select="handleUserSelect"
          @select-all="handleUserSelectAll"
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
              <!--<span style="float:left;margin-top:9px;" v-html="textAfterPage"></span> -->
            </div>
          </template>
        </comp-table>

        <div slot="footer" class="dialog-footer">
          <el-button type="primary" @click="handleSelect">确 定</el-button>
          <el-button @click="dialogTableVisible = false">取 消</el-button>
        </div>
      </el-dialog>
    </div>
  `,
  methods:{
    //本页选中和其他选中进行join
    _joinCurOther : function() {
      var allSelection = [];
      for(var key in this.multipleSelection) {
        allSelection.push(this.multipleSelection[key]);
      }
      for(var key in this.otherSelection) {
        allSelection.push(this.otherSelection[key]);
      }
      return allSelection;
    },
    //通过ajax请求从服务器获得数据
    _getRows : function() {
      var url = this.action;
      if(url=='' || url==undefined) {
        alert('pop-select 组件未定义action参数');
        return false;
      }
      //本页选中和其他选中进行join
      if(this.loadTimes>0) {
        this.allSelection = this._joinCurOther();
      }

      this.params.pagesize = this.pagesize;
      this.params.currentPage = this.currentPage;
      this.params.key = this.key;
      // var param= {pagesize:this.pagesize,currentPage:this.currentPage};
      var _this = this;
      this.$http.post(url, this.params)
      .then(function (response) {
        var cols = _this._formatColumns(response.data.columns);
        var rows = _this._formatRows(response.data.rows);
        _this.total = response.data.total;
        _this.$refs.table.columns = response.data.columns;
        _this.$refs.table.rows = rows;
        _this.loadTimes++;
        // dump(_this);

        //设置默认选中状态,
        //无法在mounted或者open事件中定义,因为那时rows还没有准备好,所以不能正确选择
        //使用nexttick是因为dom未渲染时,方法不会起作用.将回调延迟到下次 DOM 更新循环之后执行
        if(_this.allSelection.length>0) {
          //拆分成本页和非本页
          //1,给all加上key
          var tempAll = [];
          for(var key in _this.allSelection) {
            var item = _this.allSelection[key];
            tempAll[item.id] = item;
          }

          //2,分解
          var arrIn=[];
          var arrOut=[];
          rows.forEach((item,key)=>{
            // dump(key);
            if (tempAll.hasOwnProperty(item.id)) {
              arrIn[item.id] = item;
            }
          })
          for(var key in tempAll) {
            if (!arrIn.hasOwnProperty(key)) {
              arrOut.push(tempAll[key]);
            }
          }
          _this.multipleSelection = [];
          for(var key in arrIn) {
            _this.multipleSelection.push(arrIn[key]);
          }
          _this.otherSelection = arrOut;

          _this.$nextTick(function(){
            _this.multipleSelection.forEach((item,key)=>{
              _this.$refs.table.$refs.table.toggleRowSelection(item,true);
            });
          });
        }
      })
      .catch(function (error) {
        dump(error);
      });
    },
    //删除选中项触发
    handleRemoveTag : function(selection) {
      var index = this.allSelection.indexOf(selection);
      this.allSelection.splice(index,1);

      index = this.multipleSelection.indexOf(selection);
      if(index>-1) {
        var rowsRemoved = this.multipleSelection.splice(index,1);
        this.$refs.table.$refs.table.toggleRowSelection(rowsRemoved[0],false);
      }

      index = this.otherSelection.indexOf(selection);
      if(index>-1) {
        this.otherSelection.splice(index,1);
      }
    },
    //清除全部
    handleClearAll : function() {
      this.$confirm('确认清除所有选中记录吗？', '确认信息', {
        distinguishCancelAndClose: true,
        confirmButtonText: '确认',
        cancelButtonText: '取消',
        type:'warning',
      }).then(()=>{
        this.allSelection=[];
        this.multipleSelection=[];
        this.otherSelection=[];
      });

    },

    //用户选中某航触发
    handleUserSelect(selection,row) {
      this.multipleSelection = selection;
      //如果是取消某行选中,从allselect中删除,否则加入
      if(this.multipleSelection.indexOf(row)==-1) {
        for(var i=0;this.allSelection[i];i++) {
          if(this.allSelection[i][this.rowKey]==row[this.rowKey]) {
            this.allSelection.splice(i,1);
            break;
          }
        }
      } else {
        this.allSelection.push(row);
      }
    },
    //用户全选触发
    handleUserSelectAll(selection) {
      this.multipleSelection = selection;
    },
    //点击确认后回调
    handleSelect : function(){
      this.dialogTableVisible = false;
      this.allSelection = this._joinCurOther();
      this.$emit('input',this.allSelection);

      var key = `${this.fld.name}:select`;
      if(this.$root.callbacks[key]) {
        this.$root.callbacks[key].apply(this,[this.allSelection]);
      }
    },
  },
  mounted: function() {
    //如果存在选中结果显示插槽,才显示
    if(this.$slots.selectionSlot) this.showSelectionSlot=false;
  }
});

/*
//老版本备份
//弹出选择控件,单选,比如客户选择
//根据远程返回的搜索条件,构造搜索区域,搜索区域最好单独做个组件 todo
//display-text显示在input中的文字
Vue.component('comp-pop-select',{
  props:['fld','value'],
  data:function(){
    return {
      columns: {
        'date':{'text':'日期','width':200},
        'name':{'text':'姓名','width':200},
        'address':{'text':'地址','width':300},
      },
      rows: [{
        date: '2016-05-02',
        name: '王小虎',
        address: '上海市普陀区金沙江路 1518 弄'
      }],
      multiSelect:this.fld.multiSelect,
      //获取数据的url
      action:this.fld.action,
      //参数:其中可能包括从open方法中传入的参数,搜索参数,分页参数
      params:{},
      //搜索关键字
      searchKey:'',
      //弹窗选择列表记录中的字段名,表单提交时该字段的值会提交到后台,并作为外键进行保存
      rowKey:'id',
      //需要回显在text中的弹窗列表字段,比如客户名称
      displayKey:this.fld.displayKey,
      //作为固定值从fld中传入
      displayText:this.fld.displayText,
      //对话框是否显示
      dialogTableVisible:false,
      //分页参数
      currentPage:1,
      pagesize:20,
      total:400,//记录总数
    };
  },
  template: `
    <div>
      <el-input placeholder="点击选择"  :value="displayText" clearable @clear="handleClear" >
        <el-button slot="append" icon="el-icon-more" @click="dialogTableVisible=true"></el-button>
      </el-input>
      <input type="hidden" :name='fld.name' :value="value"></input>
      <el-dialog
        :title="fld.title"
        :visible.sync="dialogTableVisible"
        append-to-body
        @open="handleOpen" >
        <div style="width:180px;float:left;">
          <el-input placeholder="关键字"  size="mini" v-model="searchKey">
            <el-button slot="append" icon="el-icon-search" @click="handleSearch"></el-button>
          </el-input>
        </div>
        <el-table
        :data="rows"
        :row-key="rowKey"
        @row-dblclick="handleSelect"
        stripe
        height=300
        ref="tbl"
        size="mini"
        >
          <el-table-column
            v-if="multiSelect"
            type="selection"
            width="55"
          ></el-table-column>
          <el-table-column
            v-for="(col,index) in columns"
            :key="index"
            :property="index"
            :label="col.text"
            :width="col.width"
          ></el-table-column>
        </el-table>

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
      </el-dialog>
    </div>
  `,
  watch: {
    // displayText : function(val,oldVal) {
    //   this.displayText = val;
    // }
  },
  methods:{
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
      return rows;
    },
    //通过ajax请求从服务器获得数据
    _getRows : function() {
      var url = this.action;
      if(url=='' || url==undefined) {
        alert('pop-select 组件未定义action参数');
        return false;
      }
      this.params.pagesize = this.pagesize;
      this.params.currentPage = this.currentPage;
      this.params.searchKey = this.searchKey;
      // var param= {pagesize:this.pagesize,currentPage:this.currentPage};
      var _this = this;
      axios.post(url, this.params)
      .then(function (response) {
        var cols = _this._formatColumns(response.data.columns);
        var rows = _this._formatRows(response.data.rows);
        _this.total = response.data.total;
        _this.columns = response.data.columns;
        _this.rows = rows;
      })
      .catch(function (error) {
        dump(error);
      });
    },
    //设置文本框的显示
    setDisplayText : function(text) {
      this.displayText = text;
    },
    //以下事件没用了,都是在handelselect事件中处理了
    // handleModelInput :function(val){
    //   this.$emit("input", val);
    // },
    //dialog打开前的回调
    handleOpen :function(){
      var key = `${this.fld.name}:open`;
      if(this.$root.callbacks[key]) {
        this.$root.callbacks[key].apply(this,[this]);
      }
      this._getRows();
    },
    //双击回调
    handleSelect : function(row,e){
      // debugger;
      var key = this.$refs.tbl.rowKey;
      if(!row.hasOwnProperty(key)) {
        alert(`选中的记录中未包含 ${key} 字段`);
        return false;
      }
      //改变文本框中的值,文本框属于组建内的元素,这个元素的改变应该组件自己负责
      //而hidden控件绑定的是记录集中的字段,这个值的改变应该由父组件负责
      this.displayText = row[this.displayKey];

      this.dialogTableVisible = false;
      this.$emit('input',row[key]);

      var key = `${this.fld.name}:select`;
      if(this.$root.callbacks[key]) {
        this.$root.callbacks[key].apply(this,[row,e]);
      }
    },

    //页数改变时触发
    handleSizeChange :function(size) {
      this.currentPage=1;
      this._getRows();
    },
    handleCurrentChange : function(page) {
      this.currentPage=page;
      this._getRows();
    },

    //点击搜索
    handleSearch : function() {
      this.currentPage=1;
      this._getRows();
    },

    //取消已选择结果
    handleClear : function() {
      this.displayText='';
      // this.$emit('clear',`${this.fld.name}:clear`,this);
      this.$emit('input','');
    }
  },
  mounted : function() {
    if(this.fld.rowKey) {
      this.rowKey=this.fld.rowKey;
    }
  }
});

//弹出选择,多选,比如多个产品
//回显效果变化太多,不进行封装,故只做一个触发弹出的多选按钮,
Vue.component('comp-pop-multi-select',{
  props:['fld','value'],
  data:function(){
    return {
      columns: {
        'date':{'text':'日期','width':200},
        'name':{'text':'姓名','width':200},
        'address':{'text':'地址','width':300},
      },
      rows: [{
        date: '2016-05-02',
        name: '王小虎',
        address: '上海市普陀区金沙江路 1518 弄'
      }],
      //获取数据的url
      action:this.fld.action,
      //是否支持选中记录
      multiSelect:true,
      //参数:其中可能包括从open方法中传入的参数,搜索参数,分页参数
      params:{},
      //搜索关键字
      searchKey:'',
      //记录的主键记录
      rowKey: this.fld.rowKey ? this.fld.rowKey : 'id',
      //对话框是否显示
      dialogTableVisible:false,
      //分页参数
      currentPage:1,
      pagesize:20,
      total:400,//记录总数
      //本页选中记录
      multipleSelection:[],
      //其他页选中记录
      otherSelection:[],
      //所有选中记录,可能跨页
      allSelection:this.value,
      //远程载入次数
      loadTimes:0,
      //是否显示结果,如果存在用户自定义插槽,不显示
      showSelection:true,
      //选中结果中,用来进行回显选中结果的字段.
      displayKey:this.fld.displayKey,
    };
  },
  template: `
    <div>
      <el-button slot="append" icon="el-icon-more" @click="dialogTableVisible=true"></el-button>
      <template v-if="showSelection">
        <el-popover
          v-if="allSelection.length>0"
          placement="right"
          width="400"
          trigger="hover">
          <el-tag
            v-for="(selection,index) in allSelection"
            :key="index"
            closable
            :disable-transitions="false"
            @close="handleRemoveTag(selection)">
            {{selection[displayKey]?selection[displayKey]:"未知"}}
          </el-tag>
          <el-button slot="reference" type="text">共选中{{allSelection.length}}条记录</el-button>
        </el-popover>
      </template>

      <!-- 选中结果回显插槽 -->
      <slot name="selectionSlot"></slot>

      <!-- dialog -->
      <el-dialog :title="fld.title" :visible.sync="dialogTableVisible" @open="handleOpen">
        <div style="width:180px;float:left;">
          <el-input placeholder="关键字"  size="mini" v-model="searchKey">
            <el-button slot="append" icon="el-icon-search" @click="handleSearch"></el-button>
          </el-input>
        </div>
        <el-table
        :data="rows"
        :row-key="rowKey"
        stripe
        height=300
        ref="table"
        @select="handleUserSelect"
        @select-all="handleUserSelectAll"
        size="mini"
        >
          <el-table-column
            v-if="multiSelect"
            type="selection"
            width="55"
          ></el-table-column>
          <el-table-column
            v-for="(col,index) in columns"
            :key="index"
            :property="index"
            :label="col.text"
            :width="col.width"
          ></el-table-column>
        </el-table>

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

        <div slot="footer" class="dialog-footer">
          <el-button type="primary" @click="handleSelect">确 定</el-button>
          <el-button @click="dialogTableVisible = false">取 消</el-button>
        </div>
      </el-dialog>
    </div>
  `,
  methods:{
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
      return rows;
    },
    //本页选中和其他选中进行join
    //本页选中和其他选中进行join
    _joinCurOther : function() {
      var allSelection = [];
      for(var key in this.multipleSelection) {
        allSelection.push(this.multipleSelection[key]);
      }
      for(var key in this.otherSelection) {
        allSelection.push(this.otherSelection[key]);
      }
      return allSelection;
    },
    //通过ajax请求从服务器获得数据
    _getRows : function() {
      var url = this.action;
      if(url=='' || url==undefined) {
        alert('pop-select 组件未定义action参数');
        return false;
      }
      //本页选中和其他选中进行join
      if(this.loadTimes>0) {
        this.allSelection = this._joinCurOther();
      }

      this.params.pagesize = this.pagesize;
      this.params.currentPage = this.currentPage;
      this.params.searchKey = this.searchKey;
      // var param= {pagesize:this.pagesize,currentPage:this.currentPage};
      var _this = this;
      axios.post(url, this.params)
      .then(function (response) {
        var cols = _this._formatColumns(response.data.columns);
        var rows = _this._formatRows(response.data.rows);
        _this.total = response.data.total;
        _this.columns = response.data.columns;
        _this.rows = rows;
        _this.loadTimes++;

        //设置默认选中状态,
        //无法在mounted或者open事件中定义,因为那时rows还没有准备好,所以不能正确选择
        //使用nexttick是因为dom未渲染时,方法不会起作用.将回调延迟到下次 DOM 更新循环之后执行
        if(_this.allSelection.length>0) {
          //拆分成本页和非本页
          //1,给all加上key
          var tempAll = [];
          for(var key in _this.allSelection) {
            var item = _this.allSelection[key];
            tempAll[item.id] = item;
          }
          // dump('tempAll',tempAll);
          //2,分解
          var arrIn=[];
          var arrOut=[];
          _this.rows.forEach((item,key)=>{
            if (tempAll.hasOwnProperty(item.id)) {
              arrIn[item.id] = item;
            }
          })
          for(var key in tempAll) {
            if (!arrIn.hasOwnProperty(key)) {
              arrOut.push(tempAll[key]);
            }
          }
          _this.multipleSelection = [];
          for(var key in arrIn) {
            _this.multipleSelection.push(arrIn[key]);
          }
          _this.otherSelection = arrOut;


          _this.$nextTick(function(){
            _this.multipleSelection.forEach((item,key)=>{_this.$refs.table.toggleRowSelection(item,true);});
          });
        }
      })
      .catch(function (error) {
        dump(error);
      });
    },
    //删除选中项触发
    handleRemoveTag : function(selection) {
      var index = this.allSelection.indexOf(selection);
      this.allSelection.splice(index,1);

      index = this.multipleSelection.indexOf(selection);
      if(index>-1) {
        var rowsRemoved = this.multipleSelection.splice(index,1);
        this.$refs.table.toggleRowSelection(rowsRemoved[0],false);
      }

      index = this.otherSelection.indexOf(selection);
      if(index>-1) {
        this.otherSelection.splice(index,1);
      }
    },

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

    //点击搜索重新载入数据
    handleSearch : function() {
      this.currentPage=1;
      this._getRows();
    },

    //弹出显示前
    handleOpen :function(dialog){
      var key = `${this.fld.name}:open`;
      if(this.$root.callbacks[key]) {
        this.$root.callbacks[key].apply(this,[this]);
      }
      // this.$emit('open',`${this.fld.name}:open`,this);
      if(this.loadTimes==0) {
        this._getRows();
      }
    },
    handleUserSelect(selection,row) {
      this.multipleSelection = selection;
    },
    handleUserSelectAll(selection) {
      this.multipleSelection = selection;
    },
    //点击确认后回调
    handleSelect : function(){
      this.dialogTableVisible = false;
      this.allSelection = this._joinCurOther();
      this.$emit('input',this.allSelection);

      var key = `${this.fld.name}:select`;
      if(this.$root.callbacks[key]) {
        this.$root.callbacks[key].apply(this,[this.allSelection]);
      }
    },
  },
  mounted: function() {
    if(this.$slots.selectionSlot) this.showSelection=false;
    // dump(this);
  }
});
*/