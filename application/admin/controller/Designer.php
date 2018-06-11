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
class Designer extends Base
{
    //设计师列表
    public function index()
    {
        if($_POST){
            $data = input("post.");
            foreach($data as $k=>$V){
                $save['sort'] = $V;
                DB::name('designer')->where(array('id'=>$k))->update($save);
            }
            $this->redirect('Designer/Index');

        }
        $des = Db::name('designer')->order('sort asc,id desc')->paginate(10);
        $cat = DB::name('designer_cat')->field("cat_id,cat_name")->select();
        $data = array(
            'des'=>$des,
            'cat'=>$cat
        );
        $this->assign($data);
        return $this->fetch();
    }
    //新增设计师
    public function adddesigner(){
        if($_POST){

            $arr['designer_name']   = input("post.name");
            $arr['ex_id']           = input("post.cat_name1");
            $arr['level_id']        = input("post.cat_name6");
            $arr['style_id']        = input("post.cat_name10");
            $arr['designer_ex']     = input("post.ex");
            $arr['designer_num']    = input("post.works");
            $arr['order_num']       = input("post.order");
            $arr['pop_num']         = input("post.pop");
            $arr['score']           = input("post.score");
            $arr['mobile']          = input("post.phone");
            $arr['designer_case']   = input("post.cases");
            $arr['designer_prize']  = input("post.intro");
            $arr['is_hot']          = input("post.hot");
            $arr['is_new']          = input("post.news");

            $imghead = request()->file('imghead');
            if($imghead){
                $img    = $imghead->move(ROOT_PATH . 'public' . DS . 'desimg');
                $imgurl = str_replace("\\","/",$img->getSaveName());
                $imgurl = "./public/desimg/".$imgurl;
                $image  = \think\Image::open($imgurl);
                $image->thumb(218, 218)->save($imgurl);
                $arr['sm_img'] = "/public/desimg/".str_replace("\\","/",$img->getSaveName());
            }else{
                echo alert_error($imghead->getError());exit;
            }

            $image = request()->file('image');
            if($image){
                $image = $image->move(ROOT_PATH . 'public' . DS . 'desimg');
                $imageurl = str_replace("\\","/",$image->getSaveName());
                $arr['img'] = "/public/desimg/".$imageurl;
            }else{
                echo alert_error($image->getError());exit;
            }

            $eqcode = request()->file('eqcode');
            if($eqcode){
                $eqcode = $eqcode->move(ROOT_PATH . 'public' . DS . 'desimg');
                $eqcodeurl = str_replace("\\","/",$eqcode->getSaveName());
                $arr['wx_img'] = "/public/desimg/".$eqcodeurl;
            }else{
                echo alert_error($eqcode->getError());exit;
            }

            $designer = DB::name("designer")->insertGetId($arr);
            if($designer){
                echo alert_success("新增成功",url('Designer/index'));exit;
            }else{
                echo alert_error("新增失败！");exit;
            }
        }

        $cat = DB::name('designer_cat')->field("cat_id,cat_name")->where(array("parent_id"=>0))->select();
        $num=0;
        foreach($cat as $key=>$value){
            $cat[$num]['nextcat'] = DB::name('designer_cat')->field("cat_id,cat_name")->where(array("parent_id"=>$value['cat_id']))->select();
            $num++;
        }

        $data = array(
            'cat'=>$cat
        );
        $this->assign($data);
        return $this->fetch();
    }
    //修改设计师
    public function modifydesigner(){
        if($_POST){
            $arr['id']              = input("post.id");
            $arr['designer_name']   = input("post.name");
            $arr['ex_id']           = input("post.cat_name1");
            $arr['level_id']        = input("post.cat_name6");
            $arr['style_id']        = input("post.cat_name10");
            $arr['designer_ex']     = input("post.ex");
            $arr['designer_num']    = input("post.works");
            $arr['order_num']       = input("post.order");
            $arr['pop_num']         = input("post.pop");
            $arr['score']           = input("post.score");
            $arr['mobile']          = input("post.phone");
            $arr['designer_case']   = input("post.cases");
            $arr['designer_prize']  = input("post.intro");
            $arr['is_hot']          = input("post.hot");
            $arr['is_new']          = input("post.news");
            $imghead = request()->file('imghead');
            if($imghead){
                $img    = $imghead->move(ROOT_PATH . 'public' . DS . 'desimg');
                $imgurl = str_replace("\\","/",$img->getSaveName());
                $imgurl = "./public/desimg/".$imgurl;
                $image  = \think\Image::open($imgurl);
                $image->thumb(218, 218)->save($imgurl);
                $arr['sm_img'] = "/public/desimg/".str_replace("\\","/",$img->getSaveName());
            }

            $image = request()->file('image');
            if($image){
                $image = $image->move(ROOT_PATH . 'public' . DS . 'desimg');
                $imageurl = str_replace("\\","/",$image->getSaveName());
                $arr['img'] = "/public/desimg/".$imageurl;
            }

            $eqcode = request()->file('eqcode');
            if($eqcode){
                $eqcode = $eqcode->move(ROOT_PATH . 'public' . DS . 'desimg');
                $eqcodeurl = str_replace("\\","/",$eqcode->getSaveName());
                $arr['wx_img'] = "/public/desimg/".$eqcodeurl;
            }

            $designer = DB::name("designer")->update($arr);
            if($designer){
                echo alert_success("修改成功",url('Designer/index'));exit;
            }else{
                echo alert_error("修改失败！");exit;
            }
        }

        $id = trim(input("id"));
        if(empty($id)){
            echo alert_error("参数错误！");exit;
        }
        $des = DB::name("designer")->where(array("id"=>$id))->find();
        $cat = DB::name('designer_cat')->field("cat_id,cat_name")->where(array("parent_id"=>0))->select();
        $num=0;
        foreach($cat as $key=>$value){
            $cat[$num]['nextcat'] = DB::name('designer_cat')->field("cat_id,cat_name")->where(array("parent_id"=>$value['cat_id']))->select();
            $num++;
        }
        $data = array(
            'cat'=>$cat,
            'des'=>$des
        );
        $this->assign($data);
        return $this->fetch();
    }
    //删除设计师
    public function deldes(){
        $id=trim(input('post.id'));
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $designer=DB::name('designer')->where(array('id'=>$id))->delete();
        if($designer){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }
    //设计师状态
    public function states(){
        $id=trim(input('post.id'));
        $type=trim(input('post.type'));
        if(empty($id)||empty($type)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $article = DB::name('designer')->field("is_hot,is_new")->where(array('id'=>$id))->find();
        if($article[$type]==1){
            $save[$type] =2;
        }else{
            $save[$type] =1;
        }
        $isarticle=DB::name('designer')->where(array('id'=>$id))->update($save);
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
    //设计师分类
    public function destypes(){
        if($_POST){
            $data = input("post.");
            foreach($data as $k=>$V){
                $save['sort_order'] = $V;
                DB::name('designer_cat')->where(array('cat_id'=>$k))->update($save);
            }
            $this->redirect('Designer/destypes');
        }
        $article_cat = DB::name('designer_cat')->field('cat_id,cat_name,sort_order')->where(array('parent_id'=>0))->order("sort_order asc")->select();
        $num=0;
        foreach($article_cat as $ks=>$vs){
            $article_cat[$num]['cat'] = DB::name('designer_cat')->field('cat_id,cat_name,sort_order')->where(array('parent_id'=>$vs['cat_id']))->order("sort_order asc")->select();
            $num++;
        }
        $data = array(
            'article_cat'=>$article_cat,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //添加设计师分类
    public function adddestype(){
        $data=input('post.');
        if($data['cat_name']==''){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $result=DB::name('designer_cat')->insertGetId($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"添加成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"添加失败");
            jsons($arr);
        }
    }
    //修改设计师分类
    public function modifydestype(){
        $data=input('post.');
        if($data['cat_name']==''){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $result=DB::name('designer_cat')->update($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"修改成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"修改失败");
            jsons($arr);
        }
    }
    //删除设计师分类
    public function deldestype(){
        $id=input('post.id');
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $article_cat = DB::name('designer_cat')->field('cat_id')->where(array('parent_id'=>$id))->find();
        if($article_cat){
            $arr = array("code"=>"1","msg"=>"请先删除子权限");
            jsons($arr);
        }
        $map['cat_id']=$id;
        $result=DB::name('designer_cat')->delete($map);
        if($result){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }
    //装修案例列表
    public function caselist()
    {
        $id = input('catid');
        $pageid = input('page');
        $starttime = input('starttime');
        $endtime   = input('endtime');
        $keyword   = input('keyword');
        if($starttime){
            $where1['jiuju_case.add_time'] = ['>=',$starttime];
            $where2['jiuju_case.add_time'] = ['<=',$endtime];
        }else{
            $where1 = "";
            $where2 = "";
        }
        if($keyword){
            $where['case_name'] = array('like', '%' . $keyword . '%');
        }else{
            $where = "";
        }
        $case = Db::name('case')->field("case_id,designer_name,case_name,jiuju_case.sort,is_show,jiuju_case.is_hot,jiuju_case.is_new,jiuju_case.style_id,apart_id,measure_id,jiuju_case.add_time,area")
            ->where($where1)
            ->where($where2)
            ->where($where)
            ->join("jiuju_designer","jiuju_case.designer_id = jiuju_designer.id","LEFT")->order('sort asc,add_time desc')->paginate(10);

        $cat = DB::name('case_cat')->field("cat_id,cat_name")->select();
        if($starttime){
            $starttime = date("Y-m-d H:i:s",$starttime);
            $endtime = date("Y-m-d H:i:s",$endtime);
        }else{
            $starttime = '';
            $endtime = '';
        }
        $data = array(
            'case'      =>$case,
            'cat'       =>$cat,
            'starttime' =>$starttime,
            'endtime'   =>$endtime,
            'keyword'   =>$keyword,
            'pageid'    =>$pageid,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //新增装修案例
    public function addcase(){
        if($_POST){
            $arr['case_name']   = trim(input('post.name'));
            $arr['designer_id'] = trim(input('post.desid'));
            $arr['style_id']    = trim(input('post.cat_name9'));
            $arr['apart_id']    = trim(input('post.cat_name17'));
            $arr['measure_id']  = trim(input('post.cat_name23'));
            $arr['area']        = trim(input('post.area'));
            $arr['sort']        = trim(input('post.sort'));
            $arr['introduce']   = trim(input('post.intro'));
            $arr['is_show']     = trim(input('post.show'));
            $arr['is_hot']      = trim(input('post.hot'));
            $arr['is_new']      = trim(input('post.news'));
            $arr['add_time']    = time();
            $casetext = $_POST['casetext'];
            $caseid = DB::name("case")->insertGetId($arr);
            if($caseid){
                // 获取表单上传文件
                $files = request()->file('caseimg');
                $num=0;
                foreach($files as $file){
                    $arr_img['caseid'] = $caseid;
                    // 移动到框架应用根目录/public/caseimg/ 目录下
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'caseimg');
                    $imgurl = str_replace("\\","/",$info->getSaveName());
                    $arr_img['image_url'] = "/public/caseimg/".$imgurl;
                    $arr_img['img_info'] = $casetext[$num];
                    $case_img = DB::name("case_images")->insert($arr_img);
                    $num++;
                }
            }
            if($case_img){
                echo alert_success("新增成功",url('Designer/caselist'));exit;
            }else{
                echo alert_error("新增失败！");exit;
            }

        }
        $des = DB::name('designer')->field("id,designer_name")->select();
        $cat = DB::name('case_cat')->field("cat_id,cat_name")->where(array("parent_id"=>0))->select();
        $num=0;
        foreach($cat as $key=>$value){
            $cat[$num]['nextcat'] = DB::name('case_cat')->field("cat_id,cat_name")->where(array("parent_id"=>$value['cat_id']))->select();
            $num++;
        }
        $data = array(
            'cat'=>$cat,
            'des'=>$des
        );
        $this->assign($data);
        return $this->fetch();
    }
    //修改装修案例
    public function modifycase(){
        if($_POST){
            $arr['case_id']     = trim(input('post.case_id'));
            $arr['case_name']   = trim(input('post.name'));
            $arr['designer_id'] = trim(input('post.desid'));
            $arr['style_id']    = trim(input('post.cat_name9'));
            $arr['apart_id']    = trim(input('post.cat_name17'));
            $arr['measure_id']  = trim(input('post.cat_name23'));
            $arr['area']        = trim(input('post.area'));
            $arr['sort']        = trim(input('post.sort'));
            $arr['introduce']   = trim(input('post.intro'));
            $arr['is_show']     = trim(input('post.show'));
            $arr['is_hot']      = trim(input('post.hot'));
            $arr['is_new']      = trim(input('post.news'));

            $img_id = $_POST['img_id'];
            $delimgid           = trim(input('post.delimgid'));
            if($delimgid!=''){
                $where['img_id'] = array("in",$delimgid);
                $caseimgurl = DB::name("case_images")->where($where)->select();
                foreach ($caseimgurl as $k=>$v){
                    $imgurl = ".".$v['image_url'];
                    @unlink($imgurl);
                }
                $caseimgdel = DB::name("case_images")->where($where)->delete();
            }
            $n=0;
            foreach($img_id as $key=>$value){
                $saveimg = request()->file('caseimg_save'.$value);
                if($saveimg){
                    $infocase = $saveimg->move(ROOT_PATH . 'public' . DS . 'caseimg');
                    $caseimgurl = str_replace("\\","/",$infocase->getSaveName());
                    $arr_img['image_url'] = "/public/caseimg/".$caseimgurl;
                }
                $save['img_info'] = $_POST['casetext_save'.$value];
                $savecimg = DB::name("case_images")->where(array("img_id"=>$value))->update($save);
                $n++;
            }
            $files = request()->file('caseimg');
            if($files){
                $casetext = $_POST['casetext'];
                $num=0;
                foreach($files as $file){
                    $arr_img['caseid'] = $arr['case_id'];
                    // 移动到框架应用根目录/public/caseimg/ 目录下
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'caseimg');
                    $imgurl = str_replace("\\","/",$info->getSaveName());
                    $arr_img['image_url'] = "/public/caseimg/".$imgurl;
                    $arr_img['img_info']  = $casetext[$num];
                    $case_img = DB::name("case_images")->insert($arr_img);
                    $num++;
                }
            }
            $caseid = DB::name("case")->update($arr);
            if($caseimgdel||$savecimg||$case_img||$caseid){
                echo alert_success("修改成功",url('Designer/caselist'));exit;
            }else{
                echo alert_error("修改失败！");exit;
            }
        }
        $id = input("id");
        if(empty($id)){
            echo alert_error("参数错误！");exit;
        }
        $case = DB::name("case")->where(array("case_id"=>$id))->find();
        $caseimg = DB::name("case_images")->where(array("caseid"=>$id))->order("img_id asc")->select();
        $des = DB::name('designer')->field("id,designer_name")->select();
        $cat = DB::name('case_cat')->field("cat_id,cat_name")->where(array("parent_id"=>0))->select();
        $num=0;
        foreach($cat as $key=>$value){
            $cat[$num]['nextcat'] = DB::name('case_cat')->field("cat_id,cat_name")->where(array("parent_id"=>$value['cat_id']))->select();
            $num++;
        }
        $data = array(
            'cat'=>$cat,
            'des'=>$des,
            'case'=>$case,
            'caseimg'=>$caseimg,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //案例状态
    public function casestates(){
        $id=trim(input('post.id'));
        $type=trim(input('post.type'));
        if(empty($id)||empty($type)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $article = DB::name('case')->field("is_show,is_hot,is_new")->where(array('case_id'=>$id))->find();
        if($article[$type]==1){
            $save[$type] =2;
        }else{
            $save[$type] =1;
        }
        $isarticle=DB::name('case')->where(array('case_id'=>$id))->update($save);
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
    //删除案例
    public function delcase(){
        $id=trim(input('post.id'));
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $caseimg = DB::name("case_images")->where(array("caseid"=>$id))->select();
        foreach ($caseimg as $k=>$v){
            $imgurl = ".".$v['image_url'];
           @unlink($imgurl);
        }
        $designer=DB::name('case')->where(array('case_id'=>$id))->delete();
        DB::name("case_images")->where(array("caseid"=>$id))->delete();
        if($designer){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }
    //案例分类
    public function casetype(){
        if($_POST){
            $data = input("post.");
            foreach($data as $k=>$V){
                $save['sort_order'] = $V;
                DB::name('case_cat')->where(array('cat_id'=>$k))->update($save);
            }
            $this->redirect('Designer/casetype');
        }
        $article_cat = DB::name('case_cat')->field('cat_id,cat_name,sort_order')->where(array('parent_id'=>0))->order("sort_order asc")->select();
        $num=0;
        foreach($article_cat as $ks=>$vs){
            $article_cat[$num]['cat'] = DB::name('case_cat')->field('cat_id,cat_name,sort_order')->where(array('parent_id'=>$vs['cat_id']))->order("sort_order asc")->select();
            $num++;
        }
        $data = array(
            'article_cat'=>$article_cat,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //添加案例分类
    public function addcasetype(){
        $data=input('post.');
        if($data['cat_name']==''){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $result=DB::name('case_cat')->insertGetId($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"添加成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"添加失败");
            jsons($arr);
        }
    }
    //修改案例分类
    public function modifycasetype(){
        $data=input('post.');
        if($data['cat_name']==''){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $result=DB::name('case_cat')->update($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"修改成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"修改失败");
            jsons($arr);
        }
    }
    //删除案例分类
    public function delcasetype(){
        $id=input('post.id');
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $article_cat = DB::name('case_cat')->field('cat_id')->where(array('parent_id'=>$id))->find();
        if($article_cat){
            $arr = array("code"=>"1","msg"=>"请先删除子权限");
            jsons($arr);
        }
        $map['cat_id']=$id;
        $result=DB::name('case_cat')->delete($map);
        if($result){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }
}
