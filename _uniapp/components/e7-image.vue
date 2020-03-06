<template>
	<view class="margin-top">
		<view class="cu-bar bg-white">
			<view class="action">
				{{title ? title : "图片上传"}}
			</view>
			<view class="action">
				0/{{limit}}
			</view>
		</view>
		<view class="cu-form-group">
			<view class="grid col-4 grid-square flex-sub">
				<view
					class="bg-img"
					v-for="(item,index) in eValue"
					:key="index"
					@tap="ViewImage"
					:data-url="item.path ? item.path : item.imagePath">
					<image :src="item.path ? item.path : item.imagePath" mode="aspectFill"></image>
					<view class="cu-tag bg-red" @tap.stop="DelImg" :data-index="index">
						<text class='cuIcon-close'></text>
					</view>
					<view v-if="item.uploading==true" class="img-mask col-4">上传中:{{item.progress ? item.progress : ''}}</view>
				</view>
				<view class="solids" @tap="ChooseImage" v-if="eValue.length<limit">
					<text class='cuIcon-cameraadd'></text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import formatData from '@/common/formData.js';

	export default {
		props: {
			title:{type: String,},
			fld:Object,
			value: Array
		},
		data() {
			return {
				eValue : this.value||[],
				limit:parseInt(this.fld.limit) || 1,
				maxSize:this.fld.maxSize ? parseFloat(this.fld.maxSize) : 12,
			};
		},
		methods:{
			//选择照片
			ChooseImage() {
				uni.chooseImage({
					count: this.limit, //默认9
					sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
					// sourceType: ['album'], //从相册选择
					success: (res) => {
						var _this = this;

						// console.log('chooseImage succ,' ,res);
						var _oldLength = this.eValue.length;
						//判断是否需要限制图片大小
						if(res.tempFiles){
							var _maxSize = (this.maxSize * 1024 * 1024);
							var _list = [];
							res.tempFiles.forEach(function(item, i){
								if(item.size <= _maxSize){
									_list.push({path:item.path});
								}
							});
							//如果有需要删除的图片
							if(_list.length < res.tempFilePaths.length){
								uni.showToast({
									title:`图片超出${this.maxSize}M,已过滤`,
									icon:"none"
								});
							}
						}

						// console.log('new info',_list);
						//把老数据和新数据合并
						var _tmpVal = [];
						if (this.eValue.length != 0) {
							_tmpVal = this.eValue.concat(_list);
						} else {
							_tmpVal = _list ;
						}
						//如果超出了限制的数量，则需要过滤后面的图片：当一次性选择的图片超出了限制的时候会出现
						if(_tmpVal.length > this.limit){
							_tmpVal.splice(this.limit);
							uni.showToast({
								title:`最多上传${this.limit}张图片`,
								icon:"none"
							});
						}
						this.eValue = _tmpVal;
						// console.log('image list,' ,this.eValue);

						//上传到服务器
						for(var i = _oldLength;i < this.eValue.length;i++){
							if(!this.eValue[i].imageId)_this.uploadImage(i);
						}
					}
				});
			},
			uploadImage(index){
				console.log('upload => ',index);
				var _this = this;
				var _file = _this.eValue[index];
				//标记开始上传
				_file.uploading = true;
				_this.$set(_this.eValue ,index ,_file);

				//开始上传服务器
				var params = {
					method:'uni.image.upload'
				};
				formatData.set(params);				
				const uploadTask = uni.uploadFile({
					url: formatData.httpUrl(),
					fileType:'image',
					filePath: _file.path,
					name: 'Images',
					formData: params,
					success: (res) => {
						var tmpData = res.data;
						tmpData = JSON.parse(tmpData);

						_file.imageId = tmpData.data.imageId;
						_file.imagePath = tmpData.data.imagePath;
						_file.uploading = false;
						_this.$set(_this.eValue ,index ,_file);
						_this.handleInput();
						console.log('upload over => ',index);
					}
				});

				//上传进度
				_this.getTaskProgress(uploadTask ,index);
			},
			//上传进度更新
			getTaskProgress(uploadTask ,index){
				var _this = this;
				var _file = _this.eValue[index];
				if(_file.uploading == true){
					uploadTask.onProgressUpdate((res) => {
						_file.progress = res.progress + '%';
						_this.$set(_this.eValue ,index ,_file);
						// console.log('已经上传的数据长度' + res.totalBytesSent);
						// console.log('预期需要上传的数据总长度' + res.totalBytesExpectedToSend);
					});
				}else{
					_file.progress = '';
					_this.$set(_this.eValue ,index ,_file);
				}
			},
			//预览照片
			ViewImage(e) {
				var list = [];
				this.eValue.forEach(function(item ,i){
					var tmp = item.path ? item.path : item.imagePath
					list.push(tmp);
				})
				uni.previewImage({
					urls: list,
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
							var _index = e.currentTarget.dataset.index;
							this.eValue.splice(_index, 1);
							//触发更新表单
							this.handleInput();

							//删除服务端数据，不用等待
							this.removeServerImg(this.eValue[_index]);
						}
					}
				})
			},
			removeServerImg(file) {

				// var that = this;
				// var params = {
				// 	method:'uni.image.remove',
				// 	imageId : file.imageId
				// };
				// formatData.set(params);
				// uni.request({
				// 	url: formatData.httpUrl(), //仅为示例，并非真实接口地址。
				// 	data:params,
				// 	success: (res) => {
				// 		var result = res.data.data;
				// 		// console.log(result);

				// 	}
				// });
			},
			//把上传成功的图片更新到表单数据
			handleInput() {
				var _this = this;
				var imageList = [];
				this.eValue.forEach(function(item ,i){
					if(item.imageId > 0){
						// imageList.push({imageId:item.imageId ,imagePath:item.imagePath,path:item.path});
						imageList.push(item);
					}
				})
				this.$emit('input',imageList);
			},
		}
	}
</script>

<style>
	.img-mask{display: block;top: 0;left: 0;background: #000;opacity:0.7;color: #fff;text-align: center;height: 200px;line-height: 1.6;font-size: 13px;padding-top: 15px;}
</style>
