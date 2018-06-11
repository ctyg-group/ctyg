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
class Worklive extends Common
{
    public function index(){

        $stage_id   = input('stage_id',0);
        $hot_id     = input('hot_id',0);
        $apart_id   = input('apart_id',0);
        $p          = input('p/d', 1);
        $size       = 6;

        $catDB = DB::name("worklive_cat");
        if($stage_id != 0){ $where['stage_id'] = $stage_id; }else{$where['stage_id']=array("gt",0);}
        if($hot_id != 0){ $where['hot_id'] = $hot_id; }else{$where['hot_id']=array("gt",0);}
        if($apart_id != 0){ $where['apart_id'] = $apart_id; }else{$where['apart_id']=array("gt",0);}
        $cat1 = $catDB->where("parent_id = 8")->field("cat_id,cat_name")->select();
        $cat2 = $catDB->where("parent_id = 10")->field("cat_id,cat_name")->select();
        $cat3 = $catDB->where("parent_id = 21")->field("cat_id,cat_name")->select();
        if($stage_id != 0){foreach($cat1 as $exk=>$exv){if($stage_id==$exv['cat_id']){$this->assign("stage_name",$exv['cat_name']);$stage_name = $exv['cat_name'];}}}
        if($hot_id != 0){foreach($cat2 as $lek=>$lev){if($hot_id==$lev['cat_id']){$this->assign("hot_name",$lev['cat_name']);$hot_name=$lev['cat_name'];}}}
        if($apart_id != 0){foreach($cat3 as $stk=>$stv){if($apart_id==$stv['cat_id']){$this->assign("apart_name",$stv['cat_name']);$apart_name = $stv['cat_name'];}}}

        //获取TDK
        if($stage_id || $hot_id || $apart_id){
            $stage_name = $stage_name;
            $hot_name = $hot_name;
            $apart_name = $apart_name;
            $this->tdk($stage_name,$hot_name,$apart_name);
        }else{
            $this->tdk('','','');
        }

        $workDB = DB::name("worklive");

        $count = $workDB->where($where)->count();
        $list = $workDB->where($where)->page("$p,$size")->field('id,live_name,apart,area,style,thumb,stage_id')->order("sort asc,add_time desc")->select();
        $pager = new Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show_goods();//分页显示输出
        //获取文章
        $article = DB::name("article")->field("article_id,title")->where(array("is_hot"=>1))->order("article_id desc")->limit(8)->select();
        $enped = DB::name("enped")->field("enped_id,title")->order("publish_time desc")->limit(24)->select();

        //获取热门设计师
        $hotdes = DB::name("designer")->field("id,sm_img,img")
            ->where(array("is_hot"=>1))->order("id desc")->limit(6)->select();
        $data = array(
            "list"      =>$list,
            "page"      =>$page,
            "stage_id"  =>$stage_id,
            "hot_id"    =>$hot_id,
            "apart_id"  =>$apart_id,
            "cat1"      =>$cat1,
            "cat2"      =>$cat2,
            "cat3"      =>$cat3,
            "article"   =>$article,
            "enped"     =>$enped,
            "hotdes"    =>$hotdes,
        );
        $this->assign($data);
        return $this->fetch();
    }

