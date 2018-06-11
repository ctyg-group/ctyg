<?php

namespace app\mobile\controller;

use think\Controller;
use think\Db;
use think\Page;
class Worklive extends Base {

    public function index(){
        $stage_id = input('stage_id',0); //年限
        $hot_id = input('hot_id',0);
        $apart_id = input('apart_id',0);

        $this->assign("stage_id",$stage_id);
        $this->assign("hot_id",$hot_id);
        $this->assign("apart_id",$apart_id);


        $p = input('p/d', 1);
        $size = 7;

        $workDB = DB::name("worklive");
        $imgDB = DB::name("worklive_images");
        $catDB = DB::name('worklive_cat');
        $stage = $catDB->where("parent_id = 1")->field("cat_id,cat_name")->select();
        $hot = $catDB->where("parent_id = 10")->field("cat_id,cat_name")->select();
        $apart = $catDB->where("parent_id = 21")->field("cat_id,cat_name")->select();


        $this->assign('cat1',$stage);
        $this->assign('cat2',$hot);
        $this->assign('cat3',$apart);  //获取分类信息

        $where = '';
        $where.=($stage_id!=0) ? "stage_id = $stage_id ":'';
        if($hot_id!=0 && $stage_id!=0){
            $where.= 'and ';
        }
        $where.=($hot_id!=0)?"hot_id = $hot_id ":'';
        if(($stage_id!=0 && $apart_id!=0) or ($hot_id!=0 && $apart_id!=0)){
            $where.= 'and ';
        }
        $where.=($apart_id!=0)?"apart_id = $apart_id":'';  //筛选条件


        $count =$workDB->where($where)->count();
        $list = $workDB->field("id,live_name,stage_id,apart,area,style,thumb")->where($where)->page("$p,$size")->select();

        if($list){
            foreach ($list as $k=>$v){
                $stage = $catDB->field('cat_name')->where("cat_id = $v[stage_id]")->find();
                $list[$k]['stage'] = $stage['cat_name'];
            }
        }


        $this->calcul_page($count,$size);//分页显示输出
        $this->assign('list',$list);
        $this->assign('p',$p);

        $this->assign("title","看工地学装修_装修施工-久居整装");
        $this->assign("keywords","看工地学装修,装修施工");
        $this->assign("description","久居整装工地直播栏目，让您了解更多装修施工知识，通过这个栏目看工地学装修，真正了解装修施工每一道工艺。");


        return $this->fetch();
    }



    public function workinfo(){
        $id = input('id');

        $info = DB::name("worklive")->where("id = $id")->find();
        $stage = DB::name("worklive_stage")->where("worklive_id = $id")->order("level asc")->select();
        $images = DB::name("worklive_images")->field('image_url')->where("worklive_id = $id")->order("id desc")->select();



        $designer = DB::name("designer")->field('designer_name,style_id')->where("id = ".$info['designer_id'])->find();
        $des_style = DB::name('designer_cat')->field("cat_name")->where("cat_id = ".$designer['style_id'])->find();
        $des_info['id'] = $info['designer_id'];
        $des_info['name'] = $designer['designer_name'];
        $des_info['image'] = $designer['img'];
        $des_info['style'] = $des_style['cat_name'];
        $worker1 = DB::name("worker")->field("worker_name,image")->where("id = $info[worker1_id] and type = 1")->find();
        $worker2 = DB::name("worker")->field("worker_name,image")->where("id = $info[worker2_id] and type = 2")->find();
        if(!$worker1){
            $worker1['worker_name'] = $info['workname'];
            $worker1['image'] = '/public/static/images/work_man.png';
        }

        $stage_name = array('开工大吉','拆改阶段','水电阶段','瓦泥阶段','木工阶段','油漆阶段','竣工阶段');
        $last_num = -1; $list = array();
        foreach ($stage as $k=>$v){
            $images = DB::name("worklive_images")->field('image_url')->where(array('stage_id'=>$v['id']))->order("id desc")->select();

            $v['images'] = $images;
            $v['name'] = $stage_name[$v['level']];
            $list[] = $v;
//            $last_num += $v['stage_num'];
        }

        $last = end($list);
        $this->assign('list',$list);
        $this->assign('info',$info);
        $this->assign('level',$last['level']*1+1);
        $this->assign('des_info',$des_info);
        $this->assign('worker1',$worker1);
        $this->assign('worker2',$worker2);

        $this->assign("title","看工地学装修_装修施工-久居整装");
        $this->assign("keywords","看工地学装修,装修施工");
        $this->assign("description","久居整装工地直播栏目，让您了解更多装修施工知识，通过这个栏目看工地学装修，真正了解装修施工每一道工艺。");

        return $this->fetch();
    }

}