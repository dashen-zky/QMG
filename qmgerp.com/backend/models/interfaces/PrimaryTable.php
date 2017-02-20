<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/8 0008
 * Time: 下午 11:37
 */

namespace backend\models\interfaces;


interface PrimaryTable
{
    public function deleteRecordByUuid($uuid);
    public function getRecordByUuid($uuid);
}