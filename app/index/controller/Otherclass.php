<?php
namespace app\index\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\Debug;
use think\facade\Request;
use think\facade\Config;
use think\facade\Cache;


class Otherclass extends BaseController 
{

    public function getHostData($http_host){

        $host_data = Db::query('select * from tp_domain where domain="'.$http_host.'" order by id asc limit 1;');

    
        if(count($host_data) == 0){
            abort(404, "host_data length eq 0 -- Otherclass");
        }else{
            return $host_data;
        } 
    }






    //返回是否是蜘蛛 1 true ; 0 false
    public static function getSpiderStatus($user_agent){
        if(preg_match("/".Config::get("app.spider_user_agent")."/i", $user_agent)){
            $spider_status = 1;
        }else{
            $spider_status = 0;
        }
        return $spider_status;
    }


        
    //能够很准确的获取到IP，只要能获取到用户的IP，可以去掉之前使用用户UA判断的选项
    public static function get_user_ip(){
        if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])) $ipaddress =  $_SERVER['HTTP_CF_CONNECTING_IP'];
        elseif (isset($_SERVER['HTTP_X_REAL_IP']))          $ipaddress = $_SERVER['HTTP_X_REAL_IP'];
        elseif (isset($_SERVER['HTTP_CLIENT_IP']))             $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']))        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        elseif (isset($_SERVER['HTTP_X_FORWARDED']))        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        elseif (isset($_SERVER['HTTP_FORWARDED']))    $ipaddress = $_SERVER['HTTP_FORWARDED'];
        elseif (isset($_SERVER['REMOTE_ADDR']))    $ipaddress = $_SERVER['REMOTE_ADDR'];
        else $ipaddress = "null";

        return $ipaddress;
    }


    //返回英文国家名称
    //https://github.com/maxmind/GeoIP2-php#city-example
    public function get_country($ip){
        try {
            $reader = new \GeoIp2\Database\Reader(Config::get("app.install_path").'vendor/geoip2/GeoLite2-City.mmdb');
            $record = $reader->city($ip);
            $country = $record->country->name;

            if(empty($country)){
                $country = "None";
            }
        }catch(\Exception $e){
            $country = "None";
        }

        return $country;
    }

    //位移解密
    public function str_rot13_decode($str){
        $str = base64_decode(str_rot13($str));
        return $str;
    }


    //位移加密
    public function str_rot13_encode($str){
        $str = str_rot13(base64_encode($str));
        return $str;
    }




    //获取youtube Video的json数据
    public function getYoutubeVideoMeta($videoId, $key){
        $ch = curl_init();
        $curlUrl = 'https://www.youtube.com/youtubei/v1/player?key=' . $key;
        curl_setopt($ch, CURLOPT_URL, $curlUrl);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $curlOptions = '{"context": {"client": {"hl": "en","clientName": "WEB",
            "clientVersion": "2.20210721.00.00","clientFormFactor": "UNKNOWN_FORM_FACTOR","clientScreen": "WATCH",
            "mainAppWebInfo": {"graftUrl": "/watch?v=' . $videoId . '",}},"user": {"lockedSafetyMode": false},
            "request": {"useSsl": true,"internalExperimentFlags": [],"consistencyTokenJars": []}},
            "videoId": "' . $videoId . '",  "playbackContext": {"contentPlaybackContext":
            {"vis": 0,"splay": false,"autoCaptionsDefaultOn": false,
            "autonavState": "STATE_NONE","html5Preference": "HTML5_PREF_WANTS","lactMilliseconds": "-1"}},
            "racyCheckOk": false,  "contentCheckOk": false}';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlOptions);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $curlResult = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return $curlResult;
    }


    //返回随机字符串
    public static function get_short_str($length=6) {


        $str_length = $length;


        $shortUrlStr = "";
        
        //随机生成32-50位长度的字符串，然后从0-6开始截取字符串去数据库中查询，如果能匹配到则自动增加1，直到匹配不到数据为止。
        //随机字符串不要太长，会占用cpu性能
        $num = mt_rand(32,32);

        $characters = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
        $randomStr = ''; 
        for ($i = 0; $i < $num; $i++) { 
            $index = mt_rand(0, strlen($characters) - 1); 
            $randomStr .= $characters[$index]; 
        }


        $timestamp = time();
        //将字符串使用base64加密
        $randomStr = base64_encode($randomStr.$timestamp);

        //替换掉base64加密后面可能产生的==号
        $randomStr = preg_replace("#=#i", "", $randomStr);


        $start_num = 0;
        while(true){

            //截取指定长度的字符串
            $shortUrlStr = substr($randomStr, $start_num, $str_length);


            //如果在redis数据库中未匹配到当前字符串，就说明当前字符串未被使用过，将数据储存在redis中，并且退出当前循环  
            if(!Cache::has(Config::get("app.redis_prefix").$shortUrlStr)){
                Cache::set(Config::get("app.redis_prefix").$shortUrlStr,1);  
                break;
            }


            //echo $shortUrlStr."\n";



            $start_num += 1;
            if($start_num > (strlen($randomStr) - $str_length - 1)){
                



                //------------    如果所有的数据都匹配完了还是没有匹配到short_str，就将长度+1   begin ------------

                //截取指定长度的字符串
                $shortUrlStr = substr($randomStr, $start_num, $str_length+1);


                //如果在redis数据库中未匹配到当前字符串，就说明当前字符串未被使用过，将数据储存在redis中，并且退出当前循环
                if(!Cache::has(Config::get("app.redis_prefix").$shortUrlStr)){
                    Cache::set(Config::get("app.redis_prefix").$shortUrlStr,1);
                    break;
                }
                
                //------------    如果所有的数据都匹配完了还是没有匹配到short_str，就将长度+1   end ------------

                //如果还是超出指定长度还没有匹配到数据，就将shortUrtStr设置为指定值
                if($start_num > strlen($randomStr) * 2){

                    //截取指定长度的字符串
                    $shortUrlStr = "errorShortStr".$timestamp."-".time();

                    //如果在redis数据库中未匹配到当前字符串，就说明当前字符串未被使用过，将数据储存在redis中，并且退出当前循环
                    if(!Cache::has(Config::get("app.redis_prefix").$shortUrlStr)){
                        Cache::set(Config::get("app.redis_prefix").$shortUrlStr,1);
                        break;
                    }
                }

            }
        }
        

        return $shortUrlStr; 
    }




}