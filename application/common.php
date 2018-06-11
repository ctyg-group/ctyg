<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
function object_array($array) {
    if(is_object($array)) {
        $array = (array)$array;
    } if(is_array($array)) {
        foreach($array as $key=>$value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}
//返回json数据
error_reporting(E_ALL^E_NOTICE);
function jsons($data)
{
    $sql = json_encode($data);
    $sql = str_replace(":null",":\"\"",$sql);
    $sql = str_replace(":false",":\"\"",$sql);
    $sql = str_replace(": false",":\"\"",$sql);
    echo $sql;exit;
}
//无限极分类
function get_attr($a,$pid)
{
    $tree = array();                                //每次都声明一个新数组用来放子元素
    foreach ($a as $v) {
        if ($v['pid'] == $pid) {                      //匹配子记录
            $v['children'] = get_attr($a, $v['id']); //递归获取子记录

            $tree[] = $v;                           //将记录存入新数组
        }
    }
    return $tree;
}
//生成GUID
function create_guid() {
    $charid = strtoupper(md5(uniqid(mt_rand(), true)));
    $hyphen = chr(45);// "-"
    $uuid = ""
        .substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12)
        ."";// "}"
    return $uuid;
}


/**成功消息
 * $msg 待提示的消息
 * $url 待跳转的链接
 * $icon 这里主要有两个，5和6，代表两种表情（哭和笑）
 * $time 弹出维持时间（单位秒）
 */
function alert_success($msg,$url=''){
    $urls = '';
    $str='<script type="text/javascript" src="'.$urls.'/public/cms/plugins/bower_components/jquery/dist/jquery.min.js"></script> <script type="text/javascript" src="'.$urls.'/public/layer/layer.js"></script>';//加载jquery和layer
    $str.='<script>
        $(function(){
         layer.msg("'.$msg.'", {icon: 1});
          setTimeout(function(){
            window.location.href="'.$url.'";
             if("'.$url.'"){
                window.location.href="'.$url.'";
            }else{
                window.history.go(-1);
            }
          },1000)
        });
      </script>';//主要方法
    return $str;
}

/**失败消息
 * $msg 待提示的消息
 * $icon 这里主要有两个，5和6，代表两种表情（哭和笑）
 * $time 弹出维持时间（单位秒）
 */
function alert_error($msg){
    $urls = '';
    $str='<script type="text/javascript" src="'.$urls.'/public/cms/plugins/bower_components/jquery/dist/jquery.min.js"></script> <script type="text/javascript" src="'.$urls.'/public/layer/layer.js"></script>';//加载jquery和layer
    $str.='<script>
        $(function(){
         layer.msg("'.$msg.'", {icon: 2});
          setTimeout(function(){
                window.history.go(-1);
          },1000)
        });
      </script>';//主要方法
    return $str;
}
//判断是否移动端
function is_mobile(){
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
    $is_mobile = false;
    foreach ($mobile_agents as $device) {
        if (stristr($user_agent, $device)) {
            $is_mobile = true;
            break;
        }
    }
    return $is_mobile;
}
