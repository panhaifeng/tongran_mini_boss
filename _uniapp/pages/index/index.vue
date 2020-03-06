<template>
	<view>
		<home v-if="PageCur=='home'"></home>
		<!-- <basics v-if="PageCur=='basics'"></basics>
		<components v-if="PageCur=='component'"></components>
		<plugin v-if="PageCur=='plugin'"></plugin>-->
		<about v-if="PageCur=='about'"></about>
		<!-- app不支持动态组件 -->
		<!-- <component :is="mapPage[PageCur]"></component> -->
		<view class="cu-bar tabbar bg-white shadow foot">
			<view class="action" @click="NavChange" data-cur="home">
				<view class='cuIcon-cu-image'>
					<image :src="'/static/tabbar/shouye' + [PageCur=='home'?'_cur':''] + '.png'"></image>
				</view>
				<view :class="PageCur=='home'?'text-blue':'text-gray'">首页</view>
			</view>
			<!-- <view class="action" @click="NavChange" data-cur="basics">
				<view class='cuIcon-cu-image'>
					<image :src="'/static/tabbar/basics' + [PageCur=='basics'?'_cur':''] + '.png'"></image>
				</view>
				<view :class="PageCur=='basics'?'text-blue':'text-gray'">报表</view>
			</view>
			<view class="action" @click="NavChange" data-cur="component">
				<view class='cuIcon-cu-image'>
					<image :src="'/static/tabbar/component' + [PageCur == 'component'?'_cur':''] + '.png'"></image>
				</view>
				<view :class="PageCur=='component'?'text-blue':'text-gray'">扫一扫</view>
			</view>
			<view class="action" @click="NavChange" data-cur="plugin">
				<view class='cuIcon-cu-image'>
					<image :src="'/static/tabbar/plugin' + [PageCur == 'plugin'?'_cur':''] + '.png'"></image>
				</view>
				<view :class="PageCur=='plugin'?'text-blue':'text-gray'">消息</view>
			</view> -->
			<view class="action" @click="NavChange" data-cur="about">
				<view class='cuIcon-cu-image'>
					<image :src="'/static/tabbar/user' + [PageCur == 'about'?'_cur':''] + '.png'"></image>
				</view>
				<view :class="PageCur=='about'?'text-blue':'text-gray'">我的</view>
			</view>
		</view>
		
		<view class="content">
			<maskView v-if="showMask"></maskView>
		</view>
	</view>
</template>

<script>
	import maskView from '@/pages/component/mask.vue';
	import formatData from '@/common/formData.js';
	export default {		
		data() {
			return {
				PageCur: 'home',
				mapPage:{
					'home':'home',
					// 'basics':'basics',
					// 'component':'components',
					// 'plugin':'plugin',
					'about':'about',
				},
				vioQs:false,
				showMask:false,
			}
		},
		components: {maskView},
		methods: {
			NavChange: function(e) {
				this.PageCur = e.currentTarget.dataset.cur
			},
		},
		onLoad(query) {	
			// console.log('onload index');
			var _this = this;
			if(query.page){
				this.PageCur = query.page;
			}
			
			if(!this.vioQs){
				// this.$vPrint.close();
				setTimeout(() =>{
					//#ifdef APP-PLUS
				    this.$vPrint.init();
					//#endif
					this.vioQs = true;
				},3000);
			}
			//获取权限信息
			// console.log('index onload call');
			// formatData.getUserinfo();
		},
		beforeMount(){
			
		},
		mounted(){
			
		},
		destroyed() {
			//#ifdef APP-PLUS
			if(this.vioQs){
				this.$vPrint.close();
			}
			//#endif
		},
		onBackPress() {			
			let self = this;
			if (this.showMask) {
				this.showMask = false;
				return true;
			} else {
				//#ifdef APP-PLUS
				uni.showModal({
					title: '提示',
					content: '是否退出？',
					success: function(res) {
						if (res.confirm) {
							self.$vPrint.close();
							plus.runtime.quit();// 退出当前应用，该方法只在App中生效
						} else if (res.cancel) {
							console.log('用户点击取消');
						}
					},
				});
				//#endif
				return true;
			}
		},
		onShareAppMessage(res) {
		    // if (res.from === 'button') {// 来自页面内分享按钮
		    //   console.log(res.target)
		    // }
		    return {
		      title: '易奇展会',
		      path: '/pages/index/index'
		    }
		}
	}
</script>

<style>

</style>
