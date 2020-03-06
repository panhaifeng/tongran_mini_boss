<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">客户索样详细</block>
		</cu-custom>
		<view class="fixed justify-start bg-gradual-pink padding" style="margin-top: -2px;">
			<view class="flex-sub text-center ">
				<view class="flex-sub text-center">
					<text class="text-white" > {{exhibition.name || '展会名称'}}</text>
				</view>
			</view>
		</view>
		<view class="cu-card case">
			<view class="cu-item shadow">
				<view class="image">
					<image class="bg-grey" style="max-height: 150rpx;" src="" mode="aspectFit"></image>
					<view class="cu-bar bg-shadeBottom">
						<view style="width: 90%;">
							<view class="flex text-df">
								<view class="flex-sub margin-right-xs ">客户：{{formData.Client.compName}}</view>
							</view>
							<view class="flex text-df">
								<view class="flex-sub margin-right-xs ">邮箱：{{formData.Client.email}}</view>
							</view>
							<view class="flex text-df">
								<view class="flex-sub margin-right-xs ">联系人：{{formData.Client.contacts}}</view>
								<view class="flex-sub margin-right-xs ">手机：{{formData.Client.mobile}}</view>
							</view>
						</view>
					</view>
				</view>
			</view>
		</view>
		
		<!-- 列表 -->
		<view class="cu-bar bg-white">
			<view class="action">
				<text class="cuIcon-title text-blue"></text>样品列表
			</view>
			<view class="action"></view>
		</view>
		<view class="cu-list menu-avatar">
			<view class="cu-item" v-for="(item,index) in formData.sampleList" :key="index">
				<view class="cu-avatar sm round bg-blue">{{index+1}}</view>
				<view class="content">
					<view class="text-grey">{{item.proCode}}</view>					
					<view class="flex text-df">
						<view class="flex-sub margin-right-xs text-gray">品名：{{item.proName || ''}}</view>
						<view class="flex-sub margin-right-xs text-gray">颜色：{{item.color || ''}}</view>
					</view>
					<view class="flex text-df">
						<view class="flex-sub margin-right-xs text-gray">门幅：{{item.menfu || ''}}</view>
						<view class="flex-sub margin-right-xs text-gray">克重：{{item.kezhong || ''}}</view>
					</view>
					<view class="flex text-df">
						<view class="flex-sub margin-right-xs text-gray">成分：{{item.chengfen || ''}}</view>						
					</view>
					<view class="flex text-df">
						<view class="flex-sub margin-right-xs text-gray">规格：{{item.guige || ''}}</view>
					</view>
					<view class="flex text-df">
						<view class="flex-sub margin-right-xs text-gray">寄样情况：{{item.is_issue || ''}}</view>
					</view>
				</view>
				<!-- <view class="action">
					<view class="text-grey text-xs">22:20</view>
					<view class="cu-tag round bg-grey sm">5</view>
				</view> -->
				<view class="move">
					<!-- <view class="bg-grey">置顶</view> -->
					<!-- <view class="bg-red" @tap="deleteSample(index)">删除</view> -->
				</view>
			</view>
			<view class="uni-loadmore text-gray text-center text-df solid-bottom padding" v-if="formData.sampleList.length==0">还没有添加样品数据</view>
		</view>
		
		<!-- 标签 -->
		<view class="cu-bar bg-white" v-if="formData.tips.length > 0">
			<view class="action">
				<text class="cuIcon-title text-blue"></text>客户标签
			</view>
			<view class="action"></view>
		</view>
		<view class="cu-list bg-white" v-if="formData.tips.length > 0">
			<view class="grid col-3 padding-sm">
				<view v-for="(item,index) in formData.tips" class="padding-xs" :key="index">
					<button class="cu-btn orange block" :class="item.checked?'bg-orange':'line-orange'"> {{item.tip}}
					</button>
				</view>
			</view>
		</view>
		<!-- 备注 -->
		<view class="cu-bar bg-white">
			<view class="action">
				<text class="cuIcon-title text-blue"></text>备注
			</view>
			<view class="action"></view>
		</view>
		<view class="cu-form-group">
			<input placeholder="其他说明" :disabled="true" v-model="formData.memo"></input>
		</view>
	</view>
</template>

<script>
	import formatData from '@/common/formData.js';
	export default {
		data() {
			return {
				formType:'',
				editType:'',
				showModelClient:false,
				showModelBarCode:false,
				scanHeight:0,
				scanStart :false,
				exhibition:{},	
				clientReadonly:false,
				formData:{
					id:'',
					sampleList:[],
					memo:'',
					tips:[],
					Client:{
						id:'',
						compName:'',
						contacts:'',
						tel:'',
						mobile:'',
						email:'',
						address:'',
						aiResult:'',
					},
				},
				listTouchStart: 0,
				listTouchStartY: 0,
				listTouchDirection: null,
				modalName:'',
				barCode:'',
			}
		},
		onLoad(query) {
			var that = this;
			if(query.exid){
				var promise = that.initExh(query.exid)
				if(promise && promise.then){
					promise.then(function(val){
						that.getClientTips();
					})
				}
			}else{
				//获取客户标签数据
				that.getClientTips();
			}
		},
		onShow() {
			
		},
		created(){
			
		},
		methods: {
			initExh (id){
				var that = this;
				var params = {
					method:'exhibition.sample.detial',
					id:id,
				};
				formatData.set(params);
				return uni.request({
							url: formatData.httpUrl(),
							data:params,
						}).then(ret => {
							var [error ,res] = ret;
							var result = res.data.data;
							result.exhibition.tips = [];
							that.formData = result.exhibition || {};
							that.exhibition = result.exhibition.Exhibition || {};
							// that.formData.tips = [];
							
							return Promise.resolve(result.exhibition);
						});
			},			
			getClientTips() {
				var that = this;
				var params = {
					method:'client.tip.list',
				};
				formatData.set(params);
				uni.request({
					url: formatData.httpUrl(),
					data:params,
					success: (res) => {
						var result = res.data.data;
						that.formData.tips = result.tips || [];
						var tips = that.formData.Client.tip.split(',');
						// console.log(tips);
						if(tips.length > 0){
							for(let i=0;i<that.formData.tips.length;i++){
								var Item = that.formData.tips[i];
								// console.log(i,tips.indexOf(Item.id));
								if(tips.indexOf(Item.id) >= 0){
									Item.checked = true;
									that.$set(that.formData.tips ,i ,Item);
								}
							}
						}
					}
				});
			},
		}
	}
</script>

<style>
.cu-list.menu-avatar>.cu-item .content{left: 50px;width: calc(100% - 48px - 10px);}
.cu-list.menu-avatar>.cu-item{height: 140px;}
.cu-modal{z-index: 1000000;}
.tech-recognition-scan {	
    position: absolute;
	z-index: 7;
	top: 0;
	left: 0;
	width: 100%;
	border-bottom: 3px solid #3e88f1;
	-webkit-animation: scan 1.4s infinite;
	-moz-animation: scan 1.4s infinite;
	animation: scan 1.4s infinite;
	background: -webkit-gradient(linear,left top,left bottom,from(transparent),to(#3e88f1));
	background: -webkit-linear-gradient(top,transparent,#3e88f1);
	background: -moz-linear-gradient(top,transparent,#3e88f1);
	background: linear-gradient(180deg,transparent,#3e88f1);
	position: absolute;
}
@keyframes scan {
	0% {
		height: 0;
	}

	to {
		opacity: 0;
		height: 400rpx;
	}
}
</style>
