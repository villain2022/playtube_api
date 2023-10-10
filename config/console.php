<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'youtube_search'  => 'app\command\YoutubeSearch',
        'sitemaps'  => 'app\command\Sitemaps',
        'reset_api_key'  => 'app\command\ResetApiKey',
    ],
];
