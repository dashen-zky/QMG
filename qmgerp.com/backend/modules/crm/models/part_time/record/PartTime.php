<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/15 0015
 * Time: 下午 9:06
 */

namespace backend\modules\crm\models\part_time\record;


use backend\models\interfaces\RecordOperator;
use backend\models\UUID;
use backend\modules\crm\models\part_time\model\PartTimeConfig;
use backend\modules\crm\models\part_time\model\PartTimeForm;
use backend\modules\crm\models\supplier\record\SupplierContactMap;
use Yii;
use yii\db\Exception;
use yii\helpers\Json;
use backend\models\MyPagination;
use backend\modules\rbac\model\PermissionManager;
class PartTime extends PartTimeBaseRecord implements RecordOperator
{
    public $path_dir;
    public $attachment;
    public static function tableName()
    {
        return self::CRMPartTime;
    }

    /**
     * 判断这个供应商是不是可以审核，
     * 首先，这个供应商要存在才可以审核
     * 其次，这个供应上要有联系人来可以审核
     * 再次，这个供应商要有收款账号才可以审核
     * 最后，要有权限的人才可以审核
     */
    public static function canAssess($uuid) {
        if(empty($uuid)) {
            return false;
        }

        $record = PartTimeFinAccountMap::find()->andWhere(['part_time_uuid'=>$uuid])->one();
        if(empty($record)) {
            return false;
        }

        return Yii::$app->authManager->canAccess(PermissionManager::SupplierAndPartTimeAccess);
    }

