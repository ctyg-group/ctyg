//获取应用实例
const app = getApp()

function initSubMenuDisplay() { 
  return ['hidden', 'hidden', 'hidden'];
};

function initSubMenuHighLight() {    
  return [
    ['','','','',''],
    ['',''],
    ['','','']
  ];
};

//定义初始化数据，用于运行时保存
var initSubMenuHighLight = [
  ['','','','',''],
  ['',''],
  ['','','']
];

Page({
  data: {
    subMenuDisplay:initSubMenuDisplay(),
    _num: 0,
    Head: [
      { text: "风格", num: "0", index: "1" },
      { text: "面积", num: "1", index: "2" },
      { text: "户型", num: "2", index: "3" }
    ],
    detailsArr:[
      { url: "../bk_details/bk_details", src: "../../images/details/wanzhuanshenghuo4.png", text: "72平米两室两厅精装装修效果图\n", style:"美式"},
      { url: "../bk_details/bk_details", src: "../../images/details/wanzhuanshenghuo4.png", text: "72平米两室两厅精装装修效果图\n", style: "美式" },
       { url: "../bk_details/bk_details", src: "../../images/details/wanzhuanshenghuo4.png", text: "72平米两室两厅精装装修效果图\n", style: "美式" },
      { url: "../bk_details/bk_details", src: "../../images/details/wanzhuanshenghuo4.png", text: "72平米两室两厅精装装修效果图\n", style: "美式" }
    ]
  },
   //获取当前显示的一级菜单标识
   tapMainMenu: function(e) {
    // 生成数组，全为hidden的，只对当前的进行显示
   var index = parseInt(e.currentTarget.dataset.index); 
   //如果目前是显示则隐藏，反之亦反之。同时要隐藏其他的菜单      
   var newSubMenuDisplay = initSubMenuDisplay();
   if(this.data.subMenuDisplay[index] == 'hidden') {
       newSubMenuDisplay[index] = 'show';
       this.setData({
         _num: e.target.dataset.num 
       });
   } else {
       newSubMenuDisplay[index] = 'hidden';
       this.setData({
         _num: 0
       });
   }        
   // 设置为新的数组,改变颜色;
   this.setData({
     subMenuDisplay: newSubMenuDisplay,
   });
},


// 监听二级点击事件
//获取当前显示的一级菜单标识
tapSubMenu: function(e) { 
 var index = parseInt(e.currentTarget.dataset.index);
 console.log(index);  // 隐藏所有一级菜单
 this.setData({ 
    subMenuDisplay: initSubMenuDisplay()
 }); 
},

tapSubMenu: function(e) {        // 隐藏所有一级菜单
 this.setData({
     subMenuDisplay: initSubMenuDisplay()
 });        // 处理二级菜单，首先获取当前显示的二级菜单标识
 var indexArray = e.currentTarget.dataset.index.split('-');        
 console.log("indexArray : " + indexArray);        
 var newSubMenuHighLight = initSubMenuHighLight();        // 与一级菜单不同，这里不需要判断当前状态，只需要点击就给class赋予highlight即可
 newSubMenuHighLight[indexArray[0]][indexArray[1]] = 'highlight';       
  console.log(newSubMenuHighLight);        // 设置为新的数组
 this.setData({
     subMenuHighLight: newSubMenuHighLight
 });
},

// 隐藏所有一级菜单
tapSubMenu: function(e) {        
 this.setData({
   // 处理二级菜单，首先获取当前显示的二级菜单标识
     subMenuDisplay: initSubMenuDisplay()
 });      
 // 初始化状态  
 var indexArray = e.currentTarget.dataset.index.split('-');        
 // var newSubMenuHighLight = initSubMenuHighLight;
  // 如果点中的是一级菜单，则先清空状态，即非高亮模式，然后再高亮点中的二级菜单；如果不是当前菜单，而不理会。经过这样处理就能保留其他菜单的高亮状态
 for (var i = 0; i < initSubMenuHighLight.length; i++) {           
     if (indexArray[0] == i) {  
        // 实现清空              
       for (var j = 0; j < initSubMenuHighLight[i].length; j++) { 
           // 将当前菜单的二级菜单设置回去
             initSubMenuHighLight[i][j] = '';
         } 
     }
 }        
 // 与一级菜单不同，这里不需要判断当前状态，只需要点击就给class赋予highlight即可
 initSubMenuHighLight[indexArray[0]][indexArray[1]] = 'highlight';        // 设置为新的数组
 this.setData({
     subMenuHighLight: initSubMenuHighLight
 });
}

});