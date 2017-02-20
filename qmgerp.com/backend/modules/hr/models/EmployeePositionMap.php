<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/8 0008
 * Time: 下午 6:36
 */

namespace backend\modules\hr\models;

use Yii;
use backend\modules\hr\models\HrBaseActiveRecord;
use backend\modules\hr\models\hrinterfaces\HrRecordOperator;
use yii\db\Exception;
use yii\helpers\Json;

class EmployeePositionMap extends HrBaseActiveRecord implements HrRecordOperator
{
    static public function tableName()
    {
        return self::EmployeePositionMapTableName;
    }

    public function insertRecord($formData)
    {
        $this->em_uuid = $formData['uuid'];
        if (!isset($formData['position_uuid'])) {
            return ;
        }
        if(!$this->updatePreHandler($formData)) {
            return ;
        }
        $position_uuids = explode(',',$this->position_uuid);
        foreach($position_uuids as $position_uuid) {
            if(!empty($position_uuid)) {
                $this->setOldAttribute('em_uuid',null);
                $this->setOldAttribute('position_uuid',null);
                $this->position_uuid = $position_uuid;
                parent::insert();
            }
        }
    }

    public function updateRecord($formData)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->deleteAll(['em_uuid'=>$formData['uuid']]);
            $this->insertRecord($formData);

        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        $transaction->commit();
    }

    public function dismiss($employee_uuid) {
        if (empty($employee_uuid)) {
            return true;
        }

        $records = self::find()->andWhere(['em_uuid'=>$employee_uuid])->asArray()->all();
        if (empty($records)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $position = new Position();
            foreach ($records as $record) {
                $position->dismiss($record['position_uuid']);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }
}