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
class Cases extends Common
{
    public function index(){
        $style_id = input('style_id',0);
        $apart_id = input('apart_id',0);
        $measure_id = input('measure_id',0);
        $p = input('p/d', 1);
        $size = 12;
        $catDB = DB::name("case_cat");
        if($style_id != 0){ $where['style_id'] = $style_id; }else{$where['style_id']=array("gt",0);}
        if($apart_id != 0){ $where['apart_id'] = $apart_id; }else{$where['apart_id']=array("gt",0);}
        if($measure_id != 0){ $where['measure_id'] = $measure_id; }else{$where['measure_id']=array("gt",0);}
        //获取分类
        $cat1 = $catDB->where("parent_id = 9")->field("cat_id,cat_name")->select();
        $cat2 = $catDB->where("parent_id = 17")->field("cat_id,cat_name")->select();
        $cat3 = $catDB->where("parent_id = 23")->field("cat_id,cat_name")->select();
        if($style_id != 0){foreach($cat1 as $exk=>$exv){if($style_id==$exv['cat_id']){$this->assign("style_name",$exv['cat_name']);$style_name = $exv['cat_name'];}}}
        if($apart_id != 0){foreach($cat2 as $lek=>$lev){if($apart_id==$lev['cat_id']){$this->assign("apart_name",$lev['cat_name']);$apart_name=$lev['cat_name'];}}}
        if($measure_id != 0){foreach($cat3 as $stk=>$stv){if($measure_id==$stv['cat_id']){$this->assign("measure_name",$stv['cat_name']);$measure_name = $stv['cat_name'];}}}

        //获取TDK
//        if($style_id || $apart_id || $measure_id){
//            $style_name = $style_name??'';
//            $apart_name = $apart_name??'';
//            $measure_name = $measure_name??'';
//            $this->tdk($style_name,$apart_name,$measure_name);
//        }else {
//            $this->tdk('', '', '');
//        }

        //获取案例
        $caseDB = DB::name("case");
        $imgDB = DB::name("case_images");
        $list = $caseDB->where($where)->page("$p,$size")->field('case_id,case_name,style_id,area')->order("sort asc,add_time desc")->select();
        $count =$caseDB->where($where)->count();
        $pager = new Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show_goods();//分页显示输出
        foreach($list as $k=>$v){
            $img = $imgDB->where("caseid = $v[case_id]")->field("image_url")->order("img_id desc")->find();
            $list[$k]['img'] = $img['image_url'];
        }
        $data = array(
            "list"=>$list,
            "page"=>$page,
            "style_id"=>$style_id,
            "apart_id"=>$apart_id,
            "measure_id"=>$measure_id,
            "cat1"=>$cat1,
            "cat2"=>$cat2,
            "cat3"=>$cat3,
        );
        $this->assign($data);
        return $this->fetch();
    }

    public function caseinfo(){
        $case_id = input("case_id");
        if(empty($case_id)){
            echo alert_error("参数错误");exit;
        }
        $case    = DB::name("case")->field("case_id,case_name,designer_id,style_id,apart_id,measure_id,area")->where(array("case_id"=>$case_id))->find();
        $casecat    = DB::name("case_cat")->select();

        foreach($casecat as $k=>$v){
            if($case['style_id']==$v['cat_id']){
                $case['style'] = $v['cat_name'];
            }
            if($case['apart_id']==$v['cat_id']){
                $case['apart'] = $v['cat_name'];
            }
        }
        $caseimg = DB::name("case_images")->where(array("caseid"=>$case_id))->order("img_id asc")->select();
        $des     = DB::name("designer")->field("id,designer_name")->where(array("id"=>$case['designer_id']))->find();
        $case['designer_name'] = $des['designer_name'];
        $case['img'] = $caseimg;

        $data = array(
            "case"=>$case,
            "des"=>$des,
        );
        $this->assign($data);

        $datas = array(
            "web_title"=>$case['case_name']."-久居整装",
            "keywords"=>$case['case_name'],
            "description"=>"久居整装为您提供".$case['case_name']."案例，为您提供最好最全面的设计方案参考案例。",
        );
        $this->assign($datas);
        return $this->fetch();
    }

//    public function tdk($name1,$name2,$name3){
//        $type = 0;
//        if($name1){ $type+=1; }
//        if($name2){ $type+=3; }
//        if($name3){ $type+=5; }
//
//        switch ($type){
//            case 0:
//                $title="装修案例_家装装修效果图_装修效果图大全-久居整装";
//                $keywords="装修案例,家装效果图,装修效果图大全";
//                $description="久居整装整装案例栏目，精选最新最清晰装修案例、假家装效果图，包含丰富时尚的客厅、卧室、厨房卫生间等最新装修效果图大全";
//                break;
//            case 1:
//                $title=$name1."装修效果图_".$name1."家装案例-久居整装";
//                $keywords=$name1."装修效果图,".$name1."家装案例";
//                $description="久居整装为您提供优质的".$name1."装修效果图、".$name1."家装案例，各种".$name1."风格装修图片尽在久居整装整装案例栏目。";
//                break;
//            case 3:
//                $title=$name2."装修效果图-久居整装";
//                $keywords=$name2."装修效果图";
//                $description="久居整装为您提供优质的".$name2."装修效果图案例，为您提供最好最全面的设计方案参考案例。";
//                break;
//            case 5:
//                $title=$name3."装修效果图-久居整装";
//                $keywords=$name3."装修效果图";
//                $description="久居整装为您提供优质的".$name3."装修效果图案例，为您提供最好最全面的设计方案参考案例。";
//                break;
//            case 4:
//                $title=$name1."装修效果图_".$name2."装修效果图-久居整装";
//                $keywords=$name1."装修效果图,".$name2."装修效果图";
//                $description="久居整装为您提供优质的".$name1."风格".$name2."装修效果图案例，为您提供最好最全面的设计方案参考案例。";
//                break;
//            case 6:
//                $title=$name1."装修效果图_".$name3."装修效果图-久居整装";
//                $keywords=$name1."装修效果图,".$name3."装修效果图";
//                $description="久居整装为您提供优质的".$name3.$name1."风格装修效果图案例，为您提供最好最全面的设计方案参考案例。";
//                break;
//            case 8:
//                $title=$name2."装修效果图_".$name3."装修效果图-久居整装";
//                $keywords=$name2."装修效果图,".$name3."装修效果图";
//                $description="久居整装为您提供优质的".$name3.$name2."装修效果图案例，为您提供最好最全面的设计方案参考案例。";
//                break;
//            case 9:
//                $title=$name3."_".$name1."风格_".$name2."装修效果图-久居整装";
//                $keywords=$name1."风格装修";
//                $description="久居整装为您提供优质的".$name3.$name1."风格".$name2."装修效果图案例，为您提供最好最全面的设计方案参考案例。";
//                break;
//        }
//
//        $this->assign('web_title',$title);
//        $this->assign('keywords',$keywords);
//        $this->assign('description',$description);
//    }
}
