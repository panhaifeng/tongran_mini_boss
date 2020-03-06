<!DOCTYPE html>
<html>
<title><{$title}></title>
<head>
  <meta charset="UTF-8">
  <{webcontrol type='LoadJsCss' src="Resource/Script/layui/layui/css/layui.css"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/layui/style/admin.css"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/index.css"}>
  <style>
    body{min-width: 700px;}
    .el-dialog__body {
        padding: 0 10px 40px 10px  !important;
    }
    .el-button.layui-a-tips.el-button--primary{color: #fff;line-height: 0.5;margin-top: 5px;}
    .layui-card-body{min-height: 300px;overflow:auto;}
    .layui-card{min-width: 200px;margin-top: 2px;margin-right: 2px;}
    .layui-card-tree{min-width: 260px;}
    .layui-card-header .layui-a-tips.clear-tree{right: 130px;color: #888;}
    .el-radio.is-bordered{width: 49%;margin: 3px 0 0 3px;border-radius:0;}
    .el-radio.is-bordered+.el-radio.is-bordered{margin-left: 3px;}
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
  </style>
  <!-- import CSS -->
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/vue.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/index.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/axios.min.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/components.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/components_card.js"}>
</head>
<body>
  <div class="" id="div1">
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
    <el-container>
        <el-main>
          <el-form
            :model="authData"
            ref='ruleForm'
            :rules="rules"
            @submit.native.prevent
            >
              <el-row>
                <el-col :span="8">
                  <el-col :span="22">
                    <div class="layui-card">
                      <div class="layui-card-header">
                        选择角色
                      </div>
                      <div ref="cardBody1" class="layui-card-body">
                        <!-- 不使用rules验证 -->
                        <!-- <el-form-item label="" prop="roleData"> -->
                          <el-radio v-for="(role ,key) in rowRole" @change="handleRoleChange" v-model="authData.roleData" :label="role.id" border>{{role.roleName}}</el-radio>
                        <!-- </el-form-item> -->
                      </div>
                    </div>
                  </el-col>
                </el-col>
                <el-col :span="10">
                  <el-col :span="20">
                    <div class="layui-card layui-card-tree">
                      <div class="layui-card-header">
                        选择功能

                        <a class="layui-a-tips clear-tree" href='javascript:;' @click="resetChecked">清空</a>
                        <el-button class="layui-a-tips" type="primary" @click="submitForm('ruleForm')" :disabled="disableSubmit"><{$form.submit.text|default:'设置权限'}></el-button>
                      </div>
                      <div ref="cardBody2" class="layui-card-body">
                          <el-tree
                            :data="treeData"
                            show-checkbox
                            node-key="id"
                            ref="tree"
                            highlight-current
                            :props="defaultProps"
                            :default-checked-keys="[]"
                            @check-change="handleCheckChange"
                            >
                          </el-tree>
                      </div>
                    </div>
                  </el-col>
                </el-col>
                <el-col :span="2" style="height: 300px;min-width: 100px;">
                  <el-steps direction="vertical" :active="1">
                    <el-step title="步骤 1" description="选择角色"></el-step>
                    <el-step title="步骤 2" description="选择功能"></el-step>
                    <el-step title="步骤 3" description="设置权限"></el-step>
                  </el-steps>
                </el-col>
              </el-row>
            </el-form>
        </el-main>
      </el-container>
  </div>
</body>


<script>
  var title='<{$title|default:"设置权限"}>';
  var rules = {
    'roleData':[{ 'required': true, 'message': '请先选择角色'}]
  };
  var treeData = <{$treeData}>;
  var rowRole = <{$rowRole}>;
  var action = "<{$action}>";
  var actionGetRole = "<{$actionGetRole}>";
  var callbacks = [];

  var app = new Vue({
    el: '#div1',
    data: function() {
      return {
        //表单标题
        formTitle : title,
        //提交按钮是否禁用
        disableSubmit : false,
        showLoading : true,
        //表单验证
        rules:rules,
        treeData:treeData,
        rowRole:rowRole,
        authData:{'roleData':'','treeData':[]},
        //提交数据action
        action:action,
        actionGetRole:actionGetRole,
        //回调接口集合
        callbacks:callbacks,//所有表单元素的自定义事件的回调集合
        defaultProps: {
          children: 'children',
          label: 'text'
        }
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
          console.log('form submit fired,params',this.authData);
          this.$http.post(this.action,this.authData).then((res)=>{

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
      //当选择角色改变时
      handleRoleChange(){
        this.resetChecked();
        // console.log(this.authData);
        this.$http.post(this.actionGetRole,this.authData).then((res)=>{
            if(res.data.success) {
              //重新赋值
              this.authData.treeData = res.data.treeData;
              console.log(this.authData);
              this.setCheckedKeys();//让选择框打勾

              return false;
            }
            this.$notify.error({
              'title':'失败',
              'message':res.data.msg ? res.data.msg : 'response.data.success not found'
            });

          }).catch(function (error) {
            this.$notify.error({
              'title':'请求失败',
              'message':'请求失败,检查url或者其他设置'
            });
        });
      },
      //表单重置
      resetForm(formName) {
        this.$refs[formName].resetFields();
      },
      //清空选择
      resetChecked() {
        this.$refs.tree.setCheckedKeys([]);
        this.authData.treeData = [];
      },
      //按照key设置选择
      setCheckedKeys() {
        this.$refs.tree.setCheckedKeys(this.authData.treeData);
      },
      //当选择tree值改变时
      handleCheckChange(data, checked, indeterminate) {
        this.authData.treeData = this.$refs.tree.getCheckedKeys();
        // console.log(data, checked, indeterminate);
        // console.log('authData is :',this.authData);
        // console.log('treeData is :',this.authData);
      }
    },
    mounted : function() {
      var bodyHeight = document.documentElement.clientHeight || document.body.clientHeight;
      bodyHeight = bodyHeight - 120;
      this.$refs.cardBody1.style.cssText = "max-height:"+bodyHeight+'px;min-height:'+bodyHeight+'px;';
      this.$refs.cardBody2.style.cssText = "max-height:"+bodyHeight+'px;min-height:'+bodyHeight+'px;';

      this.showLoading = false;
    }
  });
</script>
</html>