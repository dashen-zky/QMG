<?php

namespace backend\modules\crm\models\stamp;
use backend\models\interfaces\DeleteRecordOperator;
use backend\modules\crm\models\CRMBaseRecord;

class Stamp extends CRMBaseRecord implements DeleteRecordOperator
{
    public static function tableName()
    {
        return self::CRMStamp;
    }

    public function rules()
    {
        return [
            [
                [
                'company_name',
                'company_address',
                'stamp_number',
                'bank_of_deposit',
                'account',
                'company_phone'
                ],
                'required'
            ],
            [
                [
                    'account',
                    'stamp_number',
                ],
                'unique','message'=>'小盆友，这个账号已经被占用了！'
            ]
        ];
    }
    
    public function getRecord($uuid) {
        if(empty($uuid)) {
            return null;
        }
        
        return self::find()->andWhere(['uuid'=>$uuid])->asArray()->one();
    }

    public function updateRecord($formData)
    {
        if(empty($formData)) {
            return false;
        }

        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(empty($record) || !$this->updatePreHandler($formData, $record)) {
            return false;
        }
        if(!$record->validate()) {
            return false;
        }
        return $record->update();
    }

    public function insertRecord($formData)
    {
        if(empty($formData)) {
            return false;
        }
        if(!$this->updatePreHandler($formData)) {
            return false;
        }
        if(!$this->validate()) {
            return serialize($this->errors);
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
            return true;
        }

        return $record->delete();
    }
}