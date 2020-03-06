<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    .el-dialog__body {
        padding: 0 10px 40px 10px  !important;
    }
  </style>
  <!-- import CSS -->
  <{webcontrol type='LoadJsCss' src="Resource/Script/layui/layui/css/layui.css"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/layui/style/admin.css"}>
  <link rel="stylesheet" href="Resource/Script/vue/element/index.css">
  <script src="Resource/Script/vue/vue.js"></script>
  <script src="Resource/Script/vue/element/index.js"></script>
  <script src="Resource/Script/vue/element/axios.min.js"></script>
  <script src="Resource/Script/vue/element/components.js?v=1"></script>
  <script src="Resource/Script/vue/element/components_card.js"></script>
  <script src="Resource/Script/vue/element/components_table.js"></script>
  <script src="Resource/Script/vue/element/components_popup_select.js"></script>
</head>
<body>
  <div class="" id="div1">
    <el-form
      :model="row"
      ref='ruleForm'
      :rules="rules"
      @submit.native.prevent
      >
      <comp-card
        card-width-col="8"
        card-height="100%"
        :card-title="formTitle"
        >
        <template slot="body">
          <el-form-item
            v-for="item,i in formItems"
            :key="item.name"
            :label="item.title"
            :prop="item.name"
            label-width="100px"
            >
            <component
              :is="item.type"
              :fld="item"
              :value="row[item.name]"
              @input="row[item.name]=arguments[0]"
              :ref="item.name"
              >
              <!-- 以下插件对多选控件有效,用来显示多选结果,如果不定义,则显示多选控件的默认效果popover效果 -->
              <!--
              <template slot="selectionSlot">
                <span>bbbbb</span>
              </template>
              -->
            </component>
          </el-form-item>
        </template>
        <template slot="footer">          
          <el-button type="primary" @click="submitForm('ruleForm')" :disabled="disableSubmit">立即创建</el-button>
          <el-button @click="resetForm('ruleForm')">重置</el-button>          
        </template>

      </comp-card>
    </el-form>
  </div>
</body>


<script>
  var title='编辑';
  var row = <{$row|@json_encode}>;
  var formItems = <{$formItems|@json_encode}>;
  var rules = <{$rules|@json_encode}>;
  var action = "<{$action}>";

  //以下代码在sontpl中实现
  {
    var callbacks=[];
    callbacks['compCode:change'] = function(val) {
      this.$notify.success("客户编码改变事件触发");
      // console.log("this,arguments",this,arguments);
    }
    callbacks['compName:change'] = function(val) {
      this.$notify.success("客户编码改变事件触发");
      // console.log("this,arguments",this,arguments);
    }
    callbacks['people:select'] = function(val) {
      this.$notify.warning("联系人选中,onselect事件触发");
      // console.log("this,arguments",this,arguments);
    }
    callbacks['createDate:change'] = function(val) {
      this.$notify.warning("创建日期改变事件触发");
      // console.log("this,arguments",this,arguments);
    }
    callbacks['vDate:change'] = function(val) {
      this.$notify.warning("日期范围改变事件触发");
      // console.log("this,arguments",this,arguments);
    }
    callbacks['compFrom:change'] = function(val) {
      dump(val);
      this.$notify.warning("客户来源改变触发");
      // console.log("this,arguments",this,arguments);
    }
    callbacks['traderId:change'] = function(val) {
      this.$notify.warning("联系人改变事件触发");
      // console.log("this,arguments",this,arguments);
    }
    callbacks['isStop:change'] = function(val) {
      this.$notify.warning("联系人改变事件触发");
      console.log("this,arguments",arguments);
    }
    callbacks['associateClientId:open'] = function(dialog) {
      this.$notify.warning("弹出选择上家客户,open事件触发,增加参数");
      //可以在 dialog.action 中加入 get参数,
      //dialog.action += '&key=aaa';
      //也可以直接在dialog.params中加入参数
      //dialog.params.key='bbb';
      console.log("this,arguments",arguments[0]);
    }
    callbacks['associateClientId:select'] = function(row,e) {
      this.$notify.warning("弹出选择上家客户,选中回调事件触发");
      console.log("this,arguments",arguments);
    }
    //弹出多选回调事件,
    //rows:所有选中的记录集
    callbacks['xiajia:select'] = function(rows) {
      this.$notify.warning(`弹出选择下家客户,选中回调事件触发,共选中${rows.length}条记录`);
      console.log("this,arguments",arguments);
    }
    callbacks['associateClientId1:open'] = function(rows) {
      this.$notify.warning(`associateClientId1:open fired`);
      
    }
    callbacks['associateClientId1:select'] = function(rows) {
      this.$notify.warning(`associateClientId1:select fired`);
      
    }
    callbacks['pic:remove'] = function(file, fileList) {
      this.$notify.warning("图片删除触发");
      // console.log("this,arguments",arguments);
    }
    callbacks['pic:success'] = function(response, file, fileList) {
      this.$notify.warning("图片上传成功触发");
      // console.log("this,arguments",arguments);
    }
    callbacks['file:remove'] = function(file, fileList) {
      this.$notify.warning("文件删除触发");
      // console.log("this,arguments",arguments);
    }
    callbacks['file:success'] = function(response, file, fileList) {
      this.$notify.warning("文件上传成功触发");
      // console.log("this,arguments",arguments);
    }
  }
  

  var app = new Vue({
    el: '#div1',
    data: function() {
      //对数据集进行预处理,避免数据集中的值不符合组件要求
      formItems.forEach(function(item,i){
        //如果自动完成控件,数据集中必须存在对应字段,否则控件有问题
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
        //表单标题
        formTitle : '客户编辑',
        //提交按钮是否禁用
        disableSubmit : false,
        //表单项
        formItems:formItems,
        //数据集
        row:row,
        //表单验证
        rules:rules,
        //提交数据action
        action:action,
        //回调接口集合
        callbacks:callbacks,//所有表单元素的自定义事件的回调集合
      }
    },
    methods : {
      //表单提交事件
      submitForm(formName) {
        this.$refs[formName].validate((valid) => {
          if (!valid) {
            console.log('表单验证失败!!');
            return false;
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
      //表单重置
      resetForm(formName) {
        this.$refs[formName].resetFields();
      }
    },
    mounted : function() {

    }
  });



</script>

</html>