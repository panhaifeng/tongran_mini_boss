<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">扫码登录</block>
		</cu-custom>

		<!-- <view class="margin-top"></view> -->
		<view class="bg-white padding-lr">
			<view class="solids-bottom padding-xs flex align-center">
				<view class="flex-sub text-center">
					<view class="solid-bottom text-xsl padding">
						<text class="cuIcon-scan text-green"></text>
					</view>
					<view class="">PC - 筒染</view>
					<view class="padding">扫描绑定小程序二维码来登录</view>
				</view>
			</view>
		</view>
		<view class="uni-btn-v uni-common-mt uni-form-button padding-xl">
			<button class="btn-submit block bg-blue cu-btn lg" type="primary" @tap="scanCode">
				开始扫码
			</button>
		</view>
	</view>
</template>

<script>
	import formatData from '@/common/formData.js';
	export default {
		data() {
			return {
				code:'',
			}
		},
		onLoad(query) {
		},
		onShow(){

		},
		onReachBottom() {

		},
		onPullDownRefresh() {

		},
		onUnload() {

		},
		destroyed() {

		},
		methods: {
			scanCode(e){
				var _this = this;
				uni.scanCode({
				    success: function (res) {
						console.log(res);
						var content = res.result;
						console.log(content);
						//如果二维码上面有url前缀，需要先去掉
						var _prev = 'http://www.eqinfo.com.cn?c=';
						if(content.indexOf(_prev)==0){
							content = content.substr(_prev.length);
						}
						
						content = JSON.parse(content) || {};
						console.log('qrcode content,',content);
						if(!content.token){
							uni.showToast({
								icon:'none',
								title:"条码不合法",
								duration:2500,
								mask:false
							});
						}else{
							var verifyRes = formatData.verifyQrcodeToken(content);
							if(verifyRes){
								//设置本地的url
								uni.setStorageSync(formatData.urlStorageKey, content.serverUrl);
								_this.code = content;
								//end
								uni.showModal({
									title: '操作提示',
									content: `确定绑定帐号${content.uname}吗？`,
									cancelText: '取消绑定',
									confirmText: '确认绑定',
									success: res => {
										if (res.confirm) {
											uni.showLoading({
												mask:true,
												title:'验证数据...',
											});
											formatData.getUserinfo(function(user){
												_this.bindUser(user);
											});											
										}
									},
									complete: function(res){										
									}
								})
								
							}else{
								uni.showToast({
									icon:'none',
									title:"条码不合法或已过期",
									duration:2500,
									mask:false
								});
							}							
						}						
				    }
				});
			},
			//远程载入数据
			//载入完成后执行后面的回调函数
			bindUser(user) {
				uni.hideLoading();
				if(!this.code){
					uni.showToast({
						icon:'none',
						title:"请先扫码",
						duration:2000,
						mask:false
					});
					return false;
				}
								
				uni.showLoading({
					mask:true,
					title:'验证数据...',
				});

				var params = {
					method:'login.bind.mp',
					code:this.code,
				};
			    formatData.set(params);
				uni.request({
					url: formatData.httpUrl(),
					data:params,
					success: (res) => {
						var result = res.data.data;
						console.log('bind result',result);
						setTimeout(function(){
							uni.showToast({
								icon:'none',
								title:result.msg,
								duration:2000,
								mask:true
							});
						},200);
						if(result.success){
							//刷新本地的缓存数据
							formatData.setUserinfoStorage(result.userinfo);
							formatData.setCurrentExhibition({});
							//跳转首页
							setTimeout(function(){
								uni.reLaunch({
									url: '/pages/index/index'
								});
							},1500);
						}						
					},
					complete(res) {
						//隐藏载入效果
						uni.hideLoading();
					},
				});
			},
		}
	}
</script>

<style>
</style>
