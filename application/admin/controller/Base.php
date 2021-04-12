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
use modelapi\Data;
class Base extends Controller
{
    public function _initialize(){
        header("Content-type: text/html; charset=utf-8");
        if(empty(session::get("admin_id"))||empty(session::get("admin_name"))){
            $this->redirect('Login/index');
        }
        $jiuju_admin = DB::name("admin")->where(array("id"=>session::get("admin_id"),"account"=>session::get("admin_name"),"state"=>1))->find();
        if(!$jiuju_admin){
            $this->redirect('Login/index');
        }
        $req['module'] = request()->module();
        $req['controller'] = request()->controller();
        $req['action'] = request()->action();
        //权限管理
        if(session("admin_id")!=1){
            $auth=new Auth();
            $this->current_action = $req['module'].'/'.$req['controller'].'/'.lcfirst($req['action']);
            $result = $auth->check($this->current_action,session("admin_id"));
            if(empty($result)){
                if(isset($_GET['ajax'])) {
                    $arr = array("code"=>"999","msg"=>"您没有权限");
                    jsons($arr);
                }
                else {
                    echo alert_error("您没有权限");exit;
                }
            }
        }
        $req['navurls'] = $req['module'].'/'.$req['controller'].'/'.lcfirst($req['action']);
        //系统参数
        $configDB = DB::name("config")->field("group,name,value")->select();
        $data = array();
        foreach($configDB as $key=>$value){
            $data[$value['group']][$value['name']] = $value['value'];
        }
        Config::set($data);

        $menu = $this->menu();
        //菜单
        $this->assign('menu',$menu);
        $this->assign($req);

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
                "name"=>"文章管理",
                "url"=>"Admin/News",
                "icon"=>"mdi mdi-newspaper fa-fw",
                "nextnav"=>array(
                    "0"=>array(
                        "name"=>"新闻管理",
                        "url"=>"Admin/News/index",
                        "view"=>"index",
                    ),
                    "2"=>array(
                        "name"=>"新闻类型",
                        "url"=>"Admin/News/newstype",
                        "view"=>"newstype",
                    ),
                    "3"=>array(
                        "name"=>"行业文章",
                        "url"=>"Admin/News/baike",
                        "view"=>"baike",
                    ),
                    "4"=>array(
                        "name"=>"文章类型",
                        "url"=>"Admin/News/baiketype",
                        "view"=>"baiketype",
                    ),
                ),
            ),
            'Designer'=>array(
                "name"=>"集团动态",
                "url"=>"Admin/Designer",
                "icon"=>"mdi mdi-account-box fa-fw",
                "nextnav"=>array(
                    "0"=>array(
                        "name"=>"管理",
                        "url"=>"Admin/Designer/index",
                        "view"=>"index",
                    ),
                    "1"=>array(
                        "name"=>"分类",
                        "url"=>"Admin/Designer/destypes",
                        "view"=>"destypes",
                    ),
                    "2"=>array(
                        "name"=>"动态管理",
                        "url"=>"Admin/Designer/caselist",
                        "view"=>"caselist",
                    ),
                    "3"=>array(
                        "name"=>"动态分类",
                        "url"=>"Admin/Designer/casetype",
                        "view"=>"casetype",
                    ),
                ),
            ),
            'Worklive'=>array(
                "name"=>"旗下品牌",
                "url"=>"Admin/Worklive",
                "icon"=>"mdi mdi-camcorder fa-fw",
                "nextnav"=>array(
                    "0"=>array(
                        "name"=>"品牌列表",
                        "url"=>"Admin/Worklive/index",
                        "view"=>"index",
                    ),
                    "1"=>array(
                        "name"=>"品牌分类",
                        "url"=>"Admin/Worklive/worktype",
                        "view"=>"worktype",
                    ),
                    "2"=>array(
                        "name"=>"品牌管理",
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
        if(session("admin_id")!=1){
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
        }
        return $data;
    }

}
