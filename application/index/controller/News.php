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
class News extends Common
{
    //学装修主页
    public function index()
    {
        $art_catDB = DB::name("article_cat");
        $artDB = DB::name("article");
        $art_list = $art_catDB->where("parent_id = 0 and cat_id <> 39")->field("cat_id,cat_name")->select();
        $data = array();
        foreach($art_list as $list_id){
            $art_id =  $art_catDB->where("parent_id = $list_id[cat_id]")->field("cat_id,cat_name")->select();
            foreach($art_id as $v){
                $cat_data = $artDB->field("article_id,title,thumb")->where(array('cat_id_2'=>$v['cat_id'],'is_open'=>1))->limit(6)->order("publish_time desc")->select();
                $data[$list_id['cat_name']][$v['cat_name']] = $cat_data;
            }
        }
        //获取文章

        $size = 2;
        $article1 = $artDB->where("cat_id_2 = 40")->field("thumb,explain,article_id,title,add_time,cat_id,cat_id_2")->order("article_id desc")->select();//公司动态
        $article2 = $artDB->where("cat_id_2 = 42")->field("thumb,explain,article_id,title,add_time,cat_id,cat_id_2")->order("article_id desc")->select();//行业新闻
        foreach ($article1 as $a=>$c){
            $cat1 = $art_catDB->where("cat_id= $c[cat_id]")->field("cat_name")->find();
            $cat2 = $art_catDB->where("cat_id= $c[cat_id_2]")->field("cat_name")->find();
            $article[$a]['cat1'] = $cat1['cat_name'];
            $article[$a]['cat2'] = $cat2['cat_name'];
        }
        $count =$artDB->count();
        $pager = new Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show_goods();//分页显示输出
        $artnew  = $artDB->field("article_id,title,thumb")->where(array("is_home"=>1))->order("article_id desc")->limit(3)->select();
        //案例
        $case = DB::name("case")->field("case_id,case_name")->order("sort desc")->limit(3)->select();
        foreach($case as $k=>$v){
            $img=DB::name("case_images")->where("caseid = $v[case_id]")->field("image_url,img_info")->find();
            $case[$k]['img']=$img['image_url'];
            $case[$k]['img_info']=$img['img_info'];
        }
//        $data['art'] =  '装修流程';
        $arr = array(
//            "art1"=>$data['装修流程'],
//            "art2"=>$data['装饰搭配'],
//            "art3"=>$data['风水知识'],
            "article"=>$article1,
            "article2"=>$article2,
            "artnew"=>$artnew,
            "case"=>$case,
            "page"=>$page,
        );
        $this->assign($arr);
//        dump($art_list);exit();
        $this->tdk(1);
//      	if(strstr($_SERVER['REDIRECT_URL'],'.')!='.html'&& substr($_SERVER['REDIRECT_URL'],-4)!='html') {
//                $this->redirect('index/news/index');
//        }
        return $this->fetch();
    }
    //详情
    public function newsinfo()
    {
        $article_id = input('article_id');
        $artDB = DB::name("article");
        $artinfo = $artDB->where("article_id = $article_id")->find();
        $this->assign("artinfo",$artinfo);
        $artcatDB = DB::name("article_cat");
        $catname1 = $artcatDB->field('cat_id,cat_name')->where("cat_id = $artinfo[cat_id]")->find();
        $catname2 = $artcatDB->field('cat_id,cat_name')->where("cat_id = $artinfo[cat_id_2]")->find();

        $artnew  = $artDB->field("article_id,title,thumb")->where(array("is_new"=>1,"is_open"=>1))->order("article_id desc")->limit(4)->select();
        $arthot  = $artDB->field("article_id,title,thumb")->where(array("is_hot"=>1,"is_open"=>1,"cat_id_2"=>$artinfo['cat_id_2']))->order("article_id desc")->limit(7)->select();

        $next = $this->piece_art($article_id);
        $data = array(
            "artinfo"=>$artinfo,
            "next"=>$next,
            "artnew"=>$artnew,
            "article"=>$arthot,
            "catname1"=>$catname1,
            "catname2"=>$catname2,
        );
        $this->assign($data);
        $datas = array(
            "web_title"=>$artinfo['title'],
            "keywords"=>$artinfo['keywords'],
            "description"=>$artinfo['explain'],
        );
//        dump($data);exit();
        $this->assign($datas);
//      	if(strstr($_SERVER['REDIRECT_URL'],'.')!='.html'&& substr($_SERVER['REDIRECT_URL'],-4)!='html') {
//                $this->redirect('index/news/newsinfo', ['article_id' => $article_id]);
//        }
        return $this->fetch();
    }
    public function piece_art($article_id){
        $artDB = DB::name("article");
        $where1['article_id'] = array("gt",$article_id);
        $where1['is_open'] = 1;
        $where2['article_id'] = array("lt",$article_id);
        $where2['is_open'] = 1;
        $next = $artDB->field("article_id,title")->where($where1)->order("publish_time asc")->find();
        $prev = $artDB->field("article_id,title")->where($where2)->order("publish_time desc")->find();
        $data = array();

        if($next){
            $data['next'] = $next;
        }else{
            $data['next']['article_id'] = "0";
            $data['next']['title'] = "没有了";
        }
        if($prev){
            $data['prev'] = $prev;
        }else{
            $data['prev']['article_id'] = "0";
            $data['prev']['title'] = "没有了";
        }
        return $data;
    }

