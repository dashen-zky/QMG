<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/15 0015
 * Time: 下午 6:24
 */

namespace backend\modules\daily\models\regulation;


use backend\models\interfaces\DeleteMapRecord;
use backend\models\interfaces\DeleteRecordOperator;
use backend\modules\daily\models\BaseActiveRecord;
use Yii;
use yii\db\Exception;

class RegulationEmployeeMap extends BaseActiveRecord implements DeleteMapRecord,DeleteRecordOperator
{
    public static function tableName() {
        return self::DailyRegulationEmployeeMap;
    }

    public function insertSingleRecord($formData)
    {
        if(empty($formData)) {
            return false;
        }

        if(!$this->updatePreHandler($formData)) {
            return false;
        }

        return $this->insert();
    }

    public function recordPreHandler(&$formData, $record = null)
    {
        if(empty($record)) {
            $this->setOldAttribute('created_uuid', null);
            $this->setOldAttribute('employee_uuid', null);
            $this->setOldAttribute('regulation_uuid', null);
        }
        parent::recordPreHandler($formData, $record); // TODO: Change the autogenerated stub
    }

    public function updateSingleRecord($formData)
    {
        // TODO: Implement updateSingleRecord() method.
    }

    public function deleteSingleRecord($uuid1, $uuid2)
    {
        // TODO: Implement deleteSingleRecord() method.
    }

    public function insertRecord($formData)
    {
        if(empty($formData)) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $employee_uuid_list = explode(',', $formData['employee_uuid_list']);
            foreach($employee_uuid_list as $employee_uuid) {
                $this->insertSingleRecord([
                    'employee_uuid'=>$employee_uuid,
                    'regulation_uuid'=>$formData['regulation_uuid'],
                ]);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
        
        $transaction->commit();
        return true;
    }

    public function updateRecord($formData)
    {
        if(empty($formData)) {
            return false;
        }

        $records = self::find()->select(['employee_uuid'])
            ->andWhere(['regulation_uuid'=>$formData['regulation_uuid']])->asArray()->all();
        $old = $this->getAppointedValue($records, 'employee_uuid');
        if(empty($old) && empty($formData['pointed_uuid'])) {
            return true;
        }
        $new = explode(',', $formData['pointed_uuid']);
        $shouldInsert = array_diff($new, $old);
        $shouldDelete = array_diff($old, $new);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach($shouldDelete as $uuid) {
                $this->deleteAll([
                    'regulation_uuid'=>$formData['regulation_uuid'],
                    'employee_uuid'=>$uuid
                ]);
            }

            foreach($shouldInsert as $uuid) {
                $this->insertSingleRecord([
                    'regulation_uuid'=>$formData['regulation_uuid'],
                    'employee_uuid'=>$uuid,
                ]);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    // 根据规章删除元素
    public function deleteRecord($uuid)
    {
        if(empty($uuid)) {
            return false;
        }

        return $this->deleteAll(['regulation_uuid'=>$uuid]);
    }
}