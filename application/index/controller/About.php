<?php
/**
 * 久居
 * ============================================================================
 * 本系统由久居整装开发
 * ============================================================================
 * Author: Suki
 * Date: 2018-03-09
 */
namespace app\index\controller;
use think\Db;
class About extends Common
{

    public function index()
    {
        $datas = array(
            "web_title"=>"久居整装装修网-久居整装",
            "keywords"=>"久居整装,久居整装装修网",
            "description"=>"久居整装是一家致力成为o2o一体化运营的泛家居综合服务商，信任装修找久居！",
        );
        $this->assign($datas);
        return $this->fetch();
    }
    public function callme()
    {
        $datas = array(
            "web_title"=>"久居整装装修网-久居整装",
            "keywords"=>"久居整装,久居整装装修网",
            "description"=>"久居整装是一家致力成为o2o一体化运营的泛家居综合服务商，信任装修找久居！",
        );
        $this->assign($datas);
        return $this->fetch();
    }
    public function joinme()
    {
        $job = DB::name("job")->order("id desc")->select();
        $data = array(
            "job"=>$job,
        );
        $this->assign($data);
        $datas = array(
            "web_title"=>"久居整装装修网-久居整装",
            "keywords"=>"久居整装,久居整装装修网",
            "description"=>"久居整装是一家致力成为o2o一体化运营的泛家居综合服务商，信任装修找久居！",
        );
        $this->assign($datas);
        return $this->fetch();
    }

}
