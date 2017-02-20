<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/9 0009
 * Time: 下午 8:36
 */

namespace backend\models\interfaces;


interface DeleteMapRecord extends Map
{
    public function deleteSingleRecord($uuid1,$uuid2);
}