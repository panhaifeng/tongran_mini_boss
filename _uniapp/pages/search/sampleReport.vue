<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">高级搜索</block>
		</cu-custom>

		<form @submit="submitForm">
			<!-- 普通文本框 支持v-model-->
			<view class="cu-form-group">
				<view class="title">条码</view>
				<input name="code" v-model="formData.code" placeholder="扫码或输入完整条码"></input>
				<button class='cu-btn bg-blue shadow cuIcon-scan' @tap="scanCode"></button>
			</view>
			<view class="cu-form-group">
				<view class="title">关键字</view>
				<input v-model="formData.key" placeholder="产品关键字"></input>
				<!-- <text class='cuIcon-locationfill text-orange'></text> -->
			</view>
			<!-- <view class="cu-form-group">
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
			</view> -->
			<!-- <picker-pop
				title="客户"
				url="/pages/component/list2pop?dataKey=clientList"
				@change="selectClient"
			>
			</picker-pop> -->
			<!-- <picker-pop
				title="产品"
				url="/pages/component/list2product?dataKey=productList"
				@change="selectProduct"
			>
			</picker-pop> -->
			<!-- <view class="cu-form-group">
				<view class="title">样品类型</view>
				<picker :value="typeIndex" :range="arrayType" @change="pickerChangeType">
					<view class="picker">
						{{arrayType[typeIndex] ? arrayType[typeIndex] : '请选择'}}
					</view>
				</picker>
			</view> -->

			<view class="padding flex flex-direction">
				<button class="cu-btn bg-blue margin-tb-sm lg" form-type="submit">开始搜索</button>
			</view>
		</form>
	</view>
</template>

<script>
	import pickerPop from "@/components/picker-pop.vue"
	import formatData from '@/common/formData.js';
	// import autocomplete from '@/components/autocomplete.vue';
	export default {
		components:{pickerPop},
		data() {
			return {
				formData:{
					// clientId:'',
					code:'',
					key:'',
					// dateFrom:'',
					// dateTo:'',
					// productId:'',
					// type:''
				},
				arrayType:[],
				typeIndex:-1,
			}
		},
		onLoad(query) {
			var _this = this;
			var dateInfo = formatData.dateSearch();
			this.formData.dateFrom = dateInfo.dateFrom;
			this.formData.dateTo = dateInfo.dateTo;

			var params = {
				method:'uni.autocomplete.get.data.list',
				dataKey : 'typeList'
			};
			formatData.set(params);
			uni.request({
				url: formatData.httpUrl(), //仅为示例，并非真实接口地址。
				data:params,
				success: (res) => {
					var result = res.data;
					console.log(result);
					_this.arrayType = result.data;
				}
			});
		},
		onUnload() {
			this.$eventHub.$off('listenSearch');
		},
		methods: {
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
			pickerChangeType(res){
				console.log('pickerChangeType => ',res);
				var index = res.detail.value;
				this.typeIndex = index;
				this.formData.type = this.arrayType[index];
			},
			//响应选择事件，接收选中的数据
			selectClient(res) {
				console.log(res.data);
				this.formData.clientId = res.data.cid;
			},
			selectProduct(res){
				console.log('productId',res);
				this.formData.productId = res.data.pid;
			},
			handlOk() {
				var param=this.formData;
				this.$eventHub.$emit('listenSearch',param)
				uni.navigateBack({
					delta:1
				});
			},
			scanCode(e){
				var _this = this;
				uni.scanCode({
				    success: function (res) {
				        console.log('条码类型：' + res.scanType);
				        console.log('条码内容：' + res.result);
						_this.formData.code = res.result;
				    }
				});
			},
			handleProDesc(e){
				this.formData.proDesc = e.detail.value || '';
			},
			handleCode(e){
				this.formData.code = e.detail.value || '';
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
