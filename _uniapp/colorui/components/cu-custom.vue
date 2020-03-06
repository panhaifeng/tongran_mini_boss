<template>
	<view>
		<view class="cu-custom" :style="[{height:CustomBar + 'px'}]">
			<!-- #ifndef MP -->
			<view class="cu-bar fixed" :style="style" :class="[bgImage!=''?'none-bg text-white bg-img':'',bgColor]">
			<!-- #endif -->
			<!-- #ifdef MP -->
			<view class="cu-bar fixed justify-start" :style="style" :class="[bgImage!=''?'none-bg text-white bg-img':'',bgColor]">
			<!-- #endif -->
				<view class="action" @tap="BackPage" v-if="isBack">
					<text class="cuIcon-back"></text>
					<!-- #ifndef MP -->
					<slot name="backText"></slot>
					<!-- #endif -->
				</view>
				<view class="action" @tap="BackHome" v-if="isBackHome">
					<text class="cuIcon-back"></text>
					<slot name="backText"></slot>
				</view>
				<view class="content" :style="[{top:StatusBar + 'px'}]">
					<slot name="content"></slot>
				</view>
				<slot name="right"></slot>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				StatusBar: this.StatusBar,
				CustomBar: this.CustomBar
			};
		},
		name: 'cu-custom',
		computed: {
			style() {
				var StatusBar= this.StatusBar;
				var CustomBar= this.CustomBar;
				var bgImage = this.bgImage;
				var style = `height:${CustomBar}px;padding-top:${StatusBar}px;`;
				if (this.bgImage) {
					style = `${style}background-image:url(${bgImage});`;
				}
				return style
			}
		},
		props: {
			bgColor: {
				type: String,
				default: ''
			},
			isBack: {
				type: [Boolean, String],
				default: false
			},
			isBackHome: {
				type: [Boolean, String],
				default: false
			},
			bgImage: {
				type: String,
				default: ''
			},
		},
		methods: {
			BackPage() {
				uni.navigateBack({
					delta: 1
				});
			},
			BackHome(){
				uni.reLaunch({
					url:"../../pages/index/index"
				})
			}
		}
	}
</script>

<style>

</style>
