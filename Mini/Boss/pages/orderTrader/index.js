const api = require('../../utils/api')
const app = getApp()

Page({

  /**
   * 页面的初始数据
   */
  data: {
    yearList:[],
    index: 0,
    year: '',
    inputShowed: false,
    inputVal: "",
    page:1,//下次请求准备加载的页码
    orderDataList:[],
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    let that = this;

    //当前时间戳
    var date = new Date;
    var year = date.getFullYear();
    that.setData({
      year: year
    });

    //获取数据
    app.getYearList(function (yearList) {
      that.setData({
        yearList: yearList
      });

      //判断是否存在当前年，如果存在，则处理index值
      yearList.forEach(function (item, i) {
        if (item == year) {
          that.setData({
            index: i
          });
        }
      });
    });

    //列表数据加载
    this.appendListData();

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
  bindPickerChange(e) {
    var index = e.detail.value;
    this.setData({
      index: index,
      year: this.data.yearList[index],
      orderDataList: [],
      page: 1,
    });
    this.appendListData();
  },
  showInput: function () {
    this.setData({
      inputShowed: true
    });
  },
  hideInput: function () {
    this.setData({
      inputVal: "",
      inputShowed: false
    });
  },
  clearInput: function () {
    this.setData({
      inputVal: ""
    });
  },
  inputTyping: function (e) {
    this.setData({
      inputVal: e.detail.value
    });
  },
  inputConfirm:function(e){
    console.log('input confirm ', e);
    this.setData({
      orderDataList:[],
      page:1,
    });
    this.appendListData();
  },
  onReachBottom :function(e){
    //触底事件
    // console.log('scroll ',e);
    this.appendListData();
  },
  /*
  * 加载数据，一页一页添加
  */
  appendListData :function(){
    wx.showLoading({
      title: '加载数据',
    });

    let curPage = this.data.page;
    let key = this.data.inputVal;
    let year = this.data.year;
    // let orderDataListAll = this.data.orderDataList;

    let that = this;

    let params = {
      api_url: app.globalData.curProject.url,
      account: app.globalData.curProject.userName,
      year: year,
      pageNum: curPage,
      salesName: key,
    };
    api.httpRequest('traderOrderData', params, function (res) {
      console.log('http success');
      var _msg = "加载完成";
      if (res.data.data) {
        let orderDataList = res.data.data.params;
        console.log('orderDataList', orderDataList);
        //设置数据
        // debugger;
        if (orderDataList.data.length > 0){
          var listKey = `orderDataList[${curPage}]`;
          that.setData({
            [listKey]: orderDataList.data || [],
            page: curPage + 1,
          });
          _msg = "加载完成";
        }else{
          _msg = "没有更多数据了";
        }
      }else{
        _msg = "加载失败";
      }

      //提示加载结果
      setTimeout(function(){
        wx.showToast({
          title: _msg,
          icon: 'none',
          duration: 2000
        })
      } ,500);
    });
  },
})