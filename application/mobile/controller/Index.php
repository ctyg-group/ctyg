<?php
namespace app\mobile\controller;
use think\Controller;
use think\Db;
class Index extends Base {

    public function index(){
        $banner = $this->banner();
        $last_b = count($banner)*1-1;
        $des = $this->get_designer();
        $goods = $this->cases();
        $article = $this->get_article();
        $enped = $this->get_enped();
        $news = $this->get_news();
        $worklive = $this->get_work();
//
//
        $this->assign('banner',$banner);
        $this->assign('last_b',$last_b);
        $this->assign('des',$des);
        $this->assign('goods',$goods);
        $this->assign('article',$article);
        $this->assign('enped',$enped);
        $this->assign('news',$news);
        $this->assign('worklive',$worklive);
        return $this->fetch();
    }

    public function lfbj(){
        return $this->fetch();

    }

    public function mfyy(){
        return $this->fetch();
    }

    public function gdyy(){
        return $this->fetch();
    }

    /**
     * 获取banner
     */
    public function banner(){
        $bannerDB = DB::name("slide");
        $slide = $bannerDB->where("is_mobile = 1")->order("sort asc")->select();
        return $slide;
    }




    /**
     * 获取设计师
     */
    public function get_designer(){
        $desDB = DB::name("designer");
        $list  = $desDB->field("id,designer_name,wx_img,designer_num,img,sm_img")->where("is_new = 1")->order("designer_num desc")->limit(3)->select();

        return $list;
    }

    /**
     * 获取装修案例
     */
    public function cases(){
        $goodsDB = DB::name("case g");
        $imgDB = DB::name("case_images");
        $cateDB = DB::name("case_cat");

        $goods = $goodsDB->field("case_id,case_name,style_id,apart_id,measure_id")->order("add_time desc")->limit(4)->select();
        foreach($goods as $k=>$v){
            $img = $imgDB->where("caseid = $v[case_id]")->field("image_url")->limit(1)->find();
            $goods[$k]['img'] = $img['image_url'];
            $style = $cateDB->where("cat_id = $v[style_id]")->field("cat_name")->limit(1)->select();
            $apart = $cateDB->where("cat_id = $v[apart_id]")->field("cat_name")->limit(1)->select();
            $measure = $cateDB->where("cat_id = $v[measure_id]")->field("cat_name")->limit(1)->select();
            $goods[$k]['style'] = $style[0]['cat_name'];
            $goods[$k]['apart'] = $apart[0]['cat_name'];
            $goods[$k]['measure'] = $measure[0]['cat_name'];
            $goods[$k]['case_name'] = substr($goods[$k]['case_name'],0,strlen($goods[$k]['case_name'])*1-5*3);

        }

        return $goods;

    }

    /**
     * 获取文章
     */
    public function get_article(){
        $artDB = DB::name("article");
        $res = $artDB->field("article_id,title,thumb,explain,radio_num,comment")->order("publish_time desc")->limit(8)->select();
        return $res;
    }


    /**
     * 获取百科
     */
    public function get_enped(){
        $artDB = DB::name("enped");
        $res = $artDB->field("enped_id,title,thumb,explain,radio_num,comment")->order("publish_time desc")->limit(8)->select();
        return $res;
    }

    /**
     * 获取新闻
     */
    public function get_news()
    {
        $newsDB = DB::name("article");
        $news = $newsDB->field("article_id,title,thumb,explain,radio_num,comment")->where("cat_id_2 = 40")->limit(4)->order("publish_time desc")->select();
        return $news;
    }
    /**
     * 获取工地直播
     */
    public function get_work(){
        $work = DB::name("worklive")->field("live_name,area,style,thumb")->order("id desc")->find();
        return $work;
    }
    /**
     * 获取报价短信（ajax）
     */
    public function getprice(){
        $userDB = DB::name("userinfo");
        $data = input('post.');
        $data['ip'] = $_SERVER["REMOTE_ADDR"];
        $data['time'] = time();

        $date_num = $userDB->where("time >= '($data[time]*1-24*60*60)' and time <= '$data[time]' and mobile = $data[mobile]")->count();
        if($date_num >=3){
            $return_arr = array(
                'status' => 0,
                'msg' => '获取失败,已到达今日计算上限',
            );
            $this->ajaxReturn($return_arr);
        }

        $res = $userDB->data($data)->insert();
        $send_res = $this->sendPrice($data['mobile'],$data['price']);

        if($res && $send_res){
            $this->sendAdmin('19928768132',$data['mobile']);
            $return_arr = array(
                'status' => 1,
                'msg' => '获取成功',
            );
            $this->ajaxReturn($return_arr);
        }else{
            $return_arr = array(
                'status' => 0,
                'msg' => '获取失败,请确保您的手机正常接受短信',
            );
            $this->ajaxReturn($return_arr);
        }
    }
    /**
     * @return mixed
     * 11月份活动
     */
    public function hd11(){

        return $this->fetch();
    }
    /**
     * @return mixed
     * 12月份活动
     */
    public function hd12(){

        return $this->fetch();
    }
    /**
     * @return mixed
     * 12月份活动
     */
    public function hd01(){
        $time = time();
        $start = strtotime("2018-01-22");
        $cha = ($start*1-$time*1)/(60*60);
        $tian = round($cha)/24;
        $shi = $cha-round($tian)*24;
        if(round($tian)<=9){
            $tian = '0'.round($tian);
        }else{
            $tian = round($tian);
        }
        if(round($shi)<=9){
            $shi = '0'.round($shi);
        }else{
            $shi = round($shi);
        }
        $this->assign('tian',$tian);
        $this->assign('shi',$shi);
        return $this->fetch();
    }
    public function hd201803(){
        $m = strtotime("2018-03-31 23:59:59")-time();
        if($m>=0){
            $d =  $m/86400;
            $d1 = $m-86400*(int)$d;
            $h =  $d1/3600;
            $h1 = $d1-3600*(int)$h;
            $i = $h1/60;
            $s = $h1-60*(int)$i;
        }else{
            $d = 0;
            $h = 0;
            $i = 0;
            $s = 0;
        }
        $dd = (int)$d;
        if($dd<10){
            $dd = "0".$dd;
        }
        $hh = (int)$h;
        if($hh<10){
            $hh = "0".$hh;
        }
        $ii = (int)$i;
        if($ii<10){
            $ii = "0".$ii;
        }
        $ss = (int)$s;
        if($ss<10){
            $ss = "0".$ss;
        }
        $data = array(
            "d"=>$dd,
            "h"=>$hh,
            "i"=>$ii,
            "s"=>$ss,
        );
        $this->assign($data);
        return $this->fetch();
    }
    public function hd201804(){
        return $this->fetch();
    }

}