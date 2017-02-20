<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/26 0026
 * Time: 下午 8:15
 */

namespace backend\modules\crm\models\project\record;


use backend\models\interfaces\Map;
use backend\modules\fin\accountReceivable\models\AccountReceivable;
use Yii;
use yii\db\Exception;

class ProjectAccountReceivableMap extends ProjectBaseRecord implements Map
{
    public static function tableName()
    {
        return self::CRMProjectAccountReceivable;
    }

    public function receiveMoney($formData) {
        if(empty($formData)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->insertSingleRecord($formData);
            (new AccountReceivable())->receiveMoney([
                'uuid'=>$formData['account_receivable_uuid'],
                'distributed_money'=>$formData['money'],
            ]);
            (new Project())->receiveMoney([
                'uuid'=>$formData['project_uuid'],
                'received_money'=>$formData['money']
            ]);
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function getReceiveRecordListByProjectUuid($uuid) {
        if(empty($uuid)) {
            return null;
        }

        $query = self::find()
            ->alias('t1')
            ->select([
                't1.id t1_id',
                't1.money paied_money',
                't2.*',
                't3.name created_name',
            ])
            ->leftJoin(self::FINAccountReceivable . ' t2', 't1.account_receivable_uuid = t2.uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t3', 't2.created_uuid = t3.uuid')
            ->andWhere(['t1.project_uuid'=>$uuid]);
        // 防止重复数据被过滤,indexBy 如果数值相同，这会被过滤掉
        $query->indexBy('t1_id');
        return $query->asArray()->all();
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
}