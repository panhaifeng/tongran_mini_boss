<!-- 组件的抽象，因为目前uni不支持动态组件，所以只能使用if判断 -->
<template>
		<e7-input
			v-if="type=='e7-input'"
			:title="title"
			:placeholder="placeholder"
			:value="value"
			:clearable="clearable"
			:fld="fld"
			@input="handleInput"
			></e7-input>
		<e7-passwd
			v-else-if="type=='e7-passwd'"
			:title="title"
			:placeholder="placeholder"
			:value="value"
			:clearable="clearable"
			:name="name"
			@input="handleInput"
			></e7-passwd>
		<e7-autocomplete
			v-else-if="type=='e7-autocomplete'"
			:title="title"
			:placeholder="placeholder"
			:value="value"
			:clearable="clearable"
			:loadData="loadAutocompleteData"
			:fld="fld"
			:debounce="300"
			@input="handleInput"
		></e7-autocomplete>
		<e7-autocomplete
			v-else-if="type=='e7-autocompleteStr'"
			:title="title"
			:placeholder="placeholder"
			:value="value"
			:clearable="clearable"
			:fld="fld"
			:stringList="fld.options"
			:debounce="300"
			@input="handleInput"
		></e7-autocomplete>
		<e7-calendar
			v-else-if="type=='e7-calendar'"
			:title="title"
			:value="value"
			:fld="fld"
			@input="handleInput"
		></e7-calendar>
		<e7-picker
			v-else-if="type=='e7-picker'"
			:title="title"
			:value="value"
			:fld="fld"
			@input="handleInput"
		></e7-picker>
		<e7-switch
			v-else-if="type=='e7-switch'"
			:title="title"
			:value="value"
			:fld="fld"
			@input="handleInput"
		></e7-switch>
		<e7-textarea
			v-else-if="type=='e7-textarea'"
			:title="title"
			:value="value"
			:fld="fld"
			@input="handleInput"
		></e7-textarea>
		<e7-radio
			v-else-if="type=='e7-radio'"
			:title="title"
			:value="value"
			:fld="fld"
			@input="handleInput"
		></e7-radio>
		<e7-picker-pop
			v-else-if="type=='e7-picker-pop'"
			:title="title"
			:value="value"
			:fld="fld"
			:text="displayText"
			@input="handleInput"
		></e7-picker-pop>
		<e7-image
			v-else-if="type=='e7-image'"
			:title="title"
			:value="value"
			:fld="fld"
			@input="handleInput"
		></e7-image>
		<span v-else>其他组件</span>
</template>

<script>
	import e7Input from "@/components/e7-input.vue"
	import e7Passwd from "@/components/e7-passwd.vue"
	import e7Autocomplete from "@/components/e7-autocomplete.vue"
	import e7Calendar from "@/components/e7-calendar.vue"
	import e7Picker from "@/components/e7-picker.vue"
	import e7Switch from "@/components/e7-switch.vue"
	import e7Textarea from "@/components/e7-textarea.vue"
	import e7Radio from "@/components/e7-radio.vue"
	import e7Image from "@/components/e7-image.vue"
	import e7PickerPop from "@/components/e7-picker-pop.vue"
	import formatData from '../common/formData.js';

	export default {
		components: {e7Input,e7Passwd,e7Autocomplete,e7Calendar,e7Picker,e7Switch,e7Textarea,e7Radio,e7Image,e7PickerPop},
		props:{
			type:String,
			title:String,
			placeholder:String,
			value:'',
			name:String,
			displayText:'',
			clearable: {type: [Boolean, String],default: false},
			displayable: {type: [Boolean, String],default: false},
			fld:Object,
		},
		data() {
			return {
				handleInput(newValue) {
					this.$emit('input',newValue);
				},
				// //使用静态数据
				// autocompleteStringList: [
				// 	'汉字行',
				// 	'guang zhou',
				// 	{
				// 		//自定义数据对象必须要有text属性
				// 		text: 'hello',
				// 		//其它字段根据业务需要添加
				// 		key: 'hello key'
				// 	},
				// 	'不 行',
				// 	{
				// 		//自定义数据对象必须要有text属性
				// 		text: '我是静态数据',
				// 		//其它字段根据业务需要添加
				// 		id: 'hz'
				// 	}
				// ]
			};
		},
		methods:{
			loadAutocompleteData(value,fld) {
				// console.log('每次输入经过防抖处理以后都会进到这里。');
				// console.log('此参数就是输入框的值：', value,fld);

				// 【注意】：由于此方法是组件调用进来的，这里的this对象已经不是指向当前页面了
				// 所以无法在这里通过this去取当前页面的数据；
				// 基于同样的原因，也无法通过this去调用当前页的其它方法。
				// 【正确的做法】：在这个方法内写完所有取数据的逻辑，如果需要用输入框的值则取这里的value参数
				var params = {};
				params['method'] = 'uni.autocomplete.get.data.list';
				params['dataKey'] = fld.dataKey||'';
				params['key'] = value||'';
				formatData.set(params);
				// let url = 'https://www.fastmock.site/mock/5ac037a8ecc6af666419e1e746d2172f/uniapp/getClientList';
				var curTaskey = this.autocompleteRequestTask >= 0 ? this.autocompleteRequestTask+1 : 0;

				return uni.request({
						url: formatData.httpUrl(), //仅为示例，并非真实接口地址。
						data:params,
					})
					.then(ret => {
						// console.log('curTaskey',curTaskey);
						// console.log('autocompleteRequestTask',this.autocompleteRequestTask);
						if(this.autocompleteRequestTask >= curTaskey){
							// return false;
						}
						this.autocompleteRequestTask = curTaskey;

						var [error, res] = ret;
						console.log(res);
						let data = res.data.data || [];
						if (data.length <= 0) {
							return Promise.resolve([]);
						}

						let retData = data || [];
						return Promise.resolve(retData);
					});
			},
			//响应选择事件，接收选中的数据
			selectItemD(data) {
				//选择事件
				console.log('收到数据了:', data);
			},
			selectItemS(data) {
				//选择事件
				console.log('收到数据了:', data);
			},
			printLog() {
				console.log(this.testObj);
			},
		}
	}
</script>

<style>

</style>
