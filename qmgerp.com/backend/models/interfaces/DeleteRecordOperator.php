<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/9 0009
 * Time: 下午 8:21
 */

namespace backend\models\interfaces;


interface DeleteRecordOperator extends RecordOperator
{
    public function deleteRecord($uuid);
}