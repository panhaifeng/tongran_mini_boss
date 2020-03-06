<!-- 注意uni-app暂不支持v-model -->
<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">表单</block>
		</cu-custom>
		<form @submit="submitForm">
			<!-- 普通文本框 支持v-model-->
			<view class="cu-form-group  margin-top">
				<view class="title">输入框带图标</view>
				<input placeholder="两字短标题" v-model="formData.input"></input>
				<text class='cuIcon-locationfill text-orange'></text>
			</view>
			<view class="cu-form-group  margin-top">
				<view class="title">数字输入框</view>
				<input placeholder="只能输入数字"  type="number"></input>
			</view>
			
			<!-- 输入提示+清除-->
			<view class="cu-form-group">
				<view class="title">输入提示+清除</view>
				<input placeholder="todo"></input>
				<text class='cuIcon-locationfill text-orange'></text>
			</view>
			
			<!-- 可选可输-->
			<view class="cu-form-group">
				<view class="title">可选可输效果</view>
				<input placeholder="todo"></input>
				<text class='cuIcon-locationfill text-orange'></text>
			</view>
			
			<!-- 日期 -->
			<view class="cu-form-group">
				<view class="title">入库日期</view>
				<picker mode="date" :value="formData.date" start="2015-09-01" end="2020-09-01" @change="DateChange">
					<view class="picker">
						{{formData.date}}
					</view>
				</picker>
			</view>
			<!-- 使用自定义组件的日期，代码更简单 -->
			
			<!-- picker 不支持v-model -->
			<view class="cu-form-group ">
				<view class="title">库位选择</view>
				<picker @change="PickerChange" :value="formData.kuwei" :range="arrayKuwei">
					<view class="picker">
						{{formData.kuwei>-1?arrayKuwei[formData.kuwei]:'无效值'}}
					</view>
				</picker>
			</view>	
			
			<!-- 开关 不支持v-model-->
			<!-- 但是这个开关的class和checked写法代码有点冗余，需要改进 -->
			<view class="cu-form-group">
				<view class="title">开关选择</view>
				<switch @change="kaiguanChange" :class="formData.switchA?'checked':''" :checked="formData.switchA?true:false"></switch>
			</view>
			
			<!-- 单选 不支持v-model-->
			<!-- class和checked的写法有点啰嗦，需要改进 todo -->
			<radio-group class="block" @change="RadioChange">
				<view class="cu-form-group">
					<view class="title">单选操作(radio)</view>
					<radio :class="formData.radio=='A'?'checked':''" :checked="formData.radio=='A'?true:false" value="A"></radio>
				</view>
				<view class="cu-form-group">
					<view class="title">单选操作</view>
					<radio :class="formData.radio=='B'?'checked':''" :checked="formData.radio=='B'?true:false" value="B"></radio>
				</view>
				
				<checkbox-group class="block" @change="CheckboxChange">
					<view class="cu-form-group margin-top">
						<view class="title">复选选操作(checkbox)</view>
						<checkbox :class="checkbox[0].checked?'checked':''" :checked="checkbox[0].checked?true:false" value="A"></checkbox>
					</view>
					<!-- #ifndef MP-ALIPAY -->
					<view class="cu-form-group">
						<view class="title">定义形状</view>
						<checkbox class='round' :class="checkbox[1].checked?'checked':''" :checked="checkbox[1].checked?true:false" value="B"></checkbox>
					</view>
					<view class="cu-form-group">
						<view class="title">定义颜色</view>
						<checkbox class='round blue' :class="checkbox[2].checked?'checked':''" :checked="checkbox[2].checked?true:false"
						 value="C"></checkbox>
					</view>
					<!-- #endif -->
				</checkbox-group>
			</radio-group>
			
			<!-- 备注 -->
			<view class="cu-form-group margin-top">
				<textarea maxlength="-1" v-model="formData.memo" placeholder="多行文本输入框"></textarea>
			</view>
			
			<!-- 单个客户客户选择 新窗口选择-->	
			<view class="margin-top">
				<picker-popup url="../../pages/demo/list4select" value='客户id' title='单客户选择' @change="changeClient"></picker-popup>
				<picker-popup url="../../pages/demo/list4select" value='客户id' title='多客户选择 todo' @change="changeClient"></picker-popup>
			</view>
			
			<!-- 产品明细 批号输入，物料在弹窗中选择或者扫描-->
			<view name='mingxi' v-for="(mingxi,index) in formData.mingxi" :key="index">
				<view class="cu-bar solid-bottom margin-top" style="background-color: #dddddd" >
					<view class="action">
						<text class="cuIcon-titles text-orange"></text> 订单明细{{index+1}}（可动态增删）
					</view>
					<view class="action" @click="removeMingxi(index)">
						<text class="cuIcon-roundclose text-xs text-blue"></text>删除
					</view>
				</view>
				<view class="cu-form-group">
					<view class="title">批号</view>
					<input placeholder="字母数字" v-model="mingxi.pihao"></input>
				</view>			
				<picker-popup 
					url="../../pages/demo/list4select" 
					:value="mingxi.productId" 
					title="产品"
					:text1="mingxi.proInfo?mingxi.proInfo.compName:''"
					:text="mingxi.proInfo?mingxi.proInfo.compName:''"
					:index="index"
					@change="changePro"
					></picker-popup>
				<picker-popup
					:url="mdListUrl" 
					:value="mingxi.madan" 
					title="输入码单"
					:index="index"
					:param="mingxi.madan"
					@change="changeMadan"
					></picker-popup>
				<picker-popup
					:url="mdselectUrl" 
					:value="mingxi.madan2" 
					title="选择码单"
					:index="index"
					:param="mingxi.madan2"
					@change="changeMadan2"
					></picker-popup>
				<view class="cu-form-group">
					<view class="title">单价</view>
					<input placeholder="数字" v-model="mingxi.danjia"></input>
				</view>	
							
				<!-- <view class="cu-form-group">
					<view class="title">mingxi</view>
					<text @click="dump(mingxi);$forceUpdate()">{{mingxi.proInfo}}{{mingxi.danjia}}</text>
				</view>	 -->
			</view>
			
			<view class="cu-form-group margin-top" @click="appendMingxi">
				<view class="flex-sub text-center">					
					<view class="padding text-blue">
						<text class="cuIcon-add text-xs"></text>新增明细
					</view>
				</view>
			</view>
			
			<!-- 图片上传 -->
			<view class="cu-bar bg-white margin-top">
				<view class="action">
					图片上传
				</view>
				<view class="action">
					0/4
				</view>
			</view>
			<view class="cu-form-group">
				<view class="grid col-4 grid-square flex-sub">
					<view 
						class="bg-img" 
						v-for="(item,index) in imgList" 
						:key="index" 
						@tap="ViewImage" 
						:data-url="imgList[index]">
						<image :src="imgList[index]" mode="aspectFill"></image>
						<view class="cu-tag bg-red" @tap.stop="DelImg" :data-index="index">
							<text class='cuIcon-close'></text>
						</view>
					</view>
					<view class="solids" @tap="ChooseImage" v-if="imgList.length<4">
						<text class='cuIcon-cameraadd'></text>
					</view>
				</view>
			</view>
			<!-- 列表（不包括搜索条件）， -->
			<!-- checkbox，和客户选择一样 暂不考虑-->
			<!-- 表单验证 -->
			<!-- 保存按钮放到导航栏中，或者固定底部 -->
			
			<view class="padding flex flex-direction">
				<button class="cu-btn bg-blue margin-tb-sm lg" form-type="submit">submit</button>
			</view>
		</form>
	</view>
