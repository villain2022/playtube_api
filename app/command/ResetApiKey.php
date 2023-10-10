<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class ResetApiKey extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('app\command\resetapikey')
            ->setDescription('the app\command\resetapikey command');
    }

    protected function execute(Input $input, Output $output)
    {

        $reset_hour = 11; //美国东部时间比亚洲时区慢8小时
        while(True){

            if(date("H") == $reset_hour){

                // 定时重置api
                $update_data = [
                    "today_status" => 1,
                    "status" => 1,
                    "use_times"=>0,
                    "error_times"=>0

                ];

                if(Db::table('tp_api_key')->where('id',">",0)->update($update_data)){
                    echo ">> ".date("Y-m-d H:i:s")." Reset api key success!\n";
                }else{
                    echo ">> ".date("Y-m-d H:i:s")." Reset api key failed!\n";
                }
            }
            sleep(60*60);
        }
    }
}
