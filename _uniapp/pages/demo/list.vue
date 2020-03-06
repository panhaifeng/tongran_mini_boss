<!-- 数据类容，选中后下部滑出，加载更多，载入效果,下拉刷新，右上角菜单，-->
<!-- 考虑到需要注销监听，和单击选中，最好选择列表单独弄个页面，和普通的列表区别开来。 -->
<!-- 搜索条件，选中和多选 -->

<!-- 点击看详情 -->
<!-- todo 可折叠，点击进入详情，暂不考虑 -->
<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">客户列表</block>
			<view class="action" slot="right" >
				<text class="cuIcon-filter" @click="toggleFilterModel"></text>
				<text class="cuIcon-more" @click="toggleTopModel"></text>
			</view>
		</cu-custom>

		<view class="cu-list menu" >
			<view class="cu-item" v-for="(row,index) in rows" :key="index">
				<view class="content padding-tb-sm" @click="itemClick">
					<view>
						<text class="cuIcon-friend text-blue margin-right-xs text-left"></text>{{row.compName}}
					</view>
					<view class="flex text-gray text-sm">
						<view class="flex-sub margin-right-xs">联系人：{{row.con}}</view>
						<view class="flex-sub margin-right-xs">电话:{{row.tel}}</view>
					</view>
					<view class="flex text-gray text-sm">
						<view class="flex-sub margin-right-xs">客户类型：vip</view>
						<view class="flex-sub margin-right-xs">传真:{{row.tel}}</view>
					</view>
					<view class="text-gray text-sm">
						<view class="flex-sub margin-right-xs">地址：{{row.address}}</view>
					</view>
				</view>
				<!-- 注意下面的传参方法，在事件中传入其他参数时可参考 -->
				<view class='action' @click="showModal(arguments,index)">
				<!-- <view class='action' @click="showMore=true;"> -->
					<text class="cuIcon-moreandroid text-blue lg"></text>
				</view>
			</view>
			<view class="uni-loadmore text-center" v-if="showLoadMore">{{loadMoreText}}</view>
		</view>


		<!-- 底部模态窗口 -->
		<view
			class="cu-modal bottom-modal show"
			v-if="showMore?true:false"
			@touchmove.stop.prevent="moveHandle"
			>
			<view class="cu-dialog">
				<view class="cu-bar bg-white" style="border-bottom: 0.5px solid #eee;">
					<view class="action text-green" @click="hideModal">确定</view>
					<view class="action text-blue" @click="hideModal">取消</view>
				</view>
				<view class="cu-list menu" >
					<view class="cu-item" v-for="(btn,index) in btnGroup" :key="index" @click="call(btn.handle)">
						<view class="content">
							<text class="text-grey" :class="btn.icon?`cuIcon-${btn.icon}`:''"></text>
							<text class="text-grey text-lg">{{btn.text}}</text>
						</view>
					</view>
				</view>
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
			<view class="cu-dialog":style="topModalStyle">
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

		<!-- <view class="padding flex flex-direction">
			<button class="cu-btn bg-blue margin-tb-xs lg" @click="test">submit</button>
			<button class="cu-btn bg-blue margin-tb-xs lg" @click="test1">清空</button>
		</view> -->
	</view>
</template>

