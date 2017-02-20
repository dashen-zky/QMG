<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/29 0029
 * Time: 下午 4:29
 */

namespace backend\modules\fin\models\contract;

use backend\models\interfaces\PrimaryTable;
use backend\models\MyPagination;
use backend\models\UUID;
use Yii;
use backend\models\interfaces\RecordOperator;
use backend\modules\fin\models\FINBaseRecord;
use yii\data\Pagination;
use yii\db\Exception;
use yii\web\UploadedFile;

class ContractTemplateRecord extends FINBaseRecord implements RecordOperator,PrimaryTable
{
    public $attachment;
    public $path_dir;
    public static function tableName()
    {
        return self::FinContractTemplate;
    }

    public function formDataPreHandler(&$formData, $record)
    {
        if(!isset($formData['uuid']) || empty($formData['uuid'])) {
            $formData['uuid'] = UUID::getUUID();
        }

        $formData['path_dir'] =
            "/upload/contract-template/".$formData['uuid'];
        if (empty($record)) {
            $this->clearEmptyField($formData);
        }
    }

    public function recordPreHandler(&$formData, $record = null)
    {
        if (!empty($record)) {
            $record->path_dir = $formData['path_dir'];
        } else {
            $this->attachment = $formData['attachment'];
            $this->path_dir = $formData['path_dir'];
            // 设置存放路径
            // iconv('utf-8','gb2312',$record->attachment->baseName)
            $this->path = $this->path_dir . "/" .
                'contract_template' . '.' . $this->attachment->extension;
        }
    }

    public function insertRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }

        if(!parent::updatePreHandler($formData)) {
            return true;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            self::insert();

            // 如果存放的目录不存在，创建目录
            if(!file_exists(Yii::getAlias("@app") . $this->path_dir)) {
                mkdir(Yii::getAlias("@app") . $this->path_dir, 0777, true);
            }
            $this->attachment->saveAs(Yii::getAlias("@app") . $this->path);
        } catch(Exception $e) {
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

        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        $transaction = Yii::$app->db->beginTransaction();
        try{
            if (!empty($record) && parent::updatePreHandler($formData, $record)) {
                $record->update();
            }
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function contractTemplateList() {
        $query = self::find()->select([
            '*'
        ]);
        $pagination = new MyPagination([
            'pageSize'=>self::PageSize,
            'totalCount'=>$query->count(),
        ]);
        $list = $query->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        return [
            'pagination'=>$pagination,
            'list'=>$list,
        ];
    }

    public function deleteRecordByUuid($uuid)
    {
        // TODO: Implement deleteRecordByUuid() method.
    }

    public function getRecordByUuid($uuid)
    {
        if(empty($uuid)) {
            return true;
        }

        return self::find()->andWhere(['uuid'=>$uuid])->asArray()->one();
    }
}