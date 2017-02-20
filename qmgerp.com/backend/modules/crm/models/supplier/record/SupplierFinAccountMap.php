<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/9 0009
 * Time: 下午 3:03
 */

namespace backend\modules\crm\models\supplier\record;

use backend\models\interfaces\DeleteMapRecord;
use backend\models\MyPagination;
use backend\modules\fin\models\account\record\FINAccount;
use backend\modules\fin\models\FINBaseRecord;
use Yii;
use backend\models\interfaces\RecordOperator;
use backend\models\UUID;
use yii\data\Pagination;
use yii\db\Exception;

class SupplierFinAccountMap extends SupplierBaseRecord implements RecordOperator,DeleteMapRecord
{
    public static function tableName()
    {
        return self::CRMSupplierReceiveAccountMap;
    }

    public function updateRecord($formData)
    {
        // TODO: Implement updateRecord() method.
    }

    public function insertRecord($formData)
    {

    }

    public function formDataPreHandler(&$formData, $record)
    {
        $formData['supplier_uuid'] = $formData['object_uuid'];
        unset($formData['object_uuid']);
        if(!isset($formData['uuid']) || empty($formData['uuid'])) {
            $formData['uuid'] = UUID::getUUID();
        }
        $formData['account_uuid'] = $formData['uuid'];
        if(empty($record)) {
            $this->clearEmptyField($formData);
        }
        parent::formDataPreHandler($formData, $record);
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

    public function updateSingleRecord($formData)
    {
        // TODO: Implement updateSingleRecord() method.
    }

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
                't1.supplier_uuid'=>$uuid
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

    public function deleteSingleRecord($uuid1, $uuid2)
    {
        if(empty($uuid2) || empty($uuid1)) {
            return true;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $record = self::find()->andWhere([
                'supplier_uuid'=>$uuid1,
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

    public function deleteRecordsBySupplierUuid($uuid) {
        if(empty($uuid)) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $finAccount = new FINAccount();
            $records = self::find()->andWhere(['supplier_uuid'=>$uuid])->asArray()->all();
            self::deleteAll(['supplier_uuid'=>$uuid]);
            foreach ($records as $item) {
                $finAccount->deleteRecord($item['account_uuid']);
            }

        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }
}