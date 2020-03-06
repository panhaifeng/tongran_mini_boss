<template>
	<view class="cu-form-group">
		<view class="title">{{title}}</view>
		<input 
			:placeholder="fld.placeholder" 
			:value="myValue"
			@input="handleInput"
			></input>
		<text class='cuIcon-roundclosefill' v-if="myValue!='' && clearable" @click="handleClear"></text>
	</view>
</template>

<script>
	export default {		
		props: {
			title:{type: String,},
			fld:Object,
			//placeholder:{type: String,},
			// name:{type: String,default:''},
			value: String,
			clearable: {type: [Boolean, String],default: false},
			
			//密码显示属性
			displayable: {type: [Boolean, String],default: false},
			type: String,
			/**
			 * 自动获取焦点
			 */
			focus: {type: [Boolean, String],default: false}
		},
		data() {
			// console.log(this.fld.name,this.value);
			return {
				myValue : this.value||'',				
				showClearIcon :this.value==''?false:true,
			};
		},
		methods:{
			handleInput(e){
				this.myValue = e.detail.value;
				this.$emit('input',e.detail.value);
			},
			handleClear() {
				this.myValue = '';
				this.$emit('input','');
			}
		}
	}
</script>

<style>
	input {
		text-align: right;
	}
	/* webkit solution */
	/* ::-webkit-input-placeholder { text-align:right; } */
	/* mozilla solution */
	input:-moz-placeholder { text-align:right; }
</style>
