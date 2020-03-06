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
  </style>
  <script src="Resource/Script/vue/vue.js"></script>
  <script src="Resource/Script/vue/element/index.js"></script>
  <script src="Resource/Script/vue/element/axios.min.js"></script>
  <script src="Resource/Script/vue/element/components.js?v=1"></script>
  <script src="Resource/Script/vue/element/components_card.js"></script>
  <script src="Resource/Script/vue/element/components_popup_select.js"></script>
  <script src="Resource/Script/vue/element/components_table.js"></script>
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
                :span="fld.type=='comp-textarea'?24:8"
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
                    :ref="fld.name"
                  ></component>
                </el-form-item>
              </el-col>

              <!-- 子表区域 -->
              <!-- 
              鼠标移上显示操作按钮
              固定操作列
              弹出显示编辑窗口
              编辑窗口中弹出选择产品
               -->
              <!-- <el-form-item prop="Products"></el-form-item> -->
              <el-table
                :data="row[sonKey]"
                stripe
                style="width:100%"
                border
                height="300"
                size="mini"
                width="100%"
                ref="sonTbl"
                @cell-mouse-enter="handelMouseOverRow"
                @cell-mouse-leave="handelMouseLeaveRow"
                @row-click = "handleEditSon" show-summary
                :summary-method="handleSummaries"    
                >
                
                <el-table-column
                  v-for="(col,key) in columnsSon"
                  v-if="col.text"
                  :key="key"
                  :prop="key"
                  :label="col.text"
                  :sum="true"
                  :width="col.width">
                </el-table-column>
                <el-table-column
                  fixed="left"
                  label=""
                  width="100">
                  <template slot="header" slot-scope="scope">
                    <el-button plain size='mini' type='primary' icon="el-icon-plus" @click="handleAddSon">新增</el-button>
                  </template>

                  <template slot-scope="scope" v-if="scope.row.__showButton">
                    <el-button type="primary" icon="el-icon-edit" circle
                      size="mini"
                      @click.stop="handleEditSon(scope.row,scope.$index)"></el-button>
                    <el-button type="danger" icon="el-icon-delete" circle
                      size="mini"
                      @click.stop="handleDelete(scope.row,scope.$index)"></el-button>
                  </template>
                </el-table-column>
              </el-table>
            </template>
            <template slot="footer">
              <el-button type="primary" @click="submitForm('mainForm')" :disabled="disableSubmit">确 定</el-button>
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
          <!-- @dispalytext-change事件为comp-popup-select控件独有事件 用来改变子表中displaytext代表的字段 -->
          <component
            :is="item.type"
            :fld="item"
            :value="rowSon[item.name]"
            @input="rowSon[item.name]=arguments[0]"
            @dispalytext-change="rowSon[item.textKey||item.displayKey]=arguments[0]"
            :ref="item.name"
          ></component>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <!-- <el-button type="primary" @click="handleSonOk">确 定</el-button> -->
        <el-button @click="dialogSonVisible = false">关 闭</el-button>
      </div>
    </el-dialog>
  </div>
</body>


