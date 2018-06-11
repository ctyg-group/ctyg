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
class Activity extends Common
{
    public function kaiye(){
        return $this->fetch();
    }
    public function huodong(){
        $time = date("Y-m-d",time());
        $now = substr($time,-1,1);
        $this->assign('now',$now);
        return $this->fetch();
    }
    public function map(){
        return $this->fetch();
    }
    public function hd11(){
        $time = date("Y-m-d",time());
        $now = substr($time,-2,2);
        $end = 26-$now*1;

        $this->assign('end',$end);
        return $this->fetch();
    }
    public function hd12(){
        $time = time();
        $start = strtotime("2018-01-22");
        $cha = ($start*1-$time*1)/(60*60);
        $tian = round($cha)/24;
        $shi = $cha-floor($tian)*24;
        if(round($tian)<=9){
            $tian = '0'.round($tian);
        }else{
            $tian = round($tian);
        }
        if(round($shi)<=9){
            $shi = '0'.round($shi);
        }else{
            $shi = round($shi);
        }



        $this->assign('tian',$tian);
        $this->assign('shi',$shi);
        $this->assign('con_name','no');
        return $this->fetch();
    }
    public function hd201803(){

        $m = strtotime("2018-03-31 23:59:59")-time();
        if($m>=0){
            $d =  $m/86400;
            $d1 = $m-86400*(int)$d;
            $h =  $d1/3600;
            $h1 = $d1-3600*(int)$h;
            $i = $h1/60;
            $s = $h1-60*(int)$i;
        }else{
            $d = 0;
            $h = 0;
            $i = 0;
            $s = 0;
        }

        $dd = (int)$d;
        if($dd<10){
            $dd = "0".$dd;
        }
        $hh = (int)$h;
        if($hh<10){
            $hh = "0".$hh;
        }
        $ii = (int)$i;
        if($ii<10){
            $ii = "0".$ii;
        }
        $ss = (int)$s;
        if($ss<10){
            $ss = "0".$ss;
        }

        $data = array(
            "d"=>$dd,
            "h"=>$hh,
            "i"=>$ii,
            "s"=>$ss,
        );

        $this->assign($data);
        return $this->fetch();
    }
    public function hd201804(){
        return $this->fetch();
    }
}
