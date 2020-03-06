<template>
	<view class="cu-form-group">
		<view class="title">{{title}}</view>
		<picker @change="PickerChange" :value="index" :range="options" range-key="text">
			<view class="picker">
				{{options[index] ? options[index].text : '请选择'}}
			</view>
		</picker>
	</view>	
</template>

<script>
	export default {
		props: {
			title:String,
			value: '',
			fld:Object,
		},
		data() {
			var _this = this;
			var _val = 0;
			if(this.fld.options.length > 0){
				for(var k in this.fld.options){
					var item = this.fld.options[k];
					// console.log(k);
					if(item.value == _this.value){
						_val = k;
						break;
					}
				}
			}			
			
			return {
				index:_val,
				eValue:this.value||'',
				options:this.fld.options || [],
				eDisabled:this.fld.disabled ? true : false
			};
		},
		methods:{
			PickerChange(e) {
				// console.log(e.detail.value);
				this.index = e.detail.value;
				this.eValue = this.options[this.index].value;
				this.$emit('input',this.eValue);
			},
		}
	}
</script>

<style>

</style>
