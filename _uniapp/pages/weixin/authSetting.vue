<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">您还未登录</block>			
		</cu-custom>
		<view class="padding-xs flex align-center">
			<view class="flex-sub text-center">
				<view class="text-sm padding">
					<open-data class="cu-avatar xl round margin-left" type="userAvatarUrl"></open-data>
				</view>
				<view class="padding"><open-data class="text-center" type="userNickName"></open-data></view>
			</view>
		</view>
		<view class="uni-loadmore text-gray text-center text-sm solid-bottom padding" >请您登录后再继续操作</view>
		<view class="cu-item " >
			<view class="content padding-left-lg">
				<text class="text-black padding-left-xs">该程序将获取以下授权</text>
			</view>
			<view class="action padding-left-xl padding-top">
				<text class="text-grey text-sm padding-left-xs">·获得您的公开信息（昵称、头像等）</text>
			</view>
		</view>	
		<view class="uni-btn-v uni-common-mt uni-form-button padding-xl">
			<button type="primary" open-type="getUserInfo" @getuserinfo="openSetting">立即登录</button>
			<button type="default" @tap="back">拒绝</button>
			<!-- <button open-type="openSetting" @opensetting="open">打开设置页</button> -->
		</view>
		
	</view>
</template>

<script>
	import formatData from '@/common/formData.js';
	export default {
		data() {
			return {
				
			}
		},
		created() {
			console.log('load setting');
			// this.isSetting();
		},
		methods: {
			back:function(){
				uni.navigateBack({
					delta: 1
				});
			},
			open: function(res){
				console.log(res);
			},
			openSetting: function(res){
				var _this = this;
				console.log(res);
				_this.isSetting();
			},
			isSetting: function(){
				var _this = this;
				uni.getSetting({
				    success(res) {
						console.log(res.authSetting);
						if(res.authSetting['scope.userInfo']){
							//获取本地的用户信息
							formatData.getUserinfo(function(user){
								if(!user.userId){
									uni.reLaunch({
										url: '/pages/login/bind'
									});
								}
								if(user.openid){
									//跳转首页
									setTimeout(function(){
										uni.reLaunch({
											url: '/pages/index/index'
										});
									},1500);
								}
							});
						}
				    }
				})
			}
		}
	}
</script>

<style>

</style>
