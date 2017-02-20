<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/26 0026
 * Time: 下午 2:16
 */
namespace backend\modules\system\models\log;
use backend\models\BaseRecord;
use backend\models\interfaces\RecordOperator;

class SystemLog extends BaseRecord implements RecordOperator
{
    public static function tableName()
    {
        return self::SystemLog;
    }

    public function insertRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }

        if(!$this->updatePreHandler($formData)) {
            return false;
        }

        return $this->insert();
    }

    public function updateRecord($formData)
    {
        // TODO: Implement updateRecord() method.
    }
}