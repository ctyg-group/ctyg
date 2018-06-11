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
        $article1   = $this->get_article1();//获取学装修资讯
        $article2   = $this->get_article2();//获取文章三篇
        $article3   = $this->get_article3();//获取公司动态
        $enped      = $this->get_enped();//获取百科
        $data = array(
            "banner"    =>$banner,
            "worklive"  =>$worklive,
            "designer"  =>$designer,
            "caselist"  =>$caselist,
            "article1"  =>$article1,
            "article2"  =>$article2,
            "article3"  =>$article3,
            "enped"  =>$enped,
        );
        $this->assign($data);
        return $this->fetch();
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
    //获取banner图片
    public function get_banner(){
        $banner = DB::name("slide")->where(array("is_mobile"=>0))->order("sort asc")->select();
        return $banner;
    }
    //获取 工地直播
    public function get_worklive(){
        $res = DB::name("worklive")->field("id,live_name,style,thumb")->order("add_time desc")->limit(3)->select();
        return $res;
    }
    //获取设计师
    public function get_designer(){
        $designer = DB::name("designer")->field("id,designer_name,designer_num,designer_ex,style_id,level_id,sm_img as img")
            ->where("is_new = 1")->limit(5)->select();
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
                ->where(array("style_id"=>$i))->order("add_time asc")->limit(3)->select();
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
        $res =  DB::name("article")->field("article_id,title,thumb,explain")->where(array("is_home"=>1))->order("add_time desc")->limit(3)->select();
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
