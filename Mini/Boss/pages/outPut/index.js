const api = require('../../utils/api')
const app = getApp()

Page({

  /**
   * 页面的初始数据
   */
  data: {
    wxlogin: true,
    date:'',
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    let dd = this.getNowFormatDate();
    this.setData({
      date: dd
    })
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
    // AUTH.checkHasLogined().then(isLogined => {
    //   this.setData({
    //     wxlogin: isLogined
    //   })
    // })
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

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
    
  },
  /**
   * 时间选择
   */
  bindDateChange:function(e){
    this.setData({
      date:e.detail.value
    })
  },
  /**
   * 加载缸信息
   */
  getGangInfo:function(e){
    console.log(e.detail);
    let ganghao = e.detail.value;
    let params = {
        api_url: project.url,
        ganghao:ganghao,
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

  },
  getNowFormatDate:function(){
    var date = new Date();
    var seperator1 = "-";
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
      month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
      strDate = "0" + strDate;
    }
    var currentdate = year + seperator1 + month + seperator1 + strDate;
    return currentdate;
  },
  async bindSave(e) {
    // 提交保存
    let comName = e.detail.value.comName;
    let tfn = e.detail.value.tfn;
    let mobile = e.detail.value.mobile;
    let amount = e.detail.value.amount;
    let consumption = e.detail.value.consumption;
    let remark = e.detail.value.remark;
    let address = e.detail.value.address;
    let bank = e.detail.value.bank;
    if (!mobile) {
      wx.showToast({
        title: '请填写您在工厂注册的手机号码',
        icon: 'none'
      })
      return
    }
    if (!comName) {
      wx.showToast({
        title: '公司名称不能为空',
        icon: 'none'
      })
      return
    }

    const extJsonStr = {}
    extJsonStr['api工厂账号'] = mobile
    extJsonStr['地址与电话'] = address
    extJsonStr['开户行与账号'] = bank
    WXAPI.invoiceApply({
      token: wx.getStorageSync('token'),
      comName,
      tfn,
      amount,
      consumption,
      remark,
      extJsonStr: JSON.stringify(extJsonStr)
    }).then(res => {
      if (res.code == 0) {
        wx.showModal({
          title: '成功',
          content: '提交成功，请耐心等待我们处理！',
          showCancel: false,
          confirmText: '我知道了',
          success(res) {
            wx.navigateTo({
              url: "/pages/invoice/list"
            })
          }
        })
      } else {
        wx.showModal({
          title: '失败',
          content: res.msg,
          showCancel: false,
          confirmText: '我知道了'
        })
      }
    })
  },
})