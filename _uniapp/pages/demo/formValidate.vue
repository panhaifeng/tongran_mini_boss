<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">表单验证</block>
		</cu-custom>
		<view class="uni-padding-wrap">
			<form @submit="formSubmit" @reset="formReset">
				<!-- 普通文本框 支持v-model-->
				<view class="cu-form-group  margin-top">
					<view class="title">姓名</view>
					<input placeholder="两字短标题" v-model="formData.name"></input>
					<text class='cuIcon-locationfill text-orange'></text>
				</view>
				<view class="cu-form-group  margin-top">
					<view class="title">电话</view>
					<input placeholder="只能输入数字"  type="number" v-model="formData.tel"></input>
				</view>
				
				
				
				<view class="uni-btn-v uni-common-mt">
					<button class="btn-submit" formType="submit" type="primary">Submit</button>
					<button type="default" formType="reset">Reset</button>
				</view>
			</form>
			<text></text>详细请参考：http://grace.hcoder.net/doc/info/73-3.html
		</view>
	</view>
</template>
<script>
	//来自 graceUI 的表单验证， 使用说明见手册 http://grace.hcoder.net/doc/info/73-3.html
	var  graceChecker = require("../../common/graceChecker.js");
	export default {
		data() {
			return {
				formData:{
					name :'曾峰',
					tel: '13602330921'
				},	
			}
		},
		methods: {
			formSubmit: function (e) {
				//将下列代码加入到对应的检查位置
				//定义表单规则
				var rule = [
					{name:"name", checkType : "string", checkRule:"1,3",  errorMsg:"姓名应为1-3个字符"},
					{name:"tel", checkType : "phoneno", checkRule:"",  errorMsg:"电话格式错误"},
					// {name:"loves", checkType : "notnull", checkRule:"",  errorMsg:"请选择爱好"}
				];
				//进行表单检查
				var formData = this.formData;
				var checkRes = graceChecker.check(formData, rule);
				if(checkRes){
					uni.showToast({title:"验证通过!", icon:"none"});
				}else{
					uni.showToast({ title: graceChecker.error, icon: "none" });
				}
			},
			formReset: function (e) {
				console.log("清空数据")
				this.chosen = ''
			}
		}
	}
</script>

<style>
</style>
