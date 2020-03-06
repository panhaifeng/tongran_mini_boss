/*
* 请求服务器的方法
*/
const config = require('../config')
const md5 = require('./md5')

//定义接口的相关参数
const methods = {
  getUserOpenid:{action:"get.openid.code",method:"POST"},
  getProjectList: { action: "get.member.project.list", method: "POST" },
  scanCodeBindProject: { action: "add.project.scancode", method: "POST" },
  unbind: { action: "member.unbind", method: "POST" },
  //erp数据
  SettingData: { action: "get.Setting.data", method: "POST" },
  homeData: { action: "get.Sales.data", method: "POST" },
  monthOrderData: { action: "get.Month.data", method: "POST" },
  monthOrderMapData: { action: "get.BarMonth.data", method: "POST" },
  clientOrderData: { action: "get.Client.data", method: "POST" },
  traderOrderData: { action: "get.Saler.data", method: "POST" },
  searchYearArray: { action: "get.Year.list", method: "POST" },
}
const _token = config.signToken;
const _ver = config.version;
const _serverUrl = config.apiUrl;

//处理参数
function formatParam(params, method){
  //判断方法是否定义，未定义不能请求
  if (!methods[method]) {
    wx.showToast({
      title: '请求失败,方法未定义',
      icon: 'none',
      duration: 2000
    });
    return false;
  }

  //时间戳
  let timestamp = Date.parse(new Date());
  timestamp = timestamp / 1000;
  //params默认值赋值
  let curMethod = methods[method];
  params.version = _ver;
  params.method = curMethod.action;
  params.timestamp = timestamp;
  //sign处理
  let tmpSign = params.timestamp + '&' + params.method + '&' + params.version + '&' + _token;
  params.sign = md5(tmpSign);

  return params;
}

//请求服务器接口
function httpRequest(method, params, callback){
  //添加一些默认的参数进来，进行格式化参数
  params = formatParam(params, method);

  //准备数据进行服务器请求
  //1.确认请求的方式GET/POST
  let curMethod = methods[method];
  let httpMethod = "POST";
  if (curMethod.method){
    httpMethod = curMethod.method;
  }

  //开始请求
  wx.request({
    url: _serverUrl, //仅为示例，并非真实的接口地址
    data: params,
    header: {
      'content-type': 'application/x-www-form-urlencoded'
    },
    success: callback,
    fail: function (res){
      wx.showToast({
        title: `请求失败: ${res.statusCode}`,
        icon: 'none',
        duration: 2000
      });
    },
    complete: function (res) {
      wx.hideLoading();
       if(res.data.rsp == 'fail'){
          wx.showToast({
            title: `请求失败: ${res.data.res}`,
            icon: 'none',
            duration: 2000
          });
      } 
    }
  })
}

//获取用户的openid和身份，项目的信息
function getUserUID(callback){
  let _Userinfo = {};
  //首先从缓存中获取openid,身份和关键信息，缓存时间大概是90天
  let keyStorage = 'eqinfo.userinfo.openid';
  let timestamp = Date.parse(new Date());
  timestamp = timestamp / 1000;
  let day = 86400 * 90;//90天

  // var doTask = new Promise(function (resolve, reject) {
    //先获取缓存中的
    let tmp = wx.getStorageSync(keyStorage);
    _Userinfo.openid = tmp ? tmp.openid : '';
    if (tmp) {
      let timedifference = timestamp - (tmp.timestamp || 0);
      if (timedifference > day) {
        _Userinfo.openid = '';
      }
    }
    //如果没有获取到，则重新获取
    if (!_Userinfo.openid) {
      wx.login({
        success: res => {
          // 发送 res.code 到后台换取 openId, sessionKey, unionId
          if (res.code) {
            httpRequest('getUserOpenid', { code: res.code }, function (res) {
              if (res.data.data.openid) {
                _Userinfo.openid = res.data.data.openid || '';
                //写入缓存
                wx.setStorage({
                  key: keyStorage,
                  data: {
                    openid: _Userinfo.openid,
                    timestamp: timestamp
                  }
                });
                //执行完的回调
                // return resolve(_Userinfo);
                if (callback) callback(_Userinfo);
              } else {
                wx.showToast({
                  title: `${res.data.data.errmsg}`,
                  icon: 'none',
                  duration: 2000
                });
              }
            });
          }
        }
      })
    } else {
      // return resolve(_Userinfo);
      if (callback) callback(_Userinfo);
    }
  // });
  
  // debugger;
  //返回
  // doTask
  //   .then(callback || function (res) {
  //     console.info('info is prm callback:');
  //   });
}

