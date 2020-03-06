<template>
	<view class="margin-top">
		<view class="cu-form-group">
			<view class="title">{{title}}</view>
		</view>
		<view class="form-level-2">
			<radio-group class="block" @change="RadioChange" :disabled="true">
				<view class="cu-form-group" v-for="(item, index) in options" :key="item.value">
					<label class="title">{{item.text}}
						<radio :class="eValue==item.value?'checked':''" :checked="eValue==item.value?true:false" :value="item.value"></radio>
					</label>
				</view>
			</radio-group>
		</view>
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
				options:this.fld.options || [],
				name:this.fld.name,
			};
		},
		methods:{
			RadioChange(e) {
				this.eValue = e.detail.value;
				this.$emit('input',e.detail.value);
			},
		}
	}
</script>

<style>
	.form-level-2 .cu-form-group{padding-left: 40px;}
	.form-level-2 label.title{width: 750upx;display: inline-block;}
	.form-level-2 label.title radio{float: right;}
</style>
