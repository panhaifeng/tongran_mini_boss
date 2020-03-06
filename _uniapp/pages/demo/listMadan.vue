<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">输入码单</block>
		</cu-custom>
		<!-- 码单明细 批号输入，物料在弹窗中选择或者扫描-->
		<view name='mingxi' v-for="(item,index) in rowset.madan" :key="index">
			<view class="cu-bar solid-bottom margin-top" style="background-color: #dddddd" >
				<view class="action">
					<text class="cuIcon-titles text-orange"></text> 订单明细{{index+1}}（可动态增删）
				</view>
				<view class="action" @click="removeMingxi(index)">
					<text class="cuIcon-roundclose text-xs text-blue"></text>删除
				</view>
			</view>
			<view class="cu-form-group">
				<view class="title">卷号</view>
				<input placeholder="" v-model="item.pihao"></input>
			</view>			
			
			<view class="cu-form-group">
				<view class="title">数量</view>
				<input placeholder="数字" v-model="item.cnt"></input>
			</view>	
			
		</view>
		
		<view class="cu-form-group margin-top" @click="appendMingxi">
			<view class="flex-sub text-center">					
				<view class="padding text-blue">
					<text class="cuIcon-add text-xs"></text>新增明细
				</view>
			</view>
		</view>
		
		<view class="cu-form-group margin-top text-center" @click="confirmClick()">
			<view class="flex-sub text-center">
				确认
			</view>
		</view>
		
	</view>
</template>

<script>	
	import pickerPopup from "@/components/picker-pop.vue"
	export default {
		components: {pickerPopup},
		data() {
			return {
				rowset:{
					madan : [
						/* {id:1,pihao:'aaa',cnt:1.23},
						{id:2,pihao:'bbb',cnt:2.3},
						{id:3,pihao:'bbb',cnt:2.3}, */
					]
				},	
			}
		},
		watch:{
			// rowset:{
			// 	deep:true
			// }
		},
		onShow() {
			// console.log('show')
			// this.rowset.madan.splice(0,1);
		},
		onLoad:function (option){
			//加载码单页面
			if(option.mJson){
				let _params = JSON.parse(option.mJson);
				this.rowset.madan = _params;
				// console.log('_params',_params);
			}
		},
		methods: {
			test(e) {
				debugger;
				console.log(e)
			},
			//删除码单明细
			removeMingxi(index) {
				if(index<0) return false;
				if(index>this.rowset.madan.length-1) return false;
				this.rowset.madan.splice(index,1);
				// console.log("删除后",this.rowset.madan)
				// this.$forceUpdate(); 
			},
			appendMingxi() {
				this.rowset.madan.push({});
			},
			//明细中码单选项变化
			//注意这里只能接收一个参数，如果子组件要传递多个参数，需要瓶装成一个对象后传回
			changeMadan(res) {	
				// console.log(arguments);
				// var proInfo = newData[0];
				var proInfo = res.data; 
				var index = res.index;
				this.rowset.madan[index].proInfo = proInfo;
				// this.$forceUpdate(); 
				// console.log(`第${index}个码单改变，选中码单:`,proInfo)
				// console.log('当前码单明细',this.rowset.madan)
			},
			//客户选择改变
			changeClient(data) {
				// this.rowset.
				// var client = data.data;
				this.rowset.client = data.data;
				console.log('客户选择改变',data);
			},
			submitForm() {
				console.log('表单数据',this.rowset);
				uni.showActionSheet({					
					title:'标题',
					itemList: ['item1', 'item2', 'item3', 'item4'],
					success: (e) => {
						console.log(e.tapIndex);
						uni.showToast({
							title:"点击了第" + e.tapIndex + "个选项",
							icon:"none"
						})
					}
				})
			},
			confirmClick(e) {
				var currentRow = this.rowset.madan;
				//注意，返回的数据中必须有text和value属性，组件需要的，后面的data是回传用的参数
				this.$eventHub.$emit('eventSelect',{value:'',text:'已选'+currentRow.length,data:JSON.stringify(currentRow)});
				uni.navigateBack({
					delta: 1
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
