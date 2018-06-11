<?php
namespace app\mobile\controller;
use think\Controller;
use think\Db;
class Designer extends Base {
    public function index(){
        $ex_id = input('ex_id',0); //年限
        $level_id = input('level_id',0);
        $style_id = input('style_id',0);

        $this->assign("ex_id",$ex_id);
        $this->assign("level_id",$level_id);
        $this->assign("style_id",$style_id);

        $p = input('p/d', 1);
        $size = 10;

        $desDB = DB::name("designer");
        $catDB = DB::name('designer_cat');
        $ex = $catDB->where("parent_id = 1")->field("cat_id,cat_name")->select();
        $level = $catDB->where("parent_id = 6")->field("cat_id,cat_name")->select();
        $style = $catDB->where("parent_id = 10")->field("cat_id,cat_name")->select();

        $this->assign('cat1',$ex);
        $this->assign('cat2',$level);
        $this->assign('cat3',$style);  //获取分类信息

        $where = '';
        $where.=($ex_id!=0) ? "ex_id = $ex_id ":'';
        if($level_id!=0 && $ex_id!=0){
            $where.= 'and ';
        }
        $where.=($level_id!=0)?"level_id = $level_id ":'';
        if(($ex_id!=0 && $style_id!=0) or ($level_id!=0 && $style_id!=0)){
            $where.= 'and ';
        }
        $where.=($style_id!=0)?"style_id = $style_id":'';  //筛选条件

        $count =$desDB->where($where)->count();
        $list = $desDB->field("id,designer_name,designer_ex,designer_num,order_num,level_id,style_id,img,sm_img")->where($where)->page("$p,$size")->select();

        $goodsDB = DB::name("case");
        foreach($list as $k=>$v){
            $goods = $goodsDB->field("case_name")->where("designer_id = $v[id]")->find();
            $level = $catDB->field('cat_name')->where("cat_id = $v[level_id]")->find();
            $style = $catDB->field('cat_name')->where("cat_id = $v[style_id]")->find();

            $list[$k]['case'] = $goods['case_name'];
            $list[$k]['level'] = $level['cat_name'];
            $list[$k]['style'] = $style['cat_name'];

        }

        $this->calcul_page($count,$size);//分页显示输出

        $this->assign('list',$list);
        $this->assign('p',$p);

        return $this->fetch();
    }

    public function desinfo(){
        $id = input('id');
        $desDB = DB::name("designer");
        $goodsDB = DB::name("case");

        $info = $desDB->field("id,designer_name,designer_ex,designer_num,order_num,pop_num,level_id,style_id,mobile,img,sm_img")->where(" id =$id")->find();
        $goods = $goodsDB->field("case_name")->where("designer_id = $id")->find();
        $style = DB::name('designer_cat')->field('cat_name')->where("cat_id = $info[style_id]")->find();
        $level = DB::name('designer_cat')->field('cat_name')->where("cat_id = $info[level_id]")->find();

        $info['case_name'] = $goods['case_name'];
        $info['style'] = $style['cat_name'];
        $info['level'] = $level['cat_name'];


        $goods_list = $goodsDB->where("designer_id = $id")->field('case_id,case_name')->select();
        $goods_imgDB = DB::name("case_images");
        foreach($goods_list as $k=>$v){
            $img = $goods_imgDB->where("caseid = $v[case_id]")->field("image_url")->order("img_id desc")->find();
            $goods_list[$k]['img'] = $img['image_url'];
            $goods_list[$k]['case_name'] = substr($goods_list[$k]['case_name'],0,strlen($goods_list[$k]['case_name'])*1-5*3);
        }

        $this->assign('info',$info);
        $this->assign("goods_list",$goods_list);
        return $this->fetch();
    }
}