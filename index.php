<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
//$the_host = $_SERVER['HTTP_HOST'];//取得进入所输入的域名
//$request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';//判断后面的请求部分
//if($the_host !== 'www.jiujusz.com')//现在的域名
//{
//    header('HTTP/1.1 301 Moved Permanently');//发出301头部
//    header('Location: http://www.jiujusz.com' . $request_uri);//跳转到我的新域名地址
//    exit();
//}
// [ 应用入口文件 ]

// 定义应用目录
//var_dump('00');exit();
//define('APP_PATH', __DIR__ . '/application/index');
// 加载框架引导文件
//require __DIR__ . '/thinkphp/start.php';
define('APP_PATH', __DIR__ . '/application/');
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
//header('HTTP/1.1 301 Moved Permanently');
//header('Location: http://www.jiujusz.com/');
//exit();