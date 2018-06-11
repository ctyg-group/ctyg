<?php

namespace app\mobile\controller;

use think\Controller;
use think\Db;
use think\Page;
class School extends Base {

    public function index(){
        $artDB = DB::name("article");
        $firart = $artDB->field("article_id,thumb,title")->order("publish_time desc")->limit(1)->find();
        $art24 = $artDB->where("cat_id_2 = 24")->field("article_id,thumb,title")->order("publish_time desc")->limit(6)->select();
        $art28 = $artDB->where("cat_id_2 = 28")->field("article_id,thumb,title")->order("publish_time desc")->limit(6)->select();
        $art32 = $artDB->where("cat_id_2 = 32")->field("article_id,thumb,title")->order("publish_time desc")->limit(6)->select();


        $this->assign('firart',$firart);
        $this->assign('art24',$art24);
        $this->assign('art28',$art28);
        $this->assign('art32',$art32);
        return $this->fetch();
    }

    public function child_cat(){
        $cat_id = input('cat_id/d');
        $p = input('p/d', 1);
        $size = 10;
        $art_catDB = DB::name("article_cat");
        $artDB = DB::name("article");

        $parent_id = $art_catDB->where("cat_id = $cat_id")->field("parent_id")->find();
        if($parent_id['parent_id'] != 0 ){
            $select_id = $parent_id['parent_id'];
        }else{
            $select_id = $cat_id;
        }

        $child_list = $art_catDB->where("parent_id = $select_id")->field("cat_id,cat_name")->select();
        $title = $art_catDB->where("cat_id = $select_id")->field("cat_name")->find();

        if($parent_id['parent_id'] != 0 ){
            $list_id = $cat_id;
        }else{
            $list_id = $child_list[0]['cat_id'];
        }


        $list = $artDB->field("article_id,radio_num,comment,title,explain,thumb,add_time")->where("cat_id_2 = $list_id")->page("$p,$size")->order("publish_time desc")->select();
        $count = $artDB->where("cat_id_2 = $list_id")->count();

        $this->calcul_page($count,$size);//分页显示输出


        $this->assign("list",$list);
        $this->assign("child_list",$child_list);
        $this->assign("list_id",$list_id);
        $this->assign("title",$title['cat_name']);
        $this->assign('cat_id',$cat_id);
        $this->assign('p',$p);

        return $this->fetch();

    }



    public function artinfo(){
        $article_id = input('article_id/d');
        $artDB = DB::name("article");
        $artinfo = $artDB->where("article_id = $article_id")->find();
        $artinfo['content'] = str_replace("line-height: 1.5em;","line-height: 1rem;",$artinfo['content']);
        $artinfo['content'] = str_replace("<img","<img style=\"width:11rem;height:7rem;\"",$artinfo['content']);

        $this->assign("artinfo",$artinfo);

        $artcatDB = DB::name("article_cat");
        $cat_name = $artcatDB->field('cat_name')->where("cat_id = $artinfo[cat_id_2]")->find();

        $about = $this->about_article($article_id);
        $this->assign("about",$about);
        $this->assign("cat_name",$cat_name['cat_name']);

        $this->assign("title",$artinfo['title']);
        $this->assign("keywords",$artinfo['keywords']);
        $this->assign("description",$artinfo['explain']);
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



}