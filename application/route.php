<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
Route::get('zzal/anli/:case_id','Index/Cases/caseinfo');
Route::get('zxxt/xuetang/:article_id','Index/News/newsinfo');
Route::get('zxbk/baike/:enped_id','Index/Baike/baikeinfo');
Route::get('gdzb/gongdi/:id','Index/Worklive/worklive_info');
Route::get('xwzx/xw/:article_id','Index/News/newsid');
Route::get('zxbk/cat_<cat_id>_<p>','Index/Baike/baikecat',[],['cat_id'=>'\w+','p'=>'\w+']);
Route::get('zxxt/cat_<cat_id>_<p>','Index/News/typelist',[],['cat_id'=>'\w+','p'=>'\w+']);
Route::get('xwzx/cat_<cat_id>_<p>','Index/News/newslist',[],['cat_id'=>'\w+','p'=>'\w+']);
Route::get('fwzx/shaixuan_<ex_id>_<level_id>_<style_id>_<c>_<p>','Index/Designer/fwzx',[],['ex_id'=>'\w+','level_id'=>'\w+','style_id'=>'\w+','c'=>'\w+','p'=>'\w+']);
Route::get('zzal/shaixuan_<style_id>_<apart_id>_<measure_id>_<p>','Index/Cases/index',[],['style_id'=>'\w+','apart_id'=>'\w+','measure_id'=>'\w+','p'=>'\w+']);
Route::get('fwzx/sjs_<id>_<p>','Index/Designer/desdata',[],['id'=>'\w+','p'=>'\w+']);
Route::get('gdzb/shaixuan_<stage_id>_<hot_id>_<apart_id>_<p>','Index/Worklive/index',[],['stage_id'=>'\w+','hot_id'=>'\w+','apart_id'=>'\w+','p'=>'\w+']);
Route::rule('','Index/Index/index');
Route::rule('zpzx','jiuju/Index/Index/zpzx');
Route::rule('fwzx','Index/Designer/fwzx');
Route::rule('zzal','Index/Cases/index');
Route::rule('gdzb','Index/Worklive/index');
Route::rule('zxxt','Index/News/index');
Route::rule('zxbk','Index/Baike/index');
//Route::rule('about','Index/About/index');
Route::rule('about','ctyg/Index/Index/about');
Route::rule('join','Index/About/joinme');
Route::rule('call','Index/About/callme');
Route::rule('huodong','Index/special/special');

// 移动端
Route::rule('mindex','mobile/index/index');
Route::rule('mlfbj','mobile/index/lfbj');

Route::get('mzzal/anli/:goods_id','mobile/goods/goodsinfo');
Route::get('mzxxt/xuetang/:article_id','mobile/school/artinfo');
Route::get('mzxbk/baike/:enped_id','mobile/enped/enpedinfo');
Route::get('mxwzx/xw/:article_id','mobile/school/news');
Route::get('mgdzb/zb/:id','mobile/worklive/workinfo');

Route::get('mzxxt/cat_<cat_id>_<p>','mobile/school/child_cat',[],['cat_id'=>'\w+','p'=>'\w+']);
Route::get('mzxbk/cat_<cat_id>_<p>','mobile/enped/enpedlist',[],['cat_id'=>'\w+','p'=>'\w+']);
Route::get('mzzal/shaixuan_<style_id>_<apart_id>_<measure_id>_<p>','mobile/goods/index',[],['style_id'=>'\w+','apart_id'=>'\w+','measure_id'=>'\w+','p'=>'\w+']);
Route::get('mfwzx/sjs_<id>','mobile/designer/desinfo',[],['id'=>'\w+']);
Route::get('mfwzx/sx_<ex_id>_<level_id>_<style_id>_<p>','mobile/designer/index',[],['ex_id'=>'\w+','level_id'=>'\w+','style_id'=>'\w+','p'=>'\w+']);
Route::rule('mfwzx','mobile/designer/index');
Route::rule('mzzal','mobile/goods/index');
Route::rule('mzxxt','mobile/school/index');
Route::rule('mzxbk','mobile/enped/index');
Route::rule('m_huodong','mobile/m_special/m_special');
Route::rule('mgdzb','mobile/worklive/index');

//活动
Route::rule('jiuju315','Index/Activity/hd201803');
Route::rule('jiuju04','Index/Activity/hd201804');
Route::rule('mjiuju04','Mobile/index/hd201804');
Route::rule('special','Index/special/special');

