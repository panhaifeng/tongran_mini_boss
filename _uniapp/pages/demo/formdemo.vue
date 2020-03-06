<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">表单页面创建</block>
		</cu-custom>
		<view class="uni-padding-wrap">
			<form @submit="formSubmit">
				<block v-for="(item,index) in formItems" :key="index">
					<view v-if="item.marginTop" class="margin-top"></view>
					<e7-comp
						:type="item.type"
						:title="item.title"
						:name="item.name"
						:clearable="item.clearable"
						:fld="item"
						v-model="formData[item.name]"
					></e7-comp>
				</block>
				<view class="margin-top"></view>
				<view class="uni-btn-v uni-common-mt uni-form-button">
					<button class="btn-submit" formType="submit" type="primary">Submit</button>
					<button type="default" formType="reset">Reset</button>
				</view>
			</form>
		</view>
	</view>
</template>
<script>
	import e7Comp from "@/components/e7-comp.vue";
	import formatData from '../../common/formData.js';
	import graceChecker from '../../common/graceChecker.js';

	export default {
		components: {e7Comp},
		data() {
			return {
				formData:{},
				formItems:[],
				rules:[],
			}
		},
		methods: {
			handleInput: function(v) {
				// this.
			},
			formSubmit: function (e) {
				console.log('submit data =>',this.formData);

				//进行表单检查
				var formData = this.formData;
				var checkRes = graceChecker.check(formData, this.rules);
				if(checkRes){
					uni.showToast({title:"验证通过!", icon:"none"});
				}else{
					uni.showToast({ title: graceChecker.error, icon: "none" });
				}
			},
			//获取缓存数据
			getStoragePage: function(key){
				if(!key) return false;
				var params = uni.getStorageSync('uni.page.build.'+key);
				if(params){
					params = JSON.parse(params);
					this.formItems = params.formItems;
					this.rules = params.rules;
				}
			},
			setStoragePage: function(params ,p){
				if(!p) return false;
				uni.setStorage({
				    key: 'uni.page.build.'+p,
				    data: JSON.stringify({formItems:params.formItems ,rules:params.rules}),
				    success: function () {
				        // console.log('success');
				    }
				});
			},
			getNewPageParams: function(query){
				var that = this;
				var params = {
					method:'uni.page.build',
					pageId : query.p || ''
				};
				formatData.set(params);
				uni.request({
					url: formatData.httpUrl(), //仅为示例，并非真实接口地址。
					data:params,
					success: (res) => {
						var result = res.data.data;
						// console.log('page-params:',result);
						that.formItems = result.formItems;
						that.rules = result.rules;
						that.formData = result.formData || {};
						//放入缓存中，如果没有网络的情况加载下
						if(result && query.p){
							that.setStoragePage(result ,query.p);
						}
					},
					fail: (res) => {
						that.getStoragePage(query.p);
					}
				});
			}
		},
		onLoad : function(query) {
			// console.log('query',query);
			var _this = this;
			//可以切换p=clientPage查看效果
			uni.getNetworkType({
			    success: function (res) {
			        console.log('networkType' ,res.networkType);
					if(res.networkType == 'none'){
						uni.showToast({
							title:"当前无网络,无法操作",
							icon:"none",
							mask:true,
							duration:2000,
							complete:function(){
								// setTimeout(function(){
								// 	uni.navigateBack({
								// 		delta: 1
								// 	});
								// },2000)
							}
						});

						//没有网络的情况下使用缓存页面配置
						_this.getStoragePage(query.p);
					}else{
						_this.getNewPageParams(query);
					}
			    }
			});
		}
	}
</script>

<style>
	/* .uni-form-button{display: flex;flex-direction: row;justify-content:space-between;}
	.uni- */form-button button{width: 350upx;}
</style>
