<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3 0003
 * Time: 上午 1:58
 */
namespace backend\modules\hr\models;
use backend\modules\hr\models\HrBaseActiveRecord;
use backend\modules\hr\models\hrinterfaces\HrRecordOperator;
class EmployeeAccount extends HrBaseActiveRecord implements HrRecordOperator
{
    const STATUS_WAIT_ENTRY = 1;// 待入职
    const STATUS_INTERN = 2;
    const STATUS_ACTIVE = 3; // 在职
    const STATUS_LEAVED = 4; // 离职
    const STATUS_TRAINEE = 5; // 实习生
    const InitialPassword = "QM888888";
    static public $tableName = self::EmployeeAccountTableName;
    static public function tableName()
    {
        return self::$tableName;
    }

    public function insertRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }
        // 检查一下这个账号是不是已经存在了
        $record = self::find()
            ->alias('t1')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t2', 't1.em_uuid = t2.uuid')
            ->andWhere(['t1.username'=>$formData['username']])
            ->andWhere(['in', 't2.status', [2,3]])
            ->one();
        if(!empty($record)) {
            return serialize([
                "username"=>
                    [
                        0=>"这个账号已经被使用了,宝宝不开心:-("
                    ]
            ]);
        }
        $this->em_uuid = $formData['uuid'];
        if (!$this->updatePreHandler($formData)) {
            return false;
        }
        $this->password = md5(self::InitialPassword);
        $this->auth_key = md5($this->em_uuid);
        $this->access_token = md5($this->em_uuid);
        return parent::insert();
    }

    public function updateRecord($formData)
    {
        if (empty($formData)) {
            return true;
        }
        $record = self::find()->andWhere(['em_uuid'=>$formData['uuid']])->one();
        if (empty($record)) {
            return $this->insertRecord($formData);
        }
        if (!$this->updatePreHandler($formData, $record)) {
            return true;
        }

        $record->update();
        return true;
    }

    public function dismiss($uuid) {
        if(empty($uuid)) {
            return false;
        }
        $record = self::find()->andWhere(['em_uuid'=>$uuid])->one();
        if(empty($record)) {
            return false;
        }
        
        if($record->status == self::STATUS_LEAVED) {
            return true;
        }
        
        $record->status = self::STATUS_LEAVED;
        return $record->update();
    }
}