<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/29 0029
 * Time: 下午 4:47
 */

namespace backend\models;


use yii\base\Model;

class BaseForm extends Model
{
    public function setError($errors) {
        foreach($errors as $index => $error) {
            $this->addError($index,$error[0]);
        }
    }

    static public function genderList() {
        return [
            1=>'男',
            2=>'女',
        ];
    }

    static public function getGender($index) {
        $list = self::genderList();
        return $list[$index];
    }
}