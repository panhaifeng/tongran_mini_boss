<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">表单</block>
		</cu-custom>
		<form @submit="submitForm">
			<view class="cu-form-group margin-top">
				<!-- <view class="title">用户名</view> -->
				<text class='cuIcon-my text-orange'></text>
				<input placeholder="用户名(英文|数字)" name="userName" style="margin-left: 20upx;"></input>
			</view>
			<view class="cu-form-group margin-top">
				<!-- <view class="title">密  码</view> -->
				<text class='cuIcon-unlock text-orange'></text>
				<input type='password' placeholder="密码(英文,数字)" name="passwd" style="margin-left: 20upx;"></input>
			</view>

			<view class="padding flex flex-direction">
				<button class="cu-btn bg-blue margin-tb-sm lg" form-type="submit">登 录</button>
				<!-- <button plain class="cu-btn bg-grey lg" form-type="reset">重 置</button> -->
			</view>
		</form>
	</view>
</template>

<script>
	import formatData from '../../common/formData.js';
	export default {
		data() {
			return {

			}
		},
		methods: {
			submitForm(e) {
				console.log("UserInfo in localstorage :",uni.getStorageSync('UserInfo'));

				var detail = e.detail.value;
				console.log('form data:',detail);
				// var serverUrl = formatData.httpUrl();
				var me = this;
				var params = detail;
				params['version'] = this.version;
				params['token'] = this.token;
				params['method'] = 'uni.login';
				formatData.set(params);
				uni.request({
					//https://www.fastmock.site/mock/5ac037a8ecc6af666419e1e746d2172f/uniapp/login
					url: formatData.httpUrl(), //仅为示例，并非真实接口地址。
					data:params,
					success: (res) => {
						var result = res.data;
						console.log(12333);
						console.log(result);
						if(result.data.status!=200) {
							uni.showToast({
								icon:'none',
								title:"服务器出错",
								duration:1500,
								mask:false
							});
							return false;
						}
						if(!result.data.success) {
							uni.showToast({
								title:result.data.msg,
								icon:"none",
								duration:2000,
							});
							// uni.showToast({
							//
							// })
							return false;
						}
						//登录成功,用户信息可以保存在localstorage中，也可以保存在vuex中
						var userInfo = result.data.userInfo;
						console.log('登录成功,userInfo:',userInfo);
						//1保存在本地缓存
						uni.setStorageSync("UserInfo",userInfo);

						//2保存在store中，使用了vuex技术
						// this.login(userInfo);
						// console.log('验证通过,用户信息',userInfo);

						// console.log("ok");
						// uni.navigateBack();
						uni.reLaunch({
							url:"../../pages/index/index"
						})
					}
				});
			}
		}
	}
</script>

<style>

</style>