<script>
  var title='页面标题';
  var row = <{$row|@json_encode}>;
  var mainFormItems = <{$mainFormItems|@json_encode}>;
  var sonFormItems = <{$sonFormItems|@json_encode}>;
  var rules = <{$rules|@json_encode}>;
  var action = "<{$action}>";
  //回调函数稽核
  var callbacks = [];

  //以下代码在sontpl中写
  callbacks['isHanshuiFormatter'] = function(row, column, cellValue, index) {
    var ret = cellValue==true?'是':'否';
    //html不支持,待完善
    // ret = "<el-tag type='primary'>"+ret+"</el-tag>";
    return ret;
  }
  //自定义验证函数,订单编号中必须包含a
  callbacks['checkOrderCode'] = function(rule, value, callback) {
    // console.log(this,rule,value);
    if(value.indexOf('a')==-1) {
      //抛出错误
      return callback(new Error('订单编号中必须包含a'));
    }
    //正常返回
    return callback();
  }
  callbacks['checkProducts'] = function(rule, value, callback) {
    return callback(new Error('asdfasdf'));
    //抛出错误
    //   return callback(new Error('订单编号中必须包含a'));
    //正常返回
    // return callback();
  }
  //表单提交前验证接口
  callbacks['beforeSubmit'] = function(row) {    
    //如果错误,return false;
    this.$message('在callbacks["beforeSubmit"]中定义的错误信息','error');return false;    
    return true;
  }
  //子表记录编辑弹窗open时触发
  //row为当前记录
  callbacks['dialogSon:open'] = function(row) {
    //设置产品和产品1的displayText
    this.$nextTick(()=>{
      this.$refs.productId[0].displayText = row.proName||'';
      this.$refs.productId1[0].displayText = row.proName1||'';
    });
  }
  //子表记录编辑弹窗open时触发
  //rowSon为当前窗口编辑的子表记录
  //row为修改对象
  callbacks['dialogSon:close'] = function(rowSon,row) {
    row.proName = this.$refs.productId[0].displayText;
    row.proName1 = this.$refs.productId1[0].displayText;
  }

  
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
            item.type=='comp-file'
            ) {
            row[item.name] = [];
          } else {
            row[item.name] = '';
          }
        }
      });

      return {
        'formWidthCol':'',
        'formHeight':'500',
        'formTitle':'订单编辑',
        'mainFormItems':mainFormItems,
        'sonFormItems':sonFormItems,
        'row':row,
        //记录集中代表子表记录的字段名
        'sonKey':<{$sonKey|@json_encode}>,
        //子表记录编辑表单是否可见
        'dialogSonVisible':false,
        //子表列
        'columnsSon':<{$columnsSon|@json_encode}>,
        //子表数据渲染函数
        'columnsFormatter':<{$columnsFormatter|@json_encode}>||[],
        //子表弹窗绑定的记录集,
        'rowSon':this.getEmptyRowSon(sonFormItems),      
        //当前编辑的子表记录的index
        'indexSon':-1,
        'rules':rules,
        'action':action,
        'disableSubmit':false,
        'callbacks':callbacks,//所有表单元素的自定义事件的回调集合
      }
    },
    methods : {
      //调试用方法
      test : function(rule, value, callback) {
        console.log(arguments);
        return callback(new Error('年龄不能为空'));
      },
      //合计方法自定义
      handleSummaries : function(param) {
        const { columns, data } = param;
        const sums = [];
        columns.forEach((column, index) => {
          if (index === 0) {
            sums[index] = '总价';
            return;
          }
          const values = data.map(item => Number(item[column.property]));
          if (!values.every(value => isNaN(value))) {
            sums[index] = values.reduce((prev, curr) => {
              const value = Number(curr);
              if (!isNaN(value)) {
                return prev + curr;
              } else {
                return prev;
              }
            }, 0);
            sums[index] += ' 元';
          } else {
            sums[index] = 'N/A';
          }
        });
        dump(columns);
        return sums;
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
          
        }
        // console.log('getEmptyRowSon',rowSon);
        return rowSon;
      },
      //子表记录的显示渲染接口
      sonColFormatter : function(row, column, cellValue, index) {
        console.log(column);
        return cellValue;
      },
      //鼠标移上显示操作按钮
      handelMouseOverRow : function(row, column, cell, event) {
        if(!row.hasOwnProperty('__showButton')) {
          Vue.set(row,'__showButton',true);
        } else {
          row.__showButton = true;
        }
      },
      //鼠标移出隐藏操作按钮
      handelMouseLeaveRow : function(row, column, cell, event) {
        row.__showButton = false;
      },
      //子表编辑弹窗打开时回调
      handleOpen : function() {
        //这里可加入用户自定义的dialogSon:open回调函数,
        if(this.callbacks['dialogSon:open']) {
          var curRow = this.row[this.sonKey][this.indexSon];
          if(!curRow) curRow={};
          this.callbacks['dialogSon:open'].apply(this,[curRow]);
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
      //表格的编辑按钮点击
      handleEditSon : function(row,index) {
        //双击时没有传入index,需要遍历得到index
        if(typeof(index)!='number') {
          this.row[this.sonKey].every((r,i)=>{
            if(r.id==row.id) {
              index=i;
              return false;//终止循环,注意必须是every才会终止
            }
            return true;
          });
        }
        // console.log('continue');
        this.indexSon = index;
        // for(var key in this.sonFormItems) {
        //   this.rowSon[key] = row[key];
        // }

        //对象拷贝,避免弹窗未确认时改动生效
        // this.rowSon = JSON.parse(JSON.stringify(row));  
        this.rowSon = row;  
        
        //如果子表单项中有弹出选择控件,将弹出选择控件的displayText设置为rowSon[displayKey]
        //需要将回调延迟到下次 DOM 更新循环之后执行,否则会找不到this.$refs[item.name]
        // this.$nextTick(()=>{
        //   for(var k in this.sonFormItems) {          
        //     var item = this.sonFormItems[k];
        //     if(item.type!='comp-pop-select') continue;            
        //     this.$refs[item.name][0].setDisplayText(this.rowSon[item.displayKey]);
        //   }
        // });
        
        this.dialogSonVisible=true;
      },
      //增加子表记录
      handleAddSon : function() {
        for(var key in this.sonFormItems) {
          Vue.set(this.rowSon,key,'');          
        }

        this.indexSon = this.row[this.sonKey].length;
        // //设置弹出选择控件的displayText为空
        // this.$nextTick(()=>{
        //   for(var k in this.sonFormItems) {          
        //     var item = this.sonFormItems[k];
        //     if(item.type!='comp-pop-select') continue;            
        //     this.$refs[item.name][0].setDisplayText('');
        //   }
        // });
        // this.$message('fuck','success');
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

          //从数据集中删除当前记录
          this.row[this.sonKey].splice(index,1);
          // console.log(this.row.Products);
          this.$message('删除成功');
        }).catch(() => {
          return false;         
        });
        // console.log(arguments);
      },
      //子表记录编辑弹窗点击确认按钮
      // handleSonOk : function() {
      //   //如果包含comp-pop-select控件,将displayText赋值给某个字段
      //   for(var k in this.sonFormItems) {          
      //     var item = this.sonFormItems[k];
      //     if(item.type!='comp-pop-select') continue;   
      //     this.rowSon[item.displayKey] = this.$refs[item.name][0].DisplayText;
      //   }
      //   //改变子表对应记录
      //   if(this.indexSon==this.row[this.sonKey].length) {
      //     this.row[this.sonKey].push(this.rowSon);
      //   } else {
      //     this.row[this.sonKey][this.indexSon] = this.rowSon;        
      //   }
      //   this.dialogSonVisible = false;
      // },

      checkSon : function(son) {
        if(son.length==0) {
          this.$message('子表记录必须不为空','error');
          return false;
        }
        //这里可加入用户自定义的beforeSubmit回调函数,
        if(this.callbacks['beforeSubmit']) {
          return this.callbacks['beforeSubmit'].apply(this,[this.row]);
        }
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
            if(!res.data.success) {
              this.$notify.error({
                'title':'保存失败',
                'message':'response.data.success not found'
              });
              console.log(res);
              return false;
            }
            this.$notify.success({
              title:'成功',
              message:res.data.msg,
            });
          }).catch(function (error) {
            console.log(error);
            this.$notify.error({
              'title':'请求失败',
              'message':'请求失败,检查url或者其他设置'
            });
          });
        });
      },
      resetForm(formName) {
        this.$refs[formName].resetFields();
      }
    },
    mounted: function(){
      //处理子表带有formatter的列
      var cols = this.$refs.sonTbl.columns;
      cols.forEach((col,i)=>{
        var key = col.property;
        if(!this.columnsFormatter[key]) {
          return;
        }
        var funcName = this.columnsFormatter[key];
        if(!this.callbacks[funcName]) {
          return;
        }
        col.formatter = (row, column, cellValue, index)=> {
          return this.callbacks[funcName](row, column, cellValue, index);
        }
      });
    }
  });
</script>

</html>