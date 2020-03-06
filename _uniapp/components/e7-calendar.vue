<template>
	<view class="cu-form-group">
		<view class="title">{{title}}</view>
		<picker :mode="eMode" :value="eValue" :start="fld.start" :fields="eFields" :disabled="eDisabled" :end="fld.end" @change="DateChange">
			<view class="picker">
				{{eValue ? eValue : '请选择'}}
			</view>
		</picker>
	</view>
</template>

<script>
	export default {
		props: {
			title:String,
			value: String,
			fld:Object,
		},
		data() {
			var day = '';
			if(this.fld.notnull != true){
				var _day = new Date();
				day = _day.getFullYear()+"-" + (_day.getMonth()+1) + "-" + _day.getDate();
			}
			return {
				eValue:this.value||day,
				eMode : 'date',
				eFields:this.fld.fields ? this.fld.fields : 'day',
				eDisabled:this.fld.disabled ? true : false
			};
		},
		methods:{
			DateChange(e) {
				this.eValue = e.detail.value;
				this.$emit('input',e.detail.value);
			},
		}
	}
</script>

<style>

</style>
