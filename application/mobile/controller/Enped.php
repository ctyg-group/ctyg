<?php

namespace app\mobile\controller;

use think\Controller;
use think\Db;
use think\Page;
class Enped extends Base {

    public function index(){
        $enDB = DB::name("enped");
        $firen = $enDB->field("enped_id,thumb,title")->order("publish_time desc")->limit(1)->find();
        $hot = $enDB->field("enped_id,title")->where("is_hot = 1")->order("publish_time desc")->limit(6)->select();
        $en1 = $enDB->where("cat_id = 1")->field("enped_id,thumb,title,explain")->order("publish_time desc")->limit(5)->select();
        $en5 = $enDB->where("cat_id = 5")->field("enped_id,thumb,title,explain")->order("publish_time desc")->limit(5)->select();
        $en12 = $enDB->where("cat_id = 12")->field("enped_id,thumb,title,explain")->order("publish_time desc")->limit(5)->select();
        $en18 = $enDB->where("cat_id = 18")->field("enped_id,thumb,title,explain")->order("publish_time desc")->limit(5)->select();

        $this->assign('firen',$firen);
        $this->assign('hot',$hot);
        $this->assign('en1',$en1);
        $this->assign('en5',$en5);
        $this->assign('en12',$en12);
        $this->assign('en18',$en18);

        return $this->fetch();
    }

    public function enpedlist(){
        $cat_id = input('cat_id/d');
        $p = input('p/d', 1);
        $size = 15;

        $encatDB = DB::name("enped_cat");
        $pres = $encatDB->field("parent_id,cat_name")->where("cat_id = $cat_id")->find();
        $parname = $encatDB->field("cat_name")->where("cat_id = $pres[parent_id]")->find();

        $cat_list[18] = $encatDB->field("cat_id,cat_name")->where('parent_id = 18')->select();
        $cat_list[1] = $encatDB->field("cat_id,cat_name")->where('parent_id = 1')->select();
        $cat_list[12] = $encatDB->field("cat_id,cat_name")->where('parent_id = 12')->select();
        $cat_list[5] = $encatDB->field("cat_id,cat_name")->where('parent_id = 5')->select();


        $enDB = DB::name("enped");

        if($pres['parent_id'] == 0){
            $pres['parent_id'] = $cat_id;
            $child_id = 0;
            $list = $enDB->field('enped_id,title,explain,thumb')->where("cat_id = $cat_id")->order("add_time desc")->page("$p,$size")->select();
            $count = $enDB->where("cat_id = $cat_id")->count();
        }else{
            $child_id = $cat_id;
            $list = $enDB->field('enped_id,title,explain,thumb')->where("cat_id_2 = $cat_id")->order("add_time desc")->page("$p,$size")->select();
            $count = $enDB->where("cat_id_2 = $cat_id")->count();
        }

        $this->calcul_page($count,$size);

        $this->assign('p',$p);
        $this->assign('list',$list);
        $this->assign('cat_id',$cat_id);
        $this->assign('paid',$pres['parent_id']);
        $this->assign('child_id',$child_id);
        $this->assign('catname',$pres['cat_name']);
        $this->assign('catpname',$parname['cat_name']);
        $this->assign('cat_list',$cat_list);

        return $this->fetch();
    }


    public function enpedinfo(){
        $enped_id = input('enped_id/d');
        $enpedDB = DB::name("enped");
        $enpedinfo = $enpedDB->where("enped_id = $enped_id")->find();
        $enpedinfo['content'] = str_replace("line-height: 1.5em;","line-height: 1rem;",$enpedinfo['content']);
        $enpedinfo['content'] = str_replace("<img","<img style=\"width:11rem;height:7rem;margin-left: 0.4rem;\"",$enpedinfo['content']);

        /*处理百科数据-str*/
        $content = $enpedinfo['content'];
        $content =  preg_replace("/font-size: (\S+)>(\S+)#/", "font-size: 18px; font-weight:bolder;\">$2#", $content,-1);

        preg_match_all("/>(\S+)#/",$content,$preg);

        foreach ($preg[0] as $k=>$v){
            $xh = $k*1+1;
            $nr = " id = 'enped_nr".$xh."'$v";
            $content = preg_replace("[$v]",$nr,$content);
        }

        $content =  preg_replace("/>(.*)#/", ">$1", $content);
        $content =  preg_replace("/>(\S+)#/", ">$1", $content);

        $enpedinfo['content'] = $content;
        $this->assign("preg",$preg[1]);


        /*处理百科数据-end*/

        $encatDB = DB::name("enped_cat");
        $cat = $encatDB->field("cat_name,cat_id,parent_id")->where("cat_id = ".$enpedinfo['cat_id_2'])->find();
        $parent_cat = $encatDB->field("cat_name,cat_id")->where("cat_id = $cat[parent_id]")->find();

        $this->newEnped();

        $this->assign('cat',$cat);
        $this->assign('parent_cat',$parent_cat);

        $this->assign("enpedinfo",$enpedinfo);
        $this->assign("title",$enpedinfo['title']);


        return $this->fetch();
    }


    public function about_article($article_id){
        $artDB = DB::name("article");
        $max = DB::query("select * from jiuju_article where article_id = (select max(article_id) from jiuju_article where article_id < ? )",[$article_id]);
        $min = DB::query("select * from jiuju_article where article_id = (select min(article_id) from jiuju_article where article_id > ? )",[$article_id]);

        $data = array();
        $data['max'] = $max[0];
        $data['min'] = $min[0];

        $cat_id = $artDB->field('cat_id_2')->where("article_id = $article_id")->find();
        $about =  $artDB->where("cat_id_2 = $cat_id[cat_id_2]")->limit(6)->select();
        $data['about'] = $about;

        return $data;
    }


    public function newEnped(){
        $enDB = DB::name("enped");
        $data = $enDB->field("title,enped_id")->limit(5)->order("publish_time desc")->select();
        $this->assign("newEn",$data);
    }

}