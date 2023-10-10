<?php
declare (strict_types = 1);

namespace app\middleware;

use app\BaseController;
use think\facade\Session;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;

class CheckLogin extends BaseController
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        
        


        if(!Session::get('admin_username')){  
            $this->error("还未登录，请先登录.",'/'.Config::get('app.admin_path').'/login/login',3);
        }else{
            //在模板中注册当前的控制器名称，用于控制Navbar下划线
            View::assign("controller_name",$request->controller());

        }

        //必须要返回实例，不然报错
        //The middleware must return Response instance
        return $next($request);
    }
}
