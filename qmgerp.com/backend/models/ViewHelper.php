<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/19 0019
 * Time: 上午 11:09
 */

namespace backend\models;


class ViewHelper
{
    const RequiredField = "*";
    public $requiredFields = [];
    static function defaultValueForDropDownList($edit = false, $formData, $index) {
        if($edit && isset($formData[$index])) {
            return [$formData[$index]=>['Selected'=>true]];
        }
    }

    public function __construct($model)
    {
        $this->generateRequiredField($model);
    }

    public function generateRequiredField($model) {
        $rules = $model->rules();
        for($i = 0; $i < count($rules); $i++) {
            if(in_array("required", $rules[$i])) {
                $this->requiredFields = array_merge($this->requiredFields,$rules[$i][0]);
            }
        }
    }

    public function isRequiredFiled($field) {
        if(empty($field)) {
            return null;
        }
        if(!in_array($field, $this->requiredFields)) {
            return null;
        }

        return self::RequiredField;
    }

    public static function appendElementOnDropDownList($list, $append = null) {
        if(empty($append)) {
            return [0=>'未选择']+$list;
        }
        return $list+$append;
    }
}