<?php
namespace backend\modules\daily\models\regulation;
use backend\models\interfaces\DeleteRecordOperator;
use backend\modules\daily\models\BaseActiveRecord;
use backend\models\MyPagination;
use backend\models\UUID;
use Yii;
use yii\db\Exception;

class Regulation extends BaseActiveRecord implements DeleteRecordOperator
{
    public $attachment;
    public $path_dir;

    private $aliasMap =[
        'regulation'=>'t1',
        'regulation_employee'=>'t2',
        'pointed'=>'t3', // 那些人可见
        'created'=>'t4', // 创建人
        'update'=>'t5', // 最后修改人
        'regulation_editor_map'=>'t6',
        'editor'=>'t7',
    ];
    public static function typeList() {
        $config = new RegulationConfig();
        return $config->getList('type');
    }

    public static function getType($index) {
        $list = self::typeList();
        return isset($list[$index])?$list[$index]:'';
    }

    public static function tableName()
    {
        return self::DailyRegulation;
    }

    public static function canDisable($created_uuid, $enable) {
        if ($enable != self::Enable) {
            return false;
        }

        if ($created_uuid == Yii::$app->getUser()->getId()) {
            return true;
        }

        if (Yii::$app->getUser()->getIdentity()->getUserName() == 'admin') {
            return true;
        }

        return false;
    }