</template>

<script>	
	import pickerPopup from "@/components/picker-pop.vue"
	export default {
		components: {pickerPopup},
		data() {
			return {
				formData:{
					input :'文本框输入值',
					date: '2018-12-25',
					kuwei: 0,
					switchA :true,
					radio : 'B',
					memo :'',
					client:null,
					mingxi : [
						// {id:1,pihao:'aaa',productId:1,proInfo:{compName:'螺丝1'},danjia:1.23,madan:'1234'},
						// {id:2,pihao:'bbb',productId:2,proInfo:{compName:'螺帽2'},danjia:2.3},
						// {id:3,pihao:'bbb',productId:3,proInfo:{compName:'螺帽3'},danjia:2.3},
					]
				},	
				imgList: [],
				mdListUrl :'../../pages/demo/listMadan',
				mdselectUrl:'../../pages/demo/selectMadan',
				//库位选项
				arrayKuwei :　['A01','A02','B01'],	
				checkbox: [{
					value: 'A',
					checked: true
				}, {
					value: 'B',
					checked: true
				}, {
					value: 'C',
					checked: false
				}],
			}
		},
		watch:{
			// formData:{
			// 	deep:true
			// }
		},
		onShow() {
			// console.log('show')
			// this.formData.mingxi.splice(0,1);
		},
		methods: {
			test(e) {
				console.log(e)
			},
			//选择照片
			ChooseImage() {
				uni.chooseImage({
					count: 4, //默认9
					sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
					sourceType: ['album'], //从相册选择
					success: (res) => {
						if (this.imgList.length != 0) {
							this.imgList = this.imgList.concat(res.tempFilePaths)
						} else {
							this.imgList = res.tempFilePaths
						}
					}
				});
			},
			//预览照片
			ViewImage(e) {
				uni.previewImage({
					urls: this.imgList,
					current: e.currentTarget.dataset.url
				});
			},
			//删除照片
			DelImg(e) {
				uni.showModal({
					// title: '召唤师',
					content: '确定要删除吗？',
					cancelText: '取消',
					confirmText: '确认',
					success: res => {
						if (res.confirm) {
							this.imgList.splice(e.currentTarget.dataset.index, 1)
						}
					}
				})
			},
			//删除产品明细
			removeMingxi(index) {
				if(index<0) return false;
				if(index>this.formData.mingxi.length-1) return false;
				this.formData.mingxi.splice(index,1);
				// console.log("删除后",this.formData.mingxi)
				// this.$forceUpdate(); 
			},
			appendMingxi() {
				this.formData.mingxi.push({});
				
				//选择码单的页面可能会带参数过去，用来加载数据
				let url = '../../pages/demo/selectMadan';
				let params = {  
					proId: 111  
				}  
				let result =this.$navTo.formate(url,params);
				this.mdselectUrl = result;
			},
			//明细中产品选项变化
			//注意这里只能接收一个参数，如果子组件要传递多个参数，需要瓶装成一个对象后传回
			changePro(res) {	
				// console.log(arguments);
				// var proInfo = newData[0];
				var proInfo = res.data; 
				var index = res.index;
				this.formData.mingxi[index].proInfo = proInfo;
				// this.$forceUpdate(); 
				// console.log(`第${index}个产品改变，选中产品:`,proInfo)
				// console.log('当前产品明细',this.formData.mingxi)
			},
			//选择码单 发生变化
			changeMadan(res){
				var madanJson = res.data;
				var index = res.index;
				this.formData.mingxi[index].madan = madanJson;
				
				let url = '../../pages/demo/listMadan';
				let params = {  
					mJson:madanJson
				}  
				let result =this.$navTo.formate(url,params);
				this.mdListUrl = result;
			},
			//选择码单 发生变化
			changeMadan2(res){
				var madanJson = res.data;
				var index = res.index;
				this.formData.mingxi[index].madan2 = madanJson;
				let url = '../../pages/demo/selectMadan';
				let params = {  
					mJson:madanJson
				}  
				let result =this.$navTo.formate(url,params);
				this.mdselectUrl = result;
			},
			//客户选择改变
			changeClient(data) {
				// this.formData.
				// var client = data.data;
				this.formData.client = data.data.compName;
				console.log('客户选择改变',data);
			},
			submitForm() {
				console.log('表单数据',this.formData);
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
			//客户选择点击
			handleClick:function(e) {
				console.log(e.target);
			},
			//日期选择改变，
			//todo ，后期改为自定义组件，使得代码调用更方便
			DateChange(e) {
				this.formData.date = e.detail.value
			},
			PickerChange(e) {				
				this.formData.kuwei = e.detail.value;
				// console.log(this.kuwei);
			},
			kaiguanChange(e) {
				this.formData.switchA = e.detail.value
			},
			RadioChange(e) {
				this.formData.radio = e.detail.value;
			},
			CheckboxChange(e) {
				var items = this.checkbox,
					values = e.detail.value;
				for (var i = 0, lenI = items.length; i < lenI; ++i) {
					items[i].checked = false;
					for (var j = 0, lenJ = values.length; j < lenJ; ++j) {
						if (items[i].value == values[j]) {
							items[i].checked = true;
							break
						}
					}
				}
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
