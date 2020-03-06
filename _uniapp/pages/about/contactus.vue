<template name='about'>
	<view>
		<cu-custom bgColor="bg-gradual-pink" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">联系我们</block>
		</cu-custom>
		
		<view class="cu-list menu" :class="[menuBorder?'sm-border':'',menuCard?'card-menu margin-top':'']">				
			<view class="cu-item" >
				<view class="content">
					<text class="cuIcon-info text-green"></text>
					<text class="text-grey">开发公司</text>
				</view>
				<view class="action">
					<text class="text-grey text-sm">常州易奇信息科技有限公司</text>
				</view>
			</view>
			<view class="cu-item" >
				<view class="content">
					<text class="cuIcon-info text-green"></text>
					<text class="text-grey">地址</text>
				</view>
				<view class="action">
					<text class="text-grey text-sm">常州科教城创研港2号楼A1203-2</text>
				</view>
			</view>
			<view class="cu-item" @tap="call('4008285817')">
				<view class="content">
					<text class="cuIcon-info text-green"></text>
					<text class="text-grey">免费服务热线</text>
				</view>
				<view class="action">
					<text class="text-grey text-sm">400-828-5817</text>
				</view>
			</view>
			<view class="cu-item" @tap="call('13306115213')">
				<view class="content">
					<text class="cuIcon-info text-green"></text>
					<text class="text-grey">手机</text>
				</view>
				<view class="action">
					<text class="text-grey text-sm">133-0611-5213</text>
				</view>
			</view>	
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				menuArrow:true,
				menuBorder: false,
				menuCard: false,
			}
		},
		methods: {
			call(tel){
				uni.makePhoneCall({
				    phoneNumber: tel //仅为示例
				});
			}
		},
		onShareAppMessage(res) {
		    // if (res.from === 'button') {// 来自页面内分享按钮
		    //   console.log(res.target)
		    // }
		    return {
		      title: '易奇展会',
		      path: '/pages/index/index'
		    }
		}
	}
</script>

<style>

</style>
