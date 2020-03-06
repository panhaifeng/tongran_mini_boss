//index.js
const api = require('../../utils/api')
//获取应用实例
const app = getApp()

Page({
  data: {
    message: '',
    moneyMonth: '0.00',
    indexData: [
      { text: '本月计划数', number: '0.00' }, 
      { text: '本日计划数', number: '0.00' }, 
      { text: '本日发货数', number: '0.00' },
      { text: '本月发货数', number: '0.00' }
      //{ text: '今日销售额', number: '0.00' }
    ],
    apps: [
      { text: '生产跟踪报表', icon: 'icon-time', path:'../orderClient/index'}, 
      // { text: '客户合同统计', icon: 'icon-addressbook', path: '../orderClient/index'},
      // { text: '业务订单统计', icon: 'icon-group', path: '../orderTrader/index'}
    ],
    extBg:{text: '产量报工', icon: 'icon-group', path: '../outPut/index'},
    showBgMenu:false,
  },
  onLoad: function () {
    
  },
  onShow: function(){
    let that = this;

    //获取当前项目:如果有则直接开始加载数据，如果没有需要设置当前项目
    let curProject = app.globalData.curProject;
    if (!curProject.id) {
      //判断是否已经加载了openid，如果还没有加载，则使用回调函数等加载成功后在设置，如果已经有了，则直接跳转到设置页面
      if (!app.globalData.openid) {
        //消息提醒
        wx.showLoading({
          title: '加载中',
        })
        app.loadOpenidCallback = (res) => {
          wx.hideLoading();
          that.bindCurProject();
        }
      } else {
        that.bindCurProject();
      }
    }else{
      //设置标题
      if (curProject.compName){
        wx.setNavigationBarTitle({
          title: curProject.compName || '易奇色织'
        })
      }
      //加载数据
      this.getDataHome(curProject);
    }    
  },
  //进行绑定默认项目，支持扫码和选择两种形式
  bindCurProject : function(){
    // console.log('跳转到设置页面');
    wx.navigateTo({
      url: '/pages/bindProject/bindProject'
    })
  },
  //首页加载数据:project是需要加载的项目地址
  getDataHome: function(project){
    let that = this;
    //加载个性化菜单
    let menu = that.data.apps;
    if(!that.data.showBgMenu){
      let params = {
        api_url: project.url,
        account:project.userName,
      };
      api.httpRequest('SettingData', params, function (res) {
        let rowset = res.data.data;
        if (rowset && rowset.params.bg) {
          let bg = that.data.extBg;
          menu.push(bg);
          that.setData({
            apps: menu,
            showBgMenu:true
          });
        }
      });
    }

    if(project.id){
      let params = {
        api_url: project.url,
        account:project.userName,
      };
      api.httpRequest('homeData', params, function (res) {
        console.log(res);
        if (res.data.data) {
          let dataOrder = res.data.data.params;
          //设置数据
          that.setData({
            moneyMonth: dataOrder.money_month || "0.00"
          });

          that.setData({
            "indexData[0].number": dataOrder.cntM_month || "0.00",
            "indexData[1].number": dataOrder.cntM_Today || "0.00",
            "indexData[2].number": dataOrder.cntFh_Today || "0.00",
            "indexData[3].number": dataOrder.cntFh_Month || "0.00"
          });
        }
      });
    }else{
      wx.showToast({
        title: '请绑定项目',
        icon: 'fail',
        duration: 2000
      })
    }
  },
  onShareAppMessage() {
    return {
      title: '常州易奇科技小程序筒染版',
      path: '/pages/user/index'
    }
  },
})
