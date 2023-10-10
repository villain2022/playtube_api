<?php
namespace app\admin\controller;



use app\BaseController;
use think\facade\Debug;
use think\facade\Request;
use think\facade\Config;
use think\facade\View;
use think\facade\Db;


class Otherclass extends BaseController 
{

    public function getHostData($http_host){

        $host_data = Db::query('select * from tp_domain where domain="'.$http_host.'" order by id asc limit 1;');
        
        return $host_data;
    }

}