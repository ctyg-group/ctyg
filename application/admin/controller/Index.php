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
class Index extends Base
{
    public function index()
    {
        $ismenu = DB::name('shortcut_menu')->where(array('adid'=>session::get("admin_id")))->select();
        $data = array(
            'ismenu'=>$ismenu,
        );
        $this->assign($data);
        return $this->fetch();
    }

}