<script>
	import formatData from '../../common/formData.js';
	export default {
		data() {
			return {
				//需要获取导航栏的高度，不同终端下不一样
				topModalStyle:"margin-top:"+ (this.CustomBar+5) + "px;",
				//数据获取url
				url :'',
				//载入时的参数集
				loadParam :{pageIndex:0},
				//当前页
				// currentPage:0,
				//数据集
				rows :[],
				//数据集允许的最大条数，纺织内存溢出
				maxLength:40,
				//是否显示加载更多
				showLoadMore:false,
				//加载更多时的文字
				loadMoreText:'加载中..',
				//模态窗是否显示
				showMore: false,
				//顶部模态窗
				showTopModal: false,
				// 筛选模态窗
				showFilterModal:false,
				//当前记录key
				itemKey:-1,
				//单条记录操作按钮组
				btnGroup:[
					{text:'修改',icon:'edit',handle:()=>{
						var rowCur = this.rows[this.itemKey];
						console.log("修改记录:",rowCur);
					}},
					{text:'删除',icon:'deletefill',handle:()=>{
						var rowCur = this.rows[this.itemKey];
						console.log("删除记录:",rowCur);
					}},
					{text:'详细',handle:()=>{
						console.log('跳转')
					}},
					{text:'打印',handle:()=>{
						console.log('打印')
					}},
				],
				//高级操作的按钮组
				btnGroupTop:[
					{text:'高级搜索',icon:'search',handle:()=>{
						//创建页面监听，搜索条件确认后，刷新数据
						this.$eventHub.$on('listenSearch',(param)=>{
							this.$eventHub.$off('listenSearch');
							//构造搜索条件
							// console.log(param);
							for(var k in param) {
								this.loadParam[k] = param[k];
							}
							this.loadParam.pageIndex = 0;
							// var param={};
							//根据搜索条件重新获取数据
							this.loadData();
						});
						uni.navigateTo({
							url:"../../pages/demo/search"
						})
					}},
					{text:'返回首页',icon:'home',handle:()=>{
						// var rowCur = this.rows[this.itemKey];
						uni.reLaunch({
							url:"../../pages/index/index"
						})
					}},

				],
				//快速筛选按钮组
				btnGroupFilter:[
					{text:'全部',icon:'search',handle:()=>{

					}},
					{text:'已审',icon:'home',handle:()=>{
						// var rowCur = this.rows[this.itemKey];
						uni.reLaunch({
							url:"../../pages/index/index"
						})
					}},
					{text:'未审',icon:'home',handle:()=>{
						// var rowCur = this.rows[this.itemKey];
						uni.reLaunch({
							url:"../../pages/index/index"
						})
					}},
				],
			}
		},
		onLoad() {
			this.loadData();
		},
		onReachBottom() {
			if (this.rows.length > this.maxLength) {
				this.loadMoreText = "超过最大记录条数限制或者没有更多数据了!"
				return;
			}
			this.showLoadMore = true;
			var me = this;
			var param={pageIndex:this.currentPage+1};
			this.loadParam.pageIndex++;
			this.loadData();
		},
		onPullDownRefresh() {
			// console.log('onPullDownRefresh');
			this.loadParam.pageIndex=0;
			this.loadData();
		},
		onUnload() {
			console.log('注销 eventSelect');
			this.$eventHub.$off('eventSelect');

			// this.max = 0,
			this.rows = [],
			this.loadMoreText = "加载更多",
			this.showLoadMore = false;
		},
		methods: {
			//模态窗口显示时的空函数，禁止模态形势下拖动底部的屏幕
			moveHandle() {
				return false;
			},

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
			//远程载入数据
			//载入完成后执行后面的回调函数
			loadData() {
				var param = this.loadParam;
				param.pageIndex = param.pageIndex||0;
				// var pageIndex = param.pageIndex||0;
				console.log(`载入${param.pageIndex}页数据,参数:`,this.loadParam);
				uni.showLoading({
					mask:true
				});
				var url = formatData.httpUrl();
				param['version'] = this.version;
				param['token'] = this.token;
				param['method'] = 'uni.getClientList';
				 formatData.set(param);
				uni.request({
					url:url,
					data:param,
					success: (res) => {
						var result = res.data.data;
						if(result.status!=200) {
							uni.showToast({
								icon:'none',
								title:"服务器出错",
								duration:1500,
								mask:false
							});
							return false;
						}
						if(!result.success) {
							uni.showToast({
								title:result.msg,
								icon:"none",
								duration:2000,
							});
							return false;
						}
						if(param.pageIndex==0) {
							this.rows = result.data;
							// uni.pageScrollTo({
							// 	scrollTop:0
							// })
						} else {
							this.rows = this.rows.concat(result.data);
						}
						// this.currentPage = param.pageIndex;

					},
					complete(res) {
						//隐藏载入效果
						uni.hideLoading();
						uni.stopPullDownRefresh();
					},
				});
			},
			//显示模态窗,第二个参数表示哪个被选中
			showModal() {
				this.showFilterModal = false;
				this.showTopModal = false;
				this.showMore = true;
				this.itemKey = arguments[1];
				console.log(this.itemKey+'记录被选中')
			},
			// //隐藏模态窗
			hideModal(e) {
				this.showMore = false;
				this.itemKey = -1;
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
			//列表内容点击
			itemClick() {
				console.log('列表项主题内容点击，显示详情页')
			}
		}
	}
</script>

<style>
</style>