    public function updateRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }
        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(empty($record) || !$this->updatePreHandler($formData, $record)) {
            return true;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(isset($record->attachment) && !empty($record->attachment)) {
                $dir = Yii::getAlias('@app') . $record->path_dir;
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                $paths = unserialize($record->path);
                foreach ($record->attachment as $index => $item) {
                    // 判断是否后重名的文件
                    $tail = '';
                    if(isset($paths[$item->baseName . '.' . $item->extension])) {
                        $tail = rand(0, 1000);
                        $path = $dir . "/" . iconv("UTF-8", "GBK", $item->baseName . $tail)
                            . "." . $item->extension;
                    } else {
                        $path = $dir . "/" . iconv("UTF-8", "GBK", $item->baseName)
                            . "." . $item->extension;
                    }
                    $item->saveAs($path);
                    // 将文件尾加上，在文件重名的时候需要用到
                    $baseName = $item->baseName.$tail;
                    $paths[$baseName . '.' . $item->extension] =
                        $record->path_dir . "/" . $baseName
                        . "." . $item->extension;
                }
                $record->path = serialize($paths);
            }
            $record->update();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function formDataPreHandler(&$formData, $record)
    {
        if(empty($record)) {
            if(!isset($formData['uuid']) || empty($formData['uuid'])) {
                $formData['uuid'] = UUID::getUUID();
            }
        }
        parent::formDataPreHandler($formData, $record);
        // 如果manager_uuid不为空的话，表示已经被分配了
        if(isset($formData['manager_uuid'])
            && !empty($formData['manager_uuid'])
            && $formData['manager_uuid'] != 0) {
            $formData['allocate'] = PartTimeConfig::Allocated;
        }
    }

    public function recordPreHandler(&$formData, $record = null)
    {
        if(empty($record)) {
            if(isset($formData['attachment'])&&!empty($formData['attachment'])) {
                foreach($formData['attachment'] as $index => $item) {
                    $this->attachment[] = $item;
                }
                $this->path_dir = '/upload/part_time/'.$formData['uuid'];
            }
        } else {
            if(isset($formData['attachment'])&&!empty($formData['attachment'])) {
                foreach($formData['attachment'] as $index => $item) {
                    $record->attachment[] = $item;
                }
                $record->path_dir = '/upload/part_time/'. $formData['uuid'];
            }
        }
    }

    public function insertRecord($formData)
    {
        if(empty($formData) || empty($formData['name'])) {
            return true;
        }

        if(!$this->updatePreHandler($formData)) {
            return true;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            if(isset($this->attachment) && !empty($this->attachment)) {
                $dir = Yii::getAlias('@app').$this->path_dir;
                if(!file_exists($dir)) {
                    mkdir($dir,0777,true);
                }
                $paths = [];
                foreach($this->attachment as $index => $item) {
                    $path = $dir . "/" .   iconv("UTF-8", "GBK", $item->baseName)
                        . "." . $item->extension;
                    $item->saveAs($path);
                    $paths[$item->baseName . '.' . $item->extension] =
                        $this->path_dir . "/" .   $item->baseName
                        . "." . $item->extension;
                }
                $this->path = serialize($paths);
            }
            $this->insert();
            // 将code+1
            $partTimeConfig = new PartTimeConfig();
            $config = $partTimeConfig->generateConfig();
            $config['code'] = $formData['code'] + 1;
            $partTimeConfig->updateDateConfigByJsonString(Json::encode($config));
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }

    //  我管理的供应商,
    // manager是我管理的供应商
    // created是我创建的供应商
    public function myPartTimeList($filed) {
        // 既不是创建者，又不是管理者，其他字符串不认
        if(empty($filed) || ($filed !== 'created' && $filed !== 'manager')) {
            return [];
        }

        $userId = Yii::$app->user->getIdentity()->getId();
        $userName = Yii::$app->user->getIdentity()->getUserName();

        $condition = [];
        $map = [
            'created'=>'created_uuid',
            'manager'=>'manager_uuid',
        ];

        if($filed === 'created') {
            $condition = [
                'part_time'=> [
                    $map[$filed] => [
                        '=',
                        $userId,
                    ]
                ]
            ];
        } else {
            $condition = null;
        }

        $list = $this->partTimeList(
            [
                'part_time'=>[
                    '*'
                ],
                'manager'=>[
                    'name',
                    'uuid',
                ]
            ],
            $condition
        );
        return $list;
    }

    public function listFilter($filter) {
        if(empty($filter)) {
            return $this->myPartTimeList('manager');
        }
        // 因为编码的前缀没有存入数据库，所以要对编码进行处理一下
        if(isset($filter['code']) && !empty($filter['code'])) {
            preg_match('/([a-zA-Z]*)(\d+)/',$filter['code'],$match);
            if($match[1] === PartTimeForm::codePrefix) {
                $filter['code'] = $match[2];
            }
        }

        $condition = [];
        foreach($filter as $key => $value) {
            if($key === 'name' || $key === 'code') {
                $condition['part_time'][] = $key . " like '%" . $value . "%'";
            } else {
                $condition['part_time'][] = $key . " = '" . $value . "'";
            }
        }

        $list = $this->partTimeList(
            [
                'part_time'=>[
                    '*'
                ],
                'manager'=>[
                    'name',
                    'uuid',
                ]
            ],
            $condition
        );
        return $list;
    }

    public function partTimeList($selects, $conditions = null, $enablePage = true) {
        $aliasMap = [
            'part_time'=>'t1',
            'manager'=>'t2',
        ];
        $selector = [];

        if (!empty($selects)) {
            foreach($aliasMap as $key=>$alias) {
                if (isset($selects[$key])) {
                    foreach($selects[$key] as $select) {
                        $select = trim($select);
                        if ($key === 'part_time') {
                            $selector[] = $alias ."." . $select;
                        } else {
                            $selector[] = $alias ."." . $select . " " . $key . "_" .$select;
                        }
                    }
                }
            }
        }

        $query = self::find()
            ->alias('t1')
            ->select($selector)
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t2', 't1.manager_uuid=t2.uuid');

        //
        if(!empty($conditions)) {
            foreach($aliasMap as $key=>$alias) {
                if (isset($conditions[$key])) {
                    foreach($conditions[$key] as $k => $condition) {
                        if(!is_array($condition)) {
                            $condition = trim($condition);
                            $query->andWhere(
                                $alias . "." . $condition
                            );
                            continue;
                        }

                        $query->andWhere([
                            $condition[0],
                            $alias . "." . $k,
                            $condition[1],
                        ]);
                    }
                }
            }
        }

        if(!$enablePage) {
            return $query->asArray()->one();
        }

        $pagination = new MyPagination([
            'totalCount'=>$query->count(),
            'pageSize' => self::PageSize,
        ]);
        $list = $query->orderBy('code')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data = [
            'pagination' => $pagination,
            'list'=> $list,
        ];
        return $data;
    }

    public function getRecordByUuid($uuid) {
        $record = $this->partTimeList(
            [
                'part_time'=>[
                    '*'
                ]
            ],
            [
                'part_time'=>[
                    'uuid="'.$uuid.'"',
                ]
            ],
            false
        );
        // 处理记录的path字段
        $record['path'] = unserialize($record['path']);
        return $record;
    }

    public function deleteAttachment($uuid = null, $path = null) {
        if(empty($uuid) || empty($path)) {
            return false;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return false;
        }

        $paths = unserialize($record->path);
        foreach($paths as $index => $item) {
            if($item === $path) {
                unset($paths[$index]);
                break;
            }
        }

        $record->path = serialize($paths);
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