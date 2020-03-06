<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{formTitle}}</block>
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
						:display-text="item.displayKey ? formData[item.displayKey] : ''"
						v-model="formData[item.name]"
					></e7-comp>
				</block>
				<view class="margin-top"></view>
				<view v-if="pageType!='detial'" class="uni-btn-v uni-common-mt uni-form-button padding-xl">
					<button class="btn-submit block bg-blue cu-btn lg" formType="submit" :disabled="disabledSubmit" type="primary">
						<text v-if="disabledSubmit" class="cuIcon-loading2 cuIconfont-spin"></text> 提交
					</button>
					<!-- <button type="default" class="cu-btn block bg-white lg margin-tb-sm" formType="reset">重置</button> -->
				</view>
			</form>
		</view>

		<view v-if="loadingPage" class="flex pull-screen">
			<view class="bg-white flex-sub radius shadow-lg">
				<image src="/static/rhomb-white.gif" mode="aspectFit" class="gif-white response" style="height:240upx"></image>
			</view>
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
				formTitle:'表单编辑',
				rules:[],
				submitMethod:'',
				disabledSubmit:false,
				pageKey:'',
				pageType:'',
				loadingPage:true,
			}
		},
		methods: {
			handleInput: function(v) {
				// this.
			},
			formSubmit: function (e) {
				this.disabledSubmit = true;
				var that = this;

				if(!that.submitMethod){
					this.disabledSubmit = false;
					uni.showToast({title:"缺少服务器地址", icon:"none"});
					return false;
				}

				//进行表单检查
				var formData = this.formData;
				var checkRes = graceChecker.check(formData, this.rules);
				if(checkRes){
					// uni.showToast({title:"验证通过!", icon:"none"});
				}else{
					this.disabledSubmit = false;
					uni.showToast({ title: graceChecker.error, icon: "none" });
					return false;
				}

				//提交表单
				var params = {
					method:that.submitMethod,
					formData:JSON.stringify(formData)
				};
				console.log('formData',formData);
				formatData.set(params);
				uni.request({
					url: formatData.httpUrl(), //仅为示例，并非真实接口地址。
					data:params,
					method:'POST',
					header: {
					  'content-type': 'application/x-www-form-urlencoded'
					},
					success: (res) => {
						var result = res.data.data;
						console.log('response:',result);
						// uni.showToast({title:result.msg || '操作成功', icon:"none"});
						if(result.success){
							uni.showLoading({
							    title: result.msg || '操作成功',
								mask:true,
							});
							setTimeout(function(){
								uni.hideLoading();
								uni.navigateBack({
									delta: 1
								});
							},500);
						}else{
							uni.showToast({title:result.msg||'提交状态未知', icon:"none",duration:3000});
							that.disabledSubmit = false;
						}

						// uni.showModal({
						//     title: result.msg || '操作成功',
						//     content: "操作完成，点击确认返回首页",
						//     success: function (res) {
						//         if (res.confirm) {
						//             uni.redirectTo({
						// 				url: './form?p='+that.pageKey
						// 			});
						//         } else if (res.cancel) {
						//             uni.navigateBack({
						//                 delta: 1
						//             });
						//         }
						//     }
						// });
					},
					fail: (res) => {
						console.log('fail:');
						uni.showToast({title:res.errMsg, icon:"none"});
					},
					complete: (res) => {
						setTimeout(function(){
							that.disabledSubmit = false;
						},200)
					}
				});
			},
			//获取缓存数据
			getStoragePage: function(key){
				if(!key) return false;
				var that = this;
				var params = uni.getStorageSync('page.build.common.form.'+key);
				if(params){
					params = JSON.parse(params);
					this.formItems = params.formItems;
					this.rules = params.rules;
					this.submitMethod = params.submitMethod;
					this.formTitle = params.formTitle;

					that.loadingPage = false;
				}
			},
			setStoragePage: function(params ,p){
				var that = this;
				if(!p) return false;
				uni.setStorage({
				    key: 'page.build.common.form.'+p,
				    data: JSON.stringify({formItems:params.formItems ,rules:params.rules ,submitMethod:params.submitMethod,formTitle:params.title}),
				    success: function () {
				        // console.log('success');

				    }
				});
			},
			getNewPageParams: function(query){
				var that = this;
				var params = {
					method:'uni.page.build',
					pageId : query.p || '',
					id:query.id||'',
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
						that.submitMethod = result.submitMethod;
						that.formData = result.formData || {};
						that.formTitle = result.title || '表单编辑';
						//放入缓存中，如果没有网络的情况加载下
						if(result && query.p){
							that.setStoragePage(result ,query.p);
							that.loadingPage = false;
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
			this.pageKey = query.p;
			var _this = this;
			_this.pageType = query.type || '';
			//可以切换p=clientPage查看效果
			uni.getNetworkType({
			    success: function (res) {
			        console.log('networkType' ,res.networkType);
					if(res.networkType == 'none'){
						uni.showToast({
							title:"当前无网络",
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
			
			//隐藏分享
			// #ifdef MP
			uni.hideShareMenu();
			// #endif
		}
	}
</script>

<style>
	/* .uni-form-button{display: flex;flex-direction: row;justify-content:space-between;}
	.uni- */form-button button{width: 350upx;}
	.pull-screen{width: 100%;height: 100%;position: fixed;top: 0;left: 0;}
	.pull-screen .gif-white{margin-top: 45%;}
</style>
