<?php
namespace backend\modules\wom_data\models;
use backend\models\interfaces\DeleteRecordOperator;
use backend\modules\crm\models\customer\model\ContactForm;
use backend\modules\crm\models\customer\record\Contact;
use backend\modules\crm\models\supplier\record\Supplier;
use backend\modules\crm\models\supplier\record\SupplierContactMap;
use backend\modules\crm\models\supplier\record\SupplierFinAccountMap;
use Yii;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-11-25
 * Time: 上午11:23
 */
class SupplierDataExchange implements DeleteRecordOperator
{
    public function insertRecord($formData)
    {
        if(empty($formData)) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $supplier = new Supplier();
            $supplier->insertRecord([
                'uuid'=>$formData['uuid'],
                'name'=>$formData['name'],
                'description'=>$formData['comment'],
                'created_uuid'=>'wom_data',
            ]);
            $supplierContact = new SupplierContactMap();
            // 同步供应商的联系人
            $contacts = Json::decode($formData['contact_info']);
            if (!empty($contacts)) {
                foreach ($contacts as $item) {
                    $supplierContact->insertContact([
                        'supplier_uuid'=>$formData['uuid'],
                        'name'=>$item['contact_person'],
                        'qq'=>$item['qq'],
                        'weichart'=>$item['weixin'],
                        'office_phone'=>$item['contact_phone'],
                        'type'=>ContactForm::CustomerContact,
                        'created_uuid'=>'wom_data',
                    ]);
                }
            }
            // 同步供应商的银行信息
            $supplierAccount = new SupplierFinAccountMap();
            if(!empty($formData['bank_account'])) {
                $supplierAccount->insertSingleRecord([
                    'object_uuid'=>$formData['uuid'],
                    'name'=>$formData['pay_user'],
                    'bank_of_deposit'=>$formData['bank_name'],
                    'account'=>$formData['bank_account'],
                    'type'=>1,
                    'created_uuid'=>'wom_data',
                ]);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            return $e->getMessage();
        }
        $transaction->commit();
        return true;
    }

    public function updateRecord($formData)
    {
        if(empty($formData)) {
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $supplier = new Supplier();
            $supplier->updateRecord([
                'uuid'=>$formData['uuid'],
                'name'=>$formData['name'],
                'description'=>$formData['comment'],
                'created_uuid'=>'wom_data',
            ]);
            $supplierContact = new SupplierContactMap();
            $supplierContact->deleteRecordBySupplierUuid($formData['uuid']);
            // 同步供应商的联系人
            $contacts = Json::decode($formData['contact_info']);
            if (!empty($contacts)) {
                foreach ($contacts as $item) {
                    $supplierContact->insertContact([
                        'supplier_uuid'=>$formData['uuid'],
                        'name'=>$item['contact_person'],
                        'qq'=>$item['qq'],
                        'weichart'=>$item['weixin'],
                        'office_phone'=>$item['contact_phone'],
                        'type'=>ContactForm::CustomerContact,
                        'created_uuid'=>'wom_data',
                    ]);
                }
            }

            // 同步供应商的银行信息
            $supplierAccount = new SupplierFinAccountMap();
            $supplierAccount->deleteRecordsBySupplierUuid($formData['uuid']);
            if(!empty($formData['bank_account'])) {
                $supplierAccount->insertSingleRecord([
                    'object_uuid'=>$formData['uuid'],
                    'name'=>$formData['pay_user'],
                    'bank_of_deposit'=>$formData['bank_name'],
                    'account'=>$formData['bank_account'],
                    'type'=>1,
                    'created_uuid'=>'wom_data',
                ]);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            return $e->getMessage();
        }

        $transaction->commit();
        return true;
    }

    public function deleteRecord($uuid)
    {
        // TODO: Implement deleteRecord() method.
    }
}