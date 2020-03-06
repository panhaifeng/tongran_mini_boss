<template>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{title}}</block>			
		</cu-custom>
		<web-view :webview-styles="webviewStyles" :src="url" :style="'margin-top: '+ccustomBar+'px;'"></web-view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				ccustomBar:this.CustomBar,
				url:'',
				title:'展会',
				webviewStyles: {
					progress: {
						color: '#FF3333'
					}
				},
			}
		},
		onLoad(query) {
			if(query.url){
				this.url = decodeURIComponent(query.url);
				console.log(this.url);
			}
			if(query.title){
				this.title = decodeURIComponent(query.title);
			}
		},
		methods: {
			
		}
	}
</script>

<style>

</style>
