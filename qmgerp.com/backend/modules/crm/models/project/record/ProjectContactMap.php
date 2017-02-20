<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/1 0001
 * Time: 上午 10:30
 */

namespace backend\modules\crm\models\project\record;

use backend\models\interfaces\DeleteMapRecord;
use backend\models\interfaces\Map;
use backend\models\interfaces\RecordOperator;
use Yii;
use yii\db\Exception;

class ProjectContactMap extends ProjectBaseRecord implements Map,RecordOperator
{
    const ProjectDuty = 2;
    const ProjectContact = 1;
    static public function tableName()
    {
        return self::CRMProjectContactMap;
    }

    // 在跟新插入数据之前，对自己的一些调整
    public function recordPreHandler(&$formData, $record = null)
    {
        if(empty($record)) {
            $this->setOldAttribute('project_uuid',null);
            $this->setOldAttribute('contact_uuid',null);
            $this->setOldAttribute('created_uuid',null);
            $this->setOldAttribute('duty',null);
        }
    }

    public function formDataPreHandler(&$formData, $record)
    {
        parent::formDataPreHandler($formData, $record); // TODO: Change the autogenerated stub
    }

    public function insertRecord($formData)
    {
        if (empty($formData) || !isset($formData['project_uuid']) || empty($formData['project_uuid'])) {
            return true;
        }
//        $contact_uuids = $this->handlerUuidString($formData['contact_uuid']);
        $contact_uuids = parent::getDistinctValueAsArray(explode(",", $formData['contact_uuid']));
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($contact_uuids as $index => $uuid) {
                $data = [
                    'project_uuid'=>$formData['project_uuid'],
                    'contact_uuid'=>$uuid,
                    'duty'=>$formData['duty']
                ];
                if (parent::updatePreHandler($data)) {
                    $this->insert();
                }
            }
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function insertSingleRecord($formData)
    {
        if(empty($formData) || !isset($formData['project_uuid']) || empty($formData['project_uuid'])) {
            return true;
        }

        if(!parent::updatePreHandler($formData)) {
            return false;
        }
        return $this->insert();
    }

    public function updateRecord($formData)
    {
        if(empty($formData) || !isset($formData['project_uuid']) || empty($formData['project_uuid'])) {
            return true;
        }
        $this->project_uuid = $formData['project_uuid'];
        $records = self::find()->andWhere([
            'project_uuid'=>$formData['project_uuid'],
            'duty'=>$formData['duty'],
        ])->asArray()->all();
        $oldContactUuids = $this->getAppointedValue($records,'contact_uuid');
        $newContactUuids = $this->getDistinctValueAsArray(explode(",",$formData['contact_uuid']));
        // 找出修改前后数据差集

        $shouldDeletes = array_diff($oldContactUuids,$newContactUuids);
        $shouldInserts = array_diff($newContactUuids,$oldContactUuids);
        $transaction = Yii::$app->db->beginTransaction();

        try{
            //删除在前台页面删除的数据
            foreach($shouldDeletes as $shouldDelete) {
                $this->deleteAll([
                    'project_uuid'=>$formData['project_uuid'],
                    'contact_uuid'=>$shouldDelete,
                    'duty'=>$formData['duty'],
                ]);
            }
            //插入在前台页面新增的数据
            foreach($shouldInserts as $shouldInsert) {
                $this->insertSingleRecord([
                    'contact_uuid'=>$shouldInsert,
                    'duty'=>$formData['duty'],
                    'project_uuid'=>$formData['project_uuid'],
                ]);
            }
        }catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function updateSingleRecord($formData)
    {
        // TODO: Implement updateSingleRecord() method.
    }

    public function handlerUuidString($uuidString) {
        $uuids = array_unique(explode(',',$uuidString));
        foreach($uuids as $index => $uuid) {
            if (empty($uuid)) {
                unset($uuids[$index]);
            }
        }
        return $uuids;
    }

    public static function deleteRecordByContactUuid($uuid) {
        return self::deleteAll([
            'contact_uuid'=>$uuid,
        ]);
    }

}