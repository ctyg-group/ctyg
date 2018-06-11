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
class Ajax extends Common
{
    public function user_info(){
        $username   = input('post.username');
        $mobile     = input('post.mobile');
        $city       = input('post.city');
        $region     = input('post.region');
        if(!preg_match("/[\x7f-\xff]/", $username)){
            $arr = array("code"=>"1","msg"=>"请输入正确的名字");
            jsons($arr);
        }
        $userDB = DB::name("userinfo");
        $time = time();
        $date_num = $userDB->where("time >= '($time*1-7*24*60*60)' and time <= '$time' and mobile = $time and is_meeting = 1")->count();
        if($date_num >=2){
            $return_arr = array(
                'status' => 0,
                'msg' => '您本周已预约两次啦！',
            );
            $this->ajaxReturn($return_arr);
        }
        $data['user_name']  = $username;
        $data['mobile']     = $mobile;
        $data['city']       = $city;
        $data['region']     = $region;
        $data['time']       = $time;
        $res = DB::name("userinfo")->insertGetId($data);
      if($res){
//            $this->sendAdmin('19928768132',$mobile);
            $arr = array("code"=>"0","msg"=>"预约成功");
//            $this->sendNotice($mobile);
            $send = new SmsDemo();

            if ($send->sendSms($mobile,'','SMS_134311008','')){          //预约短信发送给用户
                $send->sendSms('19928768132',$mobile,'SMS_122160067','');//预约信息发送给管理员
            }


//            $this->sendSms($mobile);
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"预约失败");
            jsons($arr);
        }
    }

    public function user_info_us(){
        $username   = input('post.username');
        $mobile     = input('post.mobile');
        $price       = input('post.price');
        if(!preg_match("/[\x7f-\xff]/", $username)){
            $arr = array("code"=>"1","msg"=>"请输入正确的名字");
            jsons($arr);
        }
        $userDB = DB::name("userinfo");
        $time = time();
        $date_num = $userDB->where("time >= '($time*1-7*24*60*60)' and time <= '$time' and mobile = $time and is_meeting = 1")->count();
        if($date_num >=2){
            $return_arr = array(
                'status' => 0,
                'msg' => '您本周已预约两次啦！',
            );
            $this->ajaxReturn($return_arr);
        }
        $data['user_name']  = $username;
        $data['mobile']     = $mobile;
        $data['time']      = $time;
        $res = DB::name("userinfo")->insertGetId($data);
       $send = new SmsDemo();
//        $send_res = $this->sendPrice($mobile,$price);
        $send_res = $send->sendSms($mobile,'','SMS_122160066',$price);
        if($res&&$send_res){
//            $this->sendAdmin('19928768132',$mobile);
            $send->sendSms('19928768132',$mobile,'SMS_122160067','');
            $arr = array("code"=>"0","msg"=>"预约成功");
            jsons($arr);
        }else{
            $arr = array("code"=>"1","msg"=>"预约失败");
            jsons($arr);
        }

    }


}