    public function worklive_info(){
        $id = trim(input('id'));
        if(empty($id)){
            echo alert_error("参数错误");exit;
        }
        $worker    = DB::name("worker");
        $worklive  = DB::name("worklive");
        $workcat   = DB::name("worklive_cat");
        $workstage = DB::name("worklive_stage");
        $workimg   = DB::name("worklive_images");

        $work = $worklive->field("id,live_name,stage_id,apart_id,apart,area,style,designer_id,worker1_id,worker2_id,hot_id")->where(array("id"=>$id))->find();
        $er1 = $worker->where(array("id"=>$work['worker1_id']))->find();
        $er2 = $worker->where(array("id"=>$work['worker2_id']))->find();
        $des = DB::name("designer")->field("id,designer_name,sm_img")->where(array("id"=>$work['designer_id']))->find();
        $work['worker1_name'] = $er1['worker_name'];
        $work['worker1_img']  = $er1['image'];
        $work['worker2_name'] = $er2['worker_name'];
        $work['worker2_img']  = $er2['image'];
        $work['des_name']     = $des['designer_name'];
        $work['des_img']      = $des['sm_img'];
        $cat1 = $workcat->field("cat_name")->where(array('cat_id'=>$work['hot_id']))->find();

        $where['level'] = array("elt",$work['stage_id']);
        $where['worklive_id'] = $id;
        $stage = $workstage->field("id,stage,level,add_time")->where($where)->order('level asc')->select();
        if($work['id']>=31){ $order = "id asc"; }else{ $order = "id desc"; }
        $num=0;
        foreach($stage as $k=>$v){
            $stage[$num]['imgs'] = $workimg->field("id,image_url")->where(array("stage_id"=>$v['id']))->order($order)->select();
            $num++;
        }
        $work['workimg'] = $stage[0]['imgs'][0]['image_url'];
        $data = array(
            "work"=>$work,
            "stage"=>$stage,
        );
//        dump($data);exit;
        $this->assign($data);
        $datas = array(
            "web_title"=>$cat1['cat_name']."_".$work['style']."风格装修_".$work['area']."平米装修-久居整装",
            "keywords"=>$work['style']."风格装修,".$work['area']."平米装修",
            "description"=>"久居整装工地直播栏目，为您提供".$cat1['cat_name'].$work['area']."平米".$work['style']."风格装修现场施工直播，您可预约参观，工地开工、拆改、水电施工、泥瓦等工程阶段一应仅有。",
        );
        $this->assign($datas);

        return $this->fetch();
    }

    public function tdk($name1,$name2,$name3){
        $type = 0;
        if($name1){ $type+=1; }
        if($name2){ $type+=3; }
        if($name3){ $type+=5; }

        switch ($type){
            case 0:
                $title="看工地学装修_装修施工-久居整装";
                $keywords="看工地学装修,装修施工";
                $description="久居整装工地直播栏目，让您了解更多装修施工知识，通过这个栏目看工地学装修，真正了解装修施工每一道工艺。";
                break;
            case 1:
                $title="$name1-久居整装";
                $keywords="$name1";
                $description="久居整装工地直播栏目，为您展示优质".$name1."，让您了解不同施工阶段的施工流程及工艺。";
                break;
            case 3:
                $title="$name2-久居整装";
                $keywords="$name2";
                $description="久居整装工地直播栏目，为您提供大量热门小区在线工地直播，如".$name2."小区。";
                break;
            case 5:
                $title=$name3."装修-久居整装";
                $keywords="$name3"."装修";
                $description="久居整装工地直播栏目，为你提供不同户型的在线工地直播，如".$name3."装修等等。";
                break;
            case 4:
                $title=$name1."_".$name2."-久居整装";
                $keywords=$name1.",".$name2;
                $description="久居整装工地直播栏目，为您展示".$name2."优质".$name1."，让您了解不同施工阶段的施工流程及工艺。";
                break;
            case 6:
                $title=$name1."_".$name3."装修-久居整装";
                $keywords=$name1.",".$name3."装修";
                $description="久居整装工地直播栏目，为您提供优质的".$name3."装修".$name1."，让您了解不同户型的装修施工阶段的工地进度。";
                break;
            case 8:
                $title="$name2"."_".$name3."装修-久居整装";
                $keywords=$name2.",".$name3."装修";
                $description="久居整装工地直播栏目，为您展示".$name2."在线工地直播，让您了解该小区".$name3."装修的施工工艺。";
                break;
            case 9:
                $title=$name1."_".$name2."_".$name3."装修-久居整装";
                $keywords=$name1.",".$name2.",".$name3."装修";
                $description="久居整装工地直播栏目，为您展示".$name2."优质".$name1."，以及".$name3."装修的施工工艺，让您了解更多工地施工的工艺。";
                break;
        }

        $this->assign('web_title',$title);
        $this->assign('keywords',$keywords);
        $this->assign('description',$description);
    }
}
