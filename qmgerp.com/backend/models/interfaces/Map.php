<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/19 0019
 * Time: 下午 3:00
 */

namespace backend\models\interfaces;


interface Map
{
    public function insertSingleRecord($formData);
    public function updateSingleRecord($formData);
}