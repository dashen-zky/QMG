<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-12-9
 * Time: 下午8:51
 */

namespace backend\modules\hr\recruitment\models;


use backend\models\BaseRecord;
use backend\models\interfaces\DeleteRecordOperator;
use Yii;
use yii\db\Exception;
use backend\models\helper\file\UploadFileHelper;
use backend\models\MyPagination;

class Candidate extends BaseRecord implements DeleteRecordOperator
{
    public static $aliasMap = [
        'candidate'=>'t1',
    ];
    public static function tableName()
    {
        return self::HrCandidate;
    }

    public function formDataPreHandler(&$formData, $record)
    {
        $formData['update_time'] = time();
        parent::formDataPreHandler($formData, $record);
    }

    public function insertRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }

        if (!$this->updatePreHandler($formData)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            UploadFileHelper::uploadWhileInsert(
                $this,
                isset($formData['attachment'])?$formData['attachment']:null,
                "/upload/candidate/".$formData['uuid'],
                'resume'
            );
            $this->insert();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        } catch (\yii\base\Exception $e) {
            $transaction->rollBack();
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

        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if (empty($record) || !$this->updatePreHandler($formData, $record)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            UploadFileHelper::uploadWhileUpdate(
                $record,
                isset($formData['attachment'])?$formData['attachment']:null,
                "/upload/candidate/".$formData['uuid'],
                'resume'
                );
            $record->update();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw  $e;
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function deleteRecord($uuid)
    {
        // TODO: Implement deleteRecord() method.
    }

    public function getRecord($uuid) {
        return $this->candidateList([
            'candidate'=>[
                '*'
            ]
        ], [
            '=',
            self::$aliasMap['candidate'] . '.uuid',
            $uuid
        ], true);
    }

    public function validatePhone($phone) {
        if(empty($phone)) {
            return 1;
        }

        $record = self::find()->andWhere(['phone'=>$phone])->asArray()->one();
        if (empty($record)) {
            return 1;
        }

        return $record['uuid'];
    }

    public function candidateList($selects, $conditions = null,$fetchOne = false) {
        $selector = [];

        if (!empty($selects)) {
            foreach(self::$aliasMap as $key=>$alias) {
                if (isset($selects[$key])) {
                    foreach($selects[$key] as $select) {
                        if ($key === 'candidate') {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select;
                        } else {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select . " " . $key . "_" .$select;
                        }
                    }
                }
            }
        }

        $query = self::find()
            ->alias('t1')
            ->select($selector);
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
        $list = $query->orderBy([
            't1.id' => SORT_DESC,
        ])->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        return [
            'pagination' => $pagination,
            'list'=> $list,
        ];
    }

    public function listFilter($filter) {
        if(empty($filter)) {
            return $this->candidateList([
                'candidate'=>[
                    '*'
                ]
            ], [
                '<>',
                self::$aliasMap['candidate'] . '.location',
                CandidateConfig::LocateBlackList,
            ]);
        }

        $map = [
            'name'=>[
                'like',
                self::$aliasMap['candidate'] . '.name'
            ],
            'position'=>[
                'like',
                self::$aliasMap['candidate'] . '.position',
            ],
            'phone'=>[
                'like',
                self::$aliasMap['candidate'] . '.phone',
            ],
            'email'=>[
                'like',
                self::$aliasMap['candidate'] . '.email',
            ],
        ];

        $condition = [
            'and',
            [
                '<>',
                self::$aliasMap['candidate'] . '.location',
                CandidateConfig::LocateBlackList,
            ]
        ];
        foreach ($filter as $index => $value) {
            $condition[] = [
                $map[$index][0],
                $map[$index][1],
                trim($value),
            ];
        }

        return $this->candidateList(
            [
                'candidate'=>[
                    '*'
                ]
            ],
            $condition
        );
    }

    public function blackListFilter($filter) {
        if(empty($filter)) {
            return $this->candidateList(
            [
                'candidate'=>[
                    '*'
                ]
            ],
            [
                '=',
                self::$aliasMap['candidate'] . '.location',
                CandidateConfig::LocateBlackList,
            ]
            );
        }

        $map = [
            'name'=>[
                'like',
                self::$aliasMap['candidate'] . '.name'
            ],
            'position'=>[
                'like',
                self::$aliasMap['candidate'] . '.position',
            ],
            'phone'=>[
                'like',
                self::$aliasMap['candidate'] . '.phone',
            ],
            'email'=>[
                'like',
                self::$aliasMap['candidate'] . '.email',
            ],
        ];

        $condition = [
            'and',
            [
                '=',
                self::$aliasMap['candidate'] . '.location',
                CandidateConfig::LocateBlackList,
            ]
        ];
        foreach ($filter as $index => $value) {
            $condition[] = [
                $map[$index][0],
                $map[$index][1],
                trim($value),
            ];
        }

        return $this->candidateList(
            [
                'candidate'=>[
                    '*'
                ]
            ],
            $condition
        );
    }

    public function deleteAttachment($uuid, $path) {
        if(empty($uuid) || empty($path)) {
            return false;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return false;
        }

        $attachments = unserialize($record->resume);
        foreach($attachments as $index => $item) {
            if($item === $path) {
                unset($attachments[$index]);
                break;
            }
        }

        $record->resume = serialize($attachments);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $realPath = Yii::getAlias("@app").iconv("UTF-8", "GBK", $path);
            if(file_exists($realPath)) {
                unlink($realPath);
            }

            $record->update();
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }
}