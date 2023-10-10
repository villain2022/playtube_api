<?php
namespace app\admin\controller;


use app\BaseController;
use think\facade\View;
use think\facade\Request;
use think\facade\Config;
use think\facade\Session;

class Login extends BaseController
{

    public function login(){
        View::assign("admin_path",Config::get("app.admin_path"));
        return View::fetch('/Login/login');
    }


    public function login_post(){
        $username = Request::param('username');
        $password = Request::param('password');
        
        
        if($username == Config::get("app.admin_username") && $password == Config::get("app.admin_password")){
            Session::set('admin_username',$username); 
            $this->success("登录成功，跳转中...",'/'.Config::get('app.admin_path').'/apikey/list?sort=desc',1);
        }else{
            $this->error("登录失败，请检查参数...",$_SERVER["HTTP_REFERER"],1);
        }
    }



    public function logout(){
        if(!Session::has('admin_username')){
            $this->error("未验证，请先登录...",'/'.Config::get('app.admin_path').'/login/login',1);
        }else{
            Session::delete('admin_username');
            $this->success("退出成功,跳转中...",'/'.Config::get('app.admin_path').'/login/login',1);
        }

    }
}