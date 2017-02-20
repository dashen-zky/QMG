<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/15 0015
 * Time: 上午 12:13
 */

namespace backend\modules\hr\models;

use Yii;
use backend\models\UUID;
use backend\modules\hr\models\HrBaseActiveRecord;
use backend\modules\hr\models\hrinterfaces\HrPrimaryTable;
use backend\modules\hr\models\hrinterfaces\HrRecordOperator;
use yii\db\Exception;

class EmployeeFamily extends HrBaseActiveRecord implements HrRecordOperator,HrPrimaryTable
{
    static public function tableName()
    {
        return self::EmployeeFamilyTableName;
    }

    public function updateRecord($formData)
    {
        if(!isset($formData['family']) || empty($formData['family'])) {
            return false;
        }
        $families = $formData['family'];
        $oldFamilyUuids = $families['oldFamilyUuids'];
        unset($families['oldUuids']);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach($families as $row) {
                $family = '';
                if(isset($row['uuid'])) $family = self::find()->andWhere(['uuid'=>$row['uuid']])->one();
                if (!empty($family) && $family !== '') {
                    $oldFamilyUuids = str_replace($row['uuid'],'', $oldFamilyUuids);
                    unset($row['uuid']);
                    $this->updatePreHandler($row,$family);
                    $family->update();
                } elseif(!empty($row['name']) && isset($row['name']) && $row['name'] !== '') {
                    $this->updatePreHandler($row);
                    $this->em_uuid = $formData['uuid'];
                    $this->uuid = UUID::getUUID();
                    $this->setOldAttributes(null);
                    $this->insert();
                }
            }
            if (!empty($oldFamilyUuids)) {
                $oldUuids = explode(" ", trim($oldFamilyUuids));
                foreach($oldUuids as $uuid) {
                    $this->deleteRecordByUuid($uuid);
                }
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        $transaction->commit();
        return true;
    }

    public function insertRecord($formData)
    {
        if(!isset($formData['family']) || empty($formData['family'])) {
            return false;
        }

        foreach ($formData['family'] as $value) {
            if (empty($value['name']) || !isset($value['name']) || $value['name'] === '') {
                continue;
            }
            $this->updatePreHandler($value);
            $this->em_uuid = $formData['uuid'];
            $this->uuid = UUID::getUUID();
            $this->setOldAttributes(null);
            $this->insert();
        }

        return true;
    }
    public function getFamilyListFromEmUuid($em_uuid) {
        return self::find()->andWhere(['em_uuid'=>$em_uuid])->asArray()->all();
    }

    public function deleteRecordByUuid($uuid)
    {
        return $this->deleteAll(['uuid'=>$uuid]);
    }

    public function getRecordByUuid($uuid)
    {
        // TODO: Implement getRecordByUuid() method.
    }
}