<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/18 0018
 * Time: 下午 3:05
 */

namespace backend\modules\fin\stamp\models;


use backend\models\BaseRecord;
use backend\models\interfaces\DeleteRecordOperator;
use backend\models\UUID;
use Yii;
use yii\db\Exception;
use backend\models\helper\file\UploadFileHelper;
use backend\models\MyPagination;

class Stamp extends BaseRecord implements DeleteRecordOperator
{
    const SeriesValidateError = -1;
    public $aliasMap;

    public function rules()
    {
        return [
            ['series_number', 'unique']
        ];
    }

    public function init()
    {
        $this->aliasMap = [
            'stamp'=>'t1',
            'created'=>'t2',
        ];
        parent::init();
    }

    public static function tableName()
    {
        return self::FINStamp;
    }

    public function formDataPreHandler(&$formData, $record)
    {
        if(empty($record)) {
            $formData['created_time'] = time();
            if(!isset($formData['uuid']) && empty($formData['uuid'])) {
                $formData['uuid'] = UUID::getUUID();
            }
            $formData['enable'] = StampConfig::Enable;
        }
        $this->handlerFormDataTime($formData, 'made_time');
        parent::formDataPreHandler($formData, $record); // TODO: Change the autogenerated stub
    }

    public function recordPreHandler(&$formData, $record = null)
    {
        if(empty($record)) {
            $this->setIsNewRecord(true);
        }
        parent::recordPreHandler($formData, $record); // TODO: Change the autogenerated stub
    }

    public function insertRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }

        if(!$this->updatePreHandler($formData)) {
            return false;
        }

        if(!$this->validate()) {
            return self::SeriesValidateError;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            UploadFileHelper::uploadWhileInsert(
                $this,
                isset($formData['attachment'])?$formData['attachment']:null,
                "/upload/stamp/".$formData['uuid'],
                'attachment',
                true
            );
            $this->insert();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw  $e;
            return false;
        }

        $transaction->commit();
        return true;
    }
    
    public function updateRecord($formData)
    {
        if(empty($formData) || !isset($formData['uuid']) || empty($formData['uuid'])) {
            return true;
        }

        $attachment = $formData['attachment'];
        unset($formData['attachment']);
        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(empty($record) || !$this->updatePreHandler($formData, $record)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            UploadFileHelper::uploadWhileUpdate(
                $record,
                $attachment,
                "/upload/stamp/".$formData['uuid'],
                'attachment'
            );
            if(!empty($attachment)) {
                self::updateAll(
                    [
                        'attachment'=>$record->attachment,
                    ],
                    [
                        'submit_uuid'=>$record->submit_uuid,
                    ]
                );
            }

            $record->update();
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();
        return true;
    }
    
    public function disable($uuid) {
        if(empty($uuid)) {
            return true;
        }
        
        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return true;
        }
        
        $record->enable  = StampConfig::Disable;
        return $record->update();
    }
    
    public function deleteRecord($uuid)
    {
        
    }

    public function stampList($selects, $conditions = null,$fetchOne = false) {
        $aliasMap = $this->aliasMap;
        $selector = [];

        if (!empty($selects)) {
            foreach($aliasMap as $key=>$alias) {
                if (isset($selects[$key])) {
                    foreach($selects[$key] as $select) {
                        if(in_array($key, [
                            'created',
                        ])) {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select . " " . $key . "_" .$select;
                        } elseif ($key === 'stamp') {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select;
                        }
                    }
                }
            }
        }

        $query = self::find()
            ->alias('t1')
            ->select($selector)
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t2', 't1.created_uuid = t2.uuid');

        if(!empty($conditions)) {
            $query->andWhere($conditions);
        }

        if ($fetchOne) {
            $record = $query->asArray()->one();
            return $record;
        }

        $pagination = new MyPagination([
            'totalCount'=>$query->count(),
            'pageSize' => self::PageSize,
        ]);
        $list = $query->orderBy('t1.created_time desc')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data = [
            'pagination' => $pagination,
            'list'=> $list,
        ];
        return $data;
    }

    public function getRecordFromUuid($uuid) {
        if(empty($uuid)) {
            return null;
        }

        $record = $this->stampList(
            [
                'stamp'=>[
                    '*'
                ],
                'created' => [
                    'name',
                ],
            ],
            [
                '=',
                $this->aliasMap['stamp'] . '.uuid',
                $uuid
            ],
            true
        );
        return $record;
    }

    public function deleteAttachment($uuid, $path) {
        if(empty($uuid) || empty($path)) {
            return false;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return false;
        }

        $attachments = unserialize($record->attachment);
        foreach($attachments as $index => $item) {
            if($item === $path) {
                unset($attachments[$index]);
                break;
            }
        }

        $record->attachment = serialize($attachments);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $realPath = Yii::getAlias("@app").iconv("UTF-8", "GBK", $path);
            if(file_exists($realPath)) {
                unlink($realPath);
            }
            self::updateAll(
                [
                    'attachment'=>$record->attachment
                ],
                [
                    'submit_uuid'=>$record->submit_uuid
                ]
            );
            $record->update();
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }
    
    public function getRecordByCondition($condition) {
        return self::find()->andWhere($condition)->one();
    }
}