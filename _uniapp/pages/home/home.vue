<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink">
			<block slot="content">
				<view>易奇-筒染</view>
			</block>
		</cu-custom>
		<!-- <view class="nav fixed justify-start bg-gradual-pink padding" :style="'top: '+CCustomBar+'px;margin-top: -2px;'">
			<view class="flex-sub text-center ">
				<view class="flex-sub text-center">
					<text class="text-white" @tap="openModel">{{exhibition.name || '未选择展会'}} [切换展会]</text>
				</view>
			</view>
		</view> 
		<view :style="'margin-bottom:'+CCustomBar+'px;'" ></view>-->
		<!-- 弹框选择展会数据进行切换 -->
		<view class="cu-modal padding" :class="showRadioModal?'show':''" @tap="hideModal">
			<view class="cu-dialog" @tap.stop="">
				<radio-group class="block" @change="RadioChange">
					<view class="cu-list menu text-left">
						<view class="cu-item" v-for="(item,index) in exhibitionArr" :key="index">
							<label class="flex justify-between align-center flex-sub">
								<view class="flex-sub">{{index +1}}. {{item.name}} ({{item.status}})</view>
								<radio class="round" :class="exhibition.id==item.id?'checked':''" :checked="exhibition.id==item.id?true:false"
								 :value="item.id"></radio>
							</label>
						</view>
					</view>
				</radio-group>
			</view>
		</view>
		<view class="grid col-4 text-center margin-tb-sm" v-if="isDataSee">
			<view class="padding-xs">
				<view class="padding radius text-center shadow-blur bg-gradual-pink">
					<view class="text-lg">本月</view>
					<view class="text-lg">计划数</view>
					<view class="text-lg" style="margin-top:5px">{{cntM_month}}</view>
				</view>
			</view>
			<view class="padding-xs">
				<view class="padding radius text-center shadow-blur bg-gradual-pink">
					<view class="text-lg">本日</view>
					<view class="text-lg">计划数</view>
					<view class="text-lg" style="margin-top:5px">{{cntM_Today}}</view>
				</view>
			</view>
			<view class="padding-xs">
				<view class="padding radius text-center shadow-blur bg-gradual-pink">
					<view class="text-lg">本日</view>
					<view class="text-lg">发货数</view>
					<view class="text-lg" style="margin-top:5px">{{cntFh_Today}}</view>
				</view>
			</view>
			<view class="padding-xs">
				<view class="padding radius text-center shadow-blur bg-gradual-pink">
					<view class="text-lg">本月</view>
					<view class="text-lg">发货数</view>
					<view class="text-lg" style="margin-top:5px">{{cntFh_Month}}</view>
				</view>
			</view>
		</view>		
		<!-- 导航区域 -->
		<view v-for="(menu,indexMenu) in ButtonsMenu" :key="indexMenu" :class="{'display-none':(!menu.buttons.length)}">
			<view class="cu-bar bg-white solid-bottom margin-top" >
				<view class="action">
					<text class="cuIcon-title text-orange "></text>{{menu.titleBlock}}
				</view>
			</view>
			<view class="cu-list grid" :class="['col-' + gridCol,gridBorder?'':'no-border']" :style="indexMenu==ButtonsMenu.length-1?'padding-bottom: 50px;':''">
				<view class="cu-item" v-for="(item,index) in menu.buttons" :key="index" v-if="!authcheck || item.display">
					<navigator :url="item.path">
						<view :class="['cuIcon-' + item.icon,'text-' + (item.color||'blue')]" style="font-size:30px;">
							<view class="cu-tag badge" v-if="item.badge!=0 && item.badge!=null">
								<block v-if="item.badge!=1">{{item.badge>99?'99+':item.badge}}</block>
							</view>
						</view>
						<text>{{item.text}}</text>
					</navigator>
				</view>
			</view>
		</view>

	</view>
</template>

