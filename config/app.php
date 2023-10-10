<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [

    // +------------------------------------------------------------------
    // | 网站设置
    // +------------------------------------------------------------------
    //admin后台登录的账号用户名和密码
    'admin_username'         => 'admin',    //后台用户名
    'admin_password'         => 'admin888', //后台密码
    'api_password'           => 'a8S5a6asd',
    'install_path'           => '/www/wwwroot/playtube_api/',

    //后台相关配置项
    'admin_url'              => 'https://cdn.---.com/', //后台登录网址
    'admin_path'             => 'admin.php',//后台入口文件，防止后台被爆破
    'admin_page_num'         => '50',//后台分页数量
    'font_page_num'          => '24',//前端分页数量
    'email'                  => '123@gmail.com',


    //sitemaps数量
    "sitemaps_url_num"       => '5000',  


    //redis前缀，防止在同一台服务器上不同的程序之间的redis key重复，造成数据覆盖丢失。
    'redis_host'             => '127.0.0.1',
    'redis_port'             =>  6379,
    'redis_prefix'           => 'playtube_api_20230624_', 


    //sitemaps
    "sitemaps_domain_prefix" => "https://123.com/watch/", //  以“/”结尾  


    //cdn domain
    "cdn_domain_prefix" =>  "https://cdn.123.com/", //  以“/”结尾  






    

    // ------------------------------------------------------------------
    // 默认跳转页面对应的模板文件【新增】
    'dispatch_success_tmpl' => app()->getRootPath() . '/public/tpl/dispatch_jump.tpl',
    'dispatch_error_tmpl'  => app()->getRootPath() . '/public/tpl/dispatch_jump.tpl',

    'http_exception_template'    =>  [
        // 定义404错误的模板文件地址
        404 =>  app()->getRootPath() . '/public/404.html',
        // 还可以定义其它的HTTP status
        401 =>  app()->getRootPath() . '/public/404.html',
    ],

    // ------------------------------------------------------------------
    // 应用地址
    'app_host'         => env('app.host', ''),
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    // 默认应用
    'default_app'      => 'index',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',
    // 应用映射（自动多应用模式有效）
    'app_map'          => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => ["middleware","command"],
    // 异常页面的模板文件
    'exception_tmpl'   => app()->getThinkPath() . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'    => 'Page error! Please try again later.',
    // 显示错误信息
    'show_error_msg'   => true,


];
