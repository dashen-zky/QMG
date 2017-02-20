<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/1 0001
 * Time: 下午 12:01
 */

namespace backend\modules\data_migration\models;

use backend\modules\crm\models\project\record\ProjectCustomerMap;
use yii\db\ColumnSchema;
use yii\db\Connection;
use backend\modules\hr\models\EmployeeAccount;
use backend\modules\crm\models\customer\record\Contact;
use backend\modules\crm\models\project\record\Project;
use backend\modules\crm\models\project\record\ProjectContactMap;
use Yii;
use backend\models\UUID;
use backend\modules\crm\models\customer\record\PublicCustomer;
class MigrateProject extends Migrate
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
        $projects = $db->createCommand("select * from projects")->queryAll();
        $db->close();

        $record = EmployeeAccount::find()->select(['em_uuid'])->andWhere(['username'=>'admin'])->one();
        $admin_uuid = $record->em_uuid;

        $project = new Project();
        $projectContact = new ProjectContactMap();
        $projectCustomerMap = new ProjectCustomerMap();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($projects as $item) {
                $customer = PublicCustomer::find()->andWhere(['code'=>$item['CustomerID']])->one();
                if(empty($customer)) {
                    continue;
                }

                $project_uuid = UUID::getUUID();
                $project->handlerFormDataTime($item, 'CreatTime');
                $project->handlerFormDataTime($item, 'StartTime');
                $project->handlerFormDataTime($item, 'EndTime');

                if(!$this->insertRecord([
                    'uuid'=>$project_uuid,
                    'name'=>$item['ProjectName'],
                    'actual_money_amount'=>$item['ContractMoney'],
                    'create_time'=>$item['CreatTime'],
                    'start_time'=>$item['StartTime'],
                    'end_time'=>$item['EndTime'],
                    'code'=>$item['ProjectID'],
                    'description'=>'1.0 旧数据@@' .
                        '备注:'. $item['Memo'],
                    'created_uuid'=>$admin_uuid,
                    'enable'=>Project::Enable,
                    'is_new_record'=> Project::old_record,
                ], $project)) {
                    $transaction->rollBack();
                    return false;
                }

                if(!$this->insertRecord([
                    'project_uuid'=>$project_uuid,
                    'customer_uuid'=>$customer->uuid,
                    'created_uuid'=>$admin_uuid,
                    'is_new_record'=>ProjectContactMap::old_record,
                ], $projectCustomerMap)) {
                    $transaction->rollBack();
                    return false;
                }

                if(empty($item['ContactID'])) {
                    continue;
                }

                $contact = Contact::find()->andWhere(['code'=>$item['ContactID']])->one();
                if(empty($contact)) {
                    continue;
                }

                if(!$this->insertRecord([
                    'project_uuid'=>$project_uuid,
                    'contact_uuid'=>$contact->uuid,
                    'created_uuid'=>$admin_uuid,
                    'duty'=>ProjectContactMap::ProjectContact,
                    'is_new_record'=>ProjectContactMap::old_record,
                ], $projectContact)) {
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

    private function insertRecord($formData, $record) {
        if(!$record->updateRecordBuilder($formData)) {
            return false;
        }

        foreach ($formData as $index => $item) {
            $record->setOldAttribute($index, null);
        }
//var_dump($record);die;
        return $record->insert();
    }
}