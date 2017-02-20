<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/7 0007
 * Time: 上午 12:04
 */
namespace backend\modules\hr\assets;
use Yii;
use yii\web\AssetBundle;
class HrAsset extends AssetBundle
{
    public $baseUrl = "@hr";
    public $js = [
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_END];
    //定义按需加载JS方法，注意加载顺序在最后
    public static function addScript($view, $jsfile) {
        $view->registerJsFile($jsfile, ['depends' => ['yii\web\YiiAsset']]);
    }

    //定义按需加载css方法，注意加载顺序在最后
    public static function addCss($view, $cssfile) {
        $view->registerCssFile($cssfile, ['depends' => ['yii\bootstrap\BootstrapPluginAsset']]);
    }
}