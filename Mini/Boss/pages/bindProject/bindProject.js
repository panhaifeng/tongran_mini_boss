const api = require('../../utils/api')
//获取应用实例
const app = getApp()

Page({

  /**
   * 页面的初始数据
   */
  data: {
    projectList: [{ showText: '请选择账号'}],
    index: 0,
    curProject: {},
    navigateType:'',
    openid: '',
    title:'绑定账号',
    headDesc:'若还未绑定，请到PC-ERP登录并扫码绑定',
  },
   
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    let that = this;
    console.log('bind page : ',options);
    //记住跳转类型
    if (options.navigateType){
      that.setData({
        navigateType: options.navigateType
      });
    }

    //设置openid
    that.setData({
      openid: app.globalData.openid
    });
    
    //获取当前已经设置好的全局当前项目
    let curProject = app.globalData.curProject;

    //设置可选数组
    that.setProjectList(curProject);

  },
  setProjectList : function(project){
    var that = this;
    //获取项目地址列表
    if (app.globalData.openid) {
      let index = -1;
      api.getProjectList(app.globalData.openid, function (res) {
        var newArray = [{ showText: '请选择账号'}];
        if (res.projectList.length) {
          res.projectList.forEach(function (item, i) {
            if (item && item.id){
              newArray.push(item);
            }
            
            //如果传值，则默认选中
            if (project && project.id == item.id){
              index = i + 1;
            }    
          })
        }
        // 获取项目列表后的处理事情
        that.setData({
          projectList: newArray
        });
        if (index > 0 )that.setCurProject(index);
        // console.log('new data:', that.data.projectList);
      });
    }
  },
  bindPickerChange : function(e){
    // console.log('picker value:', e);
    var _val = e.detail.value;
    this.setCurProject(_val);
    //提示消息
    wx.showToast({
      title: '设置成功',
      icon: 'success',
      duration: 2000
    })
  },
  setCurProject : function(index){
    //选中picker后，参数为picker选中的序号值
    this.setData({
      index: index
    });

    //得到当前的project
    var project = this.data.projectList[index];
    if (project && project.id > 0) {
      //重新赋值全局变量并改变缓存
      app.setCurProject(project);
    }  
  },
  scanBind : function(e){
    var that = this;
    //扫码绑定
    wx.scanCode({
      onlyFromCamera: true,
      scanType: ['qrCode'],
      success(res) {
        wx.showLoading({
          title: '处理中',
        });
        // console.log('sacn result :',res)
        //获取到二维码后，进行后台数据请求，验证二维码有效性，并保存到服务器
        var _param = { scanCode: res.result, platFrom: 'miniWeixin' ,openid:that.data.openid};
        api.httpRequest('scanCodeBindProject', _param,function(res){
          //处理请求后的数据
          // console.log(res);
          //表示项目有了
          if (res.data.data && res.data.data.id){
            var tmpPro = res.data.data;
            //把新的项目更新到项目列表的缓存中
            api.pushProjectList(tmpPro);
            //更新本页项目列表
            that.setProjectList(tmpPro);
            //提示消息
            setTimeout(function(){
              wx.showToast({
                title: '绑定成功',
                icon: 'success',
                duration: 2000
              })
            },500);
          }else{
            setTimeout(function () {
              wx.showToast({
                title: res.data.res || res.data.data.msg,
                icon: 'none',
                duration: 2000
              })
            }, 500);
          }
        });
      }
    })
    // console.log('scan event:' ,e);
  },
  back: function(){
    if (this.navigateType == 'index'){
      wx.switchTab({
        url: '/pages/index/index'
      })
    }else{
      wx.navigateBack({
        delta: 1
      })
    }
  }
})