import Vue from 'vue'
import App from './App'

//引入事件总线，在多页面传参时需要用到，比如选择客户
Vue.prototype.$eventHub = new Vue();

import navTo from '@/common/navTo'  
Vue.prototype.$navTo = navTo

//#ifdef APP-PLUS
//打印服务
import vPrint from '@/common/vPrint'
Vue.prototype.$vPrint = vPrint
//#endif

//将一级目录页组件进行注册，
import home from './pages/home/home.vue'
Vue.component('home',home)

import about from './pages/about/about.vue'
Vue.component('about',about)

// import basics from './pages/basics/home.vue'
// Vue.component('basics',basics)

// import components from './pages/component/home.vue'
// Vue.component('components',components)

// import plugin from './pages/plugin/home.vue'
// Vue.component('plugin',plugin)

//注册顶部导航栏组件
import cuCustom from './colorui/components/cu-custom.vue'
Vue.component('cu-custom',cuCustom)

Vue.config.productionTip = false

//是否是测试开发
Vue.prototype.test = false;

			
App.mpType = 'app'

const app = new Vue({
    ...App
})
app.$mount()
