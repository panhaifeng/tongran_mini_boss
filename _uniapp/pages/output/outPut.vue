<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBackHome="true">
			<block slot="backText">返回</block>
			<block slot="content">{{title}}</block>
		</cu-custom>
		
		<view class="cu-form-group">
			<view class="title">日期选择</view>
			<picker mode="date" :value="formData.dateInput" start="2015-09-01" end="2020-09-01" @change="DateChange">
				<view class="picker">
					{{formData.dateInput}}
				</view>
			</picker>
		</view>
		<view class="cu-form-group ">
			<view class="title">报工人员</view>
			<input placeholder="报工人员" v-model="formData.peopleText" disabled="true"></input>
			<button @tap="choosePeople" class="cu-btn bg-green margin-tb-sm " >选择</button>
		</view>
		<view class="cu-form-group ">
			<view class="title">系统缸号</view>
			<input placeholder="系统缸号" v-model="formData.ganghao" disabled="true"></input>
		</view>
		<view class="cu-form-group ">
			<view class="title">物理缸号</view>
			<input placeholder="物理缸号" v-model="formData.luojiGh" disabled="true"></input>
		</view>
		<view class="cu-form-group ">
			<view class="title">纱支规格</view>
			<input placeholder="纱支规格" v-model="formData.guige" disabled="true"></input>
		</view>
		<view class="cu-form-group ">
			<view class="title">颜色</view>
			<input placeholder="颜色" v-model="formData.color" disabled="true"></input>
		</view>
		
		<view class="cu-form-group">
			<view class="title">染色类型</view>
			<picker @change="rsChange" :value="formData.rs" :range="rsModel">
				<view class="picker">
					{{formData.rs>-1?rsModel[formData.rs]:'无效值'}}
				</view>
			</picker>
		</view>
		<view class="cu-form-group ">
			<view class="title">选择工序</view>
			<input placeholder="工序" v-model="formData.gongxuText" disabled="true"></input>
			<button @tap="chooseGx" class="cu-btn bg-blue margin-tb-sm" >选择</button>
		</view>
		<view class="cu-form-group">
			<view class="title">是否完成</view>
			<picker @change="isOverChange" :value="formData.isOver" :range="overModel">
				<view class="picker">
					{{formData.isOver>-1?overModel[formData.isOver]:'无效值'}}
				</view>
			</picker>
		</view>
		
		<!-- 工序的Model -->
		<view class="cu-modal" :class="showModelGx?'show':''">
			<view class="cu-dialog">
				<view class="cu-bar bg-white" v-if="gxModel.length > 0">
					<view class="action">
						<text class="cuIcon-title text-blue"></text>工序信息
					</view>
					<view class="action"></view>
				</view>
				<view class="cu-list bg-white" v-if="gxModel.length > 0">
					<view class="grid col-3 padding-sm">
						<view v-for="(item,index) in gxModel" class="padding-xs" :key="index">
							<button class="cu-btn orange text-df block" :class="item.checked?'bg-orange':'line-orange'" @tap="ChooseCheckbox"
							 :data-value="item.value" :disabled="item.disabled?true:false"> {{item.text}}
							</button>
						</view>
					</view>
				</view>
				<view class="padding">
					<view class="uni-btn-v uni-common-mt uni-form-button margin-top">
						<button class="btn-submit" @tap="hideModal" type="primary">确认</button>
					</view>
				</view>
			</view>
		</view>
		<!-- 报工人员的Model -->
		<view class="cu-modal" :class="showModelPeople?'show':''">
			<view class="cu-dialog">
				<view class="cu-bar bg-white" v-if="peopleModel.length > 0">
					<view class="action">
						<text class="cuIcon-title text-blue"></text>人员信息
					</view>
					<view class="action"></view>
				</view>
				<view class="cu-list bg-white" v-if="peopleModel.length > 0">
					<view class="grid col-3 padding-sm">
						<view v-for="(item,index) in peopleModel" class="padding-xs" :key="index">
							<button class="cu-btn orange text-df block" :class="item.checked?'bg-orange':'line-orange'" @tap="ChoosePeople"
							 :data-value="item.value"> {{item.text}}
							</button>
						</view>
					</view>
				</view>
				<view class="padding">
					<view class="uni-btn-v uni-common-mt uni-form-button margin-top">
						<button class="btn-submit" @tap="hideModalPeople" type="primary">确认</button>
					</view>
				</view>
			</view>
		</view>
		

		

	
		<view class="margin-bottom" style="height: 45px;"></view>
		<!-- 操作栏 -->
		<view class="cu-bar tabbar bg-white shadow foot">
			<!-- <view class="action text-blue">
				<view class="cuIcon-delete"></view> 清空
			</view> -->
			<view class="action text-gray" @tap="confirmForm">
				<view class="cuIcon-check"></view> 提交
			</view>
			<view class="action text-gray add-action" @tap="scanSample">
				<button class="cu-btn cuIcon-scan bg-blue shadow"></button>
				扫码
			</view>
			<view class="action text-gray" @tap="addSample">
				<view class="cuIcon-add">
					<!-- <view class="cu-tag badge">99</view> -->
				</view>
				手输条码
			</view>
		</view>
		<!-- 手输样品条码的弹框 -->
		<view class="cu-modal" :class="showModelBarCode?'show':''">
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">输入条码</view>
					<view class="action" @tap="hideBarCodeModal">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view class="padding">
					<view class="cu-form-group">
						<view class="title">条码</view>
						<input placeholder="完整条码" v-model="barCode"></input>
					</view>
					<view class="uni-btn-v uni-common-mt uni-form-button margin-top">
						<button class="btn-submit" @tap="confirmAdd" type="primary">确认</button>
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
				title: '产量报工',
				rsModel :　['人棉','全棉','氨纶','麻棉'],
				gxModel :　[],
				peopleModel :　[],
				overModel :　['否','是'],
				showModelGx: false,
				showModelPeople: false,
				showModelBarCode: false,
				showModelProKind:false,
				scanHeight: 0,
				scanStart: false,
				exhibition: {},
				thisPro:{},
				formData: {
					id: '',
					dateInput:formatData.dateSearch().dateTo,
					ganghao: '',
					gangId:0,
					luojiGh: '',
					guige: '',
					color: '',
					rs:0,//染色类型
					gongxuText:'',
					peopleText:'',
					isOver:0,
					gxArr:[],
					peopleArr:[],
				},
				listTouchStart: 0,
				listTouchStartY: 0,
				listTouchDirection: null,
				modalName: '',
				barCode: '',
				mYnum: '',
				anmiaton: '-1',
			}
		},
		onLoad(query) {
			var oId = query.oId || 0;
			this.initPage(oId);
		},
		onShow() {

		},
		created() {

		},
		methods: {
			initPage(oId) {
				this.getOutPutPeople();//获取报工人员
				this.getGxList();//获取工序列表
				
				var that = this;
				//获取报工数据
				if (oId) {
					var promise = that.getEditData(oId)
					if (promise && promise.then) {
						promise.then(function(val) {
							that.getGxList('edit');
						})
					}
				}

			},
			DateChange(e) {
				this.formData.date = e.detail.value
			},
			isOverChange(e) {
				this.formData.isOver = e.detail.value
			},
			rsChange(e){
				this.formData.rs = e.detail.value
			},
			//如果是修改，需要获取原来的数据
			getEditData(oId) {
				var that = this;
				var params = {
					method: 'exhibition.sample.detial',
					id: oId,
				};
				formatData.set(params);
				return uni.request({
					url: formatData.httpUrl(),
					data: params,
				}).then(ret => {
					var [error, res] = ret;
					var result = res.data.data;

					that.exhibition = result.exhibition.Exhibition || {};
					that.formData.id = result.exhibition.id || '';
					that.formData.sampleList = result.exhibition.sampleList || '';
					that.formData.memo = result.exhibition.memo || '';
					that.formData.accountNum = result.exhibition.accountNum || '';
					that.formData.accountType = result.exhibition.accountType || '';
					that.formData.Client = result.exhibition.Client || {};
					
					for (let i = 0; i < this.checkRow.length; i++) {
						if (this.checkRow[i].value === result.exhibition.accountType) {
							this.checkRow[i].checked= true;
							break;
						}
					}
					
					return Promise.resolve(result.exhibition);
				});
			},
			chooseGx() {
				if(this.formData.ganghao==''){
					uni.showToast({
						'title':'请先输入缸号!',
						'icon':'none',
					});
					return false;
				}
				this.showModelGx = true;
			},
			hideModal() {
				this.showModelGx = false;
			},
			choosePeople() {
				this.showModelPeople = true;
			},
			hideModalPeople(){
				this.showModelPeople = false;
			},
			hideBarCodeModal() {
				this.showModelBarCode = false;
			},
			// ListTouch触摸开始
			ListTouchStart(e) {
				this.listTouchStart = e.touches[0].pageX;
				this.listTouchStartY = e.touches[0].pageY;
				// console.log('touch start',e);
			},
			// ListTouch计算方向
			ListTouchMove(e) {
				// console.log('touch Move',e);
				let touchLength = e.touches[0].pageX - this.listTouchStart;
				let touchLengthY = e.touches[0].pageY - this.listTouchStartY;

				//如果滑动过程中，高度滑动较高，则跳过
				// console.log('touchLengthY',touchLengthY);
				if (Math.abs(touchLengthY) < 40) {
					if (touchLength > 40) {
						this.listTouchDirection = 'right';
					} else if (touchLength < -40) {
						this.listTouchDirection = 'left';
						// this.modalName = e.currentTarget.dataset.target
					}
				}
			},
			// ListTouch计算滚动
			ListTouchEnd(e) {
				if (this.listTouchDirection == 'left') {
					this.modalName = e.currentTarget.dataset.target
				} else {
					this.modalName = null
				}
				this.listTouchDirection = null
			},
			ChooseCheckbox(e) {
				let items = this.gxModel;
				let values = e.currentTarget.dataset.value;
				let chooseArr = [];
				for (let i = 0; i < items.length; ++i) {
					if (items[i].value == values) {
						items[i].checked = !items[i].checked;
						this.$set(this.gxModel, i, items[i]);
						break
					}
				}
				for (let i = 0; i < items.length; ++i) {
					if(items[i].checked){
						chooseArr.push(items[i].text);
					}
				}
				this.formData.gongxuText = chooseArr.join(", ");
			},
			ChoosePeople(e){
				let items = this.peopleModel;
				let values = e.currentTarget.dataset.value;
				let chooseArr = [];
				for (let i = 0; i < items.length; ++i) {
					if (items[i].value == values) {
						items[i].checked = !items[i].checked;
						this.$set(this.peopleModel, i, items[i]);
						break
					}
				}
				for (let i = 0; i < items.length; ++i) {
					if(items[i].checked){
						chooseArr.push(items[i].text);
					}
				}
				this.formData.peopleText = chooseArr.join(", ");
			},
			scanSample() {
				var that = this;
				uni.scanCode({
					onlyFromCamera: true,
					success: function(res) {
						// console.log('条码类型：' + res.scanType);
						// console.log('条码内容：' + res.result);
						that.getBarCodeSample(res.result);
					},
					fail() {
						uni.showToast({
							title: '条码识别失败',
							icon: 'none'
						})
					}
				});
			},
			addSample() {
				this.showModelBarCode = true;
			},
			confirmAdd() {
				if (this.barCode) {
					this.getBarCodeSample(this.barCode);
				}
				this.showModelBarCode = false;
				this.barCode = '';
			},
			confirmForm() {
				var that = this;
				//验证数据是否都齐全了
				//判断报工人员是否未选
				if(that.formData.peopleText==''){
					uni.showToast({
						'title':'报工人员必填!',
						'icon':'none',
					});
					return false;
				}
				if (!that.formData.ganghao) {
					uni.showToast({
						title: '请先扫码录入缸号',
						icon: 'none',
					});
					return false;
				}
				var gxAllsM = that.gxModel;
				var isgxSel = false;
				for(var aa=0;aa<gxAllsM.length;aa++){
					if(gxAllsM[aa].checked==true){
						isgxSel = true;
					}
				}
				if(!isgxSel){
					uni.showToast({
						title:'请选择工序',
						icon:'none',
					});
					return false;
				}
				uni.showModal({
					title: '提示',
					content: '确认提交数据吗',
					success: function(res) {
						if (res.confirm) {
							console.log('formData',that.formData);
							uni.showLoading({
								title: '提交...'
							});
							that.formData.gxArr = that.gxModel;
							that.formData.peopleArr = that.peopleModel;
							console.log('formData => ',that.formData);
							//开始获取详细
							var params = {
								method: 'uni.output.save',
								formData: JSON.stringify(that.formData),
							};
							formatData.set(params);
							uni.request({
								url: formatData.httpUrl(),
								data: params,
								method: 'POST',
								header: {
									'content-type': 'application/x-www-form-urlencoded'
								},
								success: (res) => {
									var result = res.data.data;
									var succ = result.rsp.success || false;

									if (!succ) {
										uni.showToast({
											title: '提交失败:' + result.rsp.msg,
											icon: 'none'
										})
									} else {
										uni.showToast({
											title: '提交完成',
											mask: true
										});
										uni.navigateTo({
											url:"../../pages/output/outPut"
										})
										//返回首页
										// setTimeout(function() {
										// 	if (that.formData.id > 0) {
										// 		uni.navigateBack({
										// 			delta: 1
										// 		});
										// 	} else {
										// 		uni.reLaunch({
										// 			url: "../../pages/index/index"
										// 		})
										// 	}
										// }, 800);
									}
								},
								complete() {
									uni.hideLoading();
								}
							});
						}
					}
				});
			},
			getBarCodeSample(barCode) {
				var that = this;
				if (!barCode) {
					return false;
				}
				//开始获取详细
				var params = {
					method: 'barcode.detail.get',
					code: barCode,
				};
				formatData.set(params);
				uni.request({
					url: formatData.httpUrl(),
					data: params,
					success: (res) => {
						var result = res.data.data;
						if(result.rsp.success==false){
							uni.showToast({
								title:result.rsp.msg,
								icon:'none'
							})
						}else{
							var detail = result.params.info || {};
							var gongxuNeedAll = result.params.gongxuAll || [];  //该缸号需要显示的所有工序
							var needHideGx = result.params.gongxuInfo || [];
							// console.log(gongxuNeedAll);
							console.log('rrrs',result);
						
							if (!detail.id) {
								
								uni.showToast({
									title: '找不到条码' + barCode,
									icon: 'none'
								})
							} else {
								this.formData.ganghao = barCode;
								this.formData.gangId = detail.id;
								this.formData.luojiGh = detail.vatCode;
								this.formData.guige = detail.wareName;
								this.formData.color = detail.color
								this.formData.rs = detail.leixing;
								//显示需要的工序
								let originGxs = this.gxModel;
								var newAr = [];
								if(gongxuNeedAll.length>0){
									for(var i=0;i<originGxs.length;i++){
									   if(gongxuNeedAll.indexOf(originGxs[i].value)>=0){
										  newAr.push({
											  'value':originGxs[i].value,
											  'text':originGxs[i].text,
										  });
										  // this.$set(originGxs,i,originGxs[i]);
									   }
									}
									this.gxModel = newAr;
								}else{
									originGxs = [];
								}
								//已经报过工的需要不能被选中
								if(needHideGx.length>0){
									for(var j=0;j<this.gxModel.length;j++){
										if(needHideGx.indexOf(this.gxModel[j].value)>=0){
											this.gxModel[j].disabled = true;
											this.$set(this.gxModel, j, this.gxModel[j]);
										}
									}
								}
							}
					  }
					},
					fail() {
						uni.showToast({
							title: '获取条码详情失败',
							icon: 'none'
						})
					}
				});

			},
			getGxList() {
				var that = this;
				var params = {
					method: 'uni.gx.list',
				};
				formatData.set(params);
				uni.request({
					url: formatData.httpUrl(),
					data: params,
					success: (res) => {
						console.log('uni.gx.list',res);
						var result = res.data.data;
						this.gxModel = result.params || [];
						this.gxModelN = this.gxModel;
						//修改的情况下，需要初始化标签选中的值
						if (that.formData.gongxu) {
							var gxValue = that.formData.gongxu.split(',');
							if (gxValue.length > 0) {
								for (let i = 0; i < that.gxModel.length; i++) {
									var Item = that.gxModel[i];
									if (gxValue.indexOf(Item.id) >= 0) {
										Item.checked = true;
										that.$set(that.gxModel, i, Item);
									}
								}
							}
						}
					},
					complete(res) {

					},
				});
			},
			getOutPutPeople() {
				var params = {
					method: 'uni.output.people',
				};
				formatData.set(params);
				uni.request({
					url: formatData.httpUrl(),
					data: params,
					success: (res) => {
						console.log('uni.output.people',res);
						var result = res.data.data;
						this.peopleModel = result.params || [];
					}
				});
			},
			pageScrollTo(selector) {
				let that = this;
				uni.createSelectorQuery()
					.select(selector).boundingClientRect()
					.selectViewport().scrollOffset().exec((ret) => {
						//console.log(ret);
						let toTop = ret[0]['top'] + ret[1]['scrollTop'] - (that.CustomBar || 50);
						if (toTop < 0) {
							toTop = 0;
						}
						uni.pageScrollTo({
							scrollTop: toTop
						})
					});
			},
		}
	}
</script>

<style>
	@import "../../colorui/animation.css";

	.cu-list.menu-avatar>.cu-item .content {
		left: 50px;
		width: calc(100% - 48px - 10px);
	}

	.cu-list.menu-avatar>.cu-item {
		height: 125px;
	}

	.cu-modal {
		z-index: 1000000;
	}

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
		background: -webkit-gradient(linear, left top, left bottom, from(transparent), to(#3e88f1));
		background: -webkit-linear-gradient(top, transparent, #3e88f1);
		background: -moz-linear-gradient(top, transparent, #3e88f1);
		background: linear-gradient(180deg, transparent, #3e88f1);
		position: absolute;
	}
	.content-class {
		width: 90%;
		margin: 20upx auto;
		display: flex;
		flex-flow: row wrap;
		justify-content: space-between;
	}
	
	.content-class .class {
		width: 30%;
		height: 60upx;
		font-size: 28upx;
		line-height: 60upx;
		border-radius: 30upx;
		margin-bottom: 20upx;
		text-align: center;
		box-sizing: border-box;
		border: 1upx solid #3f82e7;
	}
	
	.content-class .on {
		border: none;
		background-color: #3f82e7;
		color: #fff;
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
