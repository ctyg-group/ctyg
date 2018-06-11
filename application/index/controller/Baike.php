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
use think\queue\command\Listen;

class Baike extends Common
{
    public function index()
    {

        $enpedcat = DB::name("enped_cat");
        $enped = DB::name("enped");
//        Listen::Db
//        dump($enped);exit();
        $arr = array();
        $array = array();
        $enped_cat = $enpedcat->where(array("parent_id"=>0))->order('sort_order asc,cat_id asc')->select();
        $n=0;
        foreach($enped_cat as $k=>$v){
            $array[$n]['cat_id'] = $v['cat_id'];
            $array[$n]['cat_name'] = $v['cat_name'];
            $lunbo = $enped->field("enped_id,title,thumb,explain")->where(array("cat_id"=>$v['cat_id'],'is_open'=>1))->limit(6)->order("publish_time desc")->select();
            $array[$n]['list'] = $lunbo;


            $enped_cat_2 = $enpedcat->where(array("parent_id"=>$v['cat_id']))->select();
            $arr[$n]['cat_id'] = $v['cat_id'];
            $arr[$n]['cat_name'] = $v['cat_name'];
            $num=0;
            foreach($enped_cat_2 as $key=>$value){
                $endedlist = $enped->field("enped_id,title,thumb")->where(array("cat_id_2"=>$value['cat_id'],'is_open'=>1))->limit(5)->order("publish_time desc")->select();
                $arr[$n]['endlist'][$num]['cat_id'] = $value['cat_id'];
                $arr[$n]['endlist'][$num]['cat_name'] = $value['cat_name'];
                $arr[$n]['endlist'][$num]['list'] = $endedlist;
                $num++;
            }
            $n++;
        }
        $hot = $enped->field("enped_id,title,thumb,explain")->where(array("is_hot"=>1,'is_open'=>1))->limit(5)->order("publish_time desc")->select();
        $enped = $this->enped();
        //获取热门设计师
        $hotdes = $this->hotdes();
        //获取文章
        $article = $this->article();
//        dump($arr);exit;
        $data = array(
            "array"=>$array,
            "arr"=>$arr,
            "hot"=>$hot,
            "enped"=>$enped,
            "hotdes"=>$hotdes,
            "article"=>$article,
        );
        $this->assign($data);
        $datas = array(
            "web_title"=>"装修百科_设计百科_生活百科_家居百科-久居整装",
            "keywords"=>"装修百科,设计百科,生活百科,家居百科",
            "description"=>"久居整装装修百科栏目，为您提供设计、生活、家居百科等装修百科知识，让您明明白白装修您的家。",
        );
        $this->assign($datas);
        return $this->fetch();
    }

    public function baikeinfo(){
        $enped_id = input('enped_id');
        $enpedDB = DB::name("enped");
        $enpedcat = DB::name("enped_cat");


        $enpedinfo = $enpedDB->where(array("enped_id"=>$enped_id))->find();
        $catname1 = $enpedcat->field('cat_id,cat_name')->where("cat_id = $enpedinfo[cat_id]")->find();
        $catname2 = $enpedcat->field('cat_id,cat_name')->where("cat_id = $enpedinfo[cat_id_2]")->find();
        $content = $enpedinfo['content'];
        $content =  preg_replace("/font-size:(\S+)>(\S+)#/", "font-size:18px;font-weight:bolder;\">$2#", $content,-1);
        //分离内容目录
        preg_match_all("/>(\S+)#/",$content,$preg);
        foreach ($preg[0] as $k=>$v){
            $xh = $k*1+1;
            $nr = "class='enped_nr' id = 'enped_nr".$xh."'$v";
            $content = preg_replace("[$v]",$nr,$content);
        }
        $content =  preg_replace("/>(.*)#/", ">$1", $content);
        $content =  preg_replace("/>(\S+)#/", ">$1", $content);
        $enpedinfo['content'] = $content;
        //相关百科
        $odbaike = $enpedDB->field('enped_id,title,thumb,explain')->where(array('is_open'=>1,'cat_id_2'=>$enpedinfo['cat_id_2']))->limit(4)->order('publish_time desc')->select();
        //获取热门设计师
        $hotdes = $this->hotdes();
        //侧边百科
        $enped = $this->enped();
        //获取文章
        $article = $this->article();
        //
        $data = array(
            'enpedinfo'=>$enpedinfo,
            'preg'=>$preg[1],
            'odbaike'=>$odbaike,
            'hotdes'=>$hotdes,
            'enped'=>$enped,
            'article'=>$article,
            'catname1'=>$catname1,
            'catname2'=>$catname2,
        );
//        dump($data);exit;
        $this->assign($data);
        $datas = array(
            "web_title"=>$enpedinfo['title'],
            "keywords"=>$enpedinfo['keywords'],
            "description"=>$enpedinfo['explain'],
        );
        $this->assign($datas);
        return $this->fetch();
    }


