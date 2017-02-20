<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/14 0014
 * Time: 下午 3:01
 */

namespace backend\models;
use backend\models\BaseRecord;
use Yii;
class ListIndex
{
    static public function listIndex($i) {
        if ($pageIndex = Yii::$app->request->get('page')) {
            $index = ($pageIndex - 1) * BaseRecord::PageSize + $i;
        } else {
            $index = $i;
        }
        return $index;
    }
}