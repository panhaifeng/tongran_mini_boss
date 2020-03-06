const api = require('../../utils/api')
const app = getApp()
var resultArr = [];
Page({

  /**
   * 页面的初始数据
   */
  data: {
    yearList:[],
    index: 0,
    indexM: 0,
    year: '',
    monthList:[],
    month: '',
    inputShowed: false,
    inputVal: "",
    page:1,//下次请求准备加载的页码
    orderDataList:[],
    fruitList:[],
    heightHead:'',
    offset:''
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
    var month = date.getMonth()+1;
    that.setData({
       month:month
    });
       //获取数据
    app.getMonthList(function (monthList) {
      that.setData({
        monthList: monthList
      });

      //判断是否存在当前年，如果存在，则处理index值
      monthList.forEach(function (item, i) {
        if (item == month) {
          that.setData({
            indexM: i
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

  touchStart(e) {
    let that = this
    let query = wx.createSelectorQuery()
    query.selectAll(".scroll-view-item").boundingClientRect(function (res) {
      let size =(res[0].left-res[0].width);
      that.setData({
        leftSize: 'margin-left:'+size+'px;', 
      })
    }).exec()
  },
  touchEnd(e) {
    let that = this
    let query = wx.createSelectorQuery()
    query.selectAll(".scroll-view-item").boundingClientRect(function (res) {
      let size =(res[0].left-res[0].width);
      that.setData({
        leftSize: 'margin-left:'+size+'px;', 
      })
    }).exec()
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
      reload: 1,
    });
    this.appendListData();
  },
  bindPickerMonthChange(e) {
    var indexM = e.detail.value;
    this.setData({
      indexM: indexM,
      month: this.data.monthList[indexM],
      orderDataList: [],
      fruitList:[],
      page: 1,
      reload: 1,
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
      reload:1,
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
    let month = this.data.month;
    // let orderDataListAll = this.data.orderDataList;

    let that = this;

    let params = {
      api_url: app.globalData.curProject.url,
      account: app.globalData.curProject.userName,
      year: year,
      month:month,
      pageNum: curPage,
      vatNum: key,
    };
    api.httpRequest('clientOrderData', params, function (res) {
      var _msg = "加载完成";
      // console.log(res);
      if (res.data.data) {
        let orderDataList = res.data.data.params;
        let rowset = res.data.data.params;
        // console.log(that.data);
        let isReload = that.data.reload;
        // console.log('orderDataList', orderDataList);
        let firstReload = 0;
        if(isReload==1){
            wx.getSystemInfo({
              success:function(rrs){
                  var query = wx.createSelectorQuery();
                  query.select('.table-module').boundingClientRect();
                  query.exec(function(rr){
                  var is_height = Number(rr[0].height);
                    that.setData({
                       HeightH:500
                    });
                  });
              }
            });
        }else{
            that.setData({
              HeightH:'auto'
            })
        }
        if(resultArr.length==0||isReload==1||curPage==1){  //当第一次加载的时候，不需要追加数据
           if(rowset.dataOne){
              resultArr = rowset.dataOne;
           }else{
              resultArr = [];
           }
            firstReload = 1;
        }else{
            let newArr = rowset.dataOne;
            if(newArr){
              for(var i=0;i<resultArr.length;i++){
                  for(var k=0;k<newArr[i].list.length;k++){
                      resultArr[i].list.push(newArr[i].list[k]);
                  }
              }
            }
        }
        //设置数据
        if (orderDataList.data.length > 0){
          var listKey = `orderDataList[${curPage}]`;
          var listKeyOne = `fruitList[1]`;
          
          that.setData({
            [listKey]: orderDataList.data || [],
            [listKeyOne]:resultArr || [],
            page: curPage + 1,
            reload:0,
            heightHead:firstReload==1?'height:auto!important;height:200px;min-height:200px;':' '
          });
          _msg = "加载完成";
        }else{
          var lk = `fruitList[1]`;
          that.setData({
            [lk]:resultArr || []
          });
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
      } ,100);
    });
  },

    onPageScroll: function (e) {
    //console.log(e)
    let that = this
    let query = wx.createSelectorQuery()

    query.selectAll(".scroll-box").boundingClientRect(function (res) {
      

      /*that.setData({
        isshow: isshow, 
        styleText:"position:fixed;top:0;",
      })*/
    }).exec()

    query.selectAll(".scroll-view-item").boundingClientRect(function (res) {

      let size =res.length;
      let position = -1;
      let topshow = -1000;//根据需求设置大小
      let i=0;

      //根据 top  的 大小 获取 当前距离顶部最近的view 的下标， 负数最大值 或者是0，
      for(i=0;i<size;i++){
        let top = res[i].top;
        if(top<=0 && top>topshow ){
          topshow = top;
          position=i;
        }
      }
      // console.log("当前坐标是 position = "+position)
      // console.log("top "+res[0].top)
      let isshow =false;
      if (res[0].top<0){
        if(position==-1) position=0;
          isshow = true;
      }
      console.log("isshow "+isshow)


      let sizeOffset =(res[0].left-res[0].width);

      that.setData({
        isshow:isshow,
        styleText:"position:fixed;top:0;",
        leftSize: 'margin-left:'+sizeOffset+'px;', 
      })
    }).exec()
  },
})