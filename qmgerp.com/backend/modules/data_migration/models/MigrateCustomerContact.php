<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/1 0001
 * Time: 上午 10:03
 */

namespace backend\modules\data_migration\models;

use backend\models\UUID;
use backend\modules\crm\models\customer\model\ContactForm;
use backend\modules\crm\models\customer\record\Contact;
use backend\modules\crm\models\customer\record\CustomerContactMap;
use backend\modules\crm\models\customer\record\PublicCustomer;
use yii\db\Connection;
use Yii;
use backend\modules\hr\models\EmployeeAccount;
use yii\db\Exception;

class MigrateCustomerContact
{
    public function migrate()
    {
        return ;
        $db = new Connection([
            'dsn' => 'sqlsrv:Server=114.80.193.17;Database=QMYX',
            'username' => 'QMYX',
            'password' => 'crmSqlMy2016',
            'charset' => 'utf8',
        ]);
        $db->open();
        $contacts = $db->createCommand("select * from contacts")->queryAll();
        $db->close();

        $record = EmployeeAccount::find()->select(['em_uuid'])->andWhere(['username'=>'admin'])->one();
        $admin_uuid = $record->em_uuid;
        
        $contact = new Contact();
        $customerContactMap = new CustomerContactMap();
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            foreach ($contacts as $item) {
                $customer = PublicCustomer::find()->andWhere(['code'=>$item['CustomerID']])->one();
                if(empty($customer)) {
                    continue;
                }
                $uuid = UUID::getUUID();
                if(!$this->insertCustomerContact([
                    'uuid'=>$uuid,
                    'name'=>$item['ContactName'],
                    'position'=>$item['Section'],
                    'address'=>$item['Address'],
                    'code'=>$item['ContactID'],
                    'remarks'=>'1.0 旧数据@@' .
                        '办公电话:'. $item['OfficePhone']."@@".
                        '个人电话:' . $item['MobilePhone'] . "@@" .
                        'qq:' . $item['QQ'] . "@@" .
                        $item['Memo'] . '@@' .
                        '邮箱:' . $item['Email'],
                        'created_uuid'=>$admin_uuid,
                        'is_new_record'=> Contact::old_record,
                ], $contact)) {
                    $transaction->rollBack();
                    return false;
                }

                if(!$this->insertCustomerContactMap([
                    'contact_uuid'=>$uuid,
                    'customer_uuid'=>$customer->uuid,
                    'type'=>ContactForm::CustomerContact,
                    'is_new_record'=> CustomerContactMap::old_record,
                ], $customerContactMap)) {
                    $transaction->rollBack();
                    return false;
                }
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
        
        $transaction->commit();
        return true;
    }

    private function insertCustomerContact($formData, $contact) {
        if(!$contact->updateRecordBuilder($formData, $contact)) {
            return false;
        }

        foreach ($formData as $index => $item) {
            $contact->setOldAttribute($index, null);
        }

        return $contact->insert();
    }

    private function insertCustomerContactMap($formData, $customerContactMap) {
        if(!$customerContactMap->updateRecordBuilder($formData, $customerContactMap)) {
            return false;
        }

        foreach ($formData as $index => $item) {
            $customerContactMap->setOldAttribute($index, null);
        }

        return $customerContactMap->insert();
    }
}