    public function typelist(){
        $cat_id = input('cat_id');
        $p = input('p/d', 1);
        $size = 8;
        $art_catDB = DB::name("article_cat");
        $parent_id = $art_catDB->where("cat_id = $cat_id")->field("cat_id,parent_id,cat_name")->find();
        if($parent_id['parent_id'] == 0 ){
            $cat2 = $art_catDB->field("cat_id,cat_name")->where(array("parent_id"=>$cat_id))->order("sort_order asc,cat_id asc")->find();
            $cat_id = $cat2['cat_id'];
            $catname1 = $parent_id['cat_name'];
            $catname2 = $cat2['cat_name'];
            $iscatid = $parent_id['cat_id'];
        }else{
            $cat1 = $art_catDB->field("cat_id,cat_name,parent_id")->where(array("cat_id"=>$parent_id['parent_id']))->find();
            $catname2 = $parent_id['cat_name'];
            $catname1 = $cat1['cat_name'];
            $iscatid =$cat1['cat_id'];
        }
        $where['cat_id_2'] = $cat_id;
        $cat = $art_catDB->field("cat_id,cat_name")->where(array("parent_id"=>$iscatid))->order("sort_order asc,cat_id asc")->select();

        $artDB = DB::name("article");
        $count = $artDB->where($where)->count();
        $pager = new Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show_goods();//分页显示输出
        $article = $artDB->field("article_id,title,thumb,radio_num,comment,author,publish_time,explain")
            ->where($where)->where(array("is_open"=>1))->order("publish_time desc")->page("$p,$size")->select();
        //获取文章
        $hotart = $artDB->field("article_id,title")->where(array("is_hot"=>1))->order("article_id desc")->limit(8)->select();
        //案例
        $case = DB::name("case")->field("case_id")->order("sort asc")->limit(3)->select();
        foreach($case as $k=>$v){
            $img=DB::name("case_images")->where("caseid = $v[case_id]")->field("image_url,img_info")->find();
            $case[$k]['img']=$img['image_url'];
            $case[$k]['img_info']=$img['img_info'];
        }
        $data = array(
            "page"=>$page,
            "article"=>$article,
            "hotart"=>$hotart,
            "case"=>$case,
            "catname1"=>$catname1,
            "catname2"=>$catname2,
            "cat"=>$cat,
            "cat_id"=>$cat_id,
        );
        $this->assign($data);
        $this->tdk(1);

        return $this->fetch();
    }

