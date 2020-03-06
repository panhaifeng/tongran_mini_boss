<!DOCTYPE html>
<html>
<title><{$title}></title>
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
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/index.css"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/vue.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/index.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/axios.min.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/components.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/components_card.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/components_table.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/components_popup_select.js"}>
</head>
<body>
  <div class="" id="div1">
    <el-container>
        <el-main>
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
                <el-button type="primary" @click="submitForm('ruleForm')" :disabled="disableSubmit"><{$form.submit.text|default:'提交'}></el-button>
                <el-button @click="resetForm('ruleForm')"><{$form.reset.text|default:'重置'}></el-button>
              </template>

            </comp-card>
          </el-form>
        </el-main>
      </el-container>
  </div>
</body>


<script>
  var title='<{$title|default:"编辑"}>';
  var row = <{$row|@json_encode}>;
  var formItems = <{$formItems|@json_encode}>;
  var rules = <{$rules|@json_encode}>;
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
        formTitle : title,
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

            setTimeout(() => {
              this.disableSubmit = false;
            }, 2000);

            if(!res.data.success) {
              this.$notify.error({
                'title':'失败',
                'message':res.data.msg ? res.data.msg : 'response.data.success not found'
              });
              // this.$message('response.data.success not found','error');
              console.log(res);
              return false;
            }
            this.$notify.success({
              title:'成功',
              message:res.data.msg,
            });
            // this.$message(res.data.msg,'success');
            if(res.data.targetUrl) {
              window.location.href = res.data.targetUrl;
            }
          }).catch(function (error) {
            this.$notify.error({
              'title':'请求失败',
              'message':'请求失败,检查url或者其他设置'
            });
            // this.$message('请求失败,检查url或者其他设置','error');

            this.disableSubmit = false;
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