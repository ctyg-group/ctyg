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
use think\Controller;
use think\Db;
use think\Session;
use think\config;
class Ajax extends Controller
{
    public function index()
    {
//        echo config('web.web_title');exit;
        return $this->fetch();
    }

    public function newstype(){
        $id=input('post.id');
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $article = DB::name('article_cat')->field('cat_id,cat_name')->where(array('parent_id'=>$id))->select();
        if ($article) {
            $arr = array("code"=>"0","msg"=>"获取成功","article"=>$article);
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"获取失败");
            jsons($arr);
        }
    }
    public function baiketype(){
        $id=input('post.id');
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $article = DB::name('enped_cat')->field('cat_id,cat_name')->where(array('parent_id'=>$id))->select();
        if ($article) {
            $arr = array("code"=>"0","msg"=>"获取成功","article"=>$article);
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"获取失败");
            jsons($arr);
        }
    }

    public function upflie(){
        $typeArr = array("jpg", "png", "gif","ico","jpeg");
        //允许上传文件格式
        $time = time();
        $year = date("Y",$time);
        $md = date("md",$time);
        $path = "./public/workimg/$year/$md/";
        if (! file_exists ( $path )) {
            mkdir ( "$path", 0777, true );
        }

        if (isset($_POST)) {
            $name = $_FILES['file']['name'];
            $size = $_FILES['file']['size'];
            $name_tmp = $_FILES['file']['tmp_name'];
            if (empty($name)) {
                echo json_encode(array("error" => "您还未选择图片"));
                exit ;
            }
            $type = strtolower(substr(strrchr($name, '.'), 1));
            //获取文件类型

            if (!in_array($type, $typeArr)) {
                echo json_encode(array("error" => "清上传jpg,png或gif类型的图片！"));
                exit ;
            }
            if ($size > (10240 * 1024 *3)) {
                echo json_encode(array("error" => "图片大小已超过30mb！"));
                exit ;
            }

            $pic_name = create_guid() . "." . $type;
            //图片名称
            $pic_url = $path . $pic_name;

            //上传后图片路径+名称
            if (move_uploaded_file($name_tmp, $pic_url)) {//临时文件转移到目标文件夹
                $pic_url = substr($pic_url,1);
                echo json_encode(array("error" => "0", "pic" => $pic_url, "name" => $pic_name));
            } else {
                echo json_encode(array("error" => "上传有误，清检查服务器配置！"));
            }
        }
    }

    public function delstageimg(){
        $url = input("post.urls");
        $url = ".".$url;
        @unlink($url);
        $arr = array("code"=>"0","msg"=>"删除成功");
        jsons($arr);
    }

    public function addmenu(){
        $isurl=input('post.isurl');
        $isname=input('post.isname');
        if(empty($isurl)||empty($isname)||empty(session::get("admin_id"))){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $menu = DB::name("shortcut_menu")->where(array("navurl"=>$isurl,"adid"=>session::get("admin_id")))->find();
        if($menu){
            $arr = array("code"=>"0","msg"=>"快捷菜单已存在");
            jsons($arr);
        }
        $add['adid'] = session::get("admin_id");
        $add['navurl'] = $isurl;
        $add['navname'] = $isname;
        $article = DB::name("shortcut_menu")->insert($add);
        if ($article) {
            $arr = array("code"=>"0","msg"=>"添加成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"添加失败");
            jsons($arr);
        }
    }


    public function delmenu(){
        $id=input('post.id');
        if(empty($id)||empty(session::get("admin_id"))){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $menu = DB::name("shortcut_menu")->where(array("id"=>$id))->delete();

        if ($menu) {
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }

}
