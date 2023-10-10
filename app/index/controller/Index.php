<?php
namespace app\index\controller;


use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use think\facade\Config;
use think\facade\Cookie;
use think\facade\Session;


class Index extends BaseController
{

    //首页不允许访问
    public function index(){
        abort(404,"Denied");
    }


    public function video(){
        $otherclass = new Otherclass($this->app);
        $video_id = Request::param("video_id") ?? Request::pram("video_id");
        
        if($video_id == ""){
            abort(404,"video_id is null");
        }

        $key_data = Db::table("tp_api_key")->where("today_status","1")->where("status","1")->select();

        //随机获取一项值
        $key_data = $key_data[mt_rand(0,count($key_data)-1)];
        $api_key = $key_data['api_key'];
        $id = $key_data['id'];
        $limit = $key_data['limit'];
        $use_times = $key_data['use_times'];
        $today_status = $key_data['today_status'];



        $video_meta_data = json_decode($otherclass->getYoutubeVideoMeta($video_id, $api_key));
        //var_dump($video_meta_data);
        $video_formats = $video_meta_data->streamingData->formats;
        $video_url = @end($video_formats)->url;

        // if($video_url == ""){
        //     $signature = @end($video_formats)->signatureCipher;
        //     echo $signature;
        //     echo parse_url($signature, PHP_URL_QUERY);
        //    parse_str(parse_url($signature, PHP_URL_QUERY), $parse_signature);
        //     //var_dump($parse_signature);
        //     $video_url = $parse_signature['url'];
        // }


        //echo "video_url:".$video_url;
        

        //判断url
        if(strlen($video_url) < 10){
            //error
            #有小部分youtube url是加密的  下面是思路，勿要耽误时间
            //https://pkg.go.dev/github.com/89z/youtube
            //https://github.com/wayne931121/youtube_downloader
            //https://github.com/SurpassHR/Youtube_SignatureCipher_Decryptor
            $from_short_url = Request::param("f") ?? Request::param("f");

            //
            if($from_short_url != ""){
                //将特殊的标识更改为1，url_error 即当前google cdnurl是加密url
                Db::table("videos")->where("video_id",$from_short_url)->update(["url_error"=>1]);
            }

            
        }else{
            //更新数据库  //api使用量要比总配额低2000，不然可能会诱发系统封禁
            if($use_times+1 < ($limit-2000)){
                $update_data=[
                    'use_times' => $use_times+1,
                    'today_status' => 1
                ];
            }else{
                $update_data=[
                    'use_times' => $use_times+1,
                    'today_status' => 0  //将今日的状态设置为0，即超限
                ];
            }
            Db::table("tp_api_key")->where("id",$id)->update($update_data);
        }

        //加载当前视频
        header("Content-Type:video/mp4");
        header("Location:$video_url");
    }




    public function thumb(){
        $otherclass = new Otherclass($this->app);
        $video_id = Request::param("video_id") ?? Request::pram("video_id");
        
        if($video_id == ""){
            abort(404,"video_id is null");
        }


        //https://i.ytimg.com/vi/Ybqdos4gpDY/hqdefault.jpg

        //加载当前视频
        header("Content-Type:image/webp");
        header("Location:https://i.ytimg.com/vi/$video_id/hqdefault.jpg");
    }


}