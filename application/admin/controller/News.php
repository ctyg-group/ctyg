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
use think\Page;
class News extends Base
{
    //    文章列表
    public function index()
    {
        $id = input('catid');
        $pageid = input('page');
        $starttime = input('starttime');
        $endtime   = input('endtime');
        $keyword   = input('keyword');
        $author    = input('author');
        if($starttime){
            $where1['add_time'] = ['>=',$starttime];
            $where2['add_time'] = ['<=',$endtime];
        }else{
            $where1 = "";
            $where2 = "";
        }
        if($keyword){
            $where['title'] = array('like', '%' . $keyword . '%');
        }
		if($author){
            $where3['author'] = array('like','%' . $author . '%');
        }
        if($id){
            $where['cat_id_2'] =  $id;
        }else{
            $where['cat_id_2'] = array("gt",0);
        }

        $article = Db::name('article')->field('article_id,cat_id,cat_id_2,title,author,thumb,is_open,is_hot,is_new,is_home,add_time')
            ->where($where1)
            ->where($where2)
            ->where($where)
          	->where($where3)
        ->order('add_time desc')->paginate(10);
        $article_cat = DB::name('article_cat')->field('cat_id,cat_name')->where(array('parent_id'=>0))->order("sort_order asc")->select();
        $num=0;
        foreach($article_cat as $k=>$v){
            $article_cat[$num]['cat'] = DB::name('article_cat')->field('cat_id,cat_name')->where(array('parent_id'=>$v['cat_id']))->order("sort_order asc")->select();
            $num++;
        }
        if($starttime){
            $starttime = date("Y-m-d H:i:s",$starttime);
            $endtime = date("Y-m-d H:i:s",$endtime);
        }else{
            $starttime = '';
            $endtime = '';
        }
        $data = array(
            'article_cat'=>$article_cat,
            'article'=>$article,
            'catid'=>$id,
            'starttime'=>$starttime,
            'endtime'=>$endtime,
            'keyword'=>$keyword,
            'pageid'=>$pageid,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //    发布文章
    public function addnews(){
        if($_POST){
            $add['title']       = trim(input("post.title"));
            $add['cat_id']      = trim(input("post.onetype"));
            $add['cat_id_2']    = trim(input("post.twotype"));
            $add['keywords']    = trim(input("post.seo"));
            $add['author']      = trim(input("post.author"));
            $add['publish_time']= time();
            $add['is_open']     = trim(input("post.state"));
            $add['explain']     = trim(input("post.explain"));
            $add['is_hot']      = trim(input("post.hot"));
            $add['is_new']      = trim(input("post.news"));
            $add['is_home']     = trim(input("post.home"));
            $add['content']     = htmlspecialchars_decode(input("post.content"));
            $add['click']       = mt_rand(1000,1300);
            $add['add_time']    = time();
            $add['radio_num']   = rand(111,999);
            $add['comment']     = rand(111,999);
            $file = request()->file('image');
            if($file){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'newsimg');
                if($info){

                    if($add['cat_id']==39){
                        $imgurl = str_replace("\\","/",$info->getSaveName());
                        $urlimg = "/public/newsimg/".$imgurl;
                        $imgurl = "./public/newsimg/".$imgurl;
                        $image = \think\Image::open($imgurl);
                        $image->thumb(440, 200)->save($imgurl);
                    }else{
                        $imgurl = str_replace("\\","/",$info->getSaveName());
                        $urlimg = "/public/newsimg/".$imgurl;
                        $imgurl = "./public/newsimg/".$imgurl;
                        $image = \think\Image::open($imgurl);
                        $image->thumb(808, 566)->save($imgurl);
                    }
                }else{
                    // 上传失败获取错误信息
                    echo alert_error($file->getError());exit;
                }
            }
            $add['thumb'] = $urlimg;
            $article = DB::name("article")->insertGetId($add);
            if($article){
                echo alert_success("发布成功",url('News/index',array('catid'=>$add['cat_id_2'])));exit;
            }else{
                echo alert_error("发布失败！");exit;
            }
        }
        $id = input('catid');
        $article_cat = DB::name('article_cat')->field('cat_id,cat_name')->where(array('parent_id'=>0))->order("sort_order asc")->select();
        $num=0;
        $article_one = "";
        $article_two = array();
        foreach($article_cat as $k=>$v){
            $article_cat[$num]['cat'] = DB::name('article_cat')->field('cat_id,cat_name,parent_id')->where(array('parent_id'=>$v['cat_id']))->order("sort_order asc")->select();
            if($id){
                foreach($article_cat[$num]['cat'] as $key=>$value){
                    if($value['cat_id']==$id){
                        $article_one = $value['parent_id'];
                        $article_two = $article_cat[$num]['cat'];
                    }
                }
            }
            $num++;
        }
        $data = array(
            'article_cat'=>$article_cat,
            'catid'=>$id,
            'article_one'=>$article_one,
            'article_two'=>$article_two,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //修改文章
    public function modifynews(){
        if($_POST){
            $pageid = input("pageid");
            $article_id         = trim(input("post.article_id"));
            $save['title']       = trim(input("post.title"));
            $save['cat_id']      = trim(input("post.onetype"));
            $save['cat_id_2']    = trim(input("post.twotype"));
            $save['keywords']    = trim(input("post.seo"));
            $save['author']      = trim(input("post.author"));
            $save['is_open']     = trim(input("post.state"));
            $save['explain']     = trim(input("post.explain"));
            $save['is_hot']      = trim(input("post.hot"));
            $save['is_new']      = trim(input("post.news"));
            $save['is_home']     = trim(input("post.home"));
            $save['content']     = htmlspecialchars_decode(input("post.content"));
            $file = request()->file('image');
            if($file){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'newsimg');
                if($info){
                    if($save['cat_id']==39){
                        $imgurl = str_replace("\\","/",$info->getSaveName());
                        $urlimg = "/public/newsimg/".$imgurl;
                        $imgurl = "./public/newsimg/".$imgurl;
                        $image = \think\Image::open($imgurl);
                        $image->thumb(440, 200)->save($imgurl);
                    }else{
                        $imgurl = str_replace("\\","/",$info->getSaveName());
                        $urlimg = "/public/newsimg/".$imgurl;
                        $imgurl = "./public/newsimg/".$imgurl;
                        $image = \think\Image::open($imgurl);
                        $image->thumb(404, 283)->save($imgurl);
                    }
                    $save['thumb'] = $urlimg;
                }
            }
            $article = DB::name("article")->where(array("article_id"=>$article_id))->update($save);
            if($article>0){
                echo alert_success("修改成功",url('News/index',array('catid'=>$save['cat_id_2'])));exit;
            }else{
                echo alert_error("修改失败！");exit;
            }
        }
        $newsid = input('newsid');
        if(empty($newsid)){
            echo alert_error("参数错误！");exit;
        }
        $article = DB::name('article')->where(array("article_id"=>$newsid))->find();
        $article_cat = DB::name('article_cat')->field('cat_id,cat_name')->where(array('parent_id'=>0))->order("sort_order asc")->select();
        $num=0;
        $article_two = array();
        foreach($article_cat as $k=>$v){
            $article_cat[$num]['cat'] = DB::name('article_cat')->field('cat_id,cat_name,parent_id')->where(array('parent_id'=>$v['cat_id']))->order("sort_order asc")->select();
            if($article['cat_id_2']){
                foreach($article_cat[$num]['cat'] as $key=>$value){
                    if($value['cat_id']==$article['cat_id_2']){
                        $article_two = $article_cat[$num]['cat'];
                    }
                }
            }
            $num++;
        }
        $data = array(
            'article'    =>$article,
            'article_cat'=>$article_cat,
            'catid'      =>$article['cat_id_2'],
            'article_two'=>$article_two,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //    文章状态
    public function newsshow(){
        $id=trim(input('post.id'));
        $type=trim(input('post.type'));
        if(empty($id)||empty($type)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $article = DB::name('article')->field("is_open,is_hot,is_new,is_home")->where(array('article_id'=>$id))->find();
        if($article[$type]==1){
            $save[$type] =2;
        }else{
            $save[$type] =1;
        }
        $isarticle=DB::name('article')->where(array('article_id'=>$id))->update($save);
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
    //    删除文章
    public function delnews(){
        $id=trim(input('post.id'));
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $article=DB::name('article')->where(array('article_id'=>$id))->delete();
        if($article){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }
    //    文章分类
    public function newstype(){
        if($_POST){
            $data = input("post.");
            foreach($data as $k=>$V){
                $save['sort_order'] = $V;
                DB::name('article_cat')->where(array('cat_id'=>$k))->update($save);
            }
            $this->redirect('News/newstype');

        }

        $article_cat = DB::name('article_cat')->field('cat_id,cat_name,sort_order')->where(array('parent_id'=>0))->order("sort_order asc")->select();
        $num=0;
        foreach($article_cat as $ks=>$vs){
            $article_cat[$num]['cat'] = DB::name('article_cat')->field('cat_id,cat_name,sort_order')->where(array('parent_id'=>$vs['cat_id']))->order("sort_order asc")->select();

            $num++;
        }

        $data = array(
            'article_cat'=>$article_cat,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //添加文章分类
    public function addnewstype(){
        $data=input('post.');
        if($data['cat_name']==''){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $result=DB::name('article_cat')->insertGetId($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"添加成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"添加失败");
            jsons($arr);
        }
    }
    //修改文章分类
    public function modifynewstype(){
        $data=input('post.');
        if($data['cat_name']==''){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $result=DB::name('article_cat')->update($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"修改成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"修改失败");
            jsons($arr);
        }
    }
    //删除文章分类
    public function delnewstype(){
        $id=input('post.id');
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $article_cat = DB::name('article_cat')->field('cat_id')->where(array('parent_id'=>$id))->find();
        if($article_cat){
            $arr = array("code"=>"1","msg"=>"请先删除子权限");
            jsons($arr);
        }
        $map['cat_id']=$id;
        $result=DB::name('article_cat')->delete($map);
        if($result){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }
    //百科列表
    public function baike(){
        $id = input('catid');
        $pageid = input('page');
        $starttime = input('starttime');
        $endtime   = input('endtime');
        $keyword   = input('keyword');
      	$author    = input('author');
        if($starttime){
            $where1['add_time'] = ['>=',$starttime];
            $where2['add_time'] = ['<=',$endtime];
        }else{
            $where1 = "";
            $where2 = "";
        }
        if($keyword){
            $where['title'] = array('like', '%' . $keyword . '%');
        }
		if($author){
            $where3['author'] = array('like','%' . $author . '%');
        }
        if($id){
            $where['cat_id_2'] =  $id;
        }else{
            $where['cat_id_2'] = array("gt",0);
        }

        $article = Db::name('enped')->field('enped_id,cat_id,cat_id_2,title,author,thumb,is_open,is_hot,is_new,is_home,add_time')
            ->where($where1)
            ->where($where2)
            ->where($where)
          	->where($where3)
            ->order('add_time desc')->paginate(10);
        $article_cat = DB::name('enped_cat')->field('cat_id,cat_name')->where(array('parent_id'=>0))->order("sort_order asc")->select();
        $num=0;
        foreach($article_cat as $k=>$v){
            $article_cat[$num]['cat'] = DB::name('enped_cat')->field('cat_id,cat_name')->where(array('parent_id'=>$v['cat_id']))->order("sort_order asc")->select();
            $num++;
        }
        if($starttime){
            $starttime = date("Y-m-d H:i:s",$starttime);
            $endtime = date("Y-m-d H:i:s",$endtime);
        }else{
            $starttime = '';
            $endtime = '';
        }
        $data = array(
            'article_cat'=>$article_cat,
            'article'=>$article,
            'catid'=>$id,
            'starttime'=>$starttime,
            'endtime'=>$endtime,
            'keyword'=>$keyword,
            'pageid'=>$pageid,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //    新增百科
    public function addbaike(){
        if($_POST){
            $add['title']       = trim(input("post.title"));
            $add['cat_id']      = trim(input("post.onetype"));
            $add['cat_id_2']    = trim(input("post.twotype"));
            $add['keywords']    = trim(input("post.seo"));
            $add['author']      = trim(input("post.author"));
            $add['publish_time']= time();
            $add['is_open']     = trim(input("post.state"));
            $add['explain']     = trim(input("post.explain"));
            $add['is_hot']      = trim(input("post.hot"));
            $add['is_new']      = trim(input("post.news"));
            $add['content']     = htmlspecialchars_decode(input("post.content"));
            $add['click']       = mt_rand(1000,1300);
            $add['add_time']    = time();
            $add['radio_num']   = rand(111,999);
            $add['comment']     = rand(111,999);
            $file = request()->file('image');
            if($file){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'newsimg');
                if($info){

                    $imgurl = str_replace("\\","/",$info->getSaveName());
                    $urlimg = "/public/newsimg/".$imgurl;
                    $imgurl = "./public/newsimg/".$imgurl;
                    $image = \think\Image::open($imgurl);
                    $image->thumb(425, 295)->save($imgurl);
                }else{
                    // 上传失败获取错误信息
                    echo alert_error($file->getError());exit;
                }
            }
            $add['thumb'] = $urlimg;
            $article = DB::name("enped")->insertGetId($add);
            if($article){
                echo alert_success("发布成功",url('News/baike',array('catid'=>$add['cat_id_2'])));exit;
            }else{
                echo alert_error("发布失败！");exit;
            }
        }
        $id = input('catid');
        $article_cat = DB::name('enped_cat')->field('cat_id,cat_name')->where(array('parent_id'=>0))->order("sort_order asc")->select();
        $num=0;
        $article_one = "";
        $article_two = array();
        foreach($article_cat as $k=>$v){
            $article_cat[$num]['cat'] = DB::name('enped_cat')->field('cat_id,cat_name,parent_id')->where(array('parent_id'=>$v['cat_id']))->order("sort_order asc")->select();
            if($id){
                foreach($article_cat[$num]['cat'] as $key=>$value){
                    if($value['cat_id']==$id){
                        $article_one = $value['parent_id'];
                        $article_two = $article_cat[$num]['cat'];
                    }
                }
            }
            $num++;
        }
        $data = array(
            'article_cat'=>$article_cat,
            'catid'=>$id,
            'article_one'=>$article_one,
            'article_two'=>$article_two,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //修改百科
    public function modifybaike(){
        if($_POST){
            $pageid = input("pageid");
            $enped_id         = trim(input("post.enped_id"));
            $save['title']       = trim(input("post.title"));
            $save['cat_id']      = trim(input("post.onetype"));
            $save['cat_id_2']    = trim(input("post.twotype"));
            $save['keywords']    = trim(input("post.seo"));
            $save['author']      = trim(input("post.author"));
            $save['is_open']     = trim(input("post.state"));
            $save['explain']     = trim(input("post.explain"));
            $save['is_hot']      = trim(input("post.hot"));
            $save['is_new']      = trim(input("post.news"));
            $save['content']     = htmlspecialchars_decode(input("post.content"));
            $file = request()->file('image');
            if($file){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'newsimg');
                if($info){
                    $imgurl = str_replace("\\","/",$info->getSaveName());
                    $urlimg = "/public/newsimg/".$imgurl;
                    $imgurl = "./public/newsimg/".$imgurl;
                    $image = \think\Image::open($imgurl);
                    $image->thumb(425, 295)->save($imgurl);
                    $save['thumb'] = $urlimg;
                }
            }
            $article = DB::name("enped")->where(array("enped_id"=>$enped_id))->update($save);
            if($article>0){
                echo alert_success("修改成功",url('News/baike',array('catid'=>$save['cat_id_2'])));exit;
            }else{
                echo alert_error("修改失败！");exit;
            }
        }
        $enpedid = input('baikeid');
        if(empty($enpedid)){
            echo alert_error("参数错误！");exit;
        }
        $article = DB::name('enped')->where(array("enped_id"=>$enpedid))->find();
        $article_cat = DB::name('enped_cat')->field('cat_id,cat_name')->where(array('parent_id'=>0))->order("sort_order asc")->select();
        $num=0;
        $article_two = array();
        foreach($article_cat as $k=>$v){
            $article_cat[$num]['cat'] = DB::name('enped_cat')->field('cat_id,cat_name,parent_id')->where(array('parent_id'=>$v['cat_id']))->order("sort_order asc")->select();
            if($article['cat_id_2']){
                foreach($article_cat[$num]['cat'] as $key=>$value){
                    if($value['cat_id']==$article['cat_id_2']){
                        $article_two = $article_cat[$num]['cat'];
                    }
                }
            }
            $num++;
        }
        $data = array(
            'article'    =>$article,
            'article_cat'=>$article_cat,
            'catid'      =>$article['cat_id_2'],
            'article_two'=>$article_two,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //    百科状态
    public function baikeshow(){
        $id=trim(input('post.id'));
        $type=trim(input('post.type'));
        if(empty($id)||empty($type)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $article = DB::name('enped')->field("is_open,is_hot,is_new")->where(array('enped_id'=>$id))->find();
        if($article[$type]==1){
            $save[$type] =2;
        }else{
            $save[$type] =1;
        }
        $isarticle=DB::name('enped')->where(array('enped_id'=>$id))->update($save);
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
    //    删除百科
    public function delbaike(){
        $id=trim(input('post.id'));
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数错误");
            jsons($arr);
        }
        $article=DB::name('enped')->where(array('enped_id'=>$id))->delete();
        if($article){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }
    //    百科类型
    public function baiketype(){
        if($_POST){
            $data = input("post.");
            foreach($data as $k=>$V){
                $save['sort_order'] = $V;
                DB::name('enped_cat')->where(array('cat_id'=>$k))->update($save);
            }
            $this->redirect('News/baiketype');

        }

        $article_cat = DB::name('enped_cat')->field('cat_id,cat_name,sort_order')->where(array('parent_id'=>0))->order("sort_order asc")->select();
        $num=0;
        foreach($article_cat as $ks=>$vs){
            $article_cat[$num]['cat'] = DB::name('enped_cat')->field('cat_id,cat_name,sort_order')->where(array('parent_id'=>$vs['cat_id']))->order("sort_order asc")->select();

            $num++;
        }

        $data = array(
            'article_cat'=>$article_cat,
        );
        $this->assign($data);
        return $this->fetch();
    }
    //添加百科分类
    public function addbaiketype(){
        $data=input('post.');
        if($data['cat_name']==''){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $result=DB::name('enped_cat')->insertGetId($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"添加成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"添加失败");
            jsons($arr);
        }
    }
    //修改百科分类
    public function modifybaiketype(){
        $data=input('post.');
        if($data['cat_name']==''){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $result=DB::name('enped_cat')->update($data);
        if ($result) {
            $arr = array("code"=>"0","msg"=>"修改成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"修改失败");
            jsons($arr);
        }
    }
    //删除百科分类
    public function delbaiketype(){
        $id=input('post.id');
        if(empty($id)){
            $arr = array("code"=>"1","msg"=>"参数不能为空");
            jsons($arr);
        }
        $article_cat = DB::name('enped_cat')->field('cat_id')->where(array('parent_id'=>$id))->find();
        if($article_cat){
            $arr = array("code"=>"1","msg"=>"请先删除子权限");
            jsons($arr);
        }
        $map['cat_id']=$id;
        $result=DB::name('enped_cat')->delete($map);
        if($result){
            $arr = array("code"=>"0","msg"=>"删除成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"删除失败");
            jsons($arr);
        }
    }
}
