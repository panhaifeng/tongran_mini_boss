<template>
	<view class="content">
		<view class="vio-flex" style="margin:30rpx;justify-content:space-between">


			<view class="vio-flexitem vio-flexitem-span12">
				<text>扫描内容: {{scanText}}</text>
			</view>
			<view class="vio-flexitem vio-flexitem-span12">
				<button @tap="onScan">扫描</button>
				<button @tap="printBarcode">打印一维码</button>
				<button @tap="printBarcode2">打印二维码</button>
				<button @tap="printDemo">打印示例</button>
			</view>
			<view class="vio-flexitem vio-flexitem-span12">
				<text>打印一维码返回结果: {{log}}</text>
			</view>
			<view class="vio-flexitem vio-flexitem-span12">
				<text>打印二维码返回结果: {{log2}}</text>
			</view>
			<view class="vio-flexitem vio-flexitem-span12">
				<text>打印一维码、二维码返回结果: {{log3}}</text>
			</view>
			<view class="vio-flexitem vio-flexitem-span12">
				<text>打印机初始化日志: {{initlog}}</text>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name: 'qsprinter',
		components: {
		},
		data() {
			return {
				scanText:'',log:'',initlog:'',log2:'',log3:''
			}
		},
		onLoad(){
			let self=this
			// #ifdef APP-PLUS
			let main = plus.android.runtimeMainActivity();//获取activity
			let context = plus.android.importClass('android.content.Context'); //上下文
			let receiver = plus.android.implements('io.dcloud.feature.internal.reflect.BroadcastReceiver',{onReceive : doReceive });
			let IntentFilter = plus.android.importClass('android.content.IntentFilter');
			let filter = new IntentFilter();
			filter.addAction("com.qs.scancode");//监听扫描
			main.registerReceiver(receiver,filter);//注册监听

			function doReceive(context, intent) {
				plus.android.importClass(intent);//通过intent实例引入intent类，方便以后的‘.’操作
				let result = intent.getStringExtra("code");
				self.scanText=result


			}
			// #endif
		},
		destroyed() {
			this.$vPrint.close();
		},
		methods:{
			onScan:function(){
				this.$vPrint.scan();
			},
			printBarcode:function(){
				var params = {
					method:this.printUrl?this.printUrl:'uni.MadanPrintDetail',
				};
				formatData.set(params);
				uni.request({
					url: formatData.httpUrl(),
					data:params,
					success: (res) => {
						var result = res.data.data;
						this.$vPrint.print(result.data);
					}
				});
			},
			printBarcode2:function(){
				let self=this
				let _y=20
				this.$vPrint.print({
					"maxFontSize":50,
					"pageHeight":320,
					"createImage":0,
					"content":[
						{
							"type":"QR",
							"text":"我是中文，ABCabc!@#$%^&*()测试",
							"x":50,"y":132,
							"width":150,"height":150,
							"format":"qr_code"
						}
					]
				})
			},
			printDemo:function(){
				let self=this
				let _y=20
				this.$vPrint.print({
					"maxFontSize":50,
					"pageHeight":320,
					"createImage":0,
					"content":[
						{
							"type":"barcode",
							"text":'6934502301856',
							"x":150,"y":2,
							"width":300,"height":50,
							"format":"code_128"
						},
						{
							"type":"string",
							"text":"6934502301856",
							"x":150,"y":80,
							"align":"normal",
							"fontSize":14,"bold":"true"
						},
						{
							"type":"QR",
							"text":"我是中文，ABCabc!@#$%^&*()测试",
							"x":200,"y":150,
							"width":100,"height":100,
							"format":"qr_code"
						},
						{
							"type":"string",
							"text":"收",
							"x":5,"y":70,
							"align":"normal",
							"fontSize":18,"bold":"true"
						},
						{
							"type":"string",
							"text":"王晨 13861537682",
							"x":35,"y":70,
							"align":"normal",
							"fontSize":18,"bold":"true"
						},
						{
							"type":"string",
							"text":"件",
							"x":5,"y":65,
							"align":"normal",
							"fontSize":18,"bold":"true"
						},
						{
							"type":"string",
							"text":"江苏省无锡市宜兴市和桥鹅州南路41号",
							"x":35,"y":65,
							"align":"normal",
							"fontSize":18,"bold":"true"
						}
					]
				})
			},




		}
	};
</script>

<style scoped>

</style>
