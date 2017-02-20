<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application assets bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/jquery.datetimepicker.css',
        'css/lq.datetimepick.css',
    ];
    public $js = [
        'js/jquery.datetimepicker.js',
        'js/datetime.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

    //定义按需加载JS方法，注意加载顺序在最后
    public static function addScript($view, $jsfile) {
        $view->registerJsFile($jsfile, ['depends' => ['yii\web\YiiAsset']]);
    }

    //定义按需加载css方法，注意加载顺序在最后
    public static function addCss($view, $cssfile) {
        $view->registerCssFile($cssfile, ['depends' => ['yii\bootstrap\BootstrapPluginAsset']]);
    }
}
