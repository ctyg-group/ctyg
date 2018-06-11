// page/component/pages/swiper/swiper.js
Component({
  /**
   * 组件的属性列表
   */
  properties: {
    // 弹窗标题
    title: {            // 属性名
      type: String,     // 类型（必填），目前接受的类型包括：String, Number, Boolean, Object, Array, null（表示任意类型）
      value: '标题'     // 属性初始值（可选），如果未指定则会根据类型选择一个
    },
    height:{
      type:String,
      value:'300px'
    },
    indicatorDot:{
      type:Boolean,
      value:false
    },
    autoplay:{
      type:Boolean,
      value:false
    },
    interval:{
      type:Number,
      value:5000
    },
    duration:{
      type:Number,
      value:1000
    },
    imgUrls:{
      type:Array,
      value:[
        'http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg',
        'http://img06.tooopen.com/images/20160818/tooopen_sy_175866434296.jpg',
        'http://img06.tooopen.com/images/20160818/tooopen_sy_175833047715.jpg'
      ]
    }
  },

  /**
   * 组件的初始数据
   */
  data: {
    indicatorDots: false,
    dotsClass: ['on', '', '', '', ''],
    swiperCurrent:0
  },

  /**
   * 组件的方法列表
   */
  methods: {
    changeIndicatorDots:function(e){
      this.setData({
        indicatorDots:!this.data.indicatorDots
      })
    },
    changeAutoplay:function(e){
      this.setData({
        autoplay:!this.data.autoplay
      })
    },
    intervalChange:function(e){
      this.setData({
        interval:e.detail.value
      })
    },
    durationChange:function(e){
      this.setData({
        duration:e.detail.value
      })
    },
    swiperChange: function (e) {
      this.setData({
        swiperCurrent: e.detail.current
      })
    } 
  }
})
