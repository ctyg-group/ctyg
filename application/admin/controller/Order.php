<?php
/**
 * 久居
 * ============================================================================
 * 本系统由久居整装开发
 * ============================================================================
 * Author: Suki
 * Date: 2018-03-09
 */
namespace app\admin\controller;
use think\Db;
use think\Session;
use think\Config;
class Order extends Base
{
    public function index()
    {
//        echo config('web.web_title');exit;
        return $this->fetch();
    }
    //预约信息列表
    public function appointment(){
        $info = DB::name("userinfo")->order("time desc")->paginate(10);
        $data = array(
            'info'=>$info,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //修改预约信息
    public function modifyinfo(){
        $data=input('post.');
        $result=DB::name('userinfo')->update($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"修改成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"修改失败");
            jsons($arr);
        }
    }
    //删除预约信息
    public function delinfo(){
        $id=trim(input('post.id'));
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $del = DB::name("userinfo")->where(array("id"=>$id))->delete();
        if($del){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }

    public function banner(){
        if($_POST){
            $data = input("post.");
            foreach($data as $k=>$V){
                $save['sort'] = $V;
                DB::name('slide')->where(array('id'=>$k))->update($save);
            }
            $this->redirect('Order/banner');

        }
        $slide = DB::name("slide")->order("sort asc")->where(array("is_mobile"=>0))->select();
        $slide1 = DB::name("slide")->order("sort asc")->where(array("is_mobile"=>1))->select();
        $data = array(
            'slide'=>$slide,
            'slide1'=>$slide1,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //添加banner图
    public function addbanner(){
        if($_POST){
            $arr['is_mobile'] = trim(input("post.type"));
            $arr['title'] = trim(input("post.title"));
            $arr['url'] = trim(input("post.url"));
            $arr['sort'] = trim(input("post.sort"));
            $img = request()->file('image');
            if($img){
                $img    = $img->move(ROOT_PATH . 'public' . DS . 'desimg');
                $arr['image'] = "/public/desimg/".str_replace("\\","/",$img->getSaveName());
            }else{
                echo alert_error($img->getError());exit;
            }

            $worker = DB::name("slide")->insert($arr);
            if($worker){
                echo alert_success("新增成功",url('Order/banner'));exit;
            }else{
                echo alert_error("新增失败！");exit;
            }
        }
    }
    //修改banner图
    public function modifybanner(){
        if($_POST){
            $arr['id'] = trim(input("post.id"));
            $arr['is_mobile'] = trim(input("post.type"));
            $arr['title'] = trim(input("post.title"));
            $arr['url'] = trim(input("post.url"));
            $arr['sort'] = trim(input("post.sort"));
            $img = request()->file('image');
            if($img){
                $img    = $img->move(ROOT_PATH . 'public' . DS . 'desimg');
                $arr['image'] = "/public/desimg/".str_replace("\\","/",$img->getSaveName());
            }
            $worker = DB::name("slide")->update($arr);
            if($worker){
                echo alert_success("修改成功",url('Order/banner'));exit;
            }else{
                echo alert_error("修改失败！");exit;
            }
        }
    }
    //删除banner图
    public function delbanner(){
        $id=trim(input('post.id'));
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $banner = DB::name("slide")->where(array("id"=>$id))->find();
        $imgurl = ".".$banner['image'];
        @unlink($imgurl);
        $del = DB::name("slide")->where(array("id"=>$id))->delete();
        if($del){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }

    //友情链接
    public function link(){
        if($_POST){
            $data = input("post.");
            foreach($data as $k=>$V){
                $save['sort'] = $V;
                DB::name('frlink')->where(array('id'=>$k))->update($save);
            }
            $this->redirect('Order/link');
        }
        $link = DB::name('frlink')->order("sort asc")->select();

        $data = array(
            'link'=>$link,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //添加友情链接
    public function addlink(){
        $data=input('post.');
        if($data['url']==''||$data['name']==''){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $result=DB::name('frlink')->insertGetId($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"添加成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"添加失败");
            jsons($arr);
        }
    }
    //修改友情链接
    public function modifylink(){
        $data=input('post.');
        if($data['url']==''||$data['name']==''){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $result=DB::name('frlink')->update($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"修改成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"修改失败");
            jsons($arr);
        }
    }
    //删除友情链接
    public function dellink(){
        $id=trim(input('post.id'));
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $del = DB::name("frlink")->where(array("id"=>$id))->delete();
        if($del){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }

    //招聘信息
    public function job(){

        $job = DB::name('job')->order("add_time desc")->select();

        $data = array(
            'job'=>$job,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //新增招聘信息
    public function addjob(){
        $data=input('post.');
        $data['add_time'] = time();
        $result=DB::name('job')->insertGetId($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"添加成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"添加失败");
            jsons($arr);
        }
    }
    //修改招聘信息
    public function modifyjob(){
        $data=input('post.');
        $result=DB::name('job')->update($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"修改成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"修改失败");
            jsons($arr);
        }
    }

    //删除招聘信息
    public function deljob(){
        $id=trim(input('post.id'));
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $del = DB::name("job")->where(array("id"=>$id))->delete();
        if($del){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }
}
