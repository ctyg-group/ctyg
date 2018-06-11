<?php
namespace app\mobile\controller;
use think\Controller;
use think\Db;

class Base extends Controller {

// 初始化
    public function _initialize(){
        header("Content-type:text/html;charset=utf-8");

    }

    // 初始化
    protected function start()
    {

    }


    public function ajaxReturn($data,$type = 'json'){
        exit(json_encode($data));
    }


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

    public function calcul_page($count,$size){
        $num = ceil($count/$size);
        for ($i=1;$i<=$num;$i++){
            $page[]=array('value'=>$i,'text'=>"$i/$num");
        }
        $page = json_encode($page);
        $this->assign("page",$page);
        $this->assign('count',ceil($count/$size));
    }



    public function change_u($url,$width,$height){
        $turl = str_replace('upload','thumb',$url);
        $route = substr($turl,0,-37);
        $turl = substr($turl,0,-4);
        $suffix = substr($url,strlen($url)*1-4,strlen($url)*1);

        return "http://www.jiujusz.com$turl"."_$width"."_$height".$suffix;
    }


}