<script>
	import formatData from '@/common/formData.js';
	export default {
		data() {
			return {
				CCustomBar:this.CustomBar,
				cardCur: 0,
				swiperList:[{
					id: 0,
					type: 'image',
					url: '/static/sample.jpg'
				}],
				authcheck:true,//是否需要验证菜单权限
				dotStyle: true,
				showRadioModal:false,
				towerStart: 0,
				keyMenu: 'tongran.menu.uniapp.eqinfo',
				exhibition:{},
				exhibitionArr:[],
				direction: '',
				gridCol: 3,
				gridBorder: false,
				testData:'',
				openSetting:false,
				cntM_month:0,
				cntM_Today:0,
				cntFh_Today:0,
				cntFh_Month:0,
				isDataSee:false,
				ButtonsMenu:[
					{
						titleBlock:"报工现场",
						buttons:[
							{text:"产量登记",icon:"camera",path:"/pages/output/outPut",itemId:'outPut'},
							// {text:"固定高度",icon:"friend",path:"/pages/demo/fixHeight",itemId:'demo1'},
						],
					}
					,{
						titleBlock:"数据分析",
						buttons:[
							{text:"订单进度报表",icon:"people",path:"/pages/order/process",itemId:'process'},
						],
					},
				],
				ButtonsMenuBak:[],
			};
		},
		//组件的生命周期：created，beforeCreate，beforeMount，mounted，beforeUpdate，updated,beforeDestroy,destroyed
		onLoad() {			
			// 在colorui 的个性tabbar中，这个组件页面的onload是不会触发的，请勿掉坑		
			// onshow也不会触发
		},
		created(){
			
		},
		beforeMount(){
			console.log('beforeMount home');			
			var _this = this;
			//先加载缓存中的菜单：：uni.getSetting会有点延迟，所以在里面会导致菜单加载缓慢下
			_this.cacheGetMenu();
			
			// #ifdef MP-WEIXIN
			var getUserInfo = uni.canIUse('button.open-type.getUserInfo');
			if(getUserInfo){
				uni.getSetting({
				    success(res) {
						if(!res.authSetting['scope.userInfo']){
							console.log('authorize fail',res.authSetting);
							// _this.openSetting = true;
							uni.navigateTo({
							    url: '/pages/weixin/authSetting'
							});
						}else{
							// uni.navigateTo({
							//     url: '/pages/weixin/authSetting'
							// });
							_this.initHome();
						}
				    }
				});
			}else{
				_this.initHome();
			}
			// #endif
			
			// #ifndef MP-WEIXIN
			_this.initHome();
			// #endif
		},
		mounted() {
			
		},
		onShareAppMessage(res) {
		    return {
		      title: '易奇筒染',
		      path: '/pages/index/index'
		    }
		},
		// onPullDownRefresh() {
		// 	this.refresh();
		// },
		methods: {
			initHome(){
				// console.log('initHome home');
				//初始化home页面的方法集中处理下
				var _this = this;
				formatData.getUserinfo(function(user){
					console.log('initHome home user',user);
					if(!user.userId){						
						uni.showToast({
							icon:'none',
							title:"无用户信息",
							duration:2500,
							mask:true
						});
						//如果没有有效的userid登录信息,则跳转提示需要登录						
						setTimeout(function(){
							uni.reLaunch({
								url: '/pages/login/bind'
							});					
						},1000)
					}else{
						//先加载缓存中的菜单
						// _this.cacheGetMenu();
						//获取权限菜单，仅显示有权限的菜单项目
						// _this.getSwiperList();
						//更新服务器的菜单
						_this.getMenu();
						// _this.getExhibition();
						_this.getOrderInfo();
					}
				});
			},
			cacheGetMenu: function(){
				var _this = this;				
				var km = uni.getStorageSync(_this.keyMenu);
				if(km){
					km = JSON.parse(km);
					if(km){
						_this.ButtonsMenuBak = _this.ButtonsMenu;
						_this.ButtonsMenu = km;
					}
				}
			},
			getMenu: function() {
				// console.log('getMenu');
				//获取菜单并处理显示哪些菜单
				var _this = this;
				var buttonMenu = _this.ButtonsMenuBak.length > 0 ? _this.ButtonsMenuBak : _this.ButtonsMenu;
				_this.ButtonsMenuBak = [];
				// console.log('buttonMenu',buttonMenu);
				//定制轮播图的时候用
				var params = {
					method:'menu.list.get',
				};
				formatData.set(params);
				uni.request({
					url: formatData.httpUrl(), //仅为示例，并非真实接口地址。
					data:params,
					success: (res) => {
						var result = res.data.data;
						if(result.menuId){
							for(var k in buttonMenu){
								var itemM = buttonMenu[k];
								for(var i in itemM.buttons){
									var item = itemM.buttons[i];
									buttonMenu[k].buttons[i].display = false;
									if(result.menuId.indexOf(item.itemId) >= 0){
										buttonMenu[k].buttons[i].display = true;
									}									
								}
								this.$set(_this.ButtonsMenu ,k ,buttonMenu[k]);
							}
							if(result.menuId.indexOf('orderData') >= 0){
								this.isDataSee = true;
							}
						}
						// _this.ButtonsMenu = buttonMenu;
						// console.log('_this.ButtonsMenu',buttonMenu);
						//把菜单数据缓存到本地缓存中
						uni.setStorageSync(_this.keyMenu,JSON.stringify(_this.ButtonsMenu));
					}
				});
			},
			getSwiperList: function(){
				// console.log('getSwiperList');
				var _this = this;
				//定制轮播图的时候用
				var params = {
					method:'uni.getSwiperImages',
					platfrom: 'weixin',
				};
				formatData.set(params);
				uni.request({
					url: formatData.httpUrl(), //仅为示例，并非真实接口地址。
					data:params,
					success: (res) => {
						var result = res.data.data;
						if(result.swiperList.length > 0){
							_this.swiperList = result.swiperList;
						}
					}
				});
			},
			
			getOrderInfo: function(){
				var _this = this;
				var params = {
					method:'get.data.order'
				};
				formatData.set(params);
				uni.request({
					url:formatData.httpUrl(),
					data:params,
					success: (res) => {
						var result = res.data.data.params;
						this.cntM_month = result.cntM_month;
						this.cntM_Today = result.cntM_Today;
						this.cntFh_Today = result.cntFh_Today;
						this.cntFh_Month = result.cntFh_Month;
					}
				});
			},
			getExhibition: function(){
				var _this = this;
				//定制轮播图的时候用
				var params = {
					method:'exhibition.list.get'
				};
				formatData.set(params);
				uni.request({
					url: formatData.httpUrl(), //仅为示例，并非真实接口地址。
					data:params,
					success: (res) => {
						var result = res.data.data;
						if(result.exhibition.length > 0){
							_this.exhibitionArr = result.exhibition;
							
							//默认选中一个
							var curExh = formatData.getCurrentExhibition();
							//如果已经设置了，则不需要默认一个，如果没有设置，则自动默认一个
							if(!curExh.id){
								//默认设置一个未当前展会:先默认第一个，如果有进行中的状态，则替换成进行中的
								_this.exhibition = _this.exhibitionArr[0] || {};
								for(var k in _this.exhibitionArr){
									var exh = _this.exhibitionArr[k] || {};
									if(exh.status == '进行中'){
										_this.exhibition = exh;
									}
								}
								formatData.setCurrentExhibition(_this.exhibition);
							}else{
								_this.exhibition = curExh;
							}
						}
					}
				});
			},
			RadioChange(e) {
				var curId = e.detail.value;
				for(var k in this.exhibitionArr){
					var exh = this.exhibitionArr[k] || {};
					if(exh.id == curId){
						this.exhibition = exh;
						formatData.setCurrentExhibition(exh);
					}
				}
				
				this.showRadioModal = false;
			},
			openModel(){
				this.showRadioModal = true;
			},
			hideModal(e) {
				this.showRadioModal = false
			},
		},
	}
</script>

<style>
	.tower-swiper .tower-item {
		transform: scale(calc(0.5 + var(--index) / 10));
		margin-left: calc(var(--left) * 100upx - 150upx);
		z-index: var(--index);
	}
</style>
