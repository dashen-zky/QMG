<?php
namespace backend\models\helper;
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-12-6
 * Time: 上午12:35
 */
class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * 将一个二维数组里面所有的值都为空的子数组过滤掉
     * @param mixed $arr 必须是二维数组
     * @return mixed 被处理过后的数组
     */
    public static function filterEmptyArrayForDyadic(&$arr) {
        foreach ($arr as $index => $item) {
            $empty = true;
            foreach ($item as $value) {
                if (!empty($value)) {
                    $empty = false;
                    break;
                }
            }

            if ($empty) {
                unset($arr[$index]);
            }
        }
        return $arr;
    }
}