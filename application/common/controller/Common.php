<?php
/**
 * 久居
 * ============================================================================
 * 本系统由久居整装开发
 * ============================================================================
 * Author: Suki
 * Date: 2018-03-09
 */
namespace app\common\controller;
use think\Controller;
use think\config;
use think\Db;
use think\Session;
class Common extends Controller
{
    protected function _initialize()
    {
        echo 1;exit;
        $configDB = DB::name("config")->field("group,name,value")->select();
        $data = array();
        foreach($configDB as $key=>$value){
            $data[$value['group']][$value['name']] = $value['value'];
        }
        Config::set($data);
    }
}