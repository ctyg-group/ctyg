<?php
namespace app\admin\model;
use think\Db;
use think\Loader;
use think\Model;
use modelapi\Data;
class Appointment extends BaseModel {

    /**
     * @return \think\Paginator
     * 获取所有报价预约数据
     */
    public function getListAll(){
        $info = DB::name("userinfo")->order("time desc")->paginate(10);
        return $info;
    }

    /**
     * @param $name
     *根据用户姓名搜索预约表
     */
    public function getListByUserName($mobile,$name){
        $where1['user_name'] = array('like', '%' . $name . '%');
        $where['mobile'] = array('like', '%' . $mobile . '%');
        $userList = Db::name('userinfo')->field('id,user_name,mobile,city,region,ip,time,is_meeting,price,area,remarks')
            ->where($where1)
            ->where($where)
            ->order('time desc')->paginate(10);
        return $userList;
    }

}

?>
