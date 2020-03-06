<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{title}}</block>
			<!-- <view class="action" slot="right" >
				<text class="cuIcon-search" @click="toggleTopModel"></text>
			</view>
			 -->
			<view class="action" slot="right" @click.stop="call(btnSearch.handle)">
				<text class="cuIcon-search"></text>
			</view>
		</cu-custom>
		<view class="cu-bar bg-white search fixed" :style="[{top:CCustomBar + 'px'}]">
			<view class="search-form round">
				<text class="cuIcon-search"></text>
				<input type="text" placeholder="输入搜索的客户关键词" v-model="searchKey" confirm-type="search" @confirm="confirmSearch"></input>
			</view>
			<view class="action">
				<button class="cu-btn bg-gradual-blue shadow-blur round" @tap="confirmSearch">搜索</button>
			</view>
		</view>
		<view style="height: 35px"></view>
		<view class="list_box" :style="{ 'height':scrollHeight }">
			<view class="left">
				<scroll-view scroll-y="true" :style="{ 'height':scrollHeight }" lower-threshold="15" >
					<view class="item" v-for="(item,index) in leftArray" :key="index" :class="{ 'active':index==leftIndex }"
					 :data-index="index" @tap="leftTap">{{item.orderCode}}</view>
				</scroll-view>
			</view>
			<view class="main">
				<swiper class="swiper" :style="{ 'height':scrollHeight }" :current="leftIndex" @change="swiperChange" vertical="true"
				 duration="300">
					<swiper-item v-for="(item,index) in mainArray" :key="index">
						<scroll-view scroll-y="true" :style="{ 'height':scrollHeight }">
							<view class="item">
								<view class="title">
									<view>{{item.title}}</view>
								</view>
								<view class="goods" v-for="(item2,index2) in item.list" :key="index2">
									<view>
									    <view class="describe" style="color: red;">缸号:{{item2.vatNum}}</view>
										<view class="describe">客户:{{item2.compName}}</view>
										<view class="describe">规格:{{item2.guige}}</view>
										<view class="describe">颜色:{{item2.color}}</view>
										<view class="describe">经纬合计:{{item2.jwAll}}</view>
										<view class="describe">松筒:{{item2.HaveSt==true?"√":''}}</view>
										<view class="describe">染色:{{item2.HaveRs>0?"√":''}}</view>
										<view class="describe">烘纱:{{item2.HaveHs==true?"√":''}}</view>
										<view class="describe">回倒:{{item2.Havehd==true?"√":''}}</view>
										<view class="describe">发货:{{item2.HaveFh==true?"√":''}}</view>
										<view class="describe">发货数量:{{item2.cntCpck==true?"√":''}}</view>
									</view>
								</view>
							</view>
						</scroll-view>
					</swiper-item>
				</swiper>
				<view class="uni-loadmore text-center" v-if="showLoadMore">{{loadMoreText}}</view>
			</view>
		</view>
		<!-- 顶部高级功能模态窗口 -->
		<view
			class="cu-modal top-modal show"
			v-if="showTopModal"
			@touchmove.stop.prevent="moveHandle"
			@tap="toggleTopModel">
			<view class="cu-dialog" :style="topModalStyle">
				<view class="cu-list menu" >
					<view class="cu-item" v-for="(btn,index) in btnGroupTop" :key="index"
						@click.stop="call(btn.handle)">
						<view class="content">
							<text class="text-grey" :class="btn.icon?`cuIcon-${btn.icon}`:''"></text>
							<text class="text-grey text-lg">{{btn.text}}</text>
						</view>
					</view>
				</view>
			</view>
		</view>
		<!--快速筛选模态窗口 -->
		<view
			class="cu-modal top-modal show"
			v-if="showFilterModal"
			@touchmove.stop.prevent="moveHandle"
			@tap="toggleFilterModel">
			<view class="cu-dialog" :style="topModalStyle">
				<view class="cu-list menu" >
					<view class="cu-item" v-for="(btn,index) in btnGroupFilter" :key="index"
					@click.stop="call(btn.handle)">
						<view class="content">
							<text class="text-grey" :class="btn.icon?`cuIcon-${btn.icon}`:''"></text>
							<text class="text-grey text-lg">{{btn.text}}</text>
						</view>
					</view>
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
				scrollHeight:'500px',
				windowHeight:'',
				CCustomBar:this.CustomBar,
				topModalStyle:"margin-top:"+ (this.CustomBar+1) + "px;",
				searchKey:'',//输入框搜索的关键词
				leftArray:[],
				mainArray:[],
				confirmSearchData:1,
				leftIndex:0,
				pageIndex:1,
				dateFrom:formatData.dateSearch().dateFrom,
				dateTo:formatData.dateSearch().dateTo,
				loadParam:{pageNum:1},
				loadMoreText:'加载中..',
				showLoadMore:false,
				//顶部模态窗
				showTopModal: false,
				// 筛选模态窗
				showFilterModal:false,
				//高级操作的按钮组
				btnGroupTop:[
					{text:'高级搜索',icon:'search',handle:()=>{
						//创建页面监听，搜索条件确认后，刷新数据
						this.$eventHub.$on('listenSearch',(param)=>{
							this.$eventHub.$off('listenSearch');
							console.log('searchParams',param);
							//构造搜索条件
							for(var k in param) {
								this.loadParam[k] = param[k];
							}
							this.loadParam.pageNum = 1;
							//根据搜索条件重新获取数据
							this.getListData();
						});
						let url = '/pages/search/process'
						this.$navTo.set(url,this.loadParam)
					}},
					{text:'返回首页',icon:'home',handle:()=>{
						// var rowCur = this.rows[this.itemKey];
						uni.reLaunch({
							url:"../../pages/index/index"
						})
					}},
				
				],
				btnSearch:{handle:()=>{
					//创建页面监听，搜索条件确认后，刷新数据
					this.$eventHub.$on('listenSearch',(param)=>{
						this.$eventHub.$off('listenSearch');
						console.log('searchParams',param);
						//构造搜索条件
						for(var k in param) {
							this.loadParam[k] = param[k];
						}
						this.loadParam.pageNum = 1;
						//根据搜索条件重新获取数据
						this.getListData();
					});
					let url = '/pages/search/process'
					this.$navTo.set(url,this.loadParam)
				}},
				//快速筛选按钮组
				btnGroupFilter:[
					{text:'全部',icon:'search',handle:()=>{
				
					}}
				],
			}
		},
		onLoad() {
			/* 设置当前滚动容器的高，若非窗口的高度，请自行修改 */
			uni.getSystemInfo({
				success: (res) => {
					this.scrollHeight = `${res.windowHeight-this.CustomBar}px`;
				}
			});
		},
		onUnload() {
			console.log('注销 eventSelect');
			this.$eventHub.$off('eventSelect');
			
			// this.max = 0,
			this.rows = [],
			this.loadMoreText = "加载更多",
			this.showLoadMore = false;
		},
		mounted() {
			this.getListData();
		},
		methods: {
			//调用某个函数,
			//如果在模板中使用@click="item.handle",在app中编译不通过，app中需要显式声明函数
			call(fn) {
				this.showTopModal=false;
				this.showFilterModal=false;
				this.hideModal();
			
				fn.apply(this);
				// this.$root.callbacks[key].apply(this,[row,e]);
				// this.showMore =false;
			},
			/* 获取列表数据 */
			getListData() {
				var that = this;
				var param = that.loadParam;
				param.pageNum = param.pageNum||1;
				that.loadParam.dateFrom = that.loadParam.dateFrom?that.loadParam.dateFrom:that.dateFrom;
				that.loadParam.dateTo = that.loadParam.dateTo?that.loadParam.dateTo:that.dateTo;
				param.dataSearch = that.searchKey || '';
				if(that.confirmSearchData==2){
					param.pageNum = 1;
				}
				uni.showLoading({
					title: 'Loading...'
				});
				var params = {
					method: 'uni.order.process',
					// pageNum: that.pageIndex,
				};
				for(var k in param) {
					params[k] = param[k];
				}
				formatData.set(params);
				uni.request({
					url: formatData.httpUrl(),
					data: params,
					method: 'POST',
					header: {
						'content-type': 'application/x-www-form-urlencoded'
					},
					success: (res) => {
						let [left,main]=[[],[]];
						var result = res.data.data;
						console.log('process',result);
						//var succ = result.rsp.success || false;
				
						// if (!succ) {
						// 	uni.showToast({
						// 		title: result.rsp.msg,
						// 		icon: 'none'
						// 	});
						// 	return false;
						// }
						if(param.pageNum==1){
							uni.showToast({
								title: '加载完成',
								mask: true
							});
							let max = result.params.length;
							for(let j=0;j<max;j++){
								let list=[];
								for(let jj in result.params[j]){
									list.push(result.params[j][jj]);
								}
								main.push({
									title:`订单号:${result.orderCode[j].orderCode}`,
									list
								})
							}
							this.leftArray = result.orderCode;
							this.mainArray = main;
							this.goTop();
						}else{
							uni.showToast({
								title: '加载完成',
								mask: true
							});
							let max = result.params.length;
							for(let j=0;j<max;j++){
								let list=[];
								for(let jj in result.params[j]){
									list.push(result.params[j][jj]);
								}
								main.push({
									title:`订单号:${result.orderCode[j].orderCode}`,
									list
								})
							}
							var rrr = result.params;
							console.log('rrr',rrr);
							if(rrr.length>0||rrr!=''){
								this.leftArray = this.leftArray.concat(result.orderCode);
								this.mainArray = this.mainArray.concat(main);
							}else{
								this.loadMoreText = "没有更多数据了";
							}	
							this.goTop();
						}
					},
					complete() {
						uni.hideLoading();
					}
				});
			},
			goTop: function (e) {  // 一键回到顶部
			    if (wx.pageScrollTo) {
			      wx.pageScrollTo({
			        scrollTop: 0
			      })
			    } else {
			      wx.showModal({
			        title: '提示',
			        content: '当前微信版本过低，无法使用该功能，请升级到最新微信版本后重试。'
			      })
			    }
			},
			testData() {
				/* 因无真实数据，当前方法模拟数据 */
				let [left,main]=[[],[]];
				
				for(let i=0;i<10;i++){
					left.push({
						orderCode:`${i+1}类商品`,
						list
					});
					
					let list=[];
					let max = Math.floor(Math.random()*15) || 8;
					for(let j=0;j<max;j++){
						list.push(j);
					}
					main.push({
						title:`第${i+1}类商品标题`,
						list
					})
				}
				console.log(main);
				this.leftArray = this.leftArray.concat(left);
				this.mainArray = this.mainArray.concat(main);
			},
			/* 左侧导航点击 */
			leftTap(e){
				let index=e.currentTarget.dataset.index;
				this.leftIndex=Number(index);
			},
			/* 轮播图切换 */
			swiperChange(e){
				let index=e.detail.current;
				this.leftIndex=Number(index);
			},
			onPullDownRefresh() {
				console.log('onPullDownRefresh');
				// this.loadParam.pageIndex=0;
				// this.pageIndex++;
				this.confirmSearchData =1;
				this.loadParam.pageNum++;
				this.getListData();
			},
			onReachBottom() {
				console.log('onReachBottom');
				// this.pageIndex++;
				this.showLoadMore = true;
				this.loadMoreText = '加载中';
				this.confirmSearchData = 1;
				this.loadParam.pageNum++;
				this.getListData();
				// this.scrollHeight= (this.pageIndex+1)*500+'px';
			},
			//显示模态窗,第二个参数表示哪个被选中
			showModal(e,index) {
				this.showFilterModal = false;
				this.showTopModal = false;
				this.showMore = true;
				this.itemKey = arguments[1];
				console.log(this.itemKey+'记录被选中')
			},
			// //隐藏模态窗
			hideModal(e) {
				this.showMore = false;
				// this.itemKey = -1;  这里如果恢复初始值 会导致后续的处理事件失效（丢失itemKey）
			},
			//切换顶部模态窗的可见状态
			toggleTopModel() {
				this.showMore = false;
				this.showFilterModal = false;
				this.showTopModal = (this.showTopModal+1)%2;
				console.log('toggle,now is',this.showTopModal)
			},
			//切换筛选窗窗口的可见
			toggleFilterModel() {
				this.showMore = false;
				this.showTopModal = false;
				this.showFilterModal = (this.showFilterModal+1)%2;
			},
			confirmSearch(){
				this.confirmSearchData = 2;
				this.getListData();
			},
		}
	}