    public function baikecat(){
        $cat_id = input('cat_id');
        if($cat_id==0){

        }else{
            $where['cat_id'] = $cat_id;
        }
        $p = input('p/d', 1);
        $size = 8;

        $encatDB = DB::name("enped_cat");

        $catlist = $encatDB->field('cat_id,parent_id,cat_name')->where(array("parent_id"=>0))->order('sort_order asc')->select();
        $num=0;
        foreach($catlist as $k=>$v){
            $catlist[$num]['cat'] = $encatDB->field('cat_id,parent_id,cat_name')->where(array('parent_id'=>$v['cat_id']))->order('sort_order asc')->select();
            $num++;
        }
        $cid1  = 0;
        $cid2  = 0;


        $enDB = DB::name("enped");
        if($cat_id!=0){
            $pres = $encatDB->field("cat_id,parent_id,cat_name")->where($where)->find();
            if($pres['parent_id'] == 0){
//            $pres['parent_id'] = $cat_id;
                $cid1 = $pres['cat_id'];
                $cname1 = $pres['cat_name'];
                $where1['cat_id'] = $pres['cat_id'];

            }else{
                $where1['cat_id_2'] = $pres['cat_id'];
                $cid1 = $pres['parent_id'];
                $cid2 = $pres['cat_id'];
                $cname1 = $pres['cat_name'];
            }
        }else{
            $where1 = '';
            $cname1 = '百科';
        }

        $list = $enDB->field('enped_id,title,explain,thumb,author,publish_time,radio_num,comment')->where($where1)->order("publish_time desc")->page("$p,$size")->select();
        $count = $enDB->where($where1)->count();
        $pager = new Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show_goods();//分页显示输出
        //获取热门设计师
        $hotdes = $this->hotdes();
        //侧边百科
        $enped = $this->enped();
        //获取文章
        $article = $this->article();
        $data = array(
            'list'=>$list,
            'page'=>$page,
            'cat_id'=>$cat_id,
            'catlist'=>$catlist,
            'cid1'=>$cid1,
            'cid2'=>$cid2,
            'cname1'=>$cname1,
            'hotdes'=>$hotdes,
            'enped'=>$enped,
            'article'=>$article,
        );
//        dump($data);exit;
        $this->assign($data);
        $datas = array(
            "web_title"=>"装修百科_设计百科_生活百科_家居百科-久居整装",
            "keywords"=>'久居百科,'.$cname1.'-久居整装',
            "description"=>"久居整装装修百科栏目，为您提供设计、生活、家居百科等装修百科知识，让您明明白白装修您的家。",
        );
        $this->assign($datas);
        return $this->fetch();
    }

    public function hotdes(){
        $hotdes = DB::name("designer")->field("id,sm_img,img")
            ->where(array("is_hot"=>1))->order("id desc")->limit(6)->select();
        return $hotdes;
    }

    public function enped(){
        $enped = DB::name("enped")->field("enped_id,title")->where(array('is_open'=>1))->order("is_hot asc,publish_time desc")->limit(24)->select();
        return $enped;
    }
    public function article(){
        $article = DB::name("article")->field("article_id,title")->where(array("is_hot"=>1))->order("article_id desc")->limit(8)->select();
        return $article;
    }




}
