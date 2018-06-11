Page({
  data: {
    index: 0,
    multiArray: [['1室', '2室', "3室","4室"], ['1厅', '2厅'],['1厨'],["1卫","2卫"]],
    objectMultiArray: [
      [
        {
          id: 0,
          name: '1室'
        },
        {
          id: 1,
          name: '2室'
        },
        {
          id: 2,
          name: "3室"
        },
        {
          id: 3,
          name: "4室"
        }
      ], [
        {
          id: 0,
          name: '1厅'
        },
        {
          id: 1,
          name: '2厅'
        },
        {
          id: 2,
          name: "3厅"
        }
      ], [
        {
          id: 0,
          name: '1厨'
        }
      ],[
        {
          id: 0,
          name: '1卫'
        },
        {
          id: 1,
          name: '2卫'
        }
      ]
    ],
    multiIndex: [0],
    volume: '',
    text:'',
    myname: '',
    telephone: '',
  },
  bindMultiPickerColumnChange: function (e) {
    console.log('修改的列为', e.detail.column, '，值为', e.detail.value);
    var data = {
      multiArray: this.data.multiArray,
      multiIndex: this.data.multiIndex
    };
    data.multiIndex[e.detail.column] = e.detail.value;
    console.log("我的数据",data);
    this.setData(data);
  },
  /*
  *获取建筑面积
  */
  volumeInput: function (e) {
    this.setData({
      volume: e.detail.value
    })
  },
  // 获取称呼
  mynameInput: function (e) {
    this.setData({
      myname: e.detail.value
    })
  },
  // 获取电话
  telephoneInput: function (e) {
    this.setData({
      telephone: e.detail.value
    })
  },
  /**
    * 弹窗
    */
  showDialogBtn: function () {
    var volumecd = this.data.volume;
    var mynamecd = this.data.myname;
    var telephonecd = this.data.telephone;
    if (volumecd == ""  && mynamecd == "" && telephonecd == "") {
      // console.log("用户没有填写建筑面积+昵称+手机号码");
      this.setData({
        text: '建筑面积.称呼.手机号码未填写'
      }) 
    } else if (volumecd == "" && mynamecd == "") {
      // console.log("用户没有填写建筑面积+昵称");
      this.setData({
        text: '建筑面积.称呼未填写'
      }) 
    } else if (volumecd == "" && telephonecd == "") {
      // console.log("用户没有填写建筑面积+手机号码");
      this.setData({
        text: '建筑面积.手机号码未填写'
      }) 
    } else if (mynamecd == "" && telephonecd == "") {
      // console.log("用户没有填写昵称+手机号码");
      this.setData({
        text: '称呼.手机号码未填写'
      }) 
    } else if (volumecd == "") {
      // console.log("用户没有填写建筑面积");
      this.setData({
        text: '建筑面积未填写'
      }) 
    } else if (mynamecd == "") {
      // console.log("用户没有填写昵称");
      this.setData({
        text: '称呼未填写'
      }) 
    } else if (telephonecd == "") {
      // console.log("用户没有填写手机号码");
      this.setData({
        text: '手机号码未填写'
      })  
    } else {
      // console.log("全部填写");
      this.setData({
        text: '工作人员稍后会联系您'
      })  
    }
    this.setData({
      showModal: true
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
  },
  /**
   * 对话框确认按钮点击事件
   */
  onConfirm: function () {
    this.hideModal();
  }
})