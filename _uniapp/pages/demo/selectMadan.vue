<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">选择码单</block>
			<view class="action" slot="right" @click="checkAll">
				<text class="cuIcon-roundcheckfill" ></text>全选
			</view>
		</cu-custom>
		<!-- 码单明细 批号输入，物料在弹窗中选择或者扫描-->
		<view name='mingxi' v-for="(item,index) in rowset.madan" :key="index">
			<view class="cu-bar solid-bottom margin-top" style="background-color: #dddddd" >
				<view class="action">
					<text class="cuIcon-titles text-orange"></text> 订单明细{{index+1}}（可动态增删）
				</view>
				<checkbox-group class="block" @change="CheckboxChange(arguments[0],index)">
					<!-- #ifndef MP-ALIPAY -->
					<view class="action">
						<checkbox class='round' :class="item.checked?'checked':''" :checked="item.checked?true:false" value=""></checkbox>
					</view>
					<!-- #endif -->
				</checkbox-group>
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
			//虚拟数据
			this.rowset.madan = [
				{id:1,pihao:'aaa',cnt:1.23,checked:true},
				{id:2,pihao:'bbb',cnt:2.3,checked:true},
				{id:3,pihao:'bbb',cnt:2.3,checked:false},
			];
			console.log(option);
			//加载码单页面
			if(option.mJson){
				let _params = JSON.parse(option.mJson);
				console.log('码单加载',_params);
				let madan = this.rowset.madan;
				for (var i = 0, lenI = madan.length; i < lenI; ++i) {
					madan[i].checked = false;
					for (var j = 0, lenJ = _params.length; j < lenJ; ++j) {
						if (madan[i].id == _params[j].id) {
							madan[i].checked = true;
							break
						}
					}
				}
				// console.log('madan',madan);
				this.rowset.madan = madan;
			}
		},
		methods: {
			confirmClick(e) {
				var currentRow = this.rowset.madan;
				var result = [];
				var hj = 0;
				for (var i = 0, lenI = currentRow.length; i < lenI; ++i) {
					if(currentRow[i].checked == true){
						result.push(currentRow[i]);
						hj+=currentRow[i].cnt;
					}
				}
				var backText =  '已选'+result.length+'卷,共'+hj+'米';
				//注意，返回的数据中必须有text和value属性，组件需要的，后面的data是回传用的参数
				this.$eventHub.$emit('eventSelect',{value:'',text:backText,data:JSON.stringify(result)});
				uni.navigateBack({
					delta: 1
				});
			},
			CheckboxChange(e,index) {
				var items = this.rowset.madan,
					values = e.detail.value;
				var oldStatus = items[index].checked;
				if(oldStatus){
					this.rowset.madan[index].checked = false;
				}else{
					this.rowset.madan[index].checked = true;
				}
			},
			checkAll(){
				var currentRow = this.rowset.madan;
				for (var i = 0, lenI = currentRow.length; i < lenI; ++i) {
					currentRow[i].checked = true;
				}
			}
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
