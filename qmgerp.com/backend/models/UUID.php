<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3 0003
 * Time: 下午 8:49
 */

namespace backend\models;
use Yii;

class UUID
{
    public static function getUUID()
    {
        return time() . Yii::$app->security->generateRandomString(5);
    }
}