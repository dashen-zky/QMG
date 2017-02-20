<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/11 0011
 * Time: 下午 5:24
 */

namespace backend\models;


use yii\web\Controller;

class BackEndBaseController extends Controller
{
    public function getTab($tab, $defaultValue) {
        if(empty($tab)) {
            return $defaultValue;
        }
        return $tab;
    }

    public function getParam($index, $defaultValue) {
        $value = \Yii::$app->request->get($index);
        return empty($value)?$defaultValue:$value;
    }
}