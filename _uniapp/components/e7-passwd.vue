<template>
	<view class="cu-form-group">
		<view class="title">{{title}}</view>
		<input 
			:type='myType'
			:placeholder="fld.placeholder" 
			:value="myValue"
			:name="name"
			@input="handleInput"
			></input>
		<text class='cuIcon-roundclosefill'  v-if="myValue && myValue!=='' && clearable"   @click="handleClear"></text>
		<text :class="showPassword?'cuIcon-attentionfill':'cuIcon-attention'" style="margin-left: 10px;" @click="handleEye"></text>
	</view>
</template>

<script>
	export default {		
		props: {
			title:{type: String,},
			// placeholder:{type: String,},
			// name:{type: String,default:''},
			value: String,
			name: String,
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
			return {
				myValue : this.value,				
				showClearIcon :this.value==''?false:true,
				showPassword:false,
				myType:'password'
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
			},
			handleEye() {
				this.showPassword = this.showPassword ? false :true;
				this.myType = this.showPassword ? "" : "password"
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
