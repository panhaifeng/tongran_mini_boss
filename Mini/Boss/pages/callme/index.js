// pages/user/index.js

Page({

  /**
   * 页面的初始数据
   */
  data: {
    array:[
      { tel: '400-828-5817', text:'免费服务热线'},
      { tel: '133-0611-5213', text: '手机号码' },
    ],
    compName:'常州易奇信息科技',
    shotName:'EQINFO',
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
  callTel: function(e){
    console.log(e);
    wx.makePhoneCall({
      phoneNumber: e.currentTarget.dataset.tel || '400-828-5817' //仅为示例，并非真实的电话号码
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