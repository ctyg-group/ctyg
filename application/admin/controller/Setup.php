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

class Setup extends Base
{
    public function index()
    {
//        echo config('web.web_title');exit;
        return $this->fetch();
    }
   /**
    * 后台菜单
    */
    public function adnav(){
        return $this->fetch();
    }
    /**
     * 权限管理
     */
    public function authindex(){

        $auth = model("AuthRule")->getTreeData('tree','id','title');
        $data = array(
            "auth"=>$auth,
        );
        $this->assign($data);
        return $this->fetch();
    }


    //添加权限
    public function authadd(){
        $data=input('post.');
        if($data['title']==''||$data['name']==''){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $data['pid']    = 0;
        $data['status'] = 1;
        $result=DB::name('auth_rule')->insertGetId($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"添加成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"添加失败");
            jsons($arr);
        }
    }
    //添加子权限
    public function authadd_level(){
        $data=input('post.');
        if($data['pid']==''||$data['title']==''||$data['name']==''){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $data['status'] = 1;
        $result=DB::name('auth_rule')->insertGetId($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"添加成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"添加失败");
            jsons($arr);
        }
    }
    //修改权限
    public function authmodify(){
        $data=input('post.');
        if($data['title']){
            $result=DB::name('auth_rule')->update($data);
            if ($result) {
                $arr = array("code"=>"0","msg"=>"修改成功");
                jsons($arr);
            }else{
                $arr = array("code"=>"1","msg"=>"修改失败");
                jsons($arr);
            }
        }else{
            $id = trim(input('post.id'));
            if(!$id){
                $arr = array("code"=>"1","msg"=>"参数错误");
                jsons($arr);
            }
            $auth = DB::name("auth_rule")->where(array("id"=>$id))->find();
            if($auth){
                $arr = array("code"=>"0","msg"=>"获取成功","auth"=>$auth);
                jsons($arr);
            }else{
                $arr = array("code"=>"1","msg"=>"获取失败");
                jsons($arr);
            }
        }
    }
    //删除权限
    public function authdel(){
        $id=input('post.id');
        $map=array(
            'id'=>$id
        );
        $result=model('AuthRule')->deleteData($map);
        if($result){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"请先删除子权限");
            jsons($arr);
        }
    }


    //管理组列表
    public function auth(){
        $auth = DB::name("auth_group")->select();
        $data = array(
            "auth"=>$auth,
        );
        $this->assign($data);
        return $this->fetch();
    }

    //添加用户组
    public function addusergroup(){
        $data=input('post.');
        if($data['title']==''){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $result=DB::name('auth_group')->insertGetId($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"添加成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"添加失败");
            jsons($arr);
        }
    }
    //修改用户组
    public function modifyusergroup(){
        $data=input('post.');
        if($data['title']==''||$data['id']==''){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $result=DB::name('auth_group')->update($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"修改成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"修改失败");
            jsons($arr);
        }
    }
    //删除管理组
    public function delgroup(){
        $id=trim(input('post.id'));
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $result=DB::name('auth_group')->where(array('id'=>$id))->delete();
        if($result){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }

    //权限分配
    public function auth_allot(){
        if($_POST){
            $ids  = trim(input("post.id"));
            $auth = trim(input("post.auth"));
            $group_it = DB::name("auth_group")->where(array("id"=>$ids))->find();
            if(!$group_it){
                $arr = array("code"=>"1","msg"=>"参数错误！");
                jsons($arr);
            }
            $auth = substr($auth,0,strlen($auth)-1);
            $save['rules'] = $auth;
            $group = DB::name("auth_group")->where(array("id"=>$ids))->update($save);
            if($group){
                $arr = array("code"=>"0","msg"=>"修改成功");
                jsons($arr);
            }else{
                $arr = array("code"=>"1","msg"=>"请做出修改");
                jsons($arr);
            }
        }
        $id = trim(input("id"));
        if(!$id){
            $arr = array("code"=>"1","msg"=>"非法数据");
            jsons($arr);
        }
        $group = DB::name("auth_group")->where(array("id"=>$id))->find();
        $rules = $group['rules'];
        $rules = explode(",",$rules);
        $auth = DB::name("auth_rule")->select();

        $num=0;
        foreach($auth as $k=>$v){
            $isin = in_array($v['id'],$rules);
            if($isin){
                $auth[$num]['rules'] = 1;
            }else{
                $auth[$num]['rules'] = 2;
            }
            $num++;
        }

        $auth = get_attr($auth,0);
        $data = array(
            "auth"=>$auth,
            "ad_id"=>$id,
        );
        $this->assign($data);
        return $this->fetch();
    }

    //管理员列表
    public function user(){
        $admin=DB::name("AuthGroupAccess")
            ->field('u.id,u.account,u.state,u.name,aga.group_id,ag.title')
            ->alias('aga')
            ->join('admin u','aga.uid = u.id','RIGHT')
            ->join('auth_group ag','aga.group_id = ag.id','LEFT')
            ->select();
        $data = array(
            "admin"=>$admin,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //添加管理员
    public function adduser(){

        if($_POST){
            $add['account']= trim(input("post.account"));
            $isaccount = DB::name("admin")->where(array("account"=>$add['account']))->find();
            if($isaccount){
                echo alert_error("账号已存在");exit;
            }
            $add['name']   = trim(input("post.name"));
            $add['pass']   = md5(trim(input("post.password")));
            $add['state']  = trim(input("post.state"));
            $group          = trim(input("post.group"));
            $add['time'] = time();
            $adid = DB::name("admin")->insertGetId($add);
            if($adid>0){
                if($group!=''){
                    $adds['uid'] = $adid;
                    $adds['group_id'] = $group;
                    $aga = DB::name("auth_group_access")->insert($adds);
                    if($aga>0){
                        echo alert_success("添加成功",url('Setup/user'));exit;
                    }else{
                        echo alert_error("添加失败");exit;
                    }
                }else{
                    echo alert_success("添加成功",url('Setup/user'));exit;
                }
            }else{
                echo alert_error("添加失败");exit;
            }

        }
        $group = DB::name("auth_group")->select();
        $data = array(
            "group"=>$group,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //修改管理员
    public function modifyuser(){

        if($_POST){
            $id= trim(input("post.id"));
            $save['account']= trim(input("post.account"));
            $where['id'] = array("neq",$id);
            $where['account'] = $save['account'];
            $isaccount = DB::name("admin")->where($where)->find();
            if($isaccount){
                echo alert_error("账号已存在");exit;
            }
            $save['name']   = trim(input("post.name"));
            $pass          = trim(input("post.password"));
            if($pass){
                $save['pass'] = md5($pass);
            }
            $save['state']  = trim(input("post.state"));
            $group= trim(input("post.group"));


            $saves['group_id'] = $group;
            $addss['uid'] = $id;
            $addss['group_id'] = $group;
            $isageis = DB::name("auth_group_access")->where(array('uid'=>$id))->find();
            if($isageis){
                $aga = DB::name("auth_group_access")->where(array('uid'=>$id))->update($saves);
            }else{
                $aga = DB::name("auth_group_access")->insert($addss);
            }
            $adid = DB::name("admin")->where(array('id'=>$id))->update($save);
            if($aga||$adid){
                echo alert_success("修改成功",url('Setup/user'));exit;
            }else{
                echo alert_error("修改失败，可能你并未作出修改");exit;
            }
        }

        $id = trim(input("id"));
        if(!$id){
            die("error");
        }
        if($id==1){
            echo alert_error("参数错误");exit;
        }
        $admin = DB::name("admin")->where(array("id"=>$id))->find();
        $aga = DB::name("auth_group_access")->where(array("uid"=>$admin['id']))->find();
        $group = DB::name("auth_group")->select();
        $data = array(
            "admin"=>$admin,
            "aga"=>$aga,
            "group"=>$group,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //删除管理员
    public function deluser(){
        $id=trim(input('post.id'));
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        if($id==1){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $result=DB::name('admin')->where(array('id'=>$id))->delete();
        if($result){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }
    //修改登录状态
    public function signstate(){
        $id=trim(input('post.id'));
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        if($id==1){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $admin = DB::name('admin')->where(array('id'=>$id))->find();
        if($admin['state']==1){
            $save['state'] =2;
        }else{
            $save['state'] =1;
        }
        $result=DB::name('admin')->where(array('id'=>$id))->update($save);
        if($result){

            if($save['state']==1){
                $arr = array("code"=>"0","msg"=>"已允许登录","state"=>$save['state']);
                jsons($arr);
            }else{
                $arr = array("code"=>"0","msg"=>"已禁止登录","state"=>$save['state']);
                jsons($arr);
            }
        }else{
            $arr = array("code"=>"1","msg"=>"操作失败");
            jsons($arr);
        }
    }
    //修改密码
    public function modifypass(){
        if($_POST){
            $passold = trim(input("post.passold"));
            $passnew = trim(input("post.passnew"));

            $admin = DB::name("admin")->where(array("id"=>session::get("admin_id")))->find();
            if(md5($passold)!=$admin['pass']){
                $arr = array("code"=>"1","msg"=>session::get("admin_name"));
                jsons($arr);
            }
            if($passold==$passnew){
                $arr = array("code"=>"1","msg"=>"原密码不可与新密码一致");
                jsons($arr);
            }

            $save['pass'] = md5($passnew);
            $aid = DB::name("admin")->where(array("id"=>session::get("admin_id")))->update($save);
            if($aid){
                session('admin_id',null);
                session('admin_name',null);
                session('times',null);
                session('expire',-1);
                $arr = array("code"=>"0","msg"=>"修改成功");
                jsons($arr);
            }else{
                $arr = array("code"=>"1","msg"=>"修改失败");
                jsons($arr);
            }

        }

        return $this->fetch();
    }

    //系统设置
    public function setup(){
        return $this->fetch();
    }
}
