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
use org\Auth;
class Login extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
    public function newlogin()
    {
        return $this->fetch();
    }
    public function login()
    {
        $name = trim(input('post.name'));
        $pass = trim(input('post.pass'));
        if(empty($name)||empty($pass)){
            $arr = array("code"=>"-2","msg"=>"error!");
            jsons($arr);
        }else{
            $user = DB::name("admin")->where(array('account'=>$name))->find();
            if(!$user){
                $arr = array("code"=>"-2","msg"=>"没有此账户!");
                jsons($arr);
            }
            if(md5($pass)!=$user['pass']){
                $arr = array("code"=>"-2","msg"=>"密码不正确!");
                jsons($arr);
            }
            if($user['state']!=1){
                $arr = array("code"=>"-2","msg"=>"账号不允许登录!");
                jsons($arr);
            }
            $auth_group_access = DB::name('auth_group_access')->where(array('uid'=>$user['id']))->find();
            if($auth_group_access){
                Session::set('admin_id',$user['id']);
                Session::set('admin_name',$user['account']);
                Session::set('times',time());
                $nav_data=$this->menu();
                $num = 0;
                $url = "";
                foreach($nav_data as $k=>$v){
                    if($num==0){
                        if($v['nextnav']){
                            $n=0;
                            foreach($v['nextnav'] as $a=>$b){
                                if($n==0){
                                    $url = url($b['url']);
                                }
                                $n++;
                            }
                        }else{
                            $url = url($v['url']);
                        }
                    }
                    $num++;
                }
                $arr = array("code"=>"0","msg"=>"登录成功!","url"=>$url);
                jsons($arr);
            }else{
                $arr = array("code"=>"-2","msg"=>"登录失败!");
                jsons($arr);
            }
        }
    }


    public function menu(){

        $data = array(
            'Index'=>array(
                "name"=>"主页",
                "url"=>"Admin/Index/index",
                "icon"=>"mdi mdi-home fa-fw",
                "nextnav"=>"",
            ),
            'News'=>array(
                "name"=>"文章资讯",
                "url"=>"Admin/News",
                "icon"=>"mdi mdi-newspaper fa-fw",
                "nextnav"=>array(
                    "0"=>array(
                        "name"=>"文章管理",
                        "url"=>"Admin/News/index",
                        "view"=>"index",
                    ),
                    "2"=>array(
                        "name"=>"文章类型",
                        "url"=>"Admin/News/newstype",
                        "view"=>"newstype",
                    ),
                    "3"=>array(
                        "name"=>"百科列表",
                        "url"=>"Admin/News/baike",
                        "view"=>"baike",
                    ),
                    "4"=>array(
                        "name"=>"百科类型",
                        "url"=>"Admin/News/baiketype",
                        "view"=>"baiketype",
                    ),
                ),
            ),
            'Designer'=>array(
                "name"=>"设计师&案例",
                "url"=>"Admin/Designer",
                "icon"=>"mdi mdi-account-box fa-fw",
                "nextnav"=>array(
                    "0"=>array(
                        "name"=>"设计师管理",
                        "url"=>"Admin/Designer/index",
                        "view"=>"index",
                    ),
                    "1"=>array(
                        "name"=>"设计师分类",
                        "url"=>"Admin/Designer/destypes",
                        "view"=>"destypes",
                    ),
                    "2"=>array(
                        "name"=>"案例管理",
                        "url"=>"Admin/Designer/caselist",
                        "view"=>"caselist",
                    ),
                    "3"=>array(
                        "name"=>"案例分类",
                        "url"=>"Admin/Designer/casetype",
                        "view"=>"casetype",
                    ),
                ),
            ),
            'Worklive'=>array(
                "name"=>"工地直播",
                "url"=>"Admin/Worklive",
                "icon"=>"mdi mdi-camcorder fa-fw",
                "nextnav"=>array(
                    "0"=>array(
                        "name"=>"工地直播列表",
                        "url"=>"Admin/Worklive/index",
                        "view"=>"index",
                    ),
                    "1"=>array(
                        "name"=>"工地直播分类",
                        "url"=>"Admin/Worklive/worktype",
                        "view"=>"worktype",
                    ),
                    "2"=>array(
                        "name"=>"项目管家&工程监理",
                        "url"=>"Admin/Worklive/worker",
                        "view"=>"worker",
                    ),
                ),
            ),
            'Order'=>array(
                "name"=>"消息&功能",
                "url"=>"Admin/Order",
                "icon"=>"mdi mdi-widgets fa-fw",
                "nextnav"=>array(
                    "0"=>array(
                        "name"=>"预约信息",
                        "url"=>"Admin/Order/appointment",
                        "view"=>"appointment",
                    ),
                    "1"=>array(
                        "name"=>"Banner图",
                        "url"=>"Admin/Order/banner",
                        "view"=>"banner",
                    ),
                    "2"=>array(
                        "name"=>"友情链接",
                        "url"=>"Admin/Order/link",
                        "view"=>"link",
                    ),
                    "3"=>array(
                        "name"=>"职位招聘",
                        "url"=>"Admin/Order/job",
                        "view"=>"job",
                    ),
                ),
            ),
            'Setup'=>array(
                "name"=>"系统设置",
                "url"=>"Admin/Setup",
                "icon"=>"mdi mdi-settings fa-fw",
                "nextnav"=>array(
//                    "0"=>array(
//                        "name"=>"系统设置",
//                        "url"=>"Admin/Setup/setup",
//                        "view"=>"setup",
//                    ),
                    "0"=>array(
                        "name"=>"权限管理",
                        "url"=>"Admin/Setup/authindex",
                        "view"=>"authindex",
                    ),
                    "1"=>array(
                        "name"=>"用户组管理",
                        "url"=>"Admin/Setup/auth",
                        "view"=>"auth",
                    ),
                    "2"=>array(
                        "name"=>"管理员管理",
                        "url"=>"Admin/Setup/user",
                        "view"=>"user",
                    ),
                    "3"=>array(
                        "name"=>"修改密码",
                        "url"=>"Admin/Setup/modifypass",
                        "view"=>"modifypass",
                    ),
                ),
            ),
        );
        $auth=new Auth();
        foreach ($data as $k => $v) {
            if ($auth->check($v['url'],session("admin_id"))) {
                if($v['nextnav']!=''){
                    foreach ($v['nextnav'] as $mauthk => $mauthv) {
                        if($auth->check($mauthv['url'],session("admin_id"))){

                        }else{
                            unset($data[$k]['nextnav'][$mauthk]);
                        }
                    }
                }
            }else{
                // 删除无权限的菜单
                unset($data[$k]);
            }
        }
        return $data;
    }


    public function signout()
    {
        session('admin_id',null);
        session('admin_name',null);
        session('times',null);
        $this->redirect('Login/index');
    }

}
