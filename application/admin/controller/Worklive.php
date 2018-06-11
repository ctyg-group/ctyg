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
class Worklive extends Base
{
    //直播
    public function index()
    {
        if($_POST){
            $data = input("post.");
            foreach($data as $k=>$V){
                $save['sort'] = $V;
                DB::name('worklive')->where(array('id'=>$k))->update($save);
            }
            $this->redirect('Worklive/Index');

        }
        $work = Db::name('worklive')
            ->alias("w")
            ->field("w.id,w.live_name,w.stage_id,w.hot_id,w.apart_id,w.apart,w.area,w.style,w.sort,w.is_show,w.is_hot,w.is_new,w.add_time,w.worker1_id,w.worker2_id,d.designer_name")
            ->join("jiuju_designer d","w.designer_id = d.id","LEFT")->order('sort asc,id desc')->paginate(10);
        $cat = DB::name('worklive_cat')->field("cat_id,cat_name")->select();
        $worker = DB::name('worker')->field("id,worker_name")->select();
        $data = array(
            'work'=>$work,
            'cat'=>$cat,
            'worker'=>$worker,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //添加工地直播
    public function addwork(){
        if($_POST){
            $arr['live_name']   = input("post.work_name");
            $arr['apart']       = input("post.hu");
            $arr['area']        = input("post.measure");
            $arr['style']       = input("post.touch");
            $arr['designer_id'] = input("post.desid");
            $arr['worker1_id']  = input("post.butler");
            $arr['worker2_id']  = input("post.surveyor");
            $arr['stage_id']    = input("post.cat_1");
            $arr['hot_id']      = input("post.cat_2");
            $arr['apart_id']    = input("post.cat_3");
            $arr['sort']        = input("post.sort");
            $arr['is_show']     = input("post.show");
            $arr['is_hot']      = input("post.hot");
            $arr['is_new']      = input("post.news");
            $arr['add_time']    = time();
            $img = request()->file('workimg');
            if($img){
                $img    = $img->move(ROOT_PATH . 'public' . DS . 'desimg');
                $imgurl = str_replace("\\","/",$img->getSaveName());
                $imgurl = "./public/desimg/".$imgurl;
                $image  = \think\Image::open($imgurl);
                $image->thumb(390, 272)->save($imgurl);
                $arr['thumb'] = "/public/desimg/".str_replace("\\","/",$img->getSaveName());
            }else{
                echo alert_error($img->getError());exit;
            }
            $workliveid = DB::name("worklive")->insertGetId($arr);
            if($workliveid){
                for($i=1;$i<=7;$i++){
                    $stage_text = $_POST['stage_text_'.$i];
                    $arr_level['stage'] = $stage_text;
                    $arr_level['level'] = $i;
                    $arr_level['worklive_id'] = $workliveid;
                    if($_POST['stage_time_'.$i]!=''){
                        $istime = strtotime($_POST['stage_time_'.$i]);
                    }else{
                        $istime = time();
                    }
                    $arr_level['add_time'] =$istime;
                    $stageid = DB::name("worklive_stage")->insertGetId($arr_level);
                    if($stageid){
                        if($arr['stage_id']>=$i){
                            $stage_img = $_POST['stage_img_'.$i];
                            if($stage_img){
                                foreach($stage_img as $k=>$v){
                                    $arr_img['worklive_id'] = $workliveid;
                                    $arr_img['stage_id'] = $stageid;
                                    $arr_img['image_url'] = $v;
                                    $images = DB::name("worklive_images")->insert($arr_img);
                                }
                            }
                        }
                    }
                }
            }
            if($workliveid){
                echo alert_success("新增成功",url('Worklive/index'));exit;
            }else{
                echo alert_error("新增失败！");exit;
            }
        }
        $des        = DB::name('designer')->field("id,designer_name")->select();
        $butler     = DB::name("worker")->where(array("type"=>1))->select();
        $surveyor   = DB::name("worker")->where(array("type"=>2))->select();
        $cat1 = DB::name('worklive_cat')->field("cat_id,cat_name,cat_desc")->where(array("parent_id"=>8))->select();
        $cat2 = DB::name('worklive_cat')->field("cat_id,cat_name,cat_desc")->where(array("parent_id"=>10))->select();
        $cat3 = DB::name('worklive_cat')->field("cat_id,cat_name,cat_desc")->where(array("parent_id"=>21))->select();
        $data = array(
            'des'=>$des,
            'butler'=>$butler,
            'surveyor'=>$surveyor,
            'cat1'=>$cat1,
            'cat2'=>$cat2,
            'cat3'=>$cat3,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //修改工地直播
    public function modifywork(){
        if($_POST){
            $arr['id']          = input("post.id");
            $arr['live_name']   = input("post.work_name");
            $arr['apart']       = input("post.hu");
            $arr['area']        = input("post.measure");
            $arr['style']       = input("post.touch");
            $arr['designer_id'] = input("post.desid");
            $arr['worker1_id']  = input("post.butler");
            $arr['worker2_id']  = input("post.surveyor");
            $arr['stage_id']    = input("post.cat_1");
            $arr['hot_id']      = input("post.cat_2");
            $arr['apart_id']    = input("post.cat_3");
            $arr['sort']        = input("post.sort");
            $arr['is_show']     = input("post.show");
            $arr['is_hot']      = input("post.hot");
            $arr['is_new']      = input("post.news");
            $img = request()->file('workimg');
            if($img){
                $img    = $img->move(ROOT_PATH . 'public' . DS . 'desimg');
                $imgurl = str_replace("\\","/",$img->getSaveName());
                $imgurl = "./public/desimg/".$imgurl;
                $image  = \think\Image::open($imgurl);
                $image->thumb(390, 272)->save($imgurl);
                $arr['thumb'] = "/public/desimg/".str_replace("\\","/",$img->getSaveName());
            }
            for($i=1;$i<=7;$i++){
                $worklive_stage = DB::name("worklive_stage")->where(array("worklive_id"=>$arr['id'],"level"=>$i))->find();
                $stage_text = $_POST['stage_text_'.$i];
                $delimgstage = $_POST['delimgstage'.$i];
                if($delimgstage){
                    $imgwhere['id'] = array("in",$delimgstage);
                    $delimg = DB::name("worklive_images")->where($imgwhere)->select();
                    foreach($delimg as $kimg=>$vimg){
                        $delimgurl =".".$vimg['images_url'];
                        @unlink($delimgurl);
                    }
                    $delimgs = DB::name("worklive_images")->where($imgwhere)->delete();
                }
                $arr_level['stage'] = $stage_text;
                $arr_level['level'] = $i;
                if($_POST['stage_time_'.$i]!=''){
                    $arr_level['add_time'] = strtotime($_POST['stage_time_'.$i]);
                }
                $stageid = DB::name("worklive_stage")->where(array("worklive_id"=>$arr['id'],"level"=>$i))->update($arr_level);
                $stage_img = $_POST['stage_img_'.$i];
                if($stage_img){
                    foreach($stage_img as $k=>$v){
                        $arr_img['stage_id'] = $worklive_stage['id'];
                        $arr_img['image_url'] = $v;
                        $arr_img['worklive_id'] = $arr['id'];
                        $images = DB::name("worklive_images")->insert($arr_img);
                    }
                }
            }
            $workliveid = DB::name("worklive")->update($arr);
            if($workliveid||$delimgs||$stageid||$images){
                echo alert_success("修改成功",url('Worklive/index'));exit;
            }else{
                echo alert_error("修改失败！");exit;
            }
        }
        $id = trim(input("id"));
        if(empty($id)){
            echo alert_error("参数错误！");exit;
        }
        $work       = DB::name("worklive")->where(array("id"=>$id))->find();
        $stage      = DB::name("worklive_stage")->where(array("worklive_id"=>$id))->order("level asc")->select();
        $num=0;
        if($work['id']>=31){
            $order = "id asc";
        }else{
            $order = "id desc";
        }
        foreach($stage as $k=>$v){
            $stage[$num]['stage_img'] = DB::name("worklive_images")->field("id,image_url,stage_id")->where(array("stage_id"=>$v['id']))->order($order)->select();
            $num++;
        }
        $des        = DB::name('designer')->field("id,designer_name")->select();
        $butler     = DB::name("worker")->where(array("type"=>1))->select();
        $surveyor   = DB::name("worker")->where(array("type"=>2))->select();
        $cat1 = DB::name('worklive_cat')->field("cat_id,cat_name,cat_desc")->where(array("parent_id"=>8))->select();
        $cat2 = DB::name('worklive_cat')->field("cat_id,cat_name,cat_desc")->where(array("parent_id"=>10))->select();
        $cat3 = DB::name('worklive_cat')->field("cat_id,cat_name,cat_desc")->where(array("parent_id"=>21))->select();

        $stage1 = $stage[0];
        $stage2 = $stage[1];
        $stage3 = $stage[2];
        $stage4 = $stage[3];
        $stage5 = $stage[4];
        $stage6 = $stage[5];
        $stage7 = $stage[6];

        $data = array(
            'des'=>$des,
            'butler'=>$butler,
            'surveyor'=>$surveyor,
            'cat1'=>$cat1,
            'cat2'=>$cat2,
            'cat3'=>$cat3,
            'work'=>$work,
            'stage1'=>$stage1,
            'stage2'=>$stage2,
            'stage3'=>$stage3,
            'stage4'=>$stage4,
            'stage5'=>$stage5,
            'stage6'=>$stage6,
            'stage7'=>$stage7,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //工地直播状态
    public function workstates(){
        $id=trim(input('post.id'));
        $type=trim(input('post.type'));
        if(empty($id)||empty($type)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $article = DB::name('worklive')->field("is_show,is_hot,is_new")->where(array('id'=>$id))->find();
        if($article[$type]==1){
            $save[$type] =2;
        }else{
            $save[$type] =1;
        }
        $isarticle=DB::name('worklive')->where(array('id'=>$id))->update($save);
        if($isarticle){
            if($save[$type]==1){
                $arr = array("code"=>"0","msg"=>"成功","state"=>$save[$type]);
                jsons($arr);
            }else{
                $arr = array("code"=>"0","msg"=>"失败","state"=>$save[$type]);
                jsons($arr);
            }
        }else{
            $arr = array("code"=>"1","msg"=>"操作失败");
            jsons($arr);
        }
    }
    //删除工地直播
    public function delwork(){
        $id=trim(input('post.id'));
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $work = DB::name("worklive")->where(array("id"=>$id))->find();
        $wimg = DB::name("worklive_images")->where(array("worklive_id"=>$id))->select();
        $del = DB::name("worklive")->where(array("id"=>$id))->delete();
        DB::name("worklive_stage")->where(array("worklive_id"=>$id))->delete();
        DB::name("worklive_images")->where(array("worklive_id"=>$id))->delete();

        $imgurl = ".".$work['thumb'];
        @unlink($imgurl);

        foreach ($wimg as $k=>$v){
            $imgurls = ".".$v['image_url'];
            @unlink($imgurls);
        }

        if($del){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }
    //工地直播分类
    public function worktype(){
        if($_POST){
            $data = input("post.");
            foreach($data as $k=>$V){
                $save['sort_order'] = $V;
                DB::name('worklive_cat')->where(array('cat_id'=>$k))->update($save);
            }
            $this->redirect('Designer/casetype');
        }
        $article_cat = DB::name('worklive_cat')->field('cat_id,cat_name,sort_order')->where(array('parent_id'=>0))->order("sort_order asc")->select();
        $num=0;
        foreach($article_cat as $ks=>$vs){
            $article_cat[$num]['cat'] = DB::name('worklive_cat')->field('cat_id,cat_name,sort_order')->where(array('parent_id'=>$vs['cat_id']))->order("sort_order asc")->select();
            $num++;
        }
        $data = array(
            'article_cat'=>$article_cat,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //添加工地直播分类
    public function addworktype(){
        $data=input('post.');
        if($data['cat_name']==''){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $result=DB::name('worklive_cat')->insertGetId($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"添加成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"添加失败");
            jsons($arr);
        }
    }
    //修改工地直播分类
    public function modifyworktype(){
        $data=input('post.');
        if($data['cat_name']==''){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $result=DB::name('worklive_cat')->update($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"修改成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"修改失败");
            jsons($arr);
        }
    }
    //删除工地直播分类
    public function delworktype(){
        $id=input('post.id');
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $article_cat = DB::name('worklive_cat')->field('cat_id')->where(array('parent_id'=>$id))->find();
        if($article_cat){
            $arr = array("code"=>"1","msg"=>"请先删除子权限");
            jsons($arr);
        }
        $map['cat_id']=$id;
        $result=DB::name('worklive_cat')->delete($map);
        if($result){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }
    //项目管家/工程监理
    public function worker(){
        $worker = DB::name("worker")->paginate(10);
        $data = array(
            'worker'=>$worker,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //添加项目管家/工程监理
    public function addworker(){
        if($_POST){
            $arr['worker_name'] = trim(input("post.worker_name"));
            $arr['type'] = trim(input("post.type"));
            $img = request()->file('headimg');
            if($img){
                $img    = $img->move(ROOT_PATH . 'public' . DS . 'desimg');
                $arr['image'] = "/public/desimg/".str_replace("\\","/",$img->getSaveName());
            }else{
                echo alert_error($img->getError());exit;
            }

            $worker = DB::name("worker")->insert($arr);
            if($worker){
                echo alert_success("新增成功",url('Worklive/worker'));exit;
            }else{
                echo alert_error("新增失败！");exit;
            }
        }
    }
    //修改项目管家/工程监理
    public function modifyworker(){
        if($_POST){
            $arr['id'] = trim(input("post.wid"));
            $arr['worker_name'] = trim(input("post.workername"));
            $arr['type'] = trim(input("post.types"));
            $img = request()->file('imghead');
            if($img){
                $img    = $img->move(ROOT_PATH . 'public' . DS . 'desimg');
                $arr['image'] = "/public/desimg/".str_replace("\\","/",$img->getSaveName());
            }

            $worker = DB::name("worker")->update($arr);
            if($worker){
                echo alert_success("修改成功",url('Worklive/worker'));exit;
            }else{
                echo alert_error("修改失败！");exit;
            }
        }
    }
    //删除项目管家/工程监理
    public function delworker(){
        $id=trim(input('post.id'));
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $work = DB::name("worker")->where(array("id"=>$id))->find();
        $del = DB::name("worker")->where(array("id"=>$id))->delete();
        $imgurl = ".".$work['image'];
        @unlink($imgurl);
        if($del){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }
}
