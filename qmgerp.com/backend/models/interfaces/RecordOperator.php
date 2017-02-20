<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/8 0008
 * Time: 下午 7:15
 */
namespace backend\models\interfaces;
interface RecordOperator
{
    public function insertRecord($formData);
    public function updateRecord($formData);
}