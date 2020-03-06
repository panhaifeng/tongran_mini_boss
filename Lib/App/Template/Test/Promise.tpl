<!DOCTYPE html>
<html>
<title><{$title}></title>
<head>
  <meta charset="UTF-8">
  <{webcontrol type='LoadJsCss' src="Resource/Script/vue/element/index.css"}>
</head>
<body>
  <div id='div1'>
    <p>1,打开控制台观察输出结果</p>
    <p>2,comp-dropdown-checkbox效果-鼠标移上再显示scroll-bar</p>
    <comp-dropdown-checkbox></comp-dropdown-checkbox>
    
  </div>
  </div>
</body>
<script src="Resource/Script/vue/vue.js"></script>
<script src="Resource/Script/vue/element/index.js"></script>
<script src="Resource/Script/vue/element/axios.min.js"></script>
<script src="Resource/Script/vue/element/components.js"></script>
<script>
  var app = new Vue({
    el: '#div1',
    data : function() {
      return {
        show : true
      };
    },
    methods: {

    },
    mounted: function() {      
      var prm = new Promise( function(resolve,reject) {
        var url='?controller=Jichu_Test&action=getpromise';
        var param={};
        axios.post(url,param).then(function(response){
          if(response.data.success) {
            dump('ajax return success');
            return resolve(response);
          } 
          dump('ajax return unsuccessful');
          return reject(response);
        },function(error){
          //比如404错误,500错误等
          dump('ajax error',error);
        });
      });

      //简单使用
      prm.then(function(v){
        dump('prm resolve fired',v);
      },function(err){
        dump('prm reject fired',err);
      });

      //prm2会等到prm有了断言后,才会进行断言
      //比如嵌套ajax可能用到,
      var prm2 = new Promise(function(resolve,reject){
        return resolve(prm);
      });
      prm2.then(function(response){
        dump('prm2 resolve fired',response);
      },function(error){
        dump('prm2 reject fired',error);

      });

      //链式调用,
      //thenf方法返回的是一个新的Promise实例（不是原来那个Promise实例
      //第一次resolve中return的结果作为第二个resolve的参数
      var times=0;
      var prm3 = new Promise(function(resolve,reject){
        //2秒后断言成功
        setTimeout(resolve,2000,`prm3 return`);
      });
      prm3.then(function(response){
        times++;
        dump('链式调用第'+times+'次 then',response);
        return times;
      }).then(function(response){
        times++;
        dump('链式调用第'+times+'次 then',response);
        return response;
      })

      //链式调用ajax
      var url='?controller=Jichu_Test&action=getpromise';
      var param={a:1};
      axios.post(url,param).then(function(response){
        dump('第一次ajax返回',response);
        return {a:'response1'};
      }).then(function(response){
        //注意下面开始的return 一定要有,否则无法往下传递
        return axios.post(url,response).then((response2)=>{
          dump('第二次ajax返回',response2);
          return {a:'response2'};  
        });        
      }).then(function(response){
        return axios.post(url,response).then((response3)=>{
          dump('第3次ajax返回',response3);
          return 'response3';  
        }); 
      });

      //几个异步请求同时发起,不保证先后次序,
      //等到全部有结果后再执行一个回调
      var p1 = axios.post(url,param);
      var p2 = axios.post(url,param);
      var p3 = axios.post(url,param);
      Promise.all([p1,p2,p3]).then(function(values){
        dump('promise all fired',values);
      });
    }//end mounted

  });

</script>

</html>