<?php
namespace backend\modules\data_migration\models;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/31 0031
 * Time: 下午 5:46
 */
use backend\models\UUID;
use backend\modules\crm\models\customer\record\PublicCustomer;
use backend\modules\hr\models\EmployeeAccount;
use yii\db\Connection;
use Yii;
use yii\db\Exception;

class MigrateCustomer extends Migrate
{
    public function migrate() {
        return ;
        $db = new Connection([
            'dsn' => 'sqlsrv:Server=114.80.193.17;Database=QMYX',
            'username' => 'QMYX',
            'password' => 'crmSqlMy2016',
            'charset' => 'utf8',
        ]);
        $db->open();
        $customers = $db->createCommand("select * from customers")->queryAll();
        $db->close();

        $publicCustomer = new PublicCustomer();
        $record = EmployeeAccount::find()->select(['em_uuid'])->andWhere(['username'=>'admin'])->one();
        $admin_uuid = $record->em_uuid;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($customers as $item) {
                $formData = [
                    'code'=>$item['CustomerID'],
                    'name'=>$item['CompanyName'],
                    'full_name'=>$item['CompanyName'],
                    'address'=>$item['Address'],
                    'uuid'=>UUID::getUUID(),
                    'public_tag'=>PublicCustomer::publicTag,
                    'enable'=>PublicCustomer::Enable,
                    'time'=>time(),
                    'remarks'=>$item['Memo'],
                    'created_uuid'=>$admin_uuid,
                    'em_uuid'=>$admin_uuid,
                    'is_new_record'=> PublicCustomer::old_record,
                    'description'=>$item['CompanySummary'],
                    'remarks'=>'1.0 老数据@@' .
                        '客户等级:' . $item['CustomerLever'] . '@@' .
                        '客户类别:' . $item['CustomerPro'] . '@@' .
                        '客户行业:' . $item['CustomerIndustry'] . '@@' .
                        '客户来源:' . $item['CustomerRef'] . '@@' .
                        '城市:' . $item['City'] . '@@' .
                        '备注:' . $item['Memo'],
                ];
                if(!$this->insertPublicCustomer($formData, $publicCustomer)) {
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

    private function insertPublicCustomer($formData, $publicCustomer) {
        if(!$publicCustomer->updateRecordBuilder($formData, $publicCustomer)) {
            return false;
        }

        foreach ($formData as $index => $item) {
            $publicCustomer->setOldAttribute($index, null);
        }

        return $publicCustomer->insert();
    }
}