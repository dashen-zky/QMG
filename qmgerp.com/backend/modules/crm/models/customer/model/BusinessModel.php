<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/19 0019
 * Time: 上午 10:22
 */

namespace backend\modules\crm\models\customer\model;
use yii\base\Model;

class BusinessModel extends Model
{
    static public function checkIdInList(&$businessList, $id) {
        $_return = false;
        if (empty($businessList)) {
            return $_return;
        }
        foreach($businessList as $index => $business) {
            if ($id == $business['business_id']) {
                $_return = true;
                unset($business[$index]);
            }
        }

        return $_return;
    }
}