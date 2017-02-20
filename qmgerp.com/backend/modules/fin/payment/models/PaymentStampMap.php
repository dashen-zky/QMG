<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/25 0025
 * Time: 下午 12:39
 */

namespace backend\modules\fin\payment\models;


use backend\models\interfaces\DeleteMapRecord;
use backend\models\interfaces\RecordOperator;
use backend\modules\fin\models\FINBaseRecord;
use Yii;
use yii\db\Exception;

class PaymentStampMap extends FINBaseRecord implements DeleteMapRecord,RecordOperator
{
    public static function tableName()
    {
        return self::FINPaymentStampMap;
    }
    
    public function getStampUuidsByPaymentUuid($uuid) {
        if (empty($uuid)) {
            return [];
        }
        
        $records = self::find()->select(['t2.uuid','t2.series_number'])
            ->alias('t1')
            ->leftJoin(self::FINStamp . ' t2', 't2.uuid = t1.stamp_uuid')
            ->andWhere(['payment_uuid'=>$uuid])->asArray()->all();
        return $this->transformForDropDownList($records, 'series_number', 'uuid');
    }
    
    public function getStampListBySupplierUuid($uuid) {
        if (empty($uuid)) {
            return [];
        }

        $list =  self::find()->select([
            't1.*',
            't2.series_number stamp_series_number',
            't2.provider stamp_provider',
            't2.receiver stamp_receiver',
            't2.money stamp_money',
            't3.name checked_name',
        ])->alias('t1')
            ->leftJoin(self::FINStamp . ' t2', 't2.uuid = t1.stamp_uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t3', 't1.checked_uuid = t3.uuid')
            ->leftJoin(self::CRMSupplierPaymentMap . ' t4', 't1.payment_uuid = t4.payment_uuid')
            ->andWhere(['t4.supplier_uuid'=>$uuid])->asArray()->all();
        // 去掉重复的数据
        $tmpContainer = [];
        foreach ($list as $index => $item) {
            if(in_array($item['stamp_series_number'], $tmpContainer)) {
                unset($list[$index]);
                continue;
            }
            
            $tmpContainer[] = $item['stamp_series_number'];
        }
        return $list;
    }
    
    public function getStampListByPaymentUuid($uuid) {
        if (empty($uuid)) {
            return [];
        }

        return self::find()->select([
                    't1.*',
                    't2.series_number stamp_series_number',
                    't2.provider stamp_provider',
                    't2.receiver stamp_receiver',
                    't2.money stamp_money',
                    't3.name checked_name',
                ])->alias('t1')
                ->leftJoin(self::FINStamp . ' t2', 't2.uuid = t1.stamp_uuid')
                ->leftJoin(self::EmployeeBasicInformationTableName . ' t3', 't1.checked_uuid = t3.uuid')
                ->andWhere(['payment_uuid'=>$uuid])->asArray()->all();
    }

    public function checkStamp($formData) {
        if(empty($formData)) {
            return true;
        }

        $paymentUuid = explode(',', trim($formData['payment_uuid'], ' ,'));

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $payment = new Payment();
            foreach ($paymentUuid as $uuid) {
                if(!empty($formData['stamp_uuid'])) {
                    $this->updateRecord([
                        'payment_uuid' => $uuid,
                        'stamp_uuid' => $formData['stamp_uuid'],
                    ]);
                }

                $payment->updateRecord([
                    'uuid'=>$uuid,
                    'stamp_status'=>$formData['stamp_status'],
                    'checked_stamp_uuid'=>Yii::$app->user->getIdentity()->getId(),
                    'checked_stamp_time'=>time(),
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

    public function updateRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }

        $records = self::find()->andWhere(['payment_uuid'=>$formData['payment_uuid']])->asArray()->all();
        $oldStampUuid = $this->getAppointedValue($records,'stamp_uuid');
        $newStampUuid = explode(',', trim($formData['stamp_uuid'], ' ,'));
        $shouldDelete = array_diff($oldStampUuid, $newStampUuid);
        $shouldInsert = array_diff($newStampUuid, $oldStampUuid);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($shouldDelete as $uuid) {
                $this->deleteSingleRecord($formData['payment_uuid'], $uuid);
            }

            foreach ($shouldInsert as $uuid) {
                $data = [
                    'payment_uuid'=>$formData['payment_uuid'],
                    'stamp_uuid'=>$uuid,
                    'checked_time'=>time(),
                    'checked_uuid'=>Yii::$app->user->getIdentity()->getId(),
                ];
                $this->insertSingleRecord($data);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function recordPreHandler(&$formData, $record = null)
    {
        if(empty($record)) {
            $this->setOldAttribute('payment_uuid', null);
            $this->setOldAttribute('stamp_uuid', null);
            $this->setOldAttribute('checked_time', null);
            $this->setOldAttribute('checked_uuid', null);
        }
        parent::recordPreHandler($formData, $record); // TODO: Change the autogenerated stub
    }

    public function insertSingleRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }

        if(!$this->updatePreHandler($formData)) {
            return true;
        }

        return $this->insert();
    }
    
    public function updateSingleRecord($formData)
    {
        // TODO: Implement updateSingleRecord() method.
    }
    
    public function deleteSingleRecord($uuid1, $uuid2)
    {
        if(empty($uuid1) || empty($uuid2)) {
            return false;
        }

        return self::deleteAll([
            'stamp_uuid'=>$uuid1,
            'payment_uuid'=>$uuid2
        ]);
    }
    
    public function insertRecord($formData)
    {
        // TODO: Implement insertRecord() method.
    }
}