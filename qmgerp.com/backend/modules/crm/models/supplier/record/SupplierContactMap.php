<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/3 0003
 * Time: 下午 7:16
 */

namespace backend\modules\crm\models\supplier\record;


use backend\models\interfaces\DeleteMapRecord;
use backend\models\interfaces\Map;
use backend\models\interfaces\RecordOperator;
use Yii;
use yii\helpers\Json;
use backend\modules\crm\models\customer\record\Contact;
use yii\db\Exception;
use backend\models\UUID;
class SupplierContactMap extends SupplierBaseRecord implements DeleteMapRecord,RecordOperator
{
    public static function tableName()
    {
        return self::CRMSupplierContactMap;
    }

    public function updateRecord($formData)
    {
        // TODO: Implement updateRecord() method.
    }

    public function recordPreHandler(&$formData, $record = null)
    {
        if(empty($record)) {
            $this->setOldAttribute('supplier_uuid',null);
            $this->setOldAttribute('contact_uuid',null);
            $this->setOldAttribute('type',null);
            $this->setOldAttribute('created_uuid',null);
        } else {

        }
    }

    public function insertRecord($formData)
    {
        if(empty($formData) || empty($formData['supplier_uuid'])) {
            return true;
        }

        if(isset($formData['contactUuids']) && !empty($formData['contactUuids'])) {
            $contactUuids = Json::decode($formData['contactUuids']);
            $transaction = Yii::$app->db->beginTransaction();
            Try{
                foreach($contactUuids as $type => $uuids) {
                    foreach($uuids as $uuid => $index) {
                        $this->insertSingleRecord([
                            'contact_uuid'=>$uuid,
                            'supplier_uuid'=>$formData['supplier_uuid'],
                            'type'=>$type,
                        ]);
                    }
                }

            } catch(Exception $e) {
                $transaction->rollBack();
                throw $e;
                return false;
            }
            $transaction->commit();
        }
    }

    public function insertSingleRecord($formData)
    {
        if(empty($formData) ||
            !isset($formData['supplier_uuid']) || empty($formData['supplier_uuid'])
        || !isset($formData['contact_uuid']) || empty($formData['contact_uuid'])) {
            return true;
        }

        if (!parent::updatePreHandler($formData)) {
            return true;
        }

        return $this->insert();
    }

    public function updateSingleRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }
        $record = self::find()->andWhere([
            'contact_uuid'=>$formData['contact_uuid'],
            'supplier_uuid'=>$formData['supplier_uuid'],
        ])->one();
        if(empty($record) || !parent::updatePreHandler($formData, $record)) {
            return true;
        }

        return $record->update();
    }

    public static function deleteRecordByContactUuid($uuid) {
        return self::deleteAll([
            'contact_uuid'=>$uuid,
        ]);
    }

    public function deleteRecordBySupplierUuid($uuid) {
        if(empty($uuid)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $contact = new Contact();
            $records = self::find()->andWhere(['supplier_uuid'=>$uuid])->asArray()->all();
            self::deleteAll(['supplier_uuid'=>$uuid]);
            foreach ($records as $item) {
                $contact->deleteRecord($item['contact_uuid']);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function insertContact($formData) {
        if(empty($formData)) {
            return false;
        }

        if(!$this->updatePreHandler($formData)) {
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $contact = new Contact();
            if($contact->insertContact($formData)) {
                $this->insert();
            }
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function formDataPreHandler(&$formData, $record)
    {
        if (empty($record)) {
            if(!isset($formData['uuid']) || empty($formData['uuid'])) {
                $formData['uuid'] = UUID::getUUID();
            }
            if(!isset($formData['contact_uuid']) || empty($formData['contact_uuid'])) {
                $formData['contact_uuid'] = $formData['uuid'];
            }
            $this->clearEmptyField($formData);
        }
        if(isset($formData['object_uuid']) && !empty($formData['object_uuid'])) {
            $formData['supplier_uuid'] = $formData['object_uuid'];
            unset($formData['object_uuid']);
        }
        parent::formDataPreHandler($formData, $record); // TODO: Change the autogenerated stub
    }

    public function deleteSingleRecord($uuid1, $uuid2)
    {
        if(empty($uuid1)) {
            return false;
        }

        $record = self::find()->andWhere(['contact_uuid'=>$uuid1, 'supplier_uuid'=>$uuid2])->one();
        if(empty($record)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $record->delete();
            $contact = new Contact();
            $contact->deleteRecord($uuid1);
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function getRecordByUuid($uuid1,$uuid2) {
        if(empty($uuid1)) {
            return null;
        }

        $record = self::find()
            ->alias('t1')
            ->select('*')
            ->leftJoin(self::CRMContact . ' t2', 't1.contact_uuid=t2.uuid')
            ->andWhere(['t1.contact_uuid'=>$uuid1, "t1.supplier_uuid"=>$uuid2])
            ->asArray()->one();
        if(empty($record)) {
            return null;
        }
        if(isset($record['supplier_uuid'])) {
            $record['object_uuid'] = $record['supplier_uuid'];
            unset($record['supplier_uuid']);
        }
        return $record;
    }

    public function updateContact($formData) {
        if(empty($formData)) {
            return false;
        }

        $record = self::find()->andWhere([
            'contact_uuid'=>$formData['uuid'],
            'supplier_uuid'=>$formData['object_uuid']])->one();
        if(empty($record)) {
            return false;
        }

        if(!$this->updatePreHandler($formData, $record)) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            $contact = new Contact();
            $contact->updateContact($formData);
            $record->update();
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }
}