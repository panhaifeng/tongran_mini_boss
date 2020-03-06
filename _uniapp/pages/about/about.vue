<template name='about'>
	<view>
		<!-- <cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">关于</block>
		</cu-custom> -->
		<view class="header padding" v-bind:class="{'bg-gradual-blue':hasLogin}">
			<view class="flex-sub text-center" v-if="!hasLogin">
				<view class=" text-xl padding" >
					<view class=" text-xsl padding">
						<text class=" cuIcon-people text-black"></text>
					</view>
					<text class="text-black text-bold">请登录</text>
				</view>
			</view>
			<view class="flex-sub text-center " v-else>
				<view class="flex-sub text-center">
					<view class=" text-xsl padding">
						<text class=" cuIcon-emoji text-white"></text>
					</view>
					<text class="text-white text-bold">{{userInfo.realName || "已登录"}}</text>
					<view v-if="userInfo.compName" class="padding text-white text-bold">{{userInfo.compName}}</view>
				</view>
			</view>
		</view>
		<view class="cu-list menu" :class="[menuBorder?'sm-border':'',menuCard?'card-menu margin-top':'']">
			<!-- <view class="cu-item margin-top" :class="menuArrow?'arrow':''">
				<view class="content">
					<text class="cuIcon-circlefill text-grey"></text>
					<text class="text-grey">系统设置</text>
				</view>
			</view> -->
			<!-- <view class="cu-item" :class="menuArrow?'arrow':''" @click="clearCache">
				<view class="content" >
					<text class="cuIcon-circlefill text-grey"></text>
					<text class="text-grey">清除缓存</text>
				</view>
			</view> -->
			<view class="cu-item margin-top" :class="menuArrow?'arrow':''">
				<navigator class="content" hover-class="none" url="../../pages/about/contactus">
					<text class="cuIcon-discoverfill text-orange"></text>
					<text class="text-grey">联系我们</text>
				</navigator>
			</view>			
			<!-- <view class="cu-item" :class="menuArrow?'arrow':''">
				<view class="content">
					<text class="cuIcon-warn text-green"></text>
					<text class="text-grey">版本更新</text>
				</view>
				<view class="action">
					<text class="text-grey text-sm">易管宝V1.0</text>
				</view>
			</view> -->
			
		</view>
		<view class="padding"></view>
		<view class="padding flex flex-direction">
			<button v-if="hasLogin" plain class="cu-btn bg-grey lg" fotextrm-type="reset" @click="logout">注销并解除绑定</button>
			<button v-else plain class="cu-btn bg-grey lg" fotextrm-type="reset" @click="login">登 录</button>
		</view>
	</view>
</template>

<script>
	import formatData from '@/common/formData.js';
	export default {
		data() {
			return {
				menuArrow:true,
				menuBorder: false,
				menuCard: false,				
				hasLogin:false,
				userInfo:{},
			}
		},
		onShow(){
			
		},
		created() {
			
		},
		beforeMount(){
			var _this = this;
			formatData.getUserinfo(function(user){
				// console.log('about user' ,user);
				if(user.userId > 0){
					_this.hasLogin = true;
					_this.userInfo = user;
					// #ifdef MP-WEIXIN
					//到表里验证下当前openid是否存在，如果不存在，则删掉登录信息
					var params = {
						method:'userinfo.openid.check',
					};
					formatData.set(params);
					uni.request({
						url: formatData.httpUrl(), //仅为示例，并非真实接口地址。
						data:params,
						success: (res) => {
							if(res.data.data.userId != user.userId){								
								_this.hasLogin = false;
								_this.userInfo = {};
								formatData.setUserinfoStorage({});
								uni.setStorageSync(formatData.urlStorageKey, '');
							}
						}
					});			
					// #endif
					
				}else{
					_this.hasLogin = false;
					_this.userInfo = {};
				}
			});
		},
		mounted() {
			
		},	
		onShareAppMessage(res) {
		    // if (res.from === 'button') {// 来自页面内分享按钮
		    //   console.log(res.target)
		    // }
		    return {
		      title: '易奇展会',
		      path: '/pages/index/index'
		    }
		},
		methods: {
			//清理缓存
			clearCache() {
								
			},			
			logout() {
				var _this = this;
				// console.log('注销前userInfo',this.userinfo);
				uni.showModal({
				    title: '注销用户',
				    content: "确认退出并解除绑定吗？",
				    success: function (res) {
				        if (res.confirm) {
				            //定制轮播图的时候用
				            var params = {
				            	method:'login.logout',
				            };
				            formatData.set(params);
				            uni.request({
				            	url: formatData.httpUrl(), //仅为示例，并非真实接口地址。
				            	data:params,
				            	success: (res) => {
				            		var result = res.data.data;
				            		if(result.success){							
				            			//到服务器上把对应的信息解绑
				            			_this.hasLogin = false;
				            			_this.userInfo = {};
				            			// formatData.setUserinfoStorage({'userId':0});
										// uni.setStorageSync(formatData.urlStorageKey, '');
										uni.clearStorageSync();
				            			uni.showToast({icon:'none',title:"解除绑定成功",duration:2500,mask:true});							
				            		}
				            	}
				            });
				        }
				    }
				});
			},
			login() {
				uni.navigateTo({
					url:"../../pages/login/bind"
				})
			}
			
		}
	}
</script>

<style>
	.header {
		padding: 120upx 30upx 40upx 30upx;
		background-color: #ffd655;
		display: flex;
		flex-direction: row;
		justify-content: flex-start;
		height: 500upx;
	}
</style>
