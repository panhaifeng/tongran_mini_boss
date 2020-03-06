<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{title}}</block>
		</cu-custom>
		<view class="fixed justify-start bg-gradual-pink padding" style="margin-top: -2px;">
			<view class="flex-sub text-center ">
				<view class="flex-sub text-center">
					<text class="text-white text-df"> {{exhibition.name || '请首页先选择展会 [录入数据无效]'}}</text>
				</view>
			</view>
		</view>
		<view class="cu-card case" v-if="!formType">
			<view class="cu-item shadow ">
				<view class="image">
					<view v-if="scanHeight > 0" class="tech-recognition-scan"></view>
					<image class="bg-grey" style="max-height: 400rpx;" :src="formData.imagePathLocal ? formData.imagePathLocal : formData.imagePath"
					 mode="aspectFit" @tap="chooseImage"></image>
					<view class="cu-tag bg-blue" @tap="chooseImage">拍名片</view>
					<view class="cu-bar bg-shadeBottom" @tap="editClientInfo">
						<text class="cuIcon-edit" style="margin-right: 6px;"></text>
						<view style="width: 90%;" v-if="formData.Client.compName">
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
						<text v-else>可以输入客户信息</text>
					</view>
				</view>
			</view>
			<view class="cu-tag text-grey padding-left text-df">索样清单给予客户方式</view>
			<view class="grid col-3" v-if="formData.clientPrint.length > 0">
				<view v-for="(item,index) in formData.clientPrint" class="padding" :key="index">
					<button class="cu-btn orange block text-df" :class="item.checked?'bg-orange':'line-orange'" @tap="ChoosePrintType"
					 :data-value="item.value"> {{item.text}}
					</button>
				</view>
			</view>
		</view>
		<view class="cu-card case" v-else>
			<view class="cu-item shadow ">
				<view class="image">
					<view v-if="scanHeight > 0" class="tech-recognition-scan"></view>
					<image class="bg-grey" style="max-height: 200rpx;" src="" mode="aspectFit" @tap="chooseClient"></image>
					<view class="cu-tag bg-blue" @tap="chooseClient">选择客户</view>
					<view class="cu-bar bg-shadeBottom" @tap="editClientInfo">
						<text class="cuIcon-edit" style="margin-right: 6px;"></text>
						<view style="width: 90%;" v-if="formData.Client.compName">
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
						<text v-else>可以编辑客户信息</text>
					</view>
				</view>
			</view>
			<view class="cu-tag text-grey padding-left text-df">索样清单给予客户方式</view>
			<view class="grid col-3" v-if="formData.clientPrint.length > 0">
				<view v-for="(item,index) in formData.clientPrint" class="padding" :key="index">
					<button class="cu-btn orange block text-df" :class="item.checked?'bg-orange':'line-orange'" @tap="ChoosePrintType"
					 :data-value="item.value"> {{item.text}}
					</button>
				</view>
			</view>
		</view>

		<!-- 修改的Model -->
		<view class="cu-modal" :class="showModelClient?'show':''">
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">客户信息</view>
					<view class="action" @tap="hideModal">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view class="padding">
					<view class="cu-form-group">
						<view class="title">客户名称</view>
						<input placeholder="客户名称" v-model="formData.Client.compName" :disabled="clientReadonly"></input>
					</view>
					<view class="cu-form-group">
						<view class="title">联系人</view>
						<input placeholder="联系人" v-model="formData.Client.contacts"></input>
					</view>
					<view class="cu-form-group">
						<view class="title">手机</view>
						<input placeholder="手机" v-model="formData.Client.mobile"></input>
					</view>
					<view class="cu-form-group">
						<view class="title">电话</view>
						<input placeholder="电话" v-model="formData.Client.tel"></input>
					</view>
					<view class="cu-form-group">
						<view class="title">邮箱</view>
						<input placeholder="邮箱" v-model="formData.Client.email"></input>
					</view>
					<view class="cu-form-group">
						<view class="title">地址</view>
						<input placeholder="地址" v-model="formData.Client.address"></input>
					</view>
					<view class="uni-btn-v uni-common-mt uni-form-button margin-top">
						<button class="btn-submit" @tap="hideModal" type="primary">确认</button>
					</view>
				</view>
			</view>
		</view>

		<!-- 列表 -->
		<view class="cu-bar bg-white">
			<view class="action">
				<text class="cuIcon-title text-blue"></text>样品列表(左滑删除)
			</view>
			<view class="action"></view>
		</view>
		<view class="cu-list menu-avatar">
			<view v-for="(item,index) in formData.sampleList" class="cu-item" :id="'sampleItem_'+index" :class="[modalName=='move-box-'+ index?'move-cur':'',anmiaton==index?'animation-slide-right':'']"
			 :key="index" @touchstart="ListTouchStart" @touchmove="ListTouchMove" @touchend="ListTouchEnd" :data-target="'move-box-' + index">
				<view class="cu-avatar sm round bg-blue">{{index+1}}</view>
				<view class="content">
					<view class="text-grey">{{item.proCode}} {{item.selText}} 
						<view v-if="item.mYnum>0" >{{item.mYnum}}</view>
					</view>
					<view class="flex text-df">
						<view class="flex-sub margin-right-xs text-gray">规格：{{item.guige || ''}}</view>
					</view>
					<view class="flex text-df">
						<view class="flex-sub margin-right-xs text-gray">品名：{{item.proName || ''}}</view>
					</view>
					<view class="flex text-df">
						<view class="flex-sub margin-right-xs text-gray">门幅：{{item.menfu || ''}}</view>
						<view class="flex-sub margin-right-xs text-gray">克重：{{item.kezhong || ''}}</view>
					</view>
					<view class="flex text-df">
						<view class="flex-sub margin-right-xs text-gray">颜色：{{item.color || ''}}</view>
						<view class="flex-sub margin-right-xs text-gray">成分：{{item.chengfen || ''}}</view>
					</view>
				</view>
				<!-- <view class="action">
					<view class="text-grey text-xs">22:20</view>
					<view class="cu-tag round bg-grey sm">5</view>
				</view> -->
				<view class="move">
					<!-- <view class="bg-grey">置顶</view> -->
					<view class="bg-green" @tap="editSampleKind(index)">修改</view>
					<view class="bg-red" @tap="deleteSample(index)">删除</view>
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
					<button class="cu-btn orange text-df block" :class="item.checked?'bg-orange':'line-orange'" @tap="ChooseCheckbox"
					 :data-value="item.id"> {{item.tip}}
					</button>
				</view>
			</view>
		</view>

		<!-- 到付帐号 -->
		<view class="cu-bar bg-white">
			<view class="action">
				<text class="cuIcon-title text-blue"></text>到付帐号
			</view>
			<view class="action"></view>
		</view>
		<radio-group class="block content-class" @change="RadioChange">
			<label class="item" v-for="(item, index) in checkRow" :key="index" :class="{on: item.isChecked}">
				<text class="item-text">{{item.text}}</text>
				<radio :value="item.value" :checked="item.checked" :class="item.checked?'checked':''"></radio>
			</label>
		</radio-group>
		<view class="cu-form-group">
			<input placeholder="到付帐号" v-model="formData.accountNum"></input>
		</view>
		<!-- 备注 -->
		<view class="cu-bar bg-white">
			<view class="action">
				<text class="cuIcon-title text-blue"></text>备注
			</view>
			<view class="action"></view>
		</view>
		<view class="cu-form-group">
			<input placeholder="其他说明" v-model="formData.memo"></input>
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

		<!-- 修改产品类型的弹框 -->
		<view class="cu-modal" :class="showModelProKind?'show':''">
			<view class="cu-dialog">
				<view class="cu-form-group">
					<view class="title">选择类型{{thisPro.index}}</view>
				</view>
				<view class="action">
					<radio-group @change="SetSel">
						<label class="margin-left-sm">
							<radio class="blue radio" value="0" :checked="thisPro.sel==0?true:false"></radio>
							<text class="margin-left-sm"> 挂钩</text>
						</label>
						<label class="margin-left-sm">
							<radio class="blue radio" value="1" :checked="thisPro.sel==1?true:false"></radio>
							<text class="margin-left-sm"> A4</text>
						</label>
						<label class="margin-left-sm">
							<radio class="blue radio" value="2" :checked="thisPro.sel==2?true:false"></radio>
							<text class="margin-left-sm"> 米样</text>
						</label>
					</radio-group>
					<view class="cu-form-group" v-if="thisPro.sel==2">
						<view class="title">数量</view>
						<input placeholder="数量"  v-model="thisPro.mYnum"></input>
					</view>
				</view>
				<view class="padding">
					<view class="uni-btn-v uni-common-mt uni-form-button margin-top">
						<button class="btn-submit" @tap="confirmEdit" type="primary">确认</button>
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
				title: '新客户索样',
				formType: '',
				editType: '',
				showModelClient: false,
				showModelBarCode: false,
				showModelProKind:false,
				scanHeight: 0,
				scanStart: false,
				exhibition: {},
				thisPro:{},
				clientReadonly: false,
				formData: {
					id: '',
					sampleList: [],
					imagePath: '', //https://ossweb-img.qq.com/images/lol/web201310/skin/big99008.jpg',
					imagePathLocal: '',
					accountType: 0,
					accountNum: '',
					memo: '',
					clientPrint: [],
					tips: [],
					Client: {
						id: '',
						compName: '',
						contacts: '',
						tel: '',
						mobile: '',
						email: '',
						address: '',
						aiResult: '',
					},
				},
				listTouchStart: 0,
				listTouchStartY: 0,
				listTouchDirection: null,
				modalName: '',
				barCode: '',
				mYnum: '',
				anmiaton: '-1',
				checkRow: [{
					value: '0',
					checked: true,
					text: '无'
				}, {
					value: '1',
					checked: false,
					text: 'FEDEX'
				}, {
					value: '2',
					checked: false,
					text: 'UPS'
				}, {
					value: '3',
					checked: false,
					text: 'DHL'
				}, {
					value: '4',
					checked: false,
					text: '顺丰'
				}],
			}
		},
		onLoad(query) {
			this.formType = query.type || '';
			this.editType = query.edittype || '';
			if (this.formType) {
				this.clientReadonly = true;
				this.title = '老客户索样';
			}

			var exid = query.exid || 0;
			this.initExh(exid);
		},
		onShow() {

		},
		created() {

		},
		methods: {
			initExh(exid) {
				//初始化当前展会名字
				this.exhibition = formatData.getCurrentExhibition() || {};
				//获取打印方式
				this.getClientPrintType();

				var that = this;
				//获取客户标签数据
				if (exid) {
					var promise = that.getEditData(exid)
					if (promise && promise.then) {
						promise.then(function(val) {
							that.getClientTips('edit');
						})
					}
				} else {
					//获取客户标签数据
					this.getClientTips();
				}

			},
			//如果是修改，需要获取原来的数据
			getEditData(exid) {
				var that = this;
				var params = {
					method: 'exhibition.sample.detial',
					id: exid,
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
			editClientInfo() {
				this.showModelClient = true;
			},
			hideModal() {
				this.showModelClient = false;
				// console.log('_this.formData:',this.formData);
			},
			hideBarCodeModal() {
				this.showModelBarCode = false;
			},
			imageScan() {
				var _this = this;
				if (_this.scanStart) {
					//开始识别名片信息
					_this.scanHeight = 1;
					setTimeout(function() {
						_this.scanHeight = 0;
						setTimeout(function() {
							_this.imageScan();
						}, 100);
					}, 1200);
				}
			},
			chooseImage() {
				var _this = this;
				uni.chooseImage({
					count: 1, //默认9
					sizeType: ['compressed'], //可以指定是原图还是压缩图，默认二者都有
					success: function(res) {
						// "blob:http://localhost:8080/649a1c64-ef92-4391-a2b8-cdbeedc993d9"
						// console.log(res);
						// uni.getImageInfo({
						// 	src: res.tempFilePaths[0],
						// 	success: function (image) {
						// 		console.log(image);
						// 	}
						// });

						_this.formData.imagePathLocal = res.tempFilePaths[0];

						//开始扫图片
						_this.scanStart = true;
						_this.imageScan();

						//上传图片并识别
						_this.uploadImage(res.tempFilePaths[0]);
					}
				});
			},
			uploadImage(_file) {
				var _this = this;
				//开始上传服务器
				var params = {
					method: 'uni.image.upload.scan',
					compress: 800,
				};
				formatData.set(params);
				uni.uploadFile({
					url: formatData.httpUrl(),
					fileType: 'image',
					filePath: _file,
					name: 'Images',
					formData: params,
					success: (res) => {
						_this.scanStart = false;
						var tmpData = res.data;
						tmpData = JSON.parse(tmpData);
						_this.formData.imagePath = tmpData.data.imagePath || '';
						if (tmpData.data.aiResult) {
							_this.formData.Client = tmpData.data.aiResult;
						}
					},
					fail: (res) => {
						_this.scanStart = false;
					}
				});
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
				let items = this.formData.tips;
				let values = e.currentTarget.dataset.value;
				for (let i = 0; i < items.length; ++i) {
					if (items[i].id == values) {
						items[i].checked = !items[i].checked;
						this.$set(this.formData.tips, i, items[i]);
						break
					}
				}
			},
			ChoosePrintType(e) {
				let items = this.formData.clientPrint;

				let values = e.currentTarget.dataset.value;
				for (let i = 0; i < items.length; ++i) {
					if (items[i].value == values) {
						if (items[i].disabled) {
							uni.showToast({
								title: items[i].msg || '该功能不能使用',
								icon: 'none',
							})
							break;
						}
						if (values == 'mail' && !this.formData.Client.email) {
							uni.showToast({
								title: '请先输入邮箱',
								icon: 'none',
							})
							break;
						}

						items[i].checked = !items[i].checked;
						this.$set(this.formData.clientPrint, i, items[i]);
						break;
					}
				}
			},
			editSampleKind(index){
				this.showModelProKind = true;
				this.thisPro = this.formData.sampleList[index];
				this.thisPro.index=index;
			},
			SetSel(e){
				this.thisPro.sel = e.detail.value;
			},
			//确认修改产品详细
			confirmEdit(){
				let num = this.thisPro.index;
				let selText = this.thisPro.sel==0?'挂钩':this.thisPro.sel==1?'A4':'米样';
				this.formData.sampleList[num].selText = selText;
				this.formData.sampleList[num].sel = this.thisPro.sel;
				this.formData.sampleList[num].mYnum = this.thisPro.sel==2?this.thisPro.mYnum:'';
				this.showModelProKind = false;
				this.thisPro = {};
			},
			deleteSample(index) {
				// console.log('delete index:',index);
				var that = this;
				uni.showToast({
					title: '删除成功',
					icon: 'none'
				});
				setTimeout(function() {
					that.formData.sampleList.splice(index, 1);
				}, 200)
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
				if (!that.exhibition.id) {
					uni.showToast({
						title: '请先选择参展展会',
						icon: 'none',
					});
					return false;
				}
				if (that.formData.sampleList.length <= 0) {
					uni.showToast({
						title: '请先添加样品数据',
						icon: 'none',
					});
					return false;
				}
				// console.log(that.formData.Client);
				if (!that.formData.Client.compName) {
					uni.showToast({
						title: '客户名称必填',
						icon: 'none',
					});
					return false;
				}
				uni.showModal({
					title: '提示',
					content: '确认提交数据吗',
					success: function(res) {
						if (res.confirm) {
							uni.showLoading({
								title: '提交...'
							});
							// console.log('formData => ',that.formData);
							that.formData.exhibition = that.exhibition;
							//开始获取详细
							var params = {
								method: 'exhibition.save',
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
									var succ = result.success || false;

									if (!succ) {
										uni.showToast({
											title: '提交失败:' + result.msg,
											icon: 'none'
										})
									} else {
										uni.showToast({
											title: '提交完成',
											mask: true
										});
										//返回首页
										setTimeout(function() {
											if (that.formData.id > 0) {
												uni.navigateBack({
													delta: 1
												});
											} else {
												uni.reLaunch({
													url: "../../pages/index/index"
												})
											}
										}, 800);
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
				var duplicate = false;
				var _index = -1;
				//添加前先判断是否重复
				//判断是否重复，如果重复需要弹框提示
				if (that.formData.sampleList.length > 0) {
					for (let i = 0; i < that.formData.sampleList.length; i++) {
						let pro = that.formData.sampleList[i];
						if (pro.proCode == barCode) {
							_index = i;
							duplicate = true;
							break;
						}
					}
				}
				//重复就不添加
				if (duplicate && _index >= 0) {
					console.log('duplicate code', barCode)
					uni.showModal({
						title: '提示',
						content: '该条码已存在样品列表，序号：' + (_index + 1),
						confirmText: '跳该位置',
						cancelText: '我知道了',
						success: function(res) {
							if (res.confirm) {
								// console.log('用户点击确定');
								that.pageScrollTo('#sampleItem_' + _index);
							} else if (res.cancel) {
								console.log('用户点击取消');
							}
						}
					});
				} else {
					//开始获取详细
					var params = {
						method: 'product.detail.get',
						code: barCode,
					};
					formatData.set(params);
					uni.request({
						url: formatData.httpUrl(),
						data: params,
						success: (res) => {
							var result = res.data.data;
							var detail = result.product || {};
							if (!detail.id) {
								uni.showToast({
									title: '找不到条码' + barCode,
									icon: 'none'
								})
							} else {
								// console.log(detail);
								// that.appendSampleList(detail);
								detail.productId = detail.id;
								detail.id = 0;
								detail.sel = 0;detail.selText = '挂钩';//默认类型为挂钩
								that.formData.sampleList.push(detail);
								//跳转到最后一个位置
								if (that.formData.sampleList.length >= 1) {
									//添加动画效果
									that.$nextTick(function() {
										that.pageScrollTo('#sampleItem_' + (that.formData.sampleList.length - 1));
										setTimeout(function() {
											that.anmiaton = that.formData.sampleList.length - 1;
											setTimeout(() => {
												that.anmiaton = '-1';
											}, 1000)
										}, 10);
									})
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
				}

			},
			appendSampleList(product) {
				// this.formData.sampleList.push(product);
			},
			getClientTips() {
				var that = this;
				var params = {
					method: 'client.tip.list',
				};
				formatData.set(params);
				uni.request({
					url: formatData.httpUrl(),
					data: params,
					success: (res) => {
						var result = res.data.data;
						this.formData.tips = result.tips || [];
						//修改的情况下，需要初始化标签选中的值
						if (that.formData.Client.tip) {
							var tips = that.formData.Client.tip.split(',');
							if (tips.length > 0) {
								for (let i = 0; i < that.formData.tips.length; i++) {
									var Item = that.formData.tips[i];
									if (tips.indexOf(Item.id) >= 0) {
										Item.checked = true;
										that.$set(that.formData.tips, i, Item);
									}
								}
							}
						}
					},
					complete(res) {

					},
				});
			},
			getClientPrintType() {
				var params = {
					method: 'client.print.type',
				};
				formatData.set(params);
				uni.request({
					url: formatData.httpUrl(),
					data: params,
					success: (res) => {
						var result = res.data.data;
						this.formData.clientPrint = result.list || [];
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
			//选择客户列表
			chooseClient() {
				var me = this;
				//开始监听
				this.$eventHub.$on('eventSelect', function(data) {
					//先注销监听事件 
					this.$eventHub.$off('eventSelect');
					console.log('data:', data);
					if (!data.data) {
						data.data = {};
					}
					//数据回填
					var formData = {};
					formData.id = data.data.cid || '';
					formData.compName = data.data.compName || '';
					formData.mobile = data.data.mobile || '';
					formData.tel = data.data.tel || '';
					formData.email = data.data.email || '';
					formData.contacts = data.data.contacts || '';
					formData.address = data.data.address || '';
					me.formData.Client = formData;
				});
				uni.navigateTo({
					url: "/pages/component/list2pop?dataKey=clientList"
				})
			},
			RadioChange(e) {
				for (let i = 0; i < this.checkRow.length; i++) {
					if (this.checkRow[i].value === e.target.value) {
						this.formData.accountType = i;
						break;
					}
				}
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
