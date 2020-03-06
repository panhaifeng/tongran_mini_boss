<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <!-- import CSS -->
  <{webcontrol type='LoadJsCss' src="Resource/Script/layui/layui/css/layui.css"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/layui/style/admin.css"}>
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
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/components_popup_select.js"}>
  <script src="Resource/Script/vue/element/components_table_v1.1.js"></script>
  <title>主从表单New</title>
</head>
<body>
  <div class="" id="div1">
    <el-container>
      <el-main>
        <el-form
          :model="row"
          ref='mainForm'
          :rules="rules"
          @submit.native.prevent
          size="mini"
          >
          <comp-card
            :card-width-col="formWidthCol"
            :card-height="formHeight"
            :card-title="formTitle"
            >
            <template slot="body">
              <el-col
                v-for="fld,i in mainFormItems"
                :key="fld.name"
                :span="fld.span ? fld.span : ((fld.type=='comp-textarea' || fld.type=='comp-message-alert') ? 24 : 8)"
                >
                <el-form-item
                  :label="fld.title"
                  :prop="fld.name"
                  label-width="100px"
                  >
                  <component
                    :is="fld.type"
                    :fld="fld"
                    :value="row[fld.name]"
                    @input="row[fld.name]=arguments[0]"
                    :row="row"
                    :ref="fld.name"
                  ></component>
                </el-form-item>
              </el-col>

              <!-- 子表区域 -->
              <comp-table
                :data="row[sonKey]"
                :cols="columnsSon"
                border
                height="300"
                ref="sonTbl"
                @row-dblclick = "handleEditSon"
                @row-click = "handleRowClick"
                @cell-mouse-enter="handelMouseOverRow"
                @cell-mouse-leave="handelMouseLeaveRow"
                @index-header-click="handleAddSon"
                :show-summary = "true"
                >
                <!-- 操作栏的效果-下拉菜单或者平铺按钮 -->
                <template slot="rowButtonSlot" slot-scope="props">
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
                    @click="handleRowCommand({btn:btn,row:props.row,index:props.index})"
                    :title="btn.text"
                    :disabled="btn.options && props.row[btn.options.disabledColumn]"
                    ></el-button>
                  <!-- 后面的按钮以dropDown呈现 -->
                  <el-dropdown
                    size='small'
                    @command="handleRowCommand"
                    trigger="click"
                    @visible-change="showDropdown=arguments[0];showDropdownIndex=props.index;"
                    v-if="moreEditButtons.length>0">
                    <el-button type="primary" plain icon="el-icon-more" circle size="mini" style="padding:3.5px;margin-left:3px;"></el-button>
                    <el-dropdown-menu slot="dropdown">
                      <el-dropdown-item
                        v-for="(btn,index) in moreEditButtons"
                        :key="index"
                        :command="{btn:btn,row:props.row,index:props.index}"
                        :disabled="btn.options && props.row[btn.options.disabledColumn]"
                        >{{btn.text}}</el-dropdown-item>
                    </el-dropdown-menu>
                  </el-dropdown>
                </template>
            </template>
            <template slot="footer">
              <el-button type="primary" @click="submitForm('mainForm')" :loading="disableSubmit">确 定</el-button>
              <el-button @click="resetForm('mainForm')">重 置</el-button>
            </template>
          </comp-card>
        </el-form>
      </el-main>
    </el-container>

    <!-- 明细信息编辑弹窗 -->
    <el-dialog title="明细编辑" :visible.sync="dialogSonVisible"
      @open="handleOpen"
      @close="handleClose"
      ref="dialogSon">
      <el-form :model="rowSon" size="mini">
        <el-form-item
          v-for="(item,index) in sonFormItems"
          :key="index"
          :label="item.title"
          label-width="100px"
          >
          <!-- @displaytext-change事件为comp-popup-select控件独有事件 用来改变子表中displaytext代表的字段 -->
          <component
            :is="item.type"
            :fld="item"
            :value="rowSon[item.name]"
            @input="rowSon[item.name]=arguments[0]"
            @displaytext-change="setRowSon(item.textKey||item.displayKey,arguments[0])"
            @displaytext-set="setDisplaytext(arguments[0],rowSon[item.textKey||item.displayKey])"
            @change="changeSelect"
            @changeimg="changeImg"
            :ref="item.name"
          ></component>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <!-- <el-button type="primary" @click="handleSonOk">确 定</el-button> -->

        <el-button-group>
          <el-button type="plain" icon="el-icon-arrow-left" @click="handlePre">上一条</el-button>
          <el-button type="plain" icon="el-icon-arrow-right" @click="handleNext">下一条</el-button>
          <el-button @click="dialogSonVisible = false" icon="el-icon-close">关闭</el-button>
        </el-button-group>
      </div>
    </el-dialog>

    <!-- 为其他操作预留的组件位置 -->
    <!-- 比如某个订单需要在弹窗中设置备注信息 -->
    <!-- 比如在弹窗中设置某个客户的联系人等 -->
    <componet v-for="(comp,index) in otherComps" key="index" :is="comp.type" :ref="comp.name" v-bind="comp"></componet>
  </div>
</body>


<script>
  var title='页面标题';
  var row = <{$row|@json_encode}>;
  var mainFormItems = <{$mainFormItems|@json_encode}>;
  var sonFormItems = <{$sonFormItems|@json_encode}>;
  var rules = <{$rules|@json_encode}>;
  var sonKey = <{$sonKey|@json_encode}>;
  var action = "<{$action}>";
  var callbacks = [];
// sontpl加载，可以加载多个，不需要<script>

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
    data: function() {
      //对验证规则进行预处理,字符串改为函数
      for (var k in rules) {
        rules[k].forEach((rule,index)=>{
          if(!rule.validator) return;
          // console.log('validator found',rule.validator);
          if(!callbacks.hasOwnProperty(rule.validator)) {
            alert(`sontpl中未对callbacks[${rule.validator}]进行定义`);
            return;
          }
          rule.validator = callbacks[rule.validator];
        });
      }

      //对数据集进行预处理
      mainFormItems.forEach(function(item,i){
        //如果自动完成控件,数据集中必须存在对应字段,否则控件有问题,其实可以改成Vue.set实现,to do
        if(!row.hasOwnProperty(item.name)) {
          if(item.type=='comp-checkbox-group' ||
            item.type=='comp-pop-multi-select' ||
            item.type=='comp-image' ||
            item.type=='comp-file' ||
            item.type=='comp-checkbox-group' ||
            (item.type=='comp-select' && item.multiple)
            ) {
            row[item.name] = [];
          } else {
            row[item.name] = '';
          }
        }
      });
      //对字表数据集进行预处理
      if(row[sonKey]) {
        for(var i=0;row[sonKey][i];i++) {
          var _row = row[sonKey][i];
          for(var k in sonFormItems) {
            var item = sonFormItems[k];
            //如果自动完成控件,数据集中必须存在对应字段,否则控件有问题,其实可以改成Vue.set实现,to do
            if(!_row.hasOwnProperty(item.name)) {
              if(item.type=='comp-checkbox-group' ||
                item.type=='comp-pop-multi-select' ||
                item.type=='comp-image' ||
                item.type=='comp-file' ||
                item.type=='comp-checkbox-group' ||
                (item.type=='comp-select' && item.multiple)
                ) {
                _row[item.name] = [];
              } else {
                _row[item.name] = '';
              }
            }
          };
        }

      }
      dump(row);

      var formTitle = <{$formTitle|@json_encode}>||"";

      //子表记录的操作按钮组
      var sonButtons = <{$sonButtons|@json_encode}>||[
        {text:'删除',funcName:'handleDelete'},
        {text:'复制',funcName:'handleCopy'},
      ];

      //将 editButtons 分解成两个部分,前3个一组默认显示,后面的以dropdown呈现
      var defaultEditButtons = [];
      var moreEditButtons = [];
      sonButtons.forEach((item,i)=>{
        if(i<3) defaultEditButtons.push(item);
        else {
          moreEditButtons.push(item);
        }
      });

      //保证子表的操作按钮列的宽度大于130
      var columnsSon = <{$columnsSon|@json_encode}>||{};
      for(var k in columnsSon) {
        if(columnsSon[k].showButton) {
          columnsSon[k].width = columnsSon[k].width>130 ? columnsSon[k].width : 130;
        }
      }
      return {
        'formWidthCol':'',
        'formHeight':'500',
        'formTitle':formTitle,
        'mainFormItems':mainFormItems,
        'sonFormItems':sonFormItems,
        'row':row,
        //记录集中代表子表记录的字段名
        'sonKey':sonKey,
        //子表记录编辑表单是否可见
        'dialogSonVisible':false,
        //子表列
        'columnsSon':columnsSon,
        //子表弹窗绑定的记录集,
        'rowSon':this.getEmptyRowSon(sonFormItems),
        //当前编辑的子表记录的index
        'indexSon':-1,
        //验证规则
        'rules':rules,
        //提交action
        'action':action,
        //提交按钮的可点击状态
        'disableSubmit':false,
        //所有表单元素的自定义事件的回调集合
        'callbacks':callbacks,
        //子记录列表中除了修改删除外的按钮
        'otherButtons':sonButtons,
        //dropdown-men是否显示,dropdown显示时,不隐藏菜单
        'showDropdown':false,
        //当前显示dropdown的是第几行
        'showDropdownIndex':-1,
        //前三个默认显示的操作按钮
        'defaultEditButtons':defaultEditButtons,
        //从第4个开始后面的操作按钮
        'moreEditButtons':moreEditButtons,
        //每行功能按钮的可用图标
        'defaultEditButtonsIcons':['el-icon-edit','el-icon-delete','el-icon-edit-outline'],
        //其他组件,
        'otherComps':[],
        //是否显示子表记录列表的上移和下移按钮
        'showUpDown':true,
        //是否隐藏新增按钮
        'hideButtonAddSon':<{$hideButtonAddSon|@json_encode}>||false,
      }
    },
    methods : {
      //comp-image改变时触发,添加图片成功或者删除图片成功
      //改变子表记录中代表图片张数的字段
      changeImg : function(fileList,obj){
        var key = obj.fld.name;
        var col = this.columnsSon[key];
        if(!col.displayKey) return;
        if(this.sonFormItems[key].type!='comp-image') return;
        var row = this.row[this.sonKey][this.indexSon];
        row[col.displayKey] = fileList.length+'张';
        return;
      },
      //select控件改变时触发
      //如果子表表单中存在select控件,
      //比如:客户选择控件,需要在子表列中显示的是compName字段,但是选择控件中的value对应的是id字段
      //需要在这里根据displayKey来进行动态处理
      //已经改为总线处理机制了，建议取消,等待观察
      changeSelect : function(val,obj) {
        return;
        /*var key = obj.fld.name;
        var col = this.columnsSon[key];
        if(!col.displayKey) return;
        if(this.sonFormItems[key].type!='comp-select') return;

        var options = this.sonFormItems[key].options;
        var text = '';
        for(var i=0;options[i];i++) {
          if(options[i].value==val) {
            text = options[i].text;
          }
        }
        var row = this.row[this.sonKey][this.indexSon];
        //改变对象中关联字段的值为text
        row[col.displayKey] = text;
        return;*/
      },
      //调试用方法
      test : function(rule, value, callback) {
        console.log(arguments);
        // return callback(new Error('年龄不能为空'));
      },

      //当弹出选择控件,选中某个记录后,子表记录中非绑定字段改变:比如选中某个客户后,子表记录中的compName字段需要改变
      setRowSon : function(key,text) {
        // rowSon[item.textKey||item.displayKey]=arguments[0]
        this.rowSon[key] = text;
      },
      //改变PopupSelect组件中的displayText的值
      setDisplaytext : function(compPopupSelect,text) {
        // dump(arguments);
        compPopupSelect.displayText = text;
      },
      //上移
      handleMoveup : function(row,index) {
        if(index==0) return;
        this.row[this.sonKey].splice(index,1);
        this.row[this.sonKey].splice(index-1,0,row);
      },
      //下移
      handleMovedown : function(row,index) {
        var len = this.row[this.sonKey].length;
        if(index==len-1) return;
        this.row[this.sonKey].splice(index,1);
        this.row[this.sonKey].splice(index+1,0,row);
      },
      //行点击时,隐藏其他行的dropdown,然后显示当前行的dropdow
      handleRowClick : function(row, event, column) {
        if(!this.showDropdown) return;
        //隐藏之前显示的dropdown-menu
        if(this.row[this.sonKey][this.showDropdownIndex]) {
          this.row[this.sonKey][this.showDropdownIndex]['__showButton'] = false;
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
        if(this.row[this.sonKey][this.showDropdownIndex]) {
          this.row[this.sonKey][this.showDropdownIndex]['__showButton'] = false;
        }
        // dump(this,this.showDropdownIndex);
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
      handleRowCommand({btn,row,index}) {
        //自定义组件
        if(btn.type=='comp') {
          var options = btn.options;
          if(!options.name) {
            console.warn(`自定义组件${options.type}配置项目中未发现name属性`);
            return;
          }
          //定义组件的option
          var opt = {
            type : options.type,
            name : options.name,
          };
          //注册组件
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

          //调用组件中的某个方法,
          //比如显示弹窗,设置弹窗的绑定记录为当前行
          //等dom元素创建后才能得到组件实例
          this.$nextTick(()=>{
            var comp = this.$refs[options.name][0];
            //检查comp的row属性是否存在
            if(comp.row==undefined) {
              consol.warn(`自定义组件${options.type}中未发现row属性`);
              return;
            }
            comp[options.onclickButton](row);
          });
          return;
        }
        //非自定义组件
        //定义每个typ默认的handle
        var arrMap = {
          edit:{funcName:'handleEditSon'},
          remove:{funcName:'handleDelete'},
          copy:{funcName:'handleCopy'},
          func:{funcName:false},
          comp:{funcName:false},
        };
        btn.options = btn.options || {funcName:arrMap[btn.type].funcName};
        btn.options.funcName = btn.options.funcName || arrMap[btn.type].funcName;
        var funcName = btn.options.funcName;
        // dump(btn,row,index);return;
        // dump(funcName,row,index);
        if(typeof(this[funcName])=='function') {
          this[funcName].apply(this,[row,index]);
          return;
        }
        //其他处理,比如弹窗交互或者其他ajax调用,可考虑采用slot或者其他
        // var key = `${btn.name}:click`;
        if(!this.callbacks[funcName]) {
          console.error('后台未指定editbutton.funcName');
          return;
        }
        //回调函数调用.将当前记录作为参数传入
        this.callbacks[funcName].apply(this,[row,index]);
      },
      //复制
      handleCopy : function(row,index) {
        var r = JSON.parse(JSON.stringify(row));
        //删除id属性,避免重复id出现
        delete r.id
        this.row[this.sonKey].splice(index+1,0,r);
      },
      //增加子表记录
      handleAddSon : function() {
        var   len = this.row[this.sonKey].length;
        this.row[this.sonKey].push({});
        this.rowSon=this.row[this.sonKey][len];

        for(var key in this.sonFormItems) {
          var temp = '';
          var item = this.sonFormItems[key];
          if(item.type=='comp-checkbox-group' ||
            item.type=='comp-pop-multi-select' ||
            item.type=='comp-image' ||
            item.type=='comp-file' ||
            item.type=='comp-checkbox-group' ||
            (item.type=='comp-select' && item.multiple)
            ) {
            temp = [];
          }
          dump(key);
          Vue.set(this.rowSon,key,temp);
        }

        this.indexSon = this.row[this.sonKey].length - 1;
        this.dialogSonVisible=true;
      },
      //插入空白行
      handleAppendRow : function(index) {
        //在指定位置插入一行
        this.row[this.sonKey].splice(index,0,{});
        this.rowSon=this.row[this.sonKey][index];
        for(var key in this.sonFormItems) {
          Vue.set(this.rowSon,key,'');
        }
        this.indexSon = index;
        this.dialogSonVisible=true;
      },
      //除了修改删除按钮之外的按钮点击事件
      handleOtherClick : function(funcName,row,index) {
        if(!this.callbacks[funcName]) {
          this.$message('模版变量otherButtons.funcName未设置');
          return;
        }
        this.callbacks[funcName].apply(this,[row,index]);
      },
      //得到空的子表记录
      getEmptyRowSon(formItems) {
        //将rowSon构造成空对象,保证每个字段都存在
        var rowSon = {};
        for(var k in formItems) {
          var item = formItems[k];
          rowSon[item.name]='';
          //如果是弹出选择控件,还要构造displayKey字段
          if(item.type=='comp-pop-select') {
            rowSon[`${item.name}-${item.displayKey}`]='';
          }
          // if(item.type=='comp-checkbox-group') {
          //   rowSon[item.name]=[];
          // }
        }
        return rowSon;
      },
      //子表记录的显示渲染接口
      sonColFormatter : function(row, column, cellValue, index) {
        console.log(column);
        return cellValue;
      },
      //子表编辑弹窗打开时回调
      handleOpen : function() {
        var curRow = this.row[this.sonKey][this.indexSon];
        //更改弹窗中特殊控件的属性
        //comp-img组件更改filelist属性,
        //这类组件比较特殊,无法在子组件中进行属性的更改,会导致重复渲染的问题
        this.$nextTick(()=>{
          for (var k in this.sonFormItems) {
            var item = this.sonFormItems[k];
            if(item.type=='comp-image') {
              this.$refs[item.name][0].setFileList(curRow[item.name]);
            }
          }
        });

        //这里可加入用户自定义的dialogSon:open回调函数,
        if(this.callbacks['dialogSon:open']) {
          if(!curRow) curRow={};
          this.callbacks['dialogSon:open'].apply(this,[this.rowSon,curRow]);
        }
      },
      //子表编辑弹窗关闭时回调
      handleClose : function() {
        //这里可加入用户自定义的dialogSon:open回调函数,
        if(this.callbacks['dialogSon:close']) {
          var curRow = this.row[this.sonKey][this.indexSon];
          if(!curRow) curRow={};
          this.callbacks['dialogSon:close'].apply(this,[this.rowSon,curRow]);
        }
      },
      //子记录弹窗点击上一条
      handlePre: function() {
        if(this.indexSon==0) {
          alert('已经是第一条');
          return;
        }
        this.indexSon--;
        this.rowSon = this.row[this.sonKey][this.indexSon];
        if(this.callbacks['dialogSon:handlePre']) {
          this.callbacks['dialogSon:handlePre'].apply(this,[this.rowSon]);
        }
      },
      //子记录弹窗点击下一条
      handleNext: function() {
        var len = this.row[this.sonKey].length;
        if(this.indexSon==len-1) {
          if(this.hideButtonAddSon) {
            alert('已经是最后一条');
            return;
          } else {
            //如果显示新增按钮,提示创建,
            this.$confirm('已经是最后一条,点击"确认",新增一条记录!').then(_=>{
              this.row[this.sonKey].push({});
              this.rowSon=this.row[this.sonKey][len];

              for(var key in this.sonFormItems) {
                Vue.set(this.rowSon,key,'');
              }

              this.indexSon = this.row[this.sonKey].length-1;
              return ;
            }).catch(_=>{
              return;
            });
          }
          return ;
        }

        //下一条
        this.indexSon++
        this.rowSon = this.row[this.sonKey][this.indexSon];

        if(this.callbacks['dialogSon:handleNext']) {
          this.callbacks['dialogSon:handleNext'].apply(this,[this.rowSon]);
        }
      },
      //表格的编辑按钮点击
      handleEditSon : function(row,index) {
        //双击时没有传入index,需要遍历得到index
        console.log(row)
        if(typeof(index)!='number') {
          this.row[this.sonKey].every((r,i)=>{
            if(r.id==row.id) {
              index=i;
              return false;//终止循环,注意必须是every才会终止
            }
            return true;
          });
        }
        this.indexSon = index;
        this.rowSon = row;
        this.dialogSonVisible=true;
      },

      //表格的删除按钮点击事件
      handleDelete : function(row,index) {
        //阻止事件冒泡
        this.$confirm('确认删除吗?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          //ajax从后台删除记录,todo
          var url=<{$urlRemoveSon|json_encode}>;
          if(url==null) {
            console.error('必须定义模版变量urlRemoveSon');
            return;
          }
          var params = {id:row.id};
          // dump(this.rowSon)
          this.$http.post(url,params)
          .then((res)=>{
            if(!res.data.success) {
              this.$notify.error({
                'title':'保存失败',
                'message':res.data.msg ? res.data.msg : 'response.data.success not found'
              });
              dump(res);
              return false;
            }
            this.$notify.success({
              title:'成功',
              message:res.data.msg,
            });
            //从数据集中删除当前记录
            this.row[this.sonKey].splice(index,1);
          }).catch(function (error) {
            dump(error);
          });

        }).catch(() => {
          return false;
        });
        // console.log(arguments);
      },

      checkSon : function(son) {
        if(son.length==0) {
          this.$message('子表记录必须不为空','error');
          return false;
        }
        //这里可加入用户自定义的beforeSubmit回调函数,
        //一般这里处理子表记录的验证规则
        if(this.callbacks['beforeSubmit']) {
          return this.callbacks['beforeSubmit'].apply(this,[this.row]);
        }
        return true;
      },
      //表单提交事件
      submitForm(formName) {
        this.$refs[formName].validate((valid) => {
          if (!valid) {
            console.log('error submit!!');
            return false;
          }

          //检查子表记录
          if(!this.checkSon(this.row[this.sonKey])) {
            return;
          }
          this.disableSubmit = true;
          console.log('form submit fired,params',this.row);
          this.$http.post(this.action,this.row).then((res)=>{

            setTimeout(() => {
              this.disableSubmit = false;
            }, 2000);

            if(!res.data.success) {
              // this.$message('response.data.success not found','error');
              this.$notify.error({
                'title':'保存失败',
                'message':res.data.msg ? res.data.msg : 'response.data.success not found'
              });
              console.log(res);
              return false;
            }
            this.disableSubmit = false;
            this.$notify.success({
              title:'成功',
              message:res.data.msg,
            });
            // this.$message(message:res.data.msg,'success');

            if(res.data.targetUrl) {
              window.location.href = res.data.targetUrl;
            }

          }).catch(function (error) {
            console.log(error);
            // this.$message('请求失败,检查url或者其他设置','error');
            this.$notify.error({
              'title':'请求失败',
              'message':'请求失败,检查url或者其他设置'
            });

            this.disableSubmit = false;
          });
        });
      },
      resetForm(formName) {
        this.$refs[formName].resetFields();
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
      },
      //设置每行功能按钮的图标
      setEditButtonsIcon : function(arr) {
        this.defaultEditButtonsIcons = arr;
      }
    },
    watch :{
      //不建议直接监听this.row,
      //直接监听rowson效率更高点,缺点就是需要单独维护rowson
      'rowSon' :{
        handler: function (newVal,oldVal) {
          var key = '__sonWatcher';
          if(!this.$root.callbacks[key]) return;
          this.$root.callbacks[key].apply(this,[newVal,oldVal]);
          // console.log('row.products changed:',this)
        },
        deep: true,
        //该回调将会在侦听开始之后被立即调用
      }
    },
    mounted: function(){
      //隐藏表格边线
      this.$refs.sonTbl.border=false;
      // this.$refs.traderId[0].fld.options=[];
      //设置是否显示操作列
      this.$refs.sonTbl.showEditColumn=true;
      //显示合计行
      // this.$refs.sonTbl.showSummary = true;
      //不显示操作栏文字,显示按钮
      this.$refs.sonTbl.showHeaderText = false;
      //设置显示序号列
      this.$refs.sonTbl.showIndex = true;
      this.$refs.sonTbl.showHeaderIndexButton = !this.hideButtonAddSon;

      //设置编辑按钮的默认图标,图标在后台直接定义,这里不重复写了.
      // if(this.callbacks['EditButtonsIcon']) {
      //   var icons = typeof(this.callbacks['EditButtonsIcon'])=="function" ? this.callbacks['EditButtonsIcon']() : this.callbacks['EditButtonsIcon'];
      //   this.setEditButtonsIcon(icons);
      // }
      //设置编辑栏宽度
      // this.$refs.sonTbl.widthEditColumn = <{$widthEditColumn|@json_encode}>||'';
    },
    created : function() {
      //定义总线事件
      //字表记录弹窗中如果存在comp-select控件，需要改变当前字表记录的某个字段的值，用来回显子表记录
      //key是需要改变的字段名
      //opts是选中的options
      this.$bus.$on('selectChanged', (key,opts) => {
        if(!this.dialogSonVisible) return;
        if(!key) return;
        if(!this.columnsSon[key].displayKey) return;
        var t = [];
        for(var i=0;opts[i];i++) {
          t.push(opts[i].text);
        }
        t = t.join(',');
        // debugger;
        // this.rowSon[this.columnsSon[key].displayKey] = t;
        // var row = this.row[this.sonKey][this.indexSon];
        this.rowSon[this.columnsSon[key].displayKey] = t;
      });
    }
  });
</script>

</html>