    public function deleteRecord($uuid)
    {
        if(empty($uuid)) {
            return false;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            if(!empty($record->path)) {
                $paths = unserialize($record->path);
                $len = count($paths);
                $i = 1;
                foreach($paths as $item) {
                    $path = Yii::getAlias("@app") .iconv("UTF-8", "GBK", $item);
                    if(file_exists($path)) {
                        unlink($path);
                        if($i == $len) {
                            preg_match('/[\/\\\](\w+[\/\\\])+/', $item, $match);
                            $dir = Yii::getAlias('@app').iconv("UTF-8", "GBK", $match[0]);
                            rmdir($dir);
                        }
                    }
                    $i++;
                }
            }

            $regulationEmployeeMap = new RegulationEmployeeMap();
            $regulationEmployeeMap->deleteRecord($uuid);

            $regulationEditorMap = new RegulationEditorMap();
            $regulationEditorMap->deleteRecord($uuid);
            $record->delete();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function getRecordByUuid($uuid) {
        if(empty($uuid)) {
            return false;
        }
        $record = $this->regulationList(
            [
                'regulation'=>[
                    '*'
                ],
                'created'=>[
                    'name',
                ],
                'update'=>[
                    'name',
                ],
                'pointed'=>[
                    'name',
                    'uuid',
                ],
                'editor'=>[
                    'name',
                    'uuid',
                ]
            ],
            [
                '=',
                $this->aliasMap['regulation'] .'.uuid',
                $uuid,
            ],
            true
        );
        $this->handlerFieldForShow($record);
        return $record;
    }

    //处理重复的字段，因为在group_concat查询出来，会出现一些重复的内容，将其处理一下
    protected function handlerFieldForShow(&$record) {
        $record['pointed'] = $this->filterRepeatFieldAsArray($record['pointed_uuid'], $record['pointed_name']);
        $record['editor'] = $this->filterRepeatFieldAsArray($record['editor_uuid'], $record['editor_name']);
        $record['pointed_uuid'] = empty($record['pointed'])?'':implode(',', array_keys($record['pointed']));
        $record['pointed_name'] = empty($record['pointed'])?'':implode(',', $record['pointed']);
        $record['editor_uuid'] = empty($record['editor'])?'':implode(',', array_keys($record['editor']));
        $record['editor_name'] = empty($record['editor'])?'':implode(',', $record['editor']);
        unset($record['pointed']);
        unset($record['editor']);
    }

    public function updateRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }
        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(!parent::updatePreHandler($formData,$record)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
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
            $regulationEmployeeMap = new RegulationEmployeeMap();
            $regulationEmployeeMap->updateRecord([
                'pointed_uuid'=>$formData['pointed_uuid'],
                'regulation_uuid'=>$formData['uuid'],
            ]);
            
            $regulationEditorMap = new RegulationEditorMap();
            $regulationEditorMap->updateRecord([
                'editor_uuid'=>$formData['editor_uuid'],
                'regulation_uuid'=>$formData['uuid'],
            ]);
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function insertRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }

        if(!parent::updatePreHandler($formData)) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
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
            if(isset($formData['pointed_uuid']) && !empty($formData['pointed_uuid'])) {
                $regulationEmployeeMap = new RegulationEmployeeMap();
                $regulationEmployeeMap->insertRecord([
                    'regulation_uuid'=>$formData['uuid'],
                    'employee_uuid_list'=>$formData['pointed_uuid'],
                ]);
            }

            if(isset($formData['editor_uuid']) && !empty($formData['editor_uuid'])) {
                $regulationEditorMap = new RegulationEditorMap();
                $regulationEditorMap->insertRecord([
                    'regulation_uuid'=>$formData['uuid'],
                    'editor_uuid_list'=>$formData['editor_uuid'],
                ]);
            }
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function formDataPreHandler(&$formData, $record)
    {
        if (!isset($formData['uuid']) || empty($formData['uuid'])) {
            $formData['uuid'] = UUID::getUUID();
        }

        $formData['path_dir'] = "/upload/regulation/".$formData['uuid'];
        if(empty($record)) {
            $formData['created_time'] = time();
        }

        $formData['update_time'] = time();
        $formData['update_uuid'] = Yii::$app->user->getIdentity()->getId();
        parent::formDataPreHandler($formData, $record);

        $userId = Yii::$app->user->getIdentity()->getId();
        if(!isset($formData['pointed_uuid'])
            || empty($formData['pointed_uuid'])) {
            $formData['pointed_uuid'] = $userId;
        } else if(!is_int(strpos($userId, $formData['pointed_uuid']))) {
            $formData['pointed_uuid'] .= ',' . $userId;
        }

        if(!isset($formData['editor_uuid'])
            || empty($formData['editor_uuid'])) {
            $formData['editor_uuid'] = $userId;
        } else if(!is_int(strpos($userId, $formData['editor_uuid']))) {
            $formData['editor_uuid'] .= ',' . $userId;
        }
    }

    public function recordPreHandler(&$formData, $record = null)
    {
        if(empty($record)) {
            if(isset($formData['attachment'])&&!empty($formData['attachment'])) {
                foreach($formData['attachment'] as $index => $item) {
                    $this->attachment[] = $item;
                }
                $this->path_dir = $formData['path_dir'];
            }
        } else {
            if(isset($formData['attachment'])&&!empty($formData['attachment'])) {
                foreach($formData['attachment'] as $index => $item) {
                    $record->attachment[] = $item;
                }
                $record->path_dir = $formData['path_dir'];
            }
        }
        parent::recordPreHandler($formData, $record); // TODO: Change the autogenerated stub
    }

    public function myRegulationList($filter = null) {
        $userName = Yii::$app->user->getIdentity()->getUserName();
        if($userName === 'admin') {
            $condition = null;
        } else {
            $condition = [
                'or',
                [
                    'and',
                    [
                        '=',
                        $this->aliasMap['regulation_employee'] . '.employee_uuid',
                        Yii::$app->getUser()->getIdentity()->getId(),
                    ],
                    [
                        '=',
                        $this->aliasMap['regulation']. '.enable',
                        self::Enable
                    ],
                ],
                [
                    '=',
                    $this->aliasMap['regulation'] . '.created_uuid',
                    Yii::$app->getUser()->getIdentity()->getId(),
                ]
            ];
        }

        return !empty($filter)?$this->listFilter($filter) : $this->regulationList(
            [
                'regulation'=>[
                    '*'
                ],
                'created'=>[
                    'name',
                ],
                'update'=>[
                    'name',
                ],
                'editor'=>[
                    'uuid',
                ]
            ],
            $condition
        );
    }

    public function listFilter($filter) {
        if (empty($filter)) {
            return $this->myRegulationList();
        }

        $this->handlerFormDataTime($filter, 'min_update_time');
        $this->handlerFormDataTime($filter, 'max_update_time');

        $map = [
            'code'=>[
                'like',
                $this->aliasMap['regulation'] . '.code',
            ],
            'title'=>[
                'like',
                $this->aliasMap['regulation'] . '.title',
            ],
            'tags'=>[
                'like',
                $this->aliasMap['regulation'] . '.tags',
            ],
            'min_update_time'=>[
                '>=',
                $this->aliasMap['regulation'] . '.update_time',
            ],
            'max_update_time'=>[
                '<=',
                $this->aliasMap['regulation'] . '.update_time',
            ],
            'type'=>[
                '=',
                $this->aliasMap['regulation'] . '.type',
            ],
        ];

        $condition = [
            'and',
        ];
        if(Yii::$app->getUser()->getIdentity()->getUserName() !== 'admin') {
            $condition[] = [
                'or',
                [
                    '=',
                    $this->aliasMap['regulation_employee'] . '.employee_uuid',
                    Yii::$app->getUser()->getIdentity()->getId(),
                ],
                [
                    '=',
                    $this->aliasMap['regulation'] . '.created_uuid',
                    Yii::$app->getUser()->getIdentity()->getId(),
                ]
            ];
        }
        foreach ($filter as $index => $value) {
            $condition[] = [
                $map[$index][0],
                $map[$index][1],
                trim($value),
            ];
        }

        return $this->regulationList(
            [
                'regulation'=>[
                    '*'
                ],
                'created'=>[
                    'name',
                ],
                'update'=>[
                    'name',
                ],
                'editor'=>[
                    'uuid',
                ]
            ],
            $condition
        );
    }

    public function regulationList($selects, $conditions = null,$fetchOne = false, $unionCondition = null) {
        $aliasMap = $this->aliasMap;
        $selector = [];

        if (!empty($selects)) {
            foreach($aliasMap as $key=>$alias) {
                if (isset($selects[$key])) {
                    foreach($selects[$key] as $select) {
                        if ($key === 'created' || $key === 'update') {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select . " " . $key . "_" .$select;
                        } elseif ($key === 'regulation') {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select;
                        } else {
                            $select = trim($select);
                            $selector[] = "group_concat(".$alias ."." . $select .") " . $key . "_" . $select;
                        }
                    }
                }
            }
        }


        $query = self::find()
            ->alias('t1')
            ->select($selector)
            ->leftJoin(self::DailyRegulationEmployeeMap . ' t2', 't1.uuid = t2.regulation_uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t3', 't3.uuid = t2.employee_uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t4', 't1.created_uuid = t4.uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t5', 't1.update_uuid = t5.uuid')
            ->leftJoin(self::DailyRegulationEditorMap . ' t6', 't1.uuid = t6.regulation_uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t7', 't7.uuid = t6.editor_uuid')
            ->groupBy('t1.uuid');

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
            't1.enable'=>SORT_DESC,
            't1.code'=>SORT_ASC
        ])->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data = [
            'pagination' => $pagination,
            'list'=> $list,
        ];
        return $data;
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