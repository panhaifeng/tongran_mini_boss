<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">索样列表</block>
			<view class="action" slot="right" @click="toggleTopModel">
				<!-- #ifndef MP -->
				<text class="cuIcon-more" ></text>
				<!-- #endif -->
				<!-- #ifdef MP -->
				<text class="cuIcon-moreandroid"></text>
				<!-- #endif -->
			</view>
		</cu-custom>
		<view class="fixed justify-start bg-gradual-pink padding" style="margin-top: -2px;">
			<view class="flex-sub text-center ">
				<view class="flex-sub text-center">
					<text class="text-white text-df" > {{exhibition.name || '首页先选择展会'}}</text>
				</view>
			</view>
		</view>
		
		<view class="cu-list menu" >
			<view class="cu-item" v-for="(row,index) in rows" :key="index">
				<view class="content padding-tb-sm">
					<view>
						<text class="margin-right-xs text-left cu-avatar sm round bg-blue">{{index+1}}</text>						
						<text class="text-df margin-right-xs">{{row.Client.compName}}</text>
						<text class="text-df text-orange">(ID.{{row.id}})</text>
						<text v-if="row.Client.cardPath" 
						class="text-cyan cuIcon-card text-right" 
						style="font-size: 24px;position: absolute;right: 10px;" 
						@click="previewImage(row.Client.cardPath)"></text>
					</view>
					<view class="flex  text-df">
						<view class="flex-sub margin-right-xs">联 系 人：{{row.Client.contacts}}</view>
						<view class="flex-sub margin-right-xs">手机：{{row.Client.mobile}}</view>
					</view>					
					<view class="flex  text-df">
						<view class="flex-sub margin-right-xs">员工姓名：{{row.realName}}</view>
						<view class="flex-sub margin-right-xs">索样数量：{{row.sampleCnt}}</view>
					</view>
					<view class="flex text-df">
						<view class="flex-sub margin-right-xs">是否完成：{{row.is_over}}</view>
						<view class="flex-sub margin-right-xs">寄样情况：{{row.is_issue}}</view>
					</view>
					<view class="flex  text-df">
						<view class="flex-sub margin-right-xs">客户邮箱：{{row.Client.email}}</view>
					</view>
					<view class="flex  text-df">
						<view class="flex-sub margin-right-xs">客户标签：{{row.Client.tips}}</view>
					</view>
					<view class="flex  text-df">
						<view class="flex-sub margin-right-xs">操作时间：{{row.time}}</view>
					</view>
				</view>
				<!-- 注意下面的传参方法，在事件中传入其他参数时可参考 -->
				<view class='action' @click="showModal(arguments,index)">
				<!-- <view class='action' @click="showMore=true;"> -->
					<text class="cuIcon-moreandroid text-blue lg"></text>
				</view>
			</view>
			<view class="uni-loadmore text-gray text-center text-sm solid-bottom padding" v-if="showLoadMore">{{loadMoreText}}</view>
			<view class="uni-loadmore text-gray text-center text-sm solid-bottom padding" v-if="nothing">没有找到数据</view>
		</view>


		<!-- 底部模态窗口 -->
		<view
			class="cu-modal bottom-modal show"
			v-if="showMore?true:false"
			@touchmove.stop.prevent="moveHandle"
			>
			<view class="cu-dialog">
				<view class="cu-bar bg-white" style="border-bottom: 0.5px solid #eee;">
					<view class="action text-green" @click="hideModal"></view>
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
				exhibition:{},	
				//需要获取导航栏的高度，不同终端下不一样
				topModalStyle:"margin-top:"+ (this.CustomBar+5) + "px;",
				//数据获取url
				url :'',
				//载入时的参数集
				loadParam :{pageIndex:1},
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
				nothing:false,
				//模态窗是否显示
				showMore: false,
				//顶部模态窗
				showTopModal: false,
				// 筛选模态窗
				showFilterModal:false,
				//当前记录key
				itemKey:-1,
				// dateFrom:new Date().getFullYear()+'-'+new Date().getMonth()+'-'+new Date().getDate(),
				// dateTo:new Date().getFullYear()+'-'+(new Date().getMonth()+1)+'-'+new Date().getDate(),
				//打印服务是否开启
				vioQs:false,
				//单条记录操作按钮组
				btnGroup:[
					{text:'修改',icon:'edit',handle:()=>{
						// var rowCur = this.rows[this.itemKey];
						this.itemClick(this.itemKey ,'edit');
						// console.log("修改记录:",rowCur);
					}},
					{text:'删除',icon:'deletefill',handle:()=>{
						var _this = this;
						uni.showModal({
						    title: '操作提示',
						    content: "是否删除该条数据",
						    success: function (res) {
						        if (res.confirm) {
						            var rowCur = _this.rows[_this.itemKey];
						            var index = _this.itemKey;
						            if(index<0) return false;
						            var params = {
						            	method:'exhibition.sample.remove',
						            	id : rowCur.id || ''
						            };
						            formatData.set(params);

						            uni.request({
						            	url: formatData.httpUrl(), //仅为示例，并非真实接口地址。
						            	data:params,
						            	success: (ress) => {
											// console.log(ress);
											var res = ress.data.data || {};
											if(res.success){
												_this.rows.splice(index,1);
											}
											
											uni.showToast({
												title:res.msg || "操作完成",
												icon:'none'
											})
						            	}
						            });
						        } else if (res.cancel) {
						            //这里什么页不做
						        }
						    }
						});

						// console.log("删除记录:",rowCur);
					}},
					{text:'详细',icon:'info',handle:()=>{
						// var rowCur = this.rows[this.itemKey];
						this.itemClick(this.itemKey ,'detial');
					}},
					{text:'发邮件',icon:'mail',handle:()=>{
						var _this = this;
						uni.showModal({
						    title: '操作提示',
						    content: "是否发送邮件给客户",
						    success: function (res) {
						        if (res.confirm) {
						            var rowCur = _this.rows[_this.itemKey];
						            var index = _this.itemKey;
						            if(index<0) return false;
									if(!rowCur.Client.email){
										uni.showToast({
											title:"没有客户邮箱地址",
											icon:'none'
										})
										return false;
									}
						            var params = {
						            	method:'exhibition.sample.email',
						            	id : rowCur.id || ''
						            };
						            formatData.set(params);
						
						            uni.request({
						            	url: formatData.httpUrl(), //仅为示例，并非真实接口地址。
						            	data:params,
						            	success: (ress) => {
											// console.log(ress);
											var res = ress.data.data || {};
											
											uni.showToast({
												title:res.msg || "操作完成",
												icon:'none'
											})
						            	}
						            });
						        }
						    }
						});				
					}},
				],
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
							this.loadParam.pageIndex = 1;
							//根据搜索条件重新获取数据
							this.loadData();
						});
						let url = '/pages/search/rukuList'
						this.$navTo.set(url,this.loadParam)
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
		onLoad(query) {
			
		},
		onShow(){
			//初始化当前展会名字
			this.exhibition = formatData.getCurrentExhibition() || {};
			
			this.loadData();
		},
		onReachBottom() {
			// if (this.rows.length > this.maxLength) {
			// 	this.loadMoreText = "已经到底了"
			// 	return;
			// }
			console.log('onReachBottom');
			this.showLoadMore = true;

			this.loadParam.pageIndex++;
			this.loadData();
		},
		onPullDownRefresh() {
			// console.log('onPullDownRefresh');
			this.loadParam.pageIndex=1;
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
		destroyed() {

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
				param.pageIndex = param.pageIndex||1;
				param.exhId = this.exhibition.id;
				if(!param.exhId){
					return false;
				}
				
				// var pageIndex = param.pageIndex||0;
				console.log(`载入${param.pageIndex}页数据,参数:`,this.loadParam);
				uni.showLoading({
					mask:true,
					title:'加载数据...',
				});

				var params = {
					method:'exhibition.sample.list.get',
				};
				for(var k in param) {
					var _k = k;
					if(_k == 'pageIndex')_k = 'page';
					params[_k] = param[k];
				}
			    formatData.set(params);
				uni.request({
					url: formatData.httpUrl(),
					data:params,
					success: (res) => {
						var result = res.data.data;
						// console.log(res);
						if(res.statusCode!=200) {
							uni.showToast({
								icon:'none',
								title:"服务器出错",
								duration:1500,
								mask:false
							});
							return false;
						}

						if(param.pageIndex==1) {
							this.rows = result.row || [];
							// uni.pageScrollTo({
							// 	scrollTop:0
							// })
						} else {
							this.rows = this.rows.concat(result.row);
						}

						if(result.pageCount == 0 ){
							this.loadMoreText = "已经到底了";
						}
						//判断是否没有任何数据
						if(this.rows.length == 0){
							this.showLoadMore = false;
							this.nothing = true;
						}else{
							this.nothing = false;
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
			//列表内容点击
			itemClick(index ,type) {
				var rowCur = this.rows[index];
				// console.log('rowCur',rowCur);
				let url = '/pages/sample/cardSample';
				if(type=='detial'){
					url = '/pages/sample/detialSample';
				}
				let param = {
					edittype:type,
					type:'oldClient',
					exid: rowCur.id
				}
				this.$navTo.set(url,param)
			},
			previewImage(path){
				if(!path){
					console.log('找不到预览图片');
					return false;
				}
				uni.previewImage({
					urls: [path],
					longPressActions: {
						success: function(data) {
							console.log('选中了第' + (data.tapIndex + 1) + '个按钮,第' + (data.index + 1) + '张图片');
						},
						fail: function(err) {
							console.log(err.errMsg);
						}
					}
				});
			}
		}
	}
</script>

<style>
</style>
