<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/17 0017
 * Time: 下午 4:46
 */

namespace backend\modules\crm\models\part_time\record;
use backend\models\interfaces\RecordOperator;
use backend\models\interfaces\DeleteMapRecord;
use backend\models\MyPagination;
use Yii;
use backend\modules\fin\models\account\record\FINAccount;
use yii\db\Exception;
use backend\models\UUID;
use backend\modules\fin\models\FINBaseRecord;
use yii\data\Pagination;

class PartTimeFinAccountMap extends PartTimeBaseRecord implements RecordOperator,DeleteMapRecord
{
    public static function tableName()
    {
        return self::CRMPartTimeReceiveAccountMap;
    }

    public function updateRecord($formData)
    {
        // TODO: Implement updateRecord() method.
    }

    public function updateSingleRecord($formData)
    {
        // TODO: Implement updateSingleRecord() method.
    }

    public function insertRecord($formData)
    {
        // TODO: Implement insertRecord() method.
    }

    public function formDataPreHandler(&$formData, $record)
    {
        $formData['part_time_uuid'] = $formData['object_uuid'];
        unset($formData['object_uuid']);
        if(!isset($formData['uuid']) || empty($formData['uuid'])) {
            $formData['uuid'] = UUID::getUUID();
        }
        $formData['account_uuid'] = $formData['uuid'];
        if(empty($record)) {
            parent::formDataPreHandler($formData, $record);
            $this->clearEmptyField($formData);
        }
    }

    public function insertSingleRecord($formData)
    {
        if(empty($formData) || !isset($formData['object_uuid']) || empty($formData['object_uuid'])) {
            return true;
        }
        if(!parent::updatePreHandler($formData)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            $finAccount = new FINAccount();
            if($finAccount->insertRecord($formData)) {
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

    public function deleteSingleRecord($uuid1, $uuid2)
    {
        if(empty($uuid2) || empty($uuid1)) {
            return true;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $record = self::find()->andWhere([
                'part_time_uuid'=>$uuid1,
                'account_uuid'=>$uuid2
            ])->one();
            if(!empty($record)) {
                if($record->delete()) {
                    $finAccount = new FINAccount();
                    $finAccount->deleteRecord($uuid2);
                }
            }
        }catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    // 根据兼职的uuid获取账户的列表
    public function finAccountList($uuid) {
        if(empty($uuid)) {
            return null;
        }

        $query = self::find()
            ->alias('t1')
            ->select([
                't2.*'
            ])
            ->leftJoin(FINBaseRecord::FINAccount . ' t2','t1.account_uuid=t2.uuid')
            ->andWhere([
                't1.part_time_uuid'=>$uuid
            ]);
        $pagination = new MyPagination([
            'totalCount'=>$query->count(),
            'pageSize'=>self::PageSize,
        ]);
        $list = $query->asArray()->all();
        return [
            'pagination'=>$pagination,
            'list'=>$list,
        ];
    }
}