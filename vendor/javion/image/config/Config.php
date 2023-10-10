<?php

return [
    /**
     * 水印字体(默认字体不支持中文，请按需配置需要的字体)
     */
    'font'       => __DIR__ . '/font.ttf',

    /**
     * 水印位置(1~9，9宫格位置，其他为随机)
    */
    'pos'        => 9,

    /**
     * 相对pos的x偏移量
     */
    'posX'       => 0,

    /**
     * 相对pos的y偏移量
     */
    'posY'       => 0,

    /*
     * 水印透明度
     * 填写0~100间的数字,100为不透明
    */
    'opacity'        => 100,

    /**
     * 透明度参数 alpha，其值从 0 到 127。0 表示完全不透明，127 表示完全透明
     */
    'alpha'         => 0,

    /*
     * 默认水印文字
     */
    'text'       => 'Javion',

    /*
     * 文字颜色 颜色使用16进制表示
    */
    'textColor' => '#FF4040',

    /*
     * 文字大小
     */
    'textSize'  => 12,
];