    public function newslist(){
        $cat_id = input('cat_id');
        $p = input('p/d', 1);
        $size = 8;
        $art_catDB = DB::name("article_cat");
        $parent_id = $art_catDB->where("cat_id = $cat_id")->field("cat_id,parent_id,cat_name")->find();
        if($parent_id['parent_id'] == 0 ){
            $cat2 = $art_catDB->field("cat_id,cat_name")->where(array("parent_id"=>$cat_id))->order("sort_order asc,cat_id asc")->find();
            $cat_id = $cat2['cat_id'];
            $catname1 = $parent_id['cat_name'];
            $catname2 = $cat2['cat_name'];
            $iscatid = $parent_id['cat_id'];
        }else{
            $cat1 = $art_catDB->field("cat_id,cat_name,parent_id")->where(array("cat_id"=>$parent_id['parent_id']))->find();
            $catname2 = $parent_id['cat_name'];
            $catname1 = $cat1['cat_name'];
            $iscatid =$cat1['cat_id'];
        }
        $where['cat_id_2'] = $cat_id;
        $cat = $art_catDB->field("cat_id,cat_name")->where(array("parent_id"=>$iscatid))->order("sort_order asc,cat_id asc")->select();

        $artDB = DB::name("article");
        $count = $artDB->where($where)->count();
        $pager = new Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show_goods();//分页显示输出
        $article = $artDB->field("article_id,title,thumb,radio_num,comment,author,add_time,explain")
            ->where($where)->where(array("is_open"=>1))->order("publish_time desc")->page("$p,$size")->select();
        //获取文章
        $hotart = $artDB->field("article_id,title")->where(array("is_hot"=>1))->order("article_id desc")->limit(8)->select();
        //案例
        $case = DB::name("case")->field("case_id")->order("sort asc")->limit(3)->select();
        foreach($case as $k=>$v){
            $img=DB::name("case_images")->where("caseid = $v[case_id]")->field("image_url,img_info")->find();
            $case[$k]['img']=$img['image_url'];
            $case[$k]['img_info']=$img['img_info'];
        }
        $data = array(
            "page"=>$page,
            "article"=>$article,
            "hotart"=>$hotart,
            "case"=>$case,
            "catname1"=>$catname1,
            "catname2"=>$catname2,
            "cat"=>$cat,
            "cat_id"=>$cat_id,
        );
        $this->assign($data);
        $this->tdk(1);
        return $this->fetch();
    }
//详情
    public function newsid()
    {
        $article_id = input('article_id');
        $artDB = DB::name("article");
        $artinfo = $artDB->where("article_id = $article_id")->find();
        $this->assign("artinfo",$artinfo);
        $artcatDB = DB::name("article_cat");
        $catname1 = $artcatDB->field('cat_id,cat_name')->where("cat_id = $artinfo[cat_id]")->find();
        $catname2 = $artcatDB->field('cat_id,cat_name')->where("cat_id = $artinfo[cat_id_2]")->find();

        $artnew  = $artDB->field("article_id,title,thumb")->where(array("is_new"=>1,"is_open"=>1))->order("article_id desc")->limit(8)->select();
        $arthot  = $artDB->field("article_id,title,thumb")->where(array("is_hot"=>1,"is_open"=>1,"cat_id_2"=>$artinfo['cat_id_2']))->order("article_id desc")->limit(3)->select();

        $next = $this->piece_art($article_id);
        $data = array(
            "artinfo"=>$artinfo,
            "next"=>$next,
            "artnew"=>$artnew,
            "arthot"=>$arthot,
            "catname1"=>$catname1,
            "catname2"=>$catname2,
        );
        $this->assign($data);
        $datas = array(
            "web_title"=>$artinfo['title'],
            "keywords"=>$artinfo['keywords'],
            "description"=>$artinfo['explain'],
        );
        $this->assign($datas);
        if(strstr($_SERVER['REDIRECT_URL'],'.')!='.html'&& substr($_SERVER['REDIRECT_URL'],-4)!='html') {
            $this->redirect('index/news/newsid', ['article_id' => $article_id]);
        }
        return $this->fetch();
    }


    public function tdk($type){
        if($type==1){
            $this->assign("web_title","装修知识_装修流程_装修攻略-久居整装");
            $this->assign("keywords","装修知识,装修流程,装修攻略");
            $this->assign("description","久居整装学装修栏目，为广大业主朋友们提供最全面的家装装修知识、装修流程等装修攻略知识，让业主朋友们了解更多装修知识，装修自己的房子更加省心省钱省时。");
        }

        if($type==2){
            $this->assign("web_title","装修前_装修注意事项-久居整装");
            $this->assign("keywords","装修前,装修注意事项");
            $this->assign("description","久居整装装修前栏目，为您提供装修前装修注意事项有那些等装修知识，在这里你将得到一个满意的答案。");
        }

        if($type==3){
            $this->assign("web_title","装修中-久居整装");
            $this->assign("keywords","装修中");
            $this->assign("description","久居整装装修中栏目，为您提供装修中注意事项等装修知识，关于装修中的问题在这里将得到一个满意的答案。");
        }

        if($type==4){
            $this->assign("web_title","装修后-久居整装");
            $this->assign("keywords","装修后");
            $this->assign("description","久居整装装修后栏目，为您提供装修后注意事项等装修知识，关于装修后的问题在这里将得到一个满意的答案。");
        }

        if($type==5){
            $this->assign("web_title","房屋装饰_饰品搭配_装修风格-久居整装");
            $this->assign("keywords","房屋装饰,饰品搭配,装修风格");
            $this->assign("description","久居整装装饰搭配栏目，为您提供最新最全面的房屋装饰、饰品搭配等装修风格搭配知识，为您打造温馨之家。");
        }

        if($type==6){
            $this->assign("web_title","家居风水布局_装修风水禁忌_装修风水-久居整装");
            $this->assign("keywords","家居风水布局,装修风水禁忌,装修风水");
            $this->assign("description","久居整装风水知识栏目，为您提供最全面的家居风水布局、装修风水禁忌等装修风水知识，帮您解决装修风水难题。");
        }

        if($type==7){
            $this->assign("title","久居新闻_久居动态_行业新闻-久居整装");
            $this->assign("keywords","久居新闻,久居动态,行业新闻");
            $this->assign("description","久居整装新闻中心栏目，为你推荐更多久居新闻、久居动态以及家装行业新闻，让及时了解久居整装的发展动态。");
        }
    }
}
