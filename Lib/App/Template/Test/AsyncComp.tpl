<!DOCTYPE html>
<html>
<title><{$title}></title>
<head>
  <meta charset="UTF-8">
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/index.css"}>
</head>
<body>
  <div id='div1'>
    <p>1,简单异步组件</p>
    <async-example></async-example>
    <p>2,promise异步组件</p> 
      <button @click="show=true">显示</button>
      <async-test1 v-if="show" ref='test1'></async-test1>
    <p>3,promise异步嵌套组件</p> 
      <button @click="show1=true">显示</button>
      <async-test v-if="show1" ref='test'></async-test>
    <p>5,高级异步组件,有加载中或者加载失败区分的异步组件,</p>
    <p>异步组件的问题:<br />
      1,<a href='https://zhuanlan.zhihu.com/p/32015343'>生命周期的控制</a>:在异步子组件中，mounted 函数中是无法获取到子组件的实例的，所以我们需要一些技巧来实现这个功能<br/>
      2,不能使用import from语法,因为必须运行在 type="module"的script标签中,但这个标签会给其他语句带来问题<br />
      3,浏览器支持问题<br />
      4,
    </p>
  </div>
  </div>
</body>
<script src="Resource/Script/vue/vue.js"></script>
<script src="Resource/Script/vue/element/index.js"></script>
<script src="Resource/Script/vue/element/axios.min.js"></script>
<script src="Resource/Script/vue/element/components.js"></script>
<script>
  
  // import ElCheckbox from 'element-ui/packages/checkbox';
  Vue.component('async-example', function (resolve, reject) {
    setTimeout(function () {
      // 向 `resolve` 回调传递组件定义
      resolve({
        template: '<div>I am async!</div>'
      })
    }, 1000)
  })

  //异步组件,点击按钮时才会载入
  //test.js中内容:
  //export default {template:`<h1>123123</h1>`}
  // Vue.component('async-test1', ()=>import('./test1.js'));

  //带嵌套的异步组件
  // Vue.component('async-test', ()=>import('./test.js'));
  //下面写法会报错
  // Vue.component('async-test', ()=>import('./test.js').then(comp=>{}));
  //如果 script 标签后没有 type=module,下面代码会报语法错误,
  // import asyncTest1 from './test1.js'
  var app = new Vue({
    el: '#div1',
    //以下是局部组件注册,
    components:{
      asyncTest : ()=>import('./test.js'),
      asyncTest1: ()=>import('./test1.js'),
    },
    data : function() {
      return {
        show : false,
        show1 : false,
      };
    },
    methods: {

    },
    mounted: function() {   
      dump('tpl mounted',this.$refs,this.$refs.test); 
      // this.$nextTick(function(){
      //   dump(this.$refs.test);
      // });  
      // dump(this.$refs,this.$refs.test);
    }//end mounted

  });

</script>

</html>