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
use think\Controller;
use think\Db;
class Common extends Controller
{
    public function _initialize(){

//        if(is_mobile()){
//            //$this->redirect('Mobile/Index/index');exit;
//           $str=$_SERVER["QUERY_STRING"];
//            $newUrl=$this->insertToStr($str, 1, "m");
//            $this->redirect($newUrl);exit;
//
//        }

        $frlink = DB::name("frlink")->field("name,url")->order("sort asc")->select();
        $data = array(
            'frlink'=>$frlink,
        );
        $data['module'] = request()->module();
        $data['controller'] = request()->controller();
        $data['action'] = request()->action();
        $data['conact'] = request()->controller()."/".request()->action();

        $datas = array(
            "web_title"=>"深圳家装_深圳装修公司_装修网-久居整装",
            "keywords"=>"深圳家装,深圳装修公司,装修网,久居整装",
            "description"=>"久居整装装修网是一家致力于为深圳家装行业提供高品质完整整装服务的深圳装修公司网站，为您提供不同风格装修效果图、装修知识、装修报价、工地直播等家装服务，信任装修找久居！",
        );
        $this->assign($datas);
        $this->assign($data);
    }
  public function insertToStr($str, $i, $substr){
        //指定插入位置前的字符串
        $startstr="";
        for($j=0; $j<$i; $j++){
            $startstr .= $str[$j];
        }

        //指定插入位置后的字符串
        $laststr="";
        for ($j=$i; $j<strlen($str); $j++){
            $laststr .= $str[$j];
        }

        //将插入位置前，要插入的，插入位置后三个字符串拼接起来
        $str = $startstr . $substr . $laststr;

        //返回结果
        return $str;
    }

    //给管理员发短信
    public function sendAdmin($m,$p){
        $app_key = "24554291";
        $app_secret = "c252c469aae3033a2a9cb2eb5ee43f8f";
        $request_paras = array(
            'ParamString' => json_encode(array('phone'=>$p)),
            'RecNum' => $m,
            'SignName' =>'久居整装装修网',
            'TemplateCode' => 'SMS_90085008'
        );
        $request_host = "http://sms.market.alicloudapi.com";
        $request_uri = "/singleSendSms";
        $request_method = "GET";
        $info = "";
        $content = $this->do_get($app_key, $app_secret, $request_host, $request_uri, $request_method, $request_paras, $info);
        $res = json_decode($content); // API返回值
    }
    //给客户发短信
    public function sendNotice($m){
        $app_key = "24554291";
        $app_secret = "c252c469aae3033a2a9cb2eb5ee43f8f";
        $request_paras = array(
            'ParamString' => '{"no":"123456"}',
            'RecNum' => $m,
            'SignName' =>'久居整装装修网',
            'TemplateCode' => 'SMS_89635046'
        );

        $request_host = "http://sms.market.alicloudapi.com";
        $request_uri = "/singleSendSms";
        $request_method = "GET";
        $info = "";
        $content = $this->do_get($app_key, $app_secret, $request_host, $request_uri, $request_method, $request_paras, $info);

        $res = json_decode($content); // API返回值
        if($res->success){
            return true;
        }else{
            return false;
        }
        #var_dump($info);  // 系统请求返回信息
    }
    public function sendPrice($m,$p){
        $app_key = "24554291";
        $app_secret = "c252c469aae3033a2a9cb2eb5ee43f8f";
        $request_paras = array(
            'ParamString' => json_encode(array('price'=>$p)),
            'RecNum' => $m,
            'SignName' =>'久居整装装修网',
            'TemplateCode' => 'SMS_78645129'
        );

        $request_host = "http://sms.market.alicloudapi.com";
        $request_uri = "/singleSendSms";
        $request_method = "GET";
        $info = "";
        $content = $this->do_get($app_key, $app_secret, $request_host, $request_uri, $request_method, $request_paras, $info);

        $res = json_decode($content); // API返回值
        if($res->success){
            return true;
        }else{
            return false;
        }
        #var_dump($info);  // 系统请求返回信息
    }

    public function do_get($app_key, $app_secret, $request_host, $request_uri, $request_method, $request_paras, &$info) {
        ksort($request_paras);
        $request_header_accept = "application/json;charset=utf-8";
        $content_type = "";
        $headers = array(
            'X-Ca-Key' => $app_key,
            'Accept' => $request_header_accept
        );
        ksort($headers);
        $header_str = "";
        $header_ignore_list = array('X-CA-SIGNATURE', 'X-CA-SIGNATURE-HEADERS', 'ACCEPT', 'CONTENT-MD5', 'CONTENT-TYPE', 'DATE');
        $sig_header = array();
        foreach($headers as $k => $v) {
            if(in_array(strtoupper($k), $header_ignore_list)) {
                continue;
            }
            $header_str .= $k . ':' . $v . "\n";
            array_push($sig_header, $k);
        }
        $url_str = $request_uri;
        $para_array = array();
        foreach($request_paras as $k => $v) {
            array_push($para_array, $k .'='. $v);
        }
        if(!empty($para_array)) {
            $url_str .= '?' . join('&', $para_array);
        }
        $content_md5 = "";
        $date = "";
        $sign_str = "";
        $sign_str .= $request_method ."\n";
        $sign_str .= $request_header_accept."\n";
        $sign_str .= $content_md5."\n";
        $sign_str .= "\n";
        $sign_str .= $date."\n";
        $sign_str .= $header_str;
        $sign_str .= $url_str;

        $sign = base64_encode(hash_hmac('sha256', $sign_str, $app_secret, true));
        $headers['X-Ca-Signature'] = $sign;
        $headers['X-Ca-Signature-Headers'] = join(',', $sig_header);
        $request_header = array();
        foreach($headers as $k => $v) {
            array_push($request_header, $k .': ' . $v);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $request_host . $url_str);
        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $ret = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return $ret;
    }
}
