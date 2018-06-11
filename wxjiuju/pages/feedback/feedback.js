const app = getApp();

Page({

        data: {
          textarea: '',
          searchinput:'',
        },
        /*
        *获取意见
        */
        solveprogram: function (e) {
          this.setData({
            textarea: e.detail.value
          })
        },
        showDialogBtn: function (e) {
          var textareacd = this.data.textarea;
          if (textareacd == "") {
            // console.log("我的打印", textareacd);
            this.setData({
              text: '填写意见后才能提交哦',
            })
          }else{
            var textareacd = this.data.textarea;
            this.setData({
              textarea: '',
              searchinput: '',
              text: '感谢您反馈的意见',
            })
          }
          this.setData({
            showModal: true,
          })
          
        },
        bindButtonTap: function() {
          this.setData({
            focus: true
          })
        },
        bindTextAreaBlur: function(e) {
          // console.log(e.detail.value)
        },
        bindFormSubmit: function(e) {
          // console.log(e.detail.value.textarea)
        },

    //事件处理函数
    bindViewTap: function() {
      wx.navigateTo({
        url: '../logs/logs'
      })
    },
    onLoad: function () {
      if (app.globalData.userInfo) {
        this.setData({
          userInfo: app.globalData.userInfo,
          hasUserInfo: true
        })
      } else if (this.data.canIUse){
        // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
        // 所以此处加入 callback 以防止这种情况
        app.userInfoReadyCallback = res => {
          this.setData({
            userInfo: res.userInfo,
            hasUserInfo: true
          })
        }
      } else {
        // 在没有 open-type=getUserInfo 版本的兼容处理
        wx.getUserInfo({
          success: res => {
            app.globalData.userInfo = res.userInfo
            this.setData({
              userInfo: res.userInfo,
              hasUserInfo: true
            })
          }
        })
      }
    },
    getUserInfo: function(e) {
      // console.log(e)
      app.globalData.userInfo = e.detail.userInfo
      this.setData({
        userInfo: e.detail.userInfo,
        hasUserInfo: true
      })
    },
    /**
   * 弹出框蒙层截断touchmove事件
   */
    preventTouchMove: function () {
    },
    /**
     * 隐藏模态对话框
     */
    hideModal: function () {
      this.setData({
        showModal: false
      });
    },
    /**
     * 对话框取消按钮点击事件
     */
    onCancel: function () {
      this.hideModal();
      console.log("取消按钮",this);
    },
    /**
     * 对话框确认按钮点击事件
     */
    onConfirm: function () {
      this.hideModal();
    }
  })