const app = getApp();

Page({
    data: {
      toView: 'green',
      scrollTop: 100,
      scrollLeft: 0,
      imgUrls: [
        '../../images/boutique/wanzhuanshenghuo1.png',
        '../../images/boutique/wanzhuanshenghuo2.png',
        '../../images/boutique/wanzhuanshenghuo3.png',
        '../../images/boutique/wanzhuanshenghuo4.png'
      ],
      indicatorDots: true,
      autoplay: true,
      interval: 3000,
      duration: 1000,
      spaceImg:[
          {pageurl:"../kecanting/kecanting",url:"../../images/boutique/wudakongjian_kecanting.png",bgurl:"../../images/boutique/kecanting.png",spaceText:"客餐厅"},
          {pageurl:"../woshi/woshi",url:"../../images/boutique/wudakongjian_woshi.png",bgurl:"../../images/boutique/woshi.png",spaceText:"卧室"},
          {pageurl:"../yangtai/yangtai",url:"../../images/boutique/wudakongjian_yangtai.png",bgurl:"../../images/boutique/yangtai.png",spaceText:"阳台"},
          {pageurl:"../chufang/chufang",url:"../../images/boutique/wudakongjian_chufang.png",bgurl:"../../images/boutique/chufang.png",spaceText:"厨房"},
          {pageurl:"../weishengjian/weishengjian",url:"../../images/boutique/wudakongjian_weishengjian.png",bgurl:"../../images/boutique/weishengjian.png",spaceText:"卫生间"}
        ],
        upsetImg:[
            {url:"../../images/boutique/wenti (2).png"},
            {url:"../../images/boutique/wenti (3).png"},
            {url:"../../images/boutique/wenti (1).png"}
        ],
        solveImg:[
            {url:"../../images/boutique/quanbaofuqu_03.png",solveText:"全房瓷砖"},
            {url:"../../images/boutique/quanbaofuqu_05.png",solveText:"全房地板"},
            {url:"../../images/boutique/quanbaofuqu_07.png",solveText:"全房内门"},
            {url:"../../images/boutique/quanbaofuqu_09.png",solveText:"整体橱柜"},
            {url:"../../images/boutique/quanbaofuqu_15.png",solveText:"卫浴十件套"},
            {url:"../../images/boutique/quanbaofuqu_16.png",solveText:"厨房、卫生间门"},
            {url:"../../images/boutique/quanbaofuqu_17.png",solveText:"基础装修"},
            {url:"../../images/boutique/quanbaofuqu_18.png",solveText:"石膏角线"}
        ],
        customarr:[
          {text:"人体工程学设计"},
          {text:"家居收纳设计"},
          {text:"家居动线设计"},
          {text:"软装搭配设计"},
          {text:"风水学学设计"},
          {text:"人性化工程学设计"}
        ],
        
        _num:1,
        kejiPro1: [
          { src: "../../images/boutique/kjsh2.png", productText: "安防报警" },
          { src: "../../images/boutique/kjsh1.png", productText: "智能灯光" },
          { src: "../../images/boutique/kjsh4.png", productText: "智能影音" },
          { src: "../../images/boutique/kjsh3.png", productText: "智能窗帘" }
        ],
        kejiPro2: [
          { src: "../../images/boutique/kjsh2.png", productText: "安防报警" },
          { src: "../../images/boutique/kjsh1.png", productText: "智能灯光" },
          { src: "../../images/boutique/kjsh4.png", productText: "智能影音" },
          { src: "../../images/boutique/kjsh3.png", productText: "智能窗帘" }
        ],
        kejiPro3: [
          { src: "../../images/boutique/kjsh2.png", productText: "安防报警" },
          { src: "../../images/boutique/kjsh1.png", productText: "智能灯光" },
          { src: "../../images/boutique/kjsh4.png", productText: "智能影音" },
          { src: "../../images/boutique/kjsh3.png", productText: "智能窗帘" }
        ],

        technicsPro:[
          {src:"../../images/boutique/gongyi-砌墙.jpg",productText:"砌墙工艺"},
          {src:"../../images/boutique/gongyi-砌墙加固、防裂工艺.jpg",productText:"砌墙加固、防裂工艺"},
          {src:"../../images/boutique/rxh3.png",productText:"包管排污工艺"},
          {src:"../../images/boutique/gongyi-电线管铺设.jpg",productText:"电线管铺设工艺"}

        ],
        salePro:[
          {src:"../../images/boutique/shouhou (4).png",saleText:"水电10年保修"},
          {src:"../../images/boutique/shouhou (1).png",saleText:"防水保修8年"},
          {src:"../../images/boutique/shouhou (2).png",saleText:"基础工程2年"},
          {src:"../../images/boutique/shouhou (3).png",saleText:"基础工程2年"}
        ]


    },

  // 点击切换
  changetab: function (e) {
    var that=this;
     //  获取当前点击的数据
    var current = e.currentTarget.dataset.current;
  //  console.log(current);
   if (this.data._num == current) {
      return false;
       }
   else {
     this.setData({
       _num: current
     })
   } 
 },
     //点击按钮切换到下一个view
  tap: function (e) {
    for (var i = 0; i < order.length; ++i) {
      if (order === this.data.toView) {
        this.setData({
          toView: order[i + 1]
        })
        break
      }
    }
  },
  //通过设置滚动条位置实现画面滚动
  tapMove: function (e) {
    this.setData({
      scrollLeft: this.data.scrollLeft + 10
    })
  },
    changeIndicatorDots: function(e) {                                                
      this.setData({
        indicatorDots: !this.data.indicatorDots
      })
    },
    changeAutoplay: function(e) {
      this.setData({
        autoplay: !this.data.autoplay
      })
    },
    intervalChange: function(e) {
      this.setData({
        interval: e.detail.value
      })
    },
    durationChange: function(e) {
      this.setData({
        duration: e.detail.value
      })
    },
    // 滚动区域
    upper: function(e) {
      console.log(e)
    },
    lower: function(e) {
      console.log(e)
    },
    scroll: function(e) {
      console.log(e)
    },
    tap: function(e) {
      for (var i = 0; i < order.length; ++i) {
        if (order[i] === this.data.toView) {
          this.setData({
            toView: order[i + 1]
          })
          break
        }
      }
    },
    tapMove: function(e) {
      this.setData({
        scrollTop: this.data.scrollTop + 10
      })
    }
  })