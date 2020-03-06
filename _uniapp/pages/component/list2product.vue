<!-- 选择列表 -->
<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{title}}</block>
			<view class="action" slot="right" @click="clearClick">
				<text class="cuIcon-backdelete" ></text>
				<!-- #ifndef MP -->
				清空
				<!-- #endif -->
			</view>

		</cu-custom>
		<view class="cu-bar bg-white search fixed" :style="[{top:CCustomBar + 'px'}]">
			<view class="search-form round">
				<text class="cuIcon-search"></text>
				<input type="text" placeholder="输入搜索的关键词" v-model="searchKey" confirm-type="search" @confirm="confirmSearch"></input>
			</view>
			<view class="action">
				<button class="cu-btn bg-gradual-blue shadow-blur round" @tap="confirmSearch">搜索</button>
			</view>
		</view>
		<view style="height: 46px"></view>
		<view class="cu-list menu" >
			<view class="cu-item arrow" v-for="(row,index) in rows" :key="row.proCode">
				<view class="content padding-tb-sm" @click="itemClick(arguments,index)">
					<view>
						<text class="text-blue margin-right-xs text-left">{{index+1}}.</text>{{row[params.text]}}
					</view>
					<view v-for="(filed, l) in params.showFiled" class="flex text-sm" :key="l">
						<view v-for="(item,k) in filed" :key="k" class="flex-sub margin-right-xs">{{item}}: {{row[k]}}</view>
					</view>
					<!-- 其他动态扩展的字段展示 -->
					<view v-if="row.othersRows" v-for="(other,i) in row.othersRows" :key="50+i" class="flex text-sm">
						<view v-if="row.others[i*2]" class="flex-sub margin-right-xs">{{row.others[i*2]['title']}}：{{row.others[i*2]['value']}}</view>
						<view v-if="row.others[i*2+1]" class="flex-sub margin-right-xs">{{row.others[i*2+1]['title']}}：{{row.others[i*2+1]['value']}}</view>
					</view>
				</view>
			</view>
			<view class="uni-loadmore text-gray text-center text-sm solid-bottom padding" v-if="showLoadMore">{{loadMoreText}}</view>
			<view class="uni-loadmore text-gray text-center text-sm solid-bottom padding" v-if="nothing">没有找到数据</view>
		</view>

		<!-- 顶部模态窗口 -->
		<view class="cu-modal top-modal show" v-if="showTopModal" >
			<view class="cu-dialog" :style="topModalStyle">
				<view class="cu-list menu" >
					<view class="cu-item" v-for="(btn,index) in btnGroupTop" :key="index" @click="call(btn.handle)">
						<view class="content">
							<text class="text-grey" :class="btn.icon?`cuIcon-${btn.icon}`:''"></text>
							<text class="text-grey text-lg">{{btn.text}}</text>
						</view>
					</view>
				</view>
			</view>
		</view>

		<!-- 筛选模态窗口 -->
		<view class="cu-modal top-modal show" v-if="showFilterModal" >
			<view class="cu-dialog" :style="topModalStyle">
				<view class="cu-list menu" >
					<view class="cu-item" v-for="(btn,index) in btnGroupFilter" :key="index" @click="call(btn.handle)">
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
	import formatData from '../../common/formData.js';
	export default {
		data() {
			return {
				//需要获取导航栏的高度，不同终端下不一样
				CCustomBar:this.CustomBar,
				topModalStyle:"margin-top:"+ (this.CustomBar+1) + "px;",
				//数据获取url
				url :'',
				title:"",
				dataKey:'',
				params:{},
				searchKey:'',//输入框搜索的关键词
				searchParams:{},//搜索条件汇总
				rowShow:0,//需要几行显示内容
				//数据集
				rows :[],
				//数据集允许的最大条数，纺织内存溢出
				maxLength:40,
				//是否显示加载更多
				showLoadMore:false,
				nothing:false,
				//加载更多时的文字
				loadMoreText:'加载中..',
				//模态窗是否显示
				// showMore: false,
				//顶部模态窗
				showTopModal: false,
				// 筛选模态窗
				showFilterModal:false,
				//当前页
				currentPage:1,
				//当前记录key
				itemKey:-1,
				btnGroupTop:[
					{text:'筛选',icon:'search',handle:()=>{
						// var rowCur = this.rows[this.itemKey];
						console.log("跳转搜索页面:");
						this.showTopModal = false;
					}},
					{text:'首页',icon:'home',handle:()=>{
						// var rowCur = this.rows[this.itemKey];
						uni.reLaunch({
							url:"../../pages/index/index"
						})
					}},

				],
				btnGroupFilter:[
					{text:'全部',icon:'search',handle:()=>{
						// var rowCur = this.rows[this.itemKey];
						// console.log("跳转搜索页面:");
						this.showTopModal = false;
					}},
					// {text:'已审',icon:'home',handle:()=>{
					// 	// var rowCur = this.rows[this.itemKey];
					// 	uni.reLaunch({
					// 		url:"../../pages/home/home"
					// 	})
					// }},
					// {text:'未审',icon:'home',handle:()=>{
					// 	// var rowCur = this.rows[this.itemKey];
					// 	uni.reLaunch({
					// 		url:"../../pages/home/home"
					// 	})
					// }},
				],
			}
		},

		onLoad(options) {
			// console.log(options);
			this.dataKey = options.dataKey || '';
			for(var k in options){
				if(k == 'dataKey')continue;
				if(options[k])this.searchParams[k] = options[k];
			}
			console.log('this.searchParams',this.searchParams);
			this.loadData();
		},
		onReachBottom() {
			this.showLoadMore = true;
			this.loadData(this.currentPage);
		},
		onPullDownRefresh() {
			console.log('onPullDownRefresh');
			this.loadData();
		},
		onUnload() {
			// console.log('注销 eventSelect');
			this.$eventHub.$off('eventSelect');

			// this.max = 0,
			this.rows = [],
			this.loadMoreText = "加载更多",
			this.showLoadMore = false;
		},
		methods: {
			confirmSearch(){
				this.currentPage = 1;
				// this.rows = [];
				this.loadData();
			},
			//切换顶部模态窗的可见状态
			toggleTopModel() {
				// this.showMore = false;
				this.showFilterModal = false;
				this.showTopModal = (this.showTopModal+1)%2;
			},
			//切换筛选窗窗口的可见
			toggleFilterModel() {
				// this.showMore = false;
				this.showTopModal = false;
				this.showFilterModal = (this.showFilterModal+1)%2;
			},
			//调用某个函数,
			//如果在模板中使用@click="item.handle",在app中编译不通过，app中需要显式声明函数
			call(fn) {
				fn();
				this.showMore =false;
			},
			loadData(pageIndex) {
				pageIndex = pageIndex||1;
				uni.showLoading({
					mask:true
				});

				var url = formatData.httpUrl();
				var param={page:pageIndex};
				param['method'] = 'uni.pop.get.list';
				param['dataKey'] = this.dataKey;
				// param['searchKey'] = this.searchKey;
				this.searchParams.key = this.searchKey || '';
				param['searchParams'] = this.searchParams;
				formatData.set(param);
				// console.log('param',param);
				uni.request({
					url:url,
					data:param,
					success: (res) => {
						var result = res.data.data;
						console.log(result);
						if(result.pageCount == 0) {
							this.loadMoreText = "已经到底了";
						}

						this.params = result.params;
						this.title = result.params.title;
						if(!this.params.showFiled){
							this.caclFiledRow();
						}

						this.currentPage += 1;
						if(pageIndex==1) {
							this.rows = result.row;
						} else {
							this.rows = this.rows.concat(result.row);
						}
						// console.log(this.rows);
						if(this.rows.length == 0){
							this.showLoadMore = false;
							this.nothing = true;
						}else{
							this.nothing = false;
						}
					},
					complete(res) {
						//隐藏载入效果
						uni.hideLoading();
						uni.stopPullDownRefresh();
					},
				});
			},
			//计算应该显示几行数据
			caclFiledRow(){
				var _row = [];
				var _tmp = {};
				var index = 1;
				var _len = Object.keys(this.params.show).length;

				for(var k in this.params.show){
					index++;
					if(k == this.params.text){
						continue;
					}
					var item = this.params.show[k];
					//转为object
					if(typeof(item) != 'object'){
						item = {text:item};
					}
					//判断是否有width属性
					if(item.hasOwnProperty('width')){
						if(Object.keys(_tmp).length > 0){
							_row.push(_tmp);
							_tmp = {};

							_row.push({[k]:item.text});

						}else{
							_row.push({[k]:item.text});
						}
					}else{
						//加如到每行的数组中
						_tmp[k] = item.text;
					}

					//加入到新行中
					if(Object.keys(_tmp).length > 1 || index > _len){
						_row.push(_tmp);
						_tmp = {};
					}
				}
				this.params.showFiled = _row;
				// console.log('this.params.showFiled:',this.params.showFiled);
			},
			itemClick() {
				var currentRow = this.rows[arguments[1]];
				//注意，返回的数据中必须有text和value属性，组件需要的，后面的data是回传用的参数
				var _keyVal = this.params.value || 'id';
				var _keyText = this.params.text || 'showText';
				this.$eventHub.$emit('eventSelect',{value:currentRow[_keyVal],text:currentRow[_keyText],data:currentRow});
				// this.$eventHub.$off('eventSelect');
				uni.navigateBack({
					delta: 1
				});
			},
			clearClick(){
				this.$eventHub.$emit('eventSelect',{value:'',text:'',data:{}});
				// this.$eventHub.$off('eventSelect');
				uni.navigateBack({
					delta: 1
				});
			}
		}
	}
</script>

<style>

</style>
