
const echarts = require('../../component/ec-canvas/echarts')
const api = require('../../utils/api')
const app = getApp()

function setOption(chart ,data) {
  const option = {
    color: ['#003366','#278296'],
    tooltip: {
      trigger: 'axis',
      axisPointer: {            // 坐标轴指示器，坐标轴触发有效
        type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
      }
    },
    legend: {
      data: ['大货', '大样']
    },
    grid: {
      left: 10,
      right: 10,
      bottom: 15,
      top: 40,
      containLabel: true
    },
    xAxis: [
      {
        type: 'value',
        axisLine: {
          lineStyle: {
            color: '#999'
          }
        },
        axisLabel: {
          color: '#666',
          interval: 0,//让所有坐标值全部显示
          rotate: 30,//让坐标值旋转一定的角度
        },
        axisTick :{show:false}
      }
    ],
    yAxis: [
      {
        type: 'category',
        axisTick: { show: false },
        data: ['1月', '2月', '3月', '4月', '5月', '6月', '7月','8月','9月','10月','11月','12月'],
        axisLine: {
          lineStyle: {
            color: '#999'
          }
        },
        axisLabel: {
          color: '#666'
        }
      }
    ],
    series: [
      {
        name: '大货',
        type: 'bar',
        label: {
          normal: {
            show: true,
            position: 'inside',
            rich:{}
          }
        },
        data: data.dahuo,
        // itemStyle: {
        //   normal: {
        //     label: {
        //       color: '#006699',
        //       position: 'right',
        //     }
        //   }
        // }
      },
      {
        name: '大样',
        type: 'bar',
        label: {
          normal: {
            show: true,
            rich: {}
          }
        },
        data: data.dayang,
        itemStyle: {
          normal: {
            label: {
              color: '#278296',
              position :'right',
            }
          }
        }
      }
    ]
  };
  chart.setOption(option);
}

Page({

  /**
   * 页面的初始数据
   */
  data: {
    ec: {
      // 将 lazyLoad 设为 true 后，需要手动初始化图表
      lazyLoad: true
    },
    isLoaded: false,
    isDisposed: false,
    orderData:{
      dahuo:[] ,
      dayang:[]
    },
    orderDataList:[],
    listSum:[],
    yearList:[],
    index:0,
    year:'',
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
    app.getYearList(function (yearList){
      that.setData({
        yearList: yearList
      });

      //判断是否存在当前年，如果存在，则处理index值
      yearList.forEach(function(item,i){
        if (item == year){
          that.setData({
            index: i
          });
        }
      });
    });

    // console.log('yearList', yearList);
    //列表数据加载
    this.getListData();
    
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
    let that = this;
    this.ecComponent = this.selectComponent('#mychart-dom-bar');
    that.init(that.data.orderData);
    //获取数据并重新渲染
    that.getMapData(); 
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },
   /**
   * 获取图表数据
   */
  getMapData:function(){
    wx.showLoading({
      title: '数据加载',
    });

    let that = this;
    let params = {
      api_url: app.globalData.curProject.url,
      account: app.globalData.curProject.userName,
      year : that.data.year,
    };
    api.httpRequest('monthOrderMapData', params, function (res) {
      if (res.data.data) {
        let dataOrder = res.data.data.params;
        // console.log(dataOrder);
        //设置数据
        that.setData({
          "orderData.dahuo": dataOrder[0].data || [],
          "orderData.dayang": dataOrder[1].data ||[],
        });
        //渲染图表
        that.init(that.data.orderData);
      }
    });
  },
  // 点击按钮后初始化图表
  init: function (data) {
    this.ecComponent.init((canvas, width, height) => {
      // 获取组件的 canvas、width、height 后的回调函数
      // 在这里初始化图表
      const chart = echarts.init(canvas, null, {
        width: width,
        height: height
      });
      setOption(chart ,data);

      // 将图表实例绑定到 this 上，可以在其他成员函数（如 dispose）中访问
      this.chart = chart;

      this.setData({
        isLoaded: true,
        isDisposed: false
      });

      // 注意这里一定要返回 chart 实例，否则会影响事件处理等
      return chart;
    });
  },
  
  dispose: function () {
    if (this.chart) {
      this.chart.dispose();
    }

    this.setData({
      isDisposed: true
    });
  },
  //当改变年份的时候
  bindPickerChange: function(e){
    let index = e.detail.value;
    // console.log(e);
    this.setData({
      index: index,
      year: this.data.yearList[index]
    });

    //刷新图表数据
    this.getMapData();
    this.getListData();
  },
  //列表数据加载
  getListData: function(){
    let that = this;
    let params = {
      api_url: app.globalData.curProject.url,
      account: app.globalData.curProject.userName,
      year: that.data.year,
    };
    api.httpRequest('monthOrderData', params, function (res) {
      if (res.data.data) {
        let orderDataList = res.data.data.params;
        console.log('orderDataList', orderDataList);
        if (orderDataList.sum){
          orderDataList.sum.type = 'sum';
          orderDataList.data.push(orderDataList.sum);
        }        
        
        //设置数据
        that.setData({
          orderDataList: orderDataList.data || [],
        });
      }
    });
  },
})