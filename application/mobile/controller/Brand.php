<?php
namespace app\mobile\controller;

use think\Controller;
use Think\Db;
class Brand extends Base {

    public function index(){
        return $this->fetch();
    }

}