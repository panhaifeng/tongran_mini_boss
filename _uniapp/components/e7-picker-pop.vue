<template>
	<view  class="cu-form-group" @click="handleClick">
		<view class="title">{{title}}</view>
		<view class="uni-picker">
			<div>
				<view class="picker">
					<!-- <slot v-if="this.text==''"></slot> -->
					<text v-if="myText==''||myText==null" class="text-gray text-sm">点击选择</text>
					<text v-else>{{myText}}</text>
				</view>
			</div>
		</view>
	</view>
</template>

<script>
	export default {
		// <!-- https://blog.csdn.net/g401946949/article/details/86502226 -->	/
		props:{
			title:String,
			//需要跳转到的页面
			//如果存在一组组件，父组件需要传入这个参数，用来定位
			index:Number,
			//组件的值，可能后期会变成数组			
			value:[String,Number],
			//
			text:String,
			fld:Object,
		},
		mounted() {
			// console.log(this._uid+'创建,index:'+this.index)
		},
		beforeDestroy() {
			// console.log(this._uid+"销毁");
		},
		//注意，以下监听一定要有，可能是uniapp的坑
		//测试中发现：创建3个组件(1,2,3),不管删除哪个组件，组件的销毁规则：
		//1,删除时都是按照3,2,1的次序，(beforeDestroy可监听_uid)
		//2,删除后余留组件的props和期望一致，但是data中的属性有问题
		watch:{
			text:{
				handler: function(n,o){
					// console.log('text changed',o,n)
					this.myText = n;
				}
			},
			value:{
				handler: function(n,o){
					this.myValue = n;
				}
			}
		},
		data() {
			// console.log('this.text',this.text);
			return {
				//组件需要返回的值,可能是数组
				// value:null
				myValue :this.value,
				myText:this.text,
				url:this.fld.url || "/pages/component/list2pop?dataKey="+this.fld.dataKey,
			};
		},		
		methods:{
			dump() {
				console.log(this);
			},
			handleClick(e) {
				//总线中创建一个监听器，接收到结果后，注销
				//注意返回的数据必须按照如下格式返回:
				// {text:'',value:'',data:{}}
				
				var me = this;
				//开始监听
				this.$eventHub.$on('eventSelect', function (data) {
					// console.log('data-data:',data);
					//先注销监听事件 
					this.$eventHub.$off('eventSelect');
					//检查data是否合法
					if(typeof(data.value)==undefined) {
						console.warn('回传参数中必须包含value键值')
						return false;
					}
					if(typeof(data.text)==undefined) {
						console.warn('回传参数中必须包含text键值')						
						return false;
					}
					if(typeof(data.data)==undefined) {
						console.warn('回传参数中必须包含data键值')						
						return false;
					}
					//这个和vue的标准不同，需要封装成一个对象后传递
					var objSelected = data.data;
					var param = {data:objSelected};
					if(me.index!==undefined) {
						param.index=me.index;
					}
					me.$emit('input',data.value);
					//数据回填
					me.myValue = data.value;
					me.myText = data.text;
				});
				uni.navigateTo({
					url:this.url
				})
			}
		}
	}
</script>

<style>
	.uni-picker {
		-webkit-box-flex: 1;
		-webkit-flex: 1;
		-ms-flex: 1;
		flex: 1;
		padding-right: 20px;
		overflow: hidden;
		position: relative;
		display: block;
		box-sizing: border-box;

	}
	.cu-form-group .uni-picker::after {
	    font-family: cuIcon;
	    display: block;
	    content: "\E6A3";
	    position: absolute;
	    font-size: 17px;
	    color: #8799a3;
	    line-height: 50px;
	    width: 30px;
	    text-align: center;
	    top: 0;
	    bottom: 0;
	    right: -10px;
	    margin: auto;
	}
	.cu-form-group .uni-picker .picker {
	    line-height: 50px;
	    font-size: 14px;
	    -o-text-overflow: ellipsis;
	    text-overflow: ellipsis;
	    white-space: nowrap;
	    overflow: hidden;
	    width: 100%;
	    text-align: right;
		
		-webkit-box-sizing: border-box;
		box-sizing: border-box;
	}
</style>
