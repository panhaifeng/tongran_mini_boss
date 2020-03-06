<template>
	<view class="cu-form-group">
		<view v-if="title" class="title">{{title}}</view>
		<textarea class="e7-textarea" maxlength="-1" v-model="eValue" :maxlength="eMaxlength" :auto-height="eAutoHeight" 
		:disabled="eDisabled" :placeholder="ePlaceholder" @input="handleInput"></textarea>
	</view>
</template>

<script>
	export default {
		props: {
			title:{type: String,},
			fld:Object,
			value: String
		},
		data() {
			return {
				eValue : this.value||'',				
				eDisabled:this.fld.disabled ? true : false,
				eMaxlength:this.fld.max ? this.fld.max : "-1",
				eAutoHeight:this.fld.autoHeight == false ? false : true,
				ePlaceholder:this.fld.placeholder || '',
			};
		},
		methods:{
			handleInput(e){
				this.eValue = e.detail.value;
				this.$emit('input',e.detail.value);
			}
		}
	}
</script>

<style>
	.e7-textarea{max-height: 150px;}
</style>
