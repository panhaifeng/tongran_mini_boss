const api = require('./utils/api')
//app.js
App({
  onLaunch: function () {
    let that = this;
    //获取当前缓存中的项目
    let curProject = api.getCurProject();
    that.globalData.curProject = curProject;

    api.getUserUID(function (res) {
      that.globalData.openid = res.openid;
      if (that.loadOpenidCallback) {
        that.loadOpenidCallback(res);
      } 
    });

    //获取openid和项目信息
    // let doTask = new Promise(function (resolve, reject) {
    //   // 获取openid，用微信login形式
    //   api.getUserUID(function(res){
    //     that.globalData.openid = res.openid;
    //     if (that.loadOpenidCallback) {
    //       that.loadOpenidCallback(res);
    //     }
    //     //返回Promise
    //     resolve(res);
    //   });
    // });
    //获取到openid之后就获取列表
    // doTask.then(function (res){
    //   //获取微信绑定的的账号列表
    //   api.getProjectList(res.openid , function(params){
    //     // that.globalData.projectList = params.projectList;
    //     if (that.loadProjectCallback) {
    //       that.loadProjectCallback(params);
    //     }
    //   });
    // });
    
  },
  setCurProject: function(project){
    this.globalData.curProject = project;
    api.setCurProject(project);
  },
  getYearList: function(callback){
    let that = this;
    //已经有的则直接返回    
    if (that.globalData.yearList.length > 0){
      if (callback) callback(that.globalData.yearList);
      return that.globalData.yearList;
    }

    //如果没有，考虑取服务器获取
    let project = this.globalData.curProject;
    
    if (!project || !project.id){
      if (callback) callback([]);
      return [];
    }

    //开始获取
    let params = {
      api_url: project.url,
      account: project.userName,
    };
    api.httpRequest('searchYearArray', params, function (res) {
      if (res.data.data) {
        let dataYear = res.data.data.params;
        // console.log('dataYear', dataYear);
        that.globalData.yearList = dataYear;

        if (callback) callback(dataYear);
      }
    });
  },
  getMonthList: function(callback){
    let that = this;
    //已经有的则直接返回    
    if (that.globalData.monthList.length > 0){
      if (callback) callback(that.globalData.monthList);
      return that.globalData.monthList;
    }
    that.globalData.monthList = [1,2,3,4,5,6,7,8,9,10,11,12];
    let monthList = [1,2,3,4,5,6,7,8,9,10,11,12];
    if(callback) callback(monthList);
  },
  globalData: {
    //账号项目列表,一个微信可以绑定多个账号
    // projectList: [],
    //当前账号
    curProject : {},
    //当前openid
    openid: '',
    yearList:[],
    monthList:[]
  }
})