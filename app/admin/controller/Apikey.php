<?php
namespace app\admin\controller;


use app\BaseController;
use think\facade\Debug;
use think\facade\Request;
use think\facade\Config;
use think\facade\Session;
use think\facade\View;
use think\facade\Db;



class Apikey extends BaseController 
{   

    

    //控制器中间件，执行顺序：全局中间件->应用中间件->路由中间件->控制器中间件
    protected $middleware = ['\app\middleware\CheckLogin::class'];

    public function add(){
    
        View::assign('username',Session::get('admin_username'));
        View::assign('admin_path',Config::get('app.admin_path'));
        View::assign('pc_url',Config::get('app.admin_url'));
        View::assign('addtime',time());
 

        return View::fetch('/Apikey/apikey_add');
    }


    public function addpost(){

        $data = Request::param();

        $insert_data = [
            "api_key" => $data["api_key"],
            "limit" => $data["limit"],
            "use_times" => $data["use_times"],
            "comment" => $data["comment"],
            "today_status" => isset($data["today_status"]) ? 1 : 0,
            "status" => isset($data["status"]) ? 1 : 0,
            "timestamp" => time()
        ];





        if(Db::table('tp_api_key')->strict(false)->insert($insert_data)){
            $this->success("添加数据成功",$_SERVER["HTTP_REFERER"],1);
        }else{
            $this->error("添加数据失败",$_SERVER["HTTP_REFERER"],1);
        }

       
    }


    public function list(){
        $sort = Request::param("sort") ?? "0";
        $status = Request::param("status") ?? "none";    
        



        if($status != "none"){
           
            if($status == 0){
                $list = Db::table('tp_api_key')->where("status","0")->order('id', $sort)->paginate([
                    'list_rows' => Config::get('app.admin_page_num'),
                    'path'  => "/".Config::get("app.admin_path").'/apikey/list',
                    'query' => Request::param()
                ]);

            }else{
                $list = Db::table('tp_api_key')->where("status","1")->order('timestamp', "desc")->paginate([
                    'list_rows' => Config::get('app.admin_page_num'),
                    'path'  => "/".Config::get("app.admin_path").'/apikey/list',
                    'query' => Request::param()
                ]);
            
            }

        }else{
            $list = Db::table('tp_api_key')->order('id', $sort)->paginate([
                'list_rows' => Config::get('app.admin_page_num'),
                'path'  => "/".Config::get("app.admin_path").'/apikey/list',
                'query' => request()->param()
            ]);
            
        }




        View::assign('username',Session::get('admin_username'));
        View::assign('list',$list);
        View::assign('pc_url',Config::get('app.admin_url'));
        View::assign('admin_url',Config::get('app.admin_url'));
        View::assign('admin_path',Config::get('app.admin_path'));
        View::assign('article_length_num',count($list));

        return View::fetch('/Apikey/apikey_list');

    }



    public function edit(){

        $id= Request::param('id'); 
        $data = Db::table('tp_api_key')->where('id',$id)->select();

       
        View::assign('data',$data);
        View::assign('username',Session::get('admin_username'));
        View::assign('admin_path',Config::get('app.admin_path'));
        View::assign('pc_url',Config::get('app.admin_url'));
        View::assign('addtime',time());
       
        return View::fetch('/Apikey/apikey_edit');      

    }

    public function editPost(){
        $data = Request::param();

        $update_data = [
            "api_key" => $data["api_key"],
            "limit" => $data["limit"],
            "use_times" => $data["use_times"],
            "comment" => $data["comment"],
            "today_status" => isset($data["today_status"]) ? 1 : 0,
            "status" => isset($data["status"]) ? 1 : 0 
        ];




        if(Db::table('tp_api_key')->strict(false)->where('id',$data["id"])->update($update_data)){
            $this->success("数据编辑成功！","/".Config::get("app.admin_path")."/apikey/list?sort=desc&status=1",1);

        }else{
            $this->error("数据无变化",$_SERVER["HTTP_REFERER"],1);
    
        }


       

    }

    public function delete(){
        $id= Request::param("id");

        

        if(Db::table('tp_api_key')->where('id',$id)->delete()){
            $this->success("删除数据成功","/".Config::get('app.admin_path')."/apikey/list?sort=desc", 1);
        }else{
            $this->error("删除数据失败，请检查参数！","/".Config::get('app.admin_path')."/apikey/list?sort=desc",1);
        }
    }


    public function reset(){
        $update_data = [
            "today_status" => 1,
            "status" => 1,
            "use_times"=>0,
            "error_times"=>0
        ];

        if(Db::table('tp_api_key')->where('id',">",0)->update($update_data)){
            $this->success("KEY状态重置成功","/".Config::get('app.admin_path')."/apikey/list?sort=desc", 1);
        }else{
            $this->error("KEY重置失败，请检查参数！","/".Config::get('app.admin_path')."/apikey/list?sort=desc",1);
        }
    }

}
