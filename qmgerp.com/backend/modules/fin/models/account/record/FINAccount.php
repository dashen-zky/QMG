<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/5 0005
 * Time: 下午 6:14
 */
namespace backend\modules\fin\models\account\record;
use backend\models\interfaces\DeleteRecordOperator;
use backend\models\interfaces\RecordOperator;
use backend\modules\fin\models\FINBaseRecord;

class FINAccount extends FINBaseRecord implements DeleteRecordOperator
{
    public static function tableName()
    {
        return self::FINAccount;
    }

    public function updateRecord($formData)
    {
        // TODO: Implement updateRecord() method.
    }

    public function formDataPreHandler(&$formData, $record)
    {
        parent::formDataPreHandler($formData, $record);
    }

    public function insertRecord($formData)
    {
        if(empty($formData) || !isset($formData['name']) || empty($formData['name'])) {
            return true;
        }

        if(!parent::updatePreHandler($formData)) {
            return true;
        }

        return $this->insert();
    }

    public function deleteRecord($uuid)
    {
        if(empty($uuid)) {
            return false;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return false;
        }
        return $record->delete();
    }

    public function getRecordByUuid($uuid) {
        if(empty($uuid)) {
            return null;
        }

        return self::find()->andWhere(['uuid'=>$uuid])->asArray()->one();
    }
}