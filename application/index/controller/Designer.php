<?php
/**
 * 久居
 * ============================================================================
 * 本系统由久居整装开发
 * ============================================================================
 * Author: Suki
 * Date: 2018-03-09
 */
namespace app\index\controller;
use think\Db;
use think\Page;
class Designer extends Common
{

    public function fwzx()
    {
        $p          = input('p', 1);
        $c          = input('c',0);
        $ex_id      = input('ex_id',0);
        $level_id   = input('level_id',0);
        $style_id   = input('style_id',0);
        $size = 5;
        //搜索条件
        $catDB  = DB::name("designer_cat");
        if($ex_id != 0){ $where['ex_id'] = $ex_id; }else{$where['ex_id']=array("gt",0);}
        if($level_id != 0){ $where['level_id'] = $level_id; }else{$where['level_id']=array("gt",0);}
        if($style_id != 0){ $where['style_id'] = $style_id; }else{$where['style_id']=array("gt",0);}
         if($c == 0){
//            $order = "id desc";
            $order = "designer_ex desc";   //根据级别
        }elseif($c == 1){
//            $order = "is_hot asc,id desc";
            $order = "pop_num desc";    //根据人气
        }elseif($c == 2){
//            $order = "is_new asc,id desc";
            $order = 'add_time desc';   //根据最新时间
        }
        //获取设计师
        $count = DB::name("designer")->where($where)->count();
        $pager = new Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show_goods();//分页显示输出
        $designer = DB::name("designer")->field("id,designer_name,sm_img,score,designer_ex,designer_num,order_num,designer_prize,level_id,style_id")
            ->where($where)->order($order)->page("$p,$size")->select();
        $num=0;
        foreach($designer as $k=>$v){
            $level = DB::name("designer_cat")->field("cat_name")->where(array("cat_id"=>$v['level_id']))->find();
            $style = DB::name("designer_cat")->field("cat_name")->where(array("cat_id"=>$v['style_id']))->find();
            $designer[$k]['level'] = $level['cat_name'];
            $designer[$k]['style'] = $style['cat_name'];
            $designer[$k]['case'] = DB::name("case")->field("case_id,case_name")->where(array("designer_id"=>$v['id']))->limit(3)->select();
            $n=0;
            foreach($designer[$k]['case'] as $kv=>$vv){
                $case = DB::name("case_images")->where(array("caseid"=>$vv['case_id']))->find();
                $designer[$k]['case'][$n]['img'] =$case['image_url'];
                $n++;
            }
            $num++;
        }
        //获取分类
        $ex     = $catDB->where("parent_id = 1")->field("cat_id,cat_name")->select();
        $level  = $catDB->where("parent_id = 6")->field("cat_id,cat_name")->select();
        $style  = $catDB->where("parent_id = 10")->field("cat_id,cat_name")->select();
        if($ex_id != 0){foreach($ex as $exk=>$exv){if($ex_id==$exv['cat_id']){$this->assign("ex_name",$exv['cat_name']);}}}
        if($level_id != 0){foreach($level as $lek=>$lev){if($level_id==$lev['cat_id']){$this->assign("level_name",$lev['cat_name']);}}}
        if($style_id != 0){foreach($style as $stk=>$stv){if($style_id==$stv['cat_id']){$this->assign("style_name",$stv['cat_name']);}}}
        //获取文章
        $article = DB::name("article")->field("article_id,title,thumb")->where(array("is_hot"=>1))->order("article_id desc")->limit(13)->select();

        $data = array(
            "des"       =>$designer,
            "page"      =>$page,
            "ex"        =>$ex,
            "c"        =>$c,
            "level"     =>$level,
            "style"     =>$style,
            "ex_id"     =>$ex_id,
            "level_id"  =>$level_id,
            "style_id"  =>$style_id,
            "article"   =>$article,
        );
        $this->assign($data);

        $datas = array(
            "web_title"=>"家装设计师_室内设计师-久居整装",
            "keywords"=>"家装设计师,室内设计师",
            "description"=>"久居整装装修网，汇聚行业内优秀家装设计师，为您铸造品质家，真正让您做到省钱、省时、省心的装修。",
        );
        $this->assign($datas);
        return $this->fetch();
    }


    public function desdata(){
        $id = input("id");
        $p = input("p");
        $size = 12;
        if(!$id){
            echo alert_error("参数错误");exit;
        }
        $des        = DB::name("designer");
        $descat     = DB::name("designer_cat");
        $cases      = DB::name("case");
        $casecat    = DB::name("case_cat");
        $caseimg    = DB::name("case_images");

        //获取设计师详情
        $designer = $des->field("id,designer_name,sm_img,score,designer_ex,designer_num,order_num,designer_prize,level_id,style_id,wx_img,designer_case,sm_img,designer_ex")->where(array("id"=>$id))->find();
        $level = $descat->field("cat_name")->where(array("cat_id"=>$designer['level_id']))->find();
        $style = $descat->field("cat_name")->where(array("cat_id"=>$designer['style_id']))->find();
        $designer['level'] = $level['cat_name'];
        $designer['style'] = $style['cat_name'];

        //获取案例
        $list_x = $cases->field("case_id,case_name,area,style_id,apart_id,introduce")->where("designer_id = $id")->order('add_time desc')->limit(4)->select();
        $n=0;
        foreach($list_x as $k=>$v){
            $style= $casecat->field("cat_name")->where("cat_id = $v[style_id]")->find();
            $apart= $casecat->field("cat_name")->where("cat_id = $v[apart_id]")->find();
            $img  = $caseimg->field('image_url')->where("caseid = $v[case_id]")->order('img_id desc')->find();
            $list_x[$n]['img'] = $img['image_url'];
            $list_x[$n]['style'] = $style['cat_name'];
            $list_x[$n]['apart'] = $apart['cat_name'];
            $n++;
        }

        //获取全部作品
        $list = $cases->field("case_id,case_name,area,style_id,apart_id,introduce")->page($p,$size)->where("designer_id = $id")->select();
        $count = $cases->where("designer_id = $id")->count();
        $num=0;
        foreach($list as $k=>$v){
            $style= $casecat->field("cat_name")->where("cat_id = $v[style_id]")->find();
            $apart= $casecat->field("cat_name")->where("cat_id = $v[apart_id]")->find();
            $img  = $caseimg->field('image_url')->where("caseid = $v[case_id]")->order('img_id desc')->find();
            $list[$num]['img'] = $img['image_url'];
            $list[$num]['style'] = $style['cat_name'];
            $list[$num]['apart'] = $apart['cat_name'];
            $num++;
        }
        $pager = new Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show_goods();//分页显示输出

        //获取热门设计师
        $hotdes = $des->field("id,designer_name,sm_img")
            ->where(array("is_hot"=>1))->order("id desc")->limit(6)->select();
        $data = array(
            "des"=>$designer,
            "list_x"=>$list_x,
            "list"=>$list,
            "page"=>$page,
            "id"=>$id,
            "hotdes"=>$hotdes,
        );
        $this->assign($data);

        $datas = array(
            "web_title"=>"设计师".$designer['designer_name']."-久居整装",
            "keywords"=>$designer['designer_name'],
            "description"=>"设计师".$designer['designer_name']."擅长现代简约风格设计、欧式风格设计、中式风格设计等装修风格设计，其设计方案深受广大业主的喜爱.",
        );
        $this->assign($datas);
        return $this->fetch();

    }

   


}
