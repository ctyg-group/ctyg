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
class Index extends Common
{
    public function index()
    {
//        dump(ooo);exit();
        $banner     = $this->get_banner();//获取banner图
        $worklive   = $this->get_worklive();//获取工地直播
        $designer   = $this->get_designer();//获取设计师
        $caselist   = $this->get_caselist();//获取案例
        $article1   = $this->article1();//公司动态
        $article2   = $this->article2();//行业动态
        $article3   = $this->get_article3();//获取公司动态
        $enped      = $this->get_enped();//获取百科
//        print_r($article1);
//        var_dump($article2);
//        var_dump($enped);
//        exit();
        //工地直播
        $work = Db::name('worklive')
            ->alias("w")
            ->field("w.id,w.live_name,w.stage_id,w.hot_id,w.apart_id,
            w.apart,w.area,w.style,w.thumb,w.sort,w.is_show,w.is_hot,
            w.is_new,w.add_time,w.worker1_id,w.worker2_id,d.sm_img,
            d.designer_name")
            ->join("jiuju_designer d","w.designer_id = d.id","LEFT")
//            ->join("jiuju_worker wo","w.worker1_id = wo.id","LEFT")
            ->order('sort asc,id desc')->limit(3)->select();
        $cat = DB::name('worklive_cat')->field("cat_id,cat_name")->select();
        $worker = DB::name('worker')->field("id,worker_name,image")->select();
        $num=0;
        foreach($designer as $k=>$v){
            $designer[$k]['case'] = DB::name("case")->field("case_id,case_name")->where(array("designer_id"=>$v['id']))->limit(3)->select();
            $n=0;
            foreach($designer[$k]['case'] as $kv=>$vv){
                $case = DB::name("case_images")->where(array("caseid"=>$vv['case_id']))->find();
                $designer[$k]['case'][$n]['img'] =$case['image_url'];
                $n++;
            }
            $num++;
        }
        header("Content-type:text/html;charset=utf-8");
//        dump($caselist);
//        exit();
        $data = array(
            "banner"    =>$banner,
            "worklive"  =>$worklive,
            "designer"  =>$designer,
            "caselist"  =>$caselist,
            "article1"  =>$article1,
            "article2"  =>$article2,
            "article3"  =>$article3,
            "enped"  =>$enped,
            "work" =>$work,
            "cat" =>$cat,
            "worker" =>$worker
        );

//        $res = DB::name("worklive")->field("id,live_name,style,thumb,stage_id")->order("add_time desc")->limit(3)->select();
//        foreach ($res as $k=>$v){
//
//        }
        //header("Content-type:text/html;charset=utf-8");
//        dump($designer);
//        exit();

        $this->assign($data);
        return $this->fetch();
    }
    public function article1(){
        $artDB = DB::name("article");
        $article1 = $artDB->where("cat_id_2 = 40")->field("thumb,explain,article_id,title,add_time,cat_id,cat_id_2")->order("article_id desc")->limit(2)->select();//公司动态
        return $article1;
    }
    public function article2(){
        $artDB = DB::name("article");
        $article2 = $artDB->where("cat_id_2 = 42")->field("thumb,explain,article_id,title,add_time,cat_id,cat_id_2")->order("article_id desc")->limit(2)->select();//行业新闻
        return $article2;
    }
    public function verify()
    {
        $Verify = new \Think\Controller\Verify();
        $Verify->fontSize = 20; // 字体大小
        $Verify->length = 6; // 多少个字符
        $Verify->useNoise = false; // 是否添加杂点
        $Verify->imageW = 233; // 验证码宽度
        $Verify->imageH = 50; // 验证码高度
        $Verify->entry();
    }
    public function zpzx(){
        $datas = array(
            "web_title"=>"整装套餐_家装套餐-久居整装",
            "keywords"=>"整装套餐,家装套餐",
            "description"=>"久居整装臻品之选家装整装套餐，做最贴心的完整品质整装，为您打造最贴心的完美家装。",
        );
        $this->assign($datas);
        return $this->fetch();
    }
    public function about(){
//        $datas = array(
//            "web_title"=>"整装套餐_家装套餐-久居整装",
//            "keywords"=>"整装套餐,家装套餐",
//            "description"=>"久居整装臻品之选家装整装套餐，做最贴心的完整品质整装，为您打造最贴心的完美家装。",
//        );
//        $this->assign($datas);
        return $this->fetch();
    }
    public function hexin(){
        return $this->fetch();
    }
//    public function news(){
//        return $this->fetch();
//    }
    public function service(){
        return $this->fetch();
    }
    public function recruitment(){
        $job = DB::name("job")->order("id desc")->select();
        $data = array(
            "job"=>$job,
        );
        $this->assign($data);
//        $datas = array(
//            "web_title"=>"创通易购集团",
//            "keywords"=>"创通易购集团",
//            "description"=>"创通易购集团",
//        );
//        $this->assign($datas);
//        return $this->fetch();
        return $this->fetch();
    }
    public function contantus(){
        return $this->fetch();
    }
    //获取banner图片
    public function get_banner(){
        $banner = DB::name("slide")->where(array("is_mobile"=>0))->order("sort asc")->select();
        return $banner;
    }
    //获取 工地直播
    public function get_worklive(){
        $res = DB::name("worklive")->field("id,live_name,style,thumb")->order("add_time desc")->limit(3)->select();
        foreach ($res as $k=>$v){

        }
        return $res;
    }
    //获取设计师
    public function get_designer(){
        $designer = DB::name("designer")->field("id,designer_name,score,designer_prize,designer_case,designer_num,designer_ex,style_id,level_id,sm_img as img")
            ->where("is_new = 1")->limit(4)->select();
        $num=0;
        foreach($designer as $k=>$v){
            $case = DB::name("case")->alias("c")
                    ->field("i.image_url")
                    ->join("case_images i","c.case_id=i.caseid","LEFT")
                    ->where(array("designer_id"=>$v['id']))->find();
            $style = DB::name("designer_cat")->field("cat_name")->where("cat_id = $v[style_id]")->find();
            $level = DB::name("designer_cat")->field("cat_name")->where("cat_id = $v[level_id]")->find();
            $designer[$num]['caseimg'] =$case['image_url'];
            $designer[$num]['style'] = $style['cat_name'];
            $designer[$num]['level'] = $level['cat_name'];
            $num++;
        }
        return $designer;
    }

    //获取装修案例
    public function get_caselist(){
        $Case   = DB::name("case");
        $case=array();
        $num=0;
        for($i=10;$i<=14;$i++){
            $case[$num]['img'] = $Case->alias("ca")->field("ca.case_id,ca.case_name,ci.image_url,ci.img_info")
                ->join("case_images ci","ca.case_id=ci.caseid","LEFT")
                ->where(array("style_id"=>$i))->order("add_time asc")->limit(6)->select();
            $num++;
        }
        return $case;
    }

    //获取学装修
    public function get_article1(){
        $artDB = DB::name("article");
        $a = $artDB->field("article_id,title,thumb,explain")->where("cat_id = 27 and is_home <> 1")->order("add_time desc")->limit(3)->select();
        $b = $artDB->field("article_id,title,thumb,explain")->where("cat_id = 23 and is_home <> 1")->order("add_time desc")->limit(3)->select();
        $c = $artDB->field("article_id,title,thumb,explain")->where("cat_id = 31 and is_home <> 1")->order("add_time desc")->limit(3)->select();
        $res = array_merge($a,$b,$c);
        return $res;
    }

    //获取学装修
    public function get_article2(){
        $res =  DB::name("article")->field("article_id,title,thumb,explain")->where(array("is_home"=>1))->order("add_time desc")->limit(10)->select();
        return $res;
    }
    //获取公司动态
    public function get_article3(){
        $news = DB::name("article")->where(array('cat_id_2'=>'40'))->limit(3)->order("add_time desc")->select();
        $month_array=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        foreach($news as $k=>$v){
            $month = explode("-",date("Y-m-d",$v['add_time']));
            $news[$k]['month'] =  $month_array[intval($month[1])*1-1];
            $news[$k]['year']  =  $month[0];
            $news[$k]['day']   =  $month[2];
        }
        return $news;
    }
    //获取百科
    public function get_enped(){
        $artDB = DB::name("enped");
        $res = $artDB->field("enped_id,title,thumb,explain")->order("add_time desc")->limit(5)->select();
        return $res;
    }


}
