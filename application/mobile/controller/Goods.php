<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用TP5助手函数可实现单字母函数M D U等,也可db::name方式,可双向兼容
 * ============================================================================
 * $Author: 当燃 2016-01-09
 */
namespace app\mobile\controller;

use think\Controller;
use think\Db;
class Goods extends Base {
    public function index(){
        $style_id = input('style_id',0);
        $apart_id = input('apart_id',0);
        $measure_id = input('measure_id',0);
        $p = input('p/d', 1);
        $size = 18;
        $catDB = DB::name("case_cat");
        $cat1 = $catDB->where("parent_id = 9")->field("cat_id,cat_name")->select();
        $cat2 = $catDB->where("parent_id = 17")->field("cat_id,cat_name")->select();
        $cat3 = $catDB->where("parent_id = 23")->field("cat_id,cat_name")->select();

        $this->assign('cat1',$cat1);
        $this->assign('cat2',$cat2);
        $this->assign('cat3',$cat3);  //获取分类信息

        $this->assign("style_id",$style_id);
        $this->assign("apart_id",$apart_id);
        $this->assign("measure_id",$measure_id);


        $where = '';
        $where.=($style_id!=0) ? "style_id = $style_id ":'';
        if($apart_id!=0 && $style_id!=0){
            $where.= 'and ';
        }
        $where.=($apart_id!=0)?"apart_id = $apart_id ":'';
        if(($style_id!=0 && $measure_id!=0) or ($apart_id!=0 && $measure_id!=0)){
            $where.= 'and ';
        }
        $where.=($measure_id!=0)?"measure_id = $measure_id":'';  //筛选条件


        $goodsDB = DB::name("case");
        $imgDB = DB::name("case_images");


        $list = $goodsDB->where($where)->page("$p,$size")->field('case_id,case_name,style_id,apart_id,measure_id')->order("add_time desc")->select();
        $count =$goodsDB->where($where)->count();


        foreach($list as $k=>$v){
            $img = $imgDB->where("caseid = $v[case_id]")->field("image_url")->order("img_id desc")->find();
            $list[$k]['img'] = $img['image_url'];
            $style = DB::name("case_cat")->where("cat_id = $v[style_id]")->field('cat_name')->find();
            $apart = DB::name("case_cat")->where("cat_id = $v[apart_id]")->field('cat_name')->find();
            $measure = DB::name("case_cat")->where("cat_id = $v[measure_id]")->field('cat_name')->find();
            $list[$k]['style'] = $style['cat_name'];
            $list[$k]['apart'] = $apart['cat_name'];
            $list[$k]['measure'] = $measure['cat_name'];
            $list[$k]['goods_name'] = substr($list[$k]['case_name'],0,strlen($list[$k]['case_name'])*1-5*3);

        }

        $this->calcul_page($count,$size);//分页显示输出

        $this->assign('list',$list);
        $this->assign('p',$p);

        return $this->fetch();
    }

    public function goodsinfo(){
        $goods_id = input('goods_id');

        $goods = DB::name("case")->where("case_id = $goods_id")->field('case_name,style_id,apart_id,designer_id')->find();
        $style = DB::name("case_cat")->where("cat_id = $goods[style_id]")->field('cat_name')->find();
        $apart = DB::name("case_cat")->where("cat_id = $goods[apart_id]")->field('cat_name')->find();
        $img = DB::name("case_images")->field("img_id,caseid,image_url")->where("caseid = $goods_id")->select();
        $des_name = DB::name("designer")->field("designer_name,sm_img")->where("id = $goods[designer_id]")->find();

        /*$info = DB::name("goods_images")->where("goods_id = $goods_id")->field("img_info")->order("img_id desc")->select();
        foreach ($img as $k=>$v){
            $img[$k]['img_info'] =$info[$k]['img_info'];
        }*/

        $this->assign('name',$goods['goods_name']);
        $this->assign('img',$img);
        $this->assign('style',$style['cat_name']);
        $this->assign('apart',$apart['cat_name']);
        $this->assign('des_name',$des_name['designer_name']);
        $this->assign('des_img',$des_name['sm_img']);

        return $this->fetch();
    }
}