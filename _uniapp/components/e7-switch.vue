<template>
	<view class="cu-form-group">
		<view class="title">{{title}}</view>
		<switch @change="eventChange" :disabled="eDisabled" :class="eValue ? 'checked' : ''" :checked="eValue ? true : false"></switch>
	</view>
</template>

<script>
	export default {
		props: {
			title:String,
			value: Boolean,
			fld:Object,
		},
		data() {
			return {
				eValue:this.value,
				eDisabled:this.fld.disabled ? true : false
			};
		},
		methods:{
			eventChange(e) {
				this.eValue = e.detail.value;
				this.$emit('input',e.detail.value);
			},
		}
	}
</script>

<style>

</style>
