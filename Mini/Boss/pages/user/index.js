// pages/user/index.js
const api = require('../../utils/api')

//获取应用实例
const app = getApp()

Page({

  /**
   * 页面的初始数据
   */
  data: {
    project:{}
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    // console.log('load', 'user');    
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    let curProject = app.globalData.curProject;
    // console.log(curProject);
    //如果没有绑定，则提示需要绑定项目
    if (!curProject.id){
      wx.showToast({
        title: `绑定项目才可以使用`,
        icon: 'none',
        duration: 3000
      });
      curProject = { 'compName': '常州易奇科技提示先绑定项目', 'userName': '未绑定' };
    }

    this.setData({
      project: curProject
    });
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },
  noTitlemodalTap: function(){
    let that = this;
    let curProject = app.globalData.curProject;
    // console.log(curProject);
    if (!curProject || !curProject.id){
      wx.showToast({
        title: '请先绑定项目',
        icon: 'none',
        duration: 2000
      });
      return false;
    }

    //提示是否解除绑定
    wx.showModal({
      content: '确定解除已经绑定的项目',
      confirmText: '确定',
      cancelText: '取消',
      success:function(res){
        if (res.confirm != true){
          return false;
        }

        wx.showLoading({
          title: '解除绑定',
        });
        // console.log(res);
        let openid = app.globalData.openid;

        //服务器端解除绑定关系
        if (openid){
          api.httpRequest('unbind', { openid: openid}, function (res) {
            if (res.data.data.success){
              //先删除缓存数据，把项目数据都清掉
              api.removeProjectList();
              app.setCurProject({});
              let curProject = { 'compName': '常州易奇科技提示先绑定项目', 'userName': '未绑定' };
              that.setData({
                project: curProject
              });

              // 获取项目列表后的处理事情
              setTimeout(function () {
                wx.showToast({
                  title: '解除完成',
                  icon: 'success',
                  duration: 2000
                })
              }, 500);
            }else{
              setTimeout(function () {
                wx.showToast({
                  title: '解除失败:'+res.data.data.msg,
                  icon: 'fail',
                  duration: 2000
                })
              }, 200);
            }
          });
        }else{
          wx.showToast({
            title: '解除失败:个人信息丢失',
            icon: 'fail',
            duration: 2000
          })
        }
      }
    })
  },
  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
    return {
      title: '常州易奇科技小程序筒染版',
      path: '/pages/user/index'
    }
  }
})