<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Config;
use think\facade\Cache;
use think\facade\Db;
use app\index\controller\Otherclass;

class YoutubeSearch extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('app\command\youtubesearch')
            ->setDescription('the app\command\youtubesearch command');
    }

    protected function execute(Input $input, Output $output)
    {

        try{
            $begin_num = 6980; //起始开始处理行
            $file_name = "keyword.txt";


            $otherclass = new Otherclass($this->app);
            $keyword_file = app()->getRootPath()."/extend/keyword/".$file_name;
            $data_file = fopen($keyword_file,"r");
            $keyword_list = [];

            while(!feof($data_file)){
                
                $line = fgets($data_file);
                $line = trim($line);//去掉首尾空白字符
                //$line_list = explode(",", $line);
                array_push($keyword_list,$line);
                
            }
            fclose($data_file);

            $count_num = -1;
            
            while($count_num < count($keyword_list)){
                $count_num += 1;
                $error_num = 0;

                if($count_num < $begin_num){
                    continue;
                }

                # --------- log ------------------ begin
                $log_path = app()->getRootPath()."/extend/keyword/log.txt";
                $log_file = fopen($log_path,"a+");
                fwrite($log_file,date("Y-m-d H:i:s")." count:".$count_num."\n");
                fclose($log_file);
                # --------- log ------------------ end

                //获取key api数据
                $key_data = Db::table("tp_api_key")->where("today_status","1")->where("status","1")->select();

                //随机获取一项key值
                $key_data = $key_data[mt_rand(0,count($key_data)-1)];
                $api_key = $key_data['api_key'];
                $key_id = $key_data['id'];
                $limit = $key_data['limit'];
                $use_times = $key_data['use_times'];
                $error_times = $key_data['error_times'];



                $keyword = $keyword_list[$count_num];
      


                //获取youtube 根据关键词搜索的结果
                $client = new \Google_Client();
                $client->setDeveloperKey($api_key);
                $youtube = new \Google_Service_YouTube($client);
            
            
                try {
                    $searchResponse = $youtube->search->listSearch('id,snippet', array(
                        'q' => $keyword,
                        'maxResults' => 80, //每次请求数量
                    ));

                    //var_dump($searchResponse);
                    //sleep(1000);

                } catch (\Google_Service_Exception $e) {
                    echo $e->getMessage();
                    $error_num = 1;
                    
                } catch (\Google_Exception $e) {
                    echo $e->getMessage();
                    $error_num = 1;
                }
            
                //var_dump($searchResponse);


                //----------------------  更新数据库 KEY BEGIN -----------------------------
                //必须要低于限额的90%，必然cloud后台会提示规避限速，并删除应用
                if($use_times+1 < ($limit-2000)){
                    $update_data=[
                        'use_times' => $use_times+80,
                        'today_status' => 1
                    ];
                }else{
                    $update_data=[
                        'use_times' => $use_times+80,
                        'today_status' => 0  //将今日的状态设置为0，即超限
                    ];
                }
                Db::table("tp_api_key")->where("id",$key_id)->update($update_data);
                //----------------------  更新数据库 KEY END -----------------------------


                //----------------------  更新数据库错误次数 ERROR_TIMES begin -----------------------------
                if($error_num == 1){
                    echo ">>  api key error.\n";
                    Db::table("tp_api_key")->where("id",$key_id)->update(["error_times"=>$error_times+1]);
                    continue;
                }
                //----------------------  更新数据库错误次数 ERROR_TIMES end -----------------------------


            
                $videos = [];//格式 ：['user_id','title','description','publish_date','video_id']
                // Add each result to the appropriate list, and then display the lists of
                // matching videos, channels, and playlists.
                foreach ($searchResponse['items'] as $searchResult) {
                    switch ($searchResult['id']['kind']) {
                        case 'youtube#video':
                            $video_meta = [
                                'user_id' => "user-".$searchResult['snippet']['channelId'],
                                'channel_title' => $searchResult['snippet']['channelTitle'],
                                'title' => $searchResult['snippet']['title'],
                                'description' => $searchResult['snippet']['description'],
                                'published_time' => strtotime($searchResult['snippet']['publishedAt']),
                                'video_id' =>  $searchResult['id']['videoId']
                            ];
                            array_push($videos, $video_meta);
                            break;
                    //   case 'youtube#channel':
                    //     $channels .= sprintf('<li>%s (%s)</li>',
                    //         $searchResult['snippet']['title'], $searchResult['id']['channelId']);
                    //     break;
                    //   case 'youtube#playlist':
                    //     $playlists .= sprintf('<li>%s (%s)</li>',
                    //         $searchResult['snippet']['title'], $searchResult['id']['playlistId']);
                    //     break;
                    }
                }
            
                foreach($videos as $item){
                    #逻辑流程
                    #1、先判断users表中是否有此username存在，如果有则读取数据，如果没有则插入新的user数据并返回user id值
                    #2、将返回的user id拼合video数据插入videos表，并返回video id
                    #3、将video id拼合user id 将数据插入到views表

                    try{

                        //先判断数据库中是否有此video存在
                        //$video_data_exist = Db::table("videos")->where("from_url","=",$item['video_id'])->select();

                        if(Cache::has("ytb_video_".$item['video_id'])){
                            echo ">> video data is exist.\n";
                            continue;
                        }else{
                            #1、插入数据并返回id值
                            $user_data = Db::table("users")->where("username",$item['user_id'])->select();
                            if(count($user_data) > 0){
                                $user_id = $user_data[0]['id'];
                            }else{
                                $user_insert_data = [
                                    "username" => $item['user_id'],
                                    "channel_title" => $item['channel_title'],
                                    "ip_address" => "13.230.109.140",
                                    "password" => "NONE",
                                    "gender" => "male",
                                    "language" => "english",
                                    "avatar" => "upload/photos/d-avatar.jpg",
                                    "cover" => "upload/photos/d-cover.jpg",
                                    "country_id" => 0,
                                    "age" => 0,
                                    "active" => 1,
                                    "verified" => 0,
                                    "last_active" => 1687333462,
                                    "registered" => "00/0000",
                                    "uploads" => "19401324",
                                    "last_month" => '{"likes":0,"dislikes":0,"views":0,"comments":0,"update_time":1688169540}',
                                    "active_time" => "11496",
                                    "active_expire" => 1687564740,
                                    "point_day_expire" => 1687391940,
                                    "points" => 60,
                                    "daily_points" => 20,
                                    "converted_points" => 60
                                ];

                                $user_id = Db::table("users")->insertGetId($user_insert_data);  

                            }



                            


                            $short_url_15 = $otherclass->get_short_str(15);
                            $short_url_6 = $otherclass->get_short_str(6);

                            #2 组合拼接插入videos数据
                            $video_insert_data = [
                                "video_id" => $short_url_15,
                                "user_id" => $user_id,
                                "channel_title" => htmlspecialchars_decode($item['channel_title']),
                                "short_id" => $short_url_6,
                                "title" => htmlspecialchars_decode($item['title']),
                                "description" => htmlspecialchars_decode($item['description']),
                                "thumbnail" => Config::get("app.cdn_domain_prefix")."/thumb/".$item['video_id'],
                                "video_location" => Config::get("app.cdn_domain_prefix")."/video/".$item['video_id']."?f=".$short_url_15,
                                "time" => time()-mt_rand(0,99)*86400,//改为随机时间戳 原值为发布原来时间：$item['published_time']
                                "tags" => "",
                                "converted" => 1,
                                "duration" => mt_rand(0,2).mt_rand(2,9).":".mt_rand(1,9).mt_rand(1,9),//时间长度,如：09：20
                                "category_id" => mt_rand(4,13),//分类id
                                "views" => mt_rand(100,100000),
                                "registered" => "2023/6",
                                "age_restriction" => 1,
                                "approved" => 1,
                                "monetization" => 1,
                                "from_url"=>$item['video_id'] //youtube来源video_id  用于去重
                            ];


                            $video_id = Db::table("videos")->insertGetId($video_insert_data);  


                            # 拼合views数据,并插入数据
                            $views_insert_data = [
                                "video_id" => $video_id,
                                "fingerprint" => "27026bcad5af8d7c65dcba26f4de0611faa1",
                                "user_id" => $user_id,
                                "time" => time()
                            ];

                            $views_id = Db::table("views")->insertGetId($views_insert_data); 



                            //设置ytb_id catch
                            Cache::set("ytb_video_".$item['video_id'],"1");

                            echo sprintf(">>  user_id:%s video_id:%s views_id:%s insert success.\n", $user_id, $video_id, $views_id);
                   
                        }
                   
                    }catch(\Exception $e){
                        echo "insert failed.\n";
                        continue;
                    }
                }


























                echo ">> Length:".count($videos)." count_num:$count_num\n";
                echo ">> Sleep(10)\n";
                sleep(10);
            }

        

        
        }catch(\Exception $e){
            dump($e);
        }


    }
}