</script>

<style lang="scss">
	.cu-dialog{
		margin-top: 80px;
	}
	.list_box {
		display: flex;
		flex-direction: row;
		flex-wrap: nowrap;
		justify-content: flex-start;
		align-items: flex-start;
		align-content: flex-start;
		font-size: 28rpx;
		margin-top: 10px;
	.left{
		width: 200rpx;
		background-color: #f6f6f6;
		line-height: 80rpx;
		box-sizing: border-box;
		font-size: 32rpx;
		
		.item{
			padding-left: 20rpx;
			padding-right: 20rpx;
			position: relative;
			&:not(:first-child) {
				margin-top: 1px;
			
				&::after {
					content: '';
					display: block;
					height: 0;
					border-top: #d6d6d6 solid 1px;
					width: 620upx;
					position: absolute;
					top: -1px;
					right: 0;
					transform:scaleY(0.5);	/* 1px像素 */
				}
			}
			
			&.active,&:active{
				color: #42b983;
				background-color: #fff;
			}
		}
	}
	.main{
		background-color: #fff;
		padding-left: 20rpx;
		width: 0;
		flex-grow: 1;
		box-sizing: border-box;
		
		.swiper{
			height: 500px;
		}

		.title{
			line-height: 64rpx;
			font-size: 24rpx;
			font-weight: bold;
			color: #666;
			background-color: #fff;
			position: sticky;
			top: 0;
			z-index: 999;
		}
		
		.item{
			padding-bottom: 10rpx;
		}
		
		.goods{
			display: flex;
			flex-direction: row;
			flex-wrap: nowrap;
			justify-content: flex-start;
			align-items: center;
			align-content: center;
			margin-bottom: 10rpx;
			
			&>image{
				width: 120rpx;
				height: 120rpx;
				margin-right: 16rpx;
			}
			
			.describe{
				font-size: 30rpx;
				color: black;
				line-height: 30px;
				float: left;
				width: 50%;
			}
		}
	}
}
</style>