//删除列表信息
function removeProjectList(){
  let keyStorage = 'eqinfo.userinfo.project.list';
  wx.removeStorageSync(keyStorage);
}
//插入项目到缓存中，插入到最后
function pushProjectList(project) {
  let keyStorage = 'eqinfo.userinfo.project.list';
  let storage = wx.getStorageSync(keyStorage);
  let projectList = storage ? storage.projectList : [];

  let timestamp = Date.parse(new Date());
  timestamp = timestamp / 1000;
  //插入到原来的缓存中
  var index = -1;
  projectList.forEach(function(item,i){
    if (project.id == item.id){
      index = i;
    }
  });
  //如果数组中没有，则添加到缓存中，否则不添加
  if (index == -1) projectList.push(project);
  var value = {
      projectList: projectList,
      timestamp: storage.timestamp ? storage.timestamp : timestamp
  };
  wx.setStorageSync(keyStorage, value);
  // wx.setStorage({
  //   key: keyStorage,
  //   data: {
  //     projectList: projectList,
  //     timestamp: storage.timestamp ? storage.timestamp : timestamp
  //   }
  // });
  // console.log('pushProjectList', storage);
}
// 获取openid对应的绑定账号的列表，查找对应的数据
function getProjectList(openid ,callback){
  //先从缓存中获取用户列表，没有则到服务器获取,缓存时间大概是90天
  let keyStorage = 'eqinfo.userinfo.project.list';
  let timestamp = Date.parse(new Date());
  timestamp = timestamp / 1000;
  let day = 86400 * 90;//90天

  let _Userinfo = {};

  // var doTask = new Promise(function (resolve, reject) {
    //先取缓存
    let tmp = wx.getStorageSync(keyStorage);
    _Userinfo.projectList = tmp ? tmp.projectList : [];
    if (tmp) {
      let timedifference = timestamp - (tmp.timestamp || 0);
      if (timedifference > day) {
        _Userinfo.projectList = [];
      }
    }
    //debugger;
    //如果发现没有数据，则到服务器上获取
    if (!_Userinfo.projectList || !_Userinfo.projectList.length){
      httpRequest('getProjectList', { openid: openid }, function (res) {
        console.log('api request finish');
        if (res.data.data.projectList) {
          _Userinfo.projectList = res.data.data.projectList || '';
          //写入缓存
          wx.setStorage({
            key: keyStorage,
            data: {
              projectList: _Userinfo.projectList,
              timestamp: timestamp
            }
          })
        } else {
          wx.showToast({
            title: `${res.data.data.errmsg}`,
            icon: 'none',
            duration: 2000
          });
        }
        //如果有回调，执行回调
        // return resolve(_Userinfo);
        if (callback) callback(_Userinfo);
      });
    }else{
      // return resolve(_Userinfo);
      if (callback) callback(_Userinfo);
    }
  // });
  //console.log('projectList load over');
  //返回
  // doTask
  //   .then(callback || function (res) {
  //     console.info('info is prm callback:');
  //   });
}

//获取当前活动的项目账号
function getCurProject(){
  let keyStorage = 'eqinfo.current.project';
  let tmp = wx.getStorageSync(keyStorage);

  // let timestamp = Date.parse(new Date());
  // timestamp = timestamp / 1000;
  // let day = 86400 * 30;//30天

  // if (tmp) {
  //   let timedifference = timestamp - (tmp.timestamp || 0);
  //   if (timedifference > day) {
  //     tmp = false;
  //   }
  // }

  let project = tmp ? tmp.project : {};

  return project;
}

//设置当前用户
function setCurProject(project) {
  let keyStorage = 'eqinfo.current.project';
  let timestamp = Date.parse(new Date());
  timestamp = timestamp / 1000;

  var value = {
    project: project,
    timestamp: timestamp
  };
  wx.setStorageSync(keyStorage, value);
  // wx.setStorage({
  //   key: keyStorage,
  //   data: {
  //     project: project,
  //     timestamp: timestamp
  //   }
  // })

  return true;
}

module.exports = {
  formatParam,
  httpRequest,
  getUserUID,
  getProjectList,
  getCurProject,
  setCurProject,
  pushProjectList,
  removeProjectList
}