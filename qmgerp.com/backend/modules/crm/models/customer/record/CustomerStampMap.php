<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/29 0029
 * Time: 下午 4:54
 */

namespace backend\modules\crm\models\customer\record;


use backend\models\interfaces\DeleteMapRecord;
use backend\models\UUID;
use backend\modules\crm\models\CRMBaseRecord;
use backend\modules\crm\models\stamp\Stamp;
use Yii;
use yii\db\Exception;

class CustomerStampMap extends CRMBaseRecord implements DeleteMapRecord
{
    public static function tableName()
    {
        return self::CRMCustomerStampMap;
    }

    public function deleteSingleRecord($uuid1, $uuid2)
    {
        if(empty($uuid1) || empty($uuid2)) {
            return false;
        }
        $record = self::find()->andWhere([
            'customer_uuid'=>$uuid2,
            'stamp_uuid'=>$uuid1
        ])->one();
        if(empty($record)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            $record->delete();
            $stamp = new Stamp();
            $stamp->deleteRecord($uuid1);
        }catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function formDataPreHandler(&$formData, $record)
    {
        if(empty($record)) {
            if(!isset($formData['uuid']) || empty($formData['uuid'])) {
                $formData['uuid'] = UUID::getUUID();
            }
        }
        if(!isset($formData['stamp_uuid']) || empty($formData['stamp_uuid'])) {
            $formData['stamp_uuid'] = $formData['uuid'];
        }
        $formData['customer_uuid'] = $formData['object_uuid'];
        unset($formData['object_uuid']);
        parent::formDataPreHandler($formData, $record); // TODO: Change the autogenerated stub
    }

    public function insertSingleRecord($formData)
    {
        if(empty($formData)) {
            return false;
        }

        if(!$this->updatePreHandler($formData)) {
            return false;
        }

        if(!$this->validate()) {
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $stamp = new Stamp();
            $_return = $stamp->insertRecord($formData);
            if($_return === true) {
                $this->insert();
            } elseif($_return !== false) {
                return $_return;
            }
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function updateSingleRecord($formData)
    {
        if(empty($formData)) {
            return false;
        }
        $record = self::find()->andWhere(['stamp_uuid'=>$formData['uuid'],'customer_uuid'=>$formData['object_uuid']])->one();
        if(empty($record) || !$this->updatePreHandler($formData, $record)) {
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $stamp = new Stamp();
            $stamp->updateRecord($formData);
            $record->update();
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function stampList($customer_uuid) {
        if(empty($customer_uuid)) {
            return null;
        }

        $list = self::find()
            ->alias('t1')
            ->select("*")
            ->leftJoin(self::CRMStamp . " t2", 't1.stamp_uuid = t2.uuid')
            ->andWhere(['t1.customer_uuid'=>$customer_uuid])
            ->asArray()->all();
        return $list;
    }

    public function getRecordByUuid($uuid1, $uuid2) {
        if(empty($uuid1)) {
            return null;
        }

        $record = self::find()
            ->alias('t1')
            ->select('*')
            ->leftJoin(self::CRMStamp . ' t2', 't1.stamp_uuid=t2.uuid')
            ->andWhere(['t1.stamp_uuid'=>$uuid1, "t1.customer_uuid"=>$uuid2])
            ->asArray()->one();
        if(empty($record)) {
            return null;
        }
        if(isset($record['customer_uuid'])) {
            $record['object_uuid'] = $record['customer_uuid'];
            unset($record['customer_uuid']);
        }
        return $record;
    }
}