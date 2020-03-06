<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">搜索</block>
		</cu-custom>
		
		<form @submit="submitForm">
			<!-- 普通文本框 支持v-model-->
			
			<view class="cu-form-group margin-top">
				<view class="title">开始日期</view>
				<picker 
					mode="date" 
					:value="formData.dateFrom" 
					start="2017-01-01" end="2037-01-01" 
					@change="dateFromChange">
					<view class="picker">
						{{formData.dateFrom}}
					</view>
				</picker>
			</view>
			<view class="cu-form-group">
				<view class="title">结束日期</view>
				<picker 
					mode="date" 
					:value="formData.dateTo" 
					start="2017-01-01" end="2037-01-01" 
					@change="dateToChange">
					<view class="picker">
						{{formData.dateTo}}
					</view>
				</picker>
			</view>
			<view class="cu-form-group">
				<view class="title">缸号</view>
				<input placeholder="缸号" v-model="formData.vatNum"></input>
				<text class='cuIcon-locationfill text-orange'></text>
			</view>
			<view class="cu-form-group">
				<view class="title">订单号</view>
				<input placeholder="订单号" v-model="formData.orderCode"></input>
				<text class='cuIcon-locationfill text-orange'></text>
			</view>
			<!-- 客户选择 新窗口选择-->
			<picker-popup
				title="客户"
				url="/pages/component/list2pop?dataKey=clientList"
				:text="formData.compName"
				@change="changeClient"
			></picker-popup>
			<view class="padding flex flex-direction">
				<button class="cu-btn bg-blue margin-tb-sm lg" form-type="submit">确定</button>
			</view>
		</form>
	</view>
</template>

<script>
	import pickerPopup from "@/components/picker-pop.vue"
	import formatData from '@/common/formData.js';
	import e7Autocomplete from '@/components/e7-autocomplete.vue';
	export default {
		components:{pickerPopup,e7Autocomplete},
		data() {
			return {
				formData:{
					vatNum:'',
					orderCode:'',
					dateFrom:'',
					dateTo:'',
					clientId:0,
					compName:'',
				},
			}
		},
		onLoad(query) {
			var res = JSON.parse(decodeURIComponent(JSON.stringify(query)));
			var dateInfo = formatData.dateSearch();
			this.formData.dateFrom = dateInfo.dateFrom;
			this.formData.dateTo = dateInfo.dateTo;
			console.log('quuu',res);
			for(let params in this.formData){
				if(''!=res[params] && undefined!=res[params] ){
					this.formData[params]= res[params];
				}
			}
		},
		onUnload() {
			this.$eventHub.$off('listenSearch');
		},
		methods: {
			
			// changeClient(data) {
			// 	// console.log(data);
			// 	this.formData.clientId =data.data.id;
			// 	this.formData.compName = data.data.compName;
			// },	
			changeClient(data) {
				console.log(data);
				this.formData.clientId =data.data.cid;
				this.formData.compName = data.data.compName;
			},	
			pickerChange(e) {
				console.log("picker changed",e.detail.value)
				this.formData.kuwei = e.detail.value;
			},
			dateFromChange(e) {
				this.formData.dateFrom = e.detail.value;
			},
			dateToChange(e) {
				this.formData.dateTo = e.detail.value;
			},
			submitForm(e) {
				console.log(this.formData);
				this.handlOk();
			},
			handlOk() {
				var param=this.formData;
				this.$eventHub.$emit('listenSearch',param)
				uni.navigateBack({
					delta:1
				});
			},
		}
	}
</script>

<style>
	input {
		text-align: right;
	}
	/* webkit solution */
	::-webkit-input-placeholder { text-align:right; }
	/* mozilla solution */
	input:-moz-placeholder { text-align:right; }
</style>
