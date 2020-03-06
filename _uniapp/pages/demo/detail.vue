<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">单据详情</block>
			<view class="action" slot="right" >
				<!-- <text class="cuIcon-filter" @click="toggleFilterModel"></text> -->
				<text class="cuIcon-more" @click="toggleTopModel"></text>
			</view>
		</cu-custom>
		
		<view class="cu-list menu sm-border ">
			<view class="cu-item" >
				<view class="content">
					<text class="cuIcon-warn text-green"></text>
					<text class="text-grey">供应商</text>
				</view>
				<view class="action">
					<text class="text-grey text-sm">常州易奇信息科技有限公司</text>
				</view>
			</view>
			<view class="cu-item" >
				<view class="content">
					<text class="cuIcon-warn text-green"></text>
					<text class="text-grey">开单日期</text>
				</view>
				<view class="action">
					<text class="text-grey text-sm">2019年9月24日</text>
				</view>
			</view>
			<view class="cu-item" >
				<view class="content">
					<text class="cuIcon-warn text-green"></text>
					<text class="text-grey">库位</text>
				</view>
				<view class="action">
					<text class="text-grey text-sm">A01</text>
				</view>
			</view>
			<view class="cu-item" >
				<view class="content">
					<text class="cuIcon-warn text-green"></text>
					<text class="text-grey">操作人</text>
				</view>
				<view class="action">
					<text class="text-grey text-sm">曾峰</text>
				</view>
			</view>
				
			
		</view>
		
		<view class="cu-list menu sm-border ">
			<view class="cu-item " >
				<view class="content">
					<text class="text-grey">入库明细(3)</text>
				</view>
				<view class="action">
					<text class="text-grey text-sm">共：100kg</text>
				</view>
			</view>	
			
			<view class="cu-item" v-for="(row,index) in row.products" :key="index">
				<view class="content padding-tb-sm" >
					<view>
						<text class="text-blue margin-right-xs text-left"></text>{{row.proName}}						
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
				
			</view>
		</view>
		
		<!-- 高级功能按钮 -->
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
	</view>
</template>
<script>
	//来自 graceUI 的表单验证， 使用说明见手册 http://grace.hcoder.net/doc/info/73-3.html
	var  graceChecker = require("../../common/graceChecker.js");
	export default {
		data() {			
			return {
				row:{
					products:[
						{proName:'螺丝 10*10',con:'车老师',tel:'137',address:'常州科教城创研港2号1203'},
						{proName:'螺帽 12*12',con:'车老师',tel:'137',address:'常州科教城创研港2号1203'},
						{proName:'螺母 3*3',con:'车老师',tel:'137',address:'常州科教城创研港2号1203'},
					]
				},
				//需要获取导航栏的高度，不同终端下不一样
				topModalStyle:"margin-top:"+ (this.CustomBar+5) + "px;",
				showTopModal:false,
				//单条记录操作按钮组
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
			}
		},
		methods: {
			toggleTopModel() {
				console.log(this.topModalStyle);
				// this.showMore = false;
				// this.showFilterModal = false;
				this.showTopModal = (this.showTopModal+1)%2;
				console.log('toggle,now is',this.showTopModal)
			},
		}
	}
</script>

<style>
</style>
