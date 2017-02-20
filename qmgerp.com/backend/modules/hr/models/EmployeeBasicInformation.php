<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3 0003
 * Time: 上午 1:46
 */
namespace backend\modules\hr\models;

use backend\modules\hr\models\config\EmployeeConfig;
use Yii;
use backend\modules\hr\models\hrinterfaces\HrRecordOperator;
use backend\models\UUID;
use backend\modules\hr\models\hrinterfaces\HrPrimaryTable;
use yii\db\Exception;
use backend\models\MyPagination;
use yii\helpers\Json;

class EmployeeBasicInformation extends HrBaseActiveRecord implements HrRecordOperator,HrPrimaryTable
{
    public $positionName;
    public $departmentName;
    public $positionUuid;
    public $positionLevel;
    public $departmentUuid;
    public $username;
    public $address;
    public $hukou_address;
    public $remarks;

    public $attachment;
    public $path_dir;

    static $tableName = self::EmployeeBasicInformationTableName;
    public static function tableName()
    {
        return self::$tableName;
    }

    // 在职员工列表
    public function workingList() {
        $condition = [
            'employee' => [
                "name!='administer'",
                "name!='test'",
                'status'=>[
                    'in',
                    [
                        // 实习期，在职，试用
                        2,3,5
                    ]
                ]
            ]
        ];

        return $this->getEmployeeList($condition);
    }

    // 离职员工列表
    public function disabledList() {
        $condition = [
            'employee' => [
                "name!='administer'",
                "name!='test'",
                'status'=>[
                    '=',
                    4
                ]
            ]
        ];
        return $this->getEmployeeList($condition);
    }

    // 待入职员工列表
    public function waitingList() {
        $condition = [
            'employee' => [
                "name!='administer'",
                "name!='test'",
                'status'=>[
                    '=',
                    1
                ]
            ]
        ];
        return $this->getEmployeeList($condition);
    }

    // 通过指定的条件来删选员工列表
    public function getEmployeeList($condition) {
        $list =  $this->employeeList(
            [
                'employee'=>[
                    "*"
                ],
                'position' => [
                    'uuid',
                    'name',
                ],
                'department' => [
                    'name',
                    'uuid',
                    'parent_departments'
                ],
            ],
            $condition);
        $employeeList = &$list['employeeList'];
        $this->handlerListToShow($employeeList);
        return $list;
    }


    public function allEmployeeList($enablePage = true, $showDismissed = false) {
        $condition = $showDismissed?[
            'employee' => [
                "name!='administer'",
                "name!='test'",
            ]
        ]:[
            'employee' => [
                "name!='administer'",
                "name!='test'",
                'status'=>[
                    '<>',
                    4
                ]
            ]
        ];
        $list =  $this->employeeList(
            [
                'employee'=>[
                    "*"
                ],
                'position' => [
                    'uuid',
                    'name',
                ],
                'department' => [
                    'name',
                    'uuid',
                    'parent_departments'
                ],
            ],
            $condition, false, $enablePage);
        $employeeList = &$list['employeeList'];
        $this->handlerListToShow($employeeList);
        return $list;
    }

    public function handlerListToShow(&$employeeList) {
        $i = 0;
        foreach($employeeList as $employee) {
            // 通过解析所在部门的上级部门信息，得到该员工的公司信息
            $employeeList[$i]['company_name'] = $this->parseDepartment($employee['department_parent_departments'], 1, $employee['department_uuid']);;
            $i++;
        }
    }

    // 获取通过uuids员工的列表
    public function getEmployeeListByUuids($uuids) {
        $list =  $this->employeeList(
            [
                'employee'=>[
                    "*"
                ],
                'position' => [
                    'uuid',
                    'name',
                ],
                'department' => [
                    'name',
                    'parent_departments'
                ],
            ],
            [
                'employee' => [
                    "name!='administer'",
                    "name!='test'",
                    'uuid'=>[
                        'in',
                        $uuids,
                    ],
                    'status'=>[
                        'in',
                        [
                            EmployeeAccount::STATUS_INTERN,
                            EmployeeAccount::STATUS_ACTIVE,
                        ]
                    ]
                ]
            ]);
        return $list;
    }

    public function getEmployeeListByUuidsForDropDown($uuids) {
        if(empty($uuids)) {
            return [];
        }
        $list = self::find()->select(['uuid','name'])->andWhere(['in', 'uuid', $uuids])->all();
        $_list = [];
        foreach($list as $item) {
            $_list[$item['uuid']] = $item['name'];
        }
        return $_list;
    }

    public function parseDepartment($departments, $level, $department_uuids) {
        // 匹配出各个职位对应的部门的上级部门信息
        if(empty($departments) && empty($department_uuids)) {
            return null;
        }
        // 匹配非获取
        $departments = preg_split('/((?<=\})\,(?=\{))|((?<=\})\,)|(\,(?=\{))/', trim($departments));
        $department_uuids = explode(',', $department_uuids);
        $i = 0;
        $_return = [];
        foreach($departments as $department) {
            // 表示当前的人员的部门等级是公司级别的，所以没有上级部门,
            // 这种情况下，我们获取他的部门uuid就可以了

            if(empty($department)) {
                $record = Department::find()
                    ->select(['name'])
                    ->andWhere(['uuid'=>$department_uuids[$i]])
                    ->asArray()->one();
            }  else {
                if(substr($department, 0, 1) == ',') {
                    continue;
                }
                $department = Json::decode($department);
                if(isset($department[$level])) {
                    $record = Department::find()
                        ->select(['name'])
                        ->andWhere(['uuid'=>$department[$level]])
                        ->asArray()->one();
                }
            }
            $_return[] = $record['name'];
            $i++;
        }
        return implode(',', $_return);
    }

    public function listFilter($filter, $enablePage = true, &$departments = null) {
        // 获得入口
        $entrance = isset($filter['entrance'])?$filter['entrance']:null;
        unset($filter['entrance']);
        if(empty($filter)) {
            // 根据入口不同则出来的列表也是不同的
            if(!empty($entrance)) {
                switch ($entrance) {
                    case 'working':
                        return $this->workingList();
                    case 'disabled':
                        return $this->disabledList();
                    case 'waiting':
                        return $this->waitingList();
                    default:
                        return $this->allEmployeeList();
                }
            }
            return $this->allEmployeeList();
        }
        $map = [
            'department'=>[
                'department_uuid',
            ],
            'position'=>[
                'position_uuid',
            ]
        ];
        $condition = [
            'employee' => [
                "name!='administer'",
                "name!='test'",
            ]
        ];
        // 暂时来讲，不需要分页都是人员选择，给选择用的,所以不需要列出已经离职的人员
        if(!$enablePage) {
            $condition['employee']['status'] = [
                '<>',
                EmployeeAccount::STATUS_LEAVED,
            ];
        }

        if(!empty($entrance)) {
            switch ($entrance) {
                case 'working':
                    $condition['employee']['status'] = [
                        'in',
                        [
                            EmployeeAccount::STATUS_INTERN,
                            EmployeeAccount::STATUS_ACTIVE,
                            EmployeeAccount::STATUS_TRAINEE,
                        ]
                    ];
                    break;
                case 'disabled':
                    $condition['employee']['status'] = [
                        '=',
                        EmployeeAccount::STATUS_LEAVED,
                    ];
                    break;
                case 'waiting':
                    $condition['employee']['status'] = [
                        '=',
                        EmployeeAccount::STATUS_WAIT_ENTRY,
                    ];
                    break;
                default:
                    break;
            }
        }

        foreach($filter as $key => $value) {
            if(in_array($key, $map['department'])) {
                $department = new Department();
                $departments = $department->childDepartments($value);
                $condition['department']['uuid'] = [
                    'in',
                    $departments,
                ];
            } elseif(in_array($key, $map['position'])) {
                $condition['position'][] = "uuid='" .$value ."'";
            } else {
                if($key === 'name'){
                    $condition['employee'][] =  $key . " like '%" .$value . "%'";
                } else {
                    $condition['employee'][] =  $key . "='" .$value."'";
                }
            }
        }

        $list = $this->employeeList(
            [
                'employee'=>[
                    "*"
                ],
                'position' => [
                    'uuid',
                    'name',
                ],
                'department' => [
                    'name',
                    'uuid',
                    'parent_departments'
                ],
            ],
            $condition,
            false,
            $enablePage
        );
        $employeeList = &$list['employeeList'];
        $this->handlerListToShow($employeeList);
        return $list;
    }


    public function getRecordByUuid($uuid) {
        if (empty($uuid) || !isset($uuid)) {
            return false;
        }

        return $this->employeeList(
        [
            'employee'=>[
                "*"
            ],
            'position' => [
                'uuid',
                'name',
            ],
            'account' => [
                'username'
            ],
            'attachment' => [
                'remarks',
                'hukou_address',
                'address',
                'bank_account',
                'salary_adjust_record',
                'social_insurance_adjust_record',
                'house_fund_adjust_record'
            ]
        ],
        [
            'employee'=>[
                'uuid="'.$uuid.'"',
            ]
        ],true);
    }

    public function formDataPreHandler(&$formData, $record)
    {
        if(!isset($formData['uuid']) || empty($formData['uuid'])) {
            $formData['uuid'] = UUID::getUUID();
        }

        $formData['path_dir'] = "/upload/employee/".$formData['uuid'];

        if(empty($record)) {
            parent::clearEmptyField($formData);
        }
        $formData['birthday'] = isset($formData['birthday']) && !empty($formData['birthday'])?strtotime($formData['birthday']):0;
        $formData['lunar_birthday'] = isset($formData['lunar_birthday']) && !empty($formData['lunar_birthday'])?strtotime($formData['lunar_birthday']):0;
        $formData['stop_social_insurance_time'] = isset($formData['stop_social_insurance_time']) && !empty($formData['stop_social_insurance_time'])?strtotime($formData['stop_social_insurance_time']):0;
        $formData['entry_time'] = isset($formData['entry_time']) && !empty($formData['entry_time'])?strtotime($formData['entry_time']):0;
        $formData['become_full_member_time'] = isset($formData['become_full_member_time']) && !empty($formData['become_full_member_time'])?strtotime($formData['become_full_member_time']):0;
        $formData['out_time'] = isset($formData['out_time']) && !empty($formData['out_time'])?strtotime($formData['out_time']):0;
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

    public function insertRecord($formData)
    {
        if(empty($formData)) {
            return false;
        }

        if(!parent::updatePreHandler($formData)) {
            return false;
        }
        $employeePositionMap = new EmployeePositionMap();
        $employeeAttachment = new EmployeeBasicAttachmentInformation();
        $employeeAccount = new EmployeeAccount();
        $employeeFamily = new EmployeeFamily();

        $transaction = Yii::$app->db->beginTransaction();

        try {
            // 上传附件处理
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
            // 设置员工的系统id
            $employeeConfig = new EmployeeConfig();
            $config = $employeeConfig->generateConfig();
            if(empty($config['system_code'])) {
                $this->system_code = '0001';
            } else {
                $this->system_code = $config['system_code'];
            }
            parent::insert();
            $config['system_code'] += 1;
            if($config['system_code'] < 10) {
                $config['system_code'] = '000'.$config['system_code'];
            } else if($config['system_code']  < 100 && $config['system_code'] > 9) {
                $config['system_code'] = '00'.$config['system_code'];
            }else if($config['system_code']  < 1000 && $config['system_code'] > 99) {
                $config['system_code'] = '0'.$config['system_code'];
            }
            $employeeConfig->updateDateConfigByJsonString(Json::encode($config));
            $employeeAttachment->insertRecord($formData);
            $employeePositionMap->insertRecord($formData);

            $message = false;
            if(isset($formData['username'])) {
                $message = $employeeAccount->insertRecord($formData);
            }
            // 如果不是bool表明是一个信息
            if(!is_bool($message)) {
                $transaction->rollBack();
                return $message;
            }
            $employeeFamily->insertRecord($formData);
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        $transaction->commit();
        return true;
    }
    /**
     * condition的结构为
     * [
     *      'department'=>[....]
     *      'employee'=>[...]
     *      'position'=>[...]
     * ]
     * @param array $conditions
     */
    public function employeeList($selects, $conditions = null,$fetchOne = false, $enablePage = true) {
        $aliasMap = [
            'employee'=>'t1',
            'employee_position_map'=>'t2',
            'position'=>'t3',
            'department'=>'t4',
            'account'=>'t5',
            'attachment'=>'t6',
        ];
        $selector = [];

        if (!empty($selects)) {
            foreach($aliasMap as $key=>$alias) {
                if (isset($selects[$key])) {
                    foreach($selects[$key] as $select) {
                        if ($key === 'employee_position_map') {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select . " " . $key . "_" .$select;
                        } elseif ($key === 'employee' || $key === 'attachment' || $key === 'account') {
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
                    ->alias($aliasMap['employee'])
                    ->select($selector)
                    ->leftJoin(self::EmployeePositionMapTableName . ' t2', 't1.uuid = t2.em_uuid')
                    ->leftJoin(self::PositionTableName . ' t3', 't2.position_uuid = t3.uuid')
                    ->leftJoin(self::DepartmentTableName . ' t4','t3.de_uuid = t4.uuid')
                    ->leftJoin(self::EmployeeAccountTableName . " t5", "t5.em_uuid = t1.uuid")
                    ->leftJoin(self::EmployeeBasicAttachmentInformationTableName . " t6", 't6.em_uuid = t1.uuid')
                    ->groupBy('t1.uuid');
        //
        if(!empty($conditions)) {
            foreach($aliasMap as $key=>$alias) {
                if (isset($conditions[$key])) {
                    foreach($conditions[$key] as $k=>$condition) {
                        if(!is_array($condition)) {
                            $condition = trim($condition);
                            $query->andWhere(
                                $alias . "." . $condition
                            );
                            continue;
                        }

                        $query->andWhere([
                            $condition[0],
                            $alias . '.' . $k,
                            $condition[1],
                        ]);
                    }
                }
            }
        }
        if ($fetchOne) {
            return $query->asArray()->one();
        }
        if(!$enablePage) {
             return [
                 'employeeList'=>$query->orderBy('t1.code')->asArray()->all(),
             ];
        }
        $pagination = new MyPagination([
            'totalCount'=>$query->count(),
            'pageSize' => self::PageSize,
        ]);
        $employeeList = $query->orderBy('t1.code')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data = [
            'pagination' => $pagination,
            'employeeList'=> $employeeList,
        ];
        return $data;
    }

    public function dismiss($formData) {
        if (empty($formData) || !isset($formData) || !isset($formData['uuid']) || empty($formData['uuid'])) {
            return false;
        }
        
        $uuid = $formData['uuid'];
        unset($formData['uuid']);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 将记录变成离职，并且保存离职时间
            $record = self::find()->andWhere(['uuid'=>$uuid])->one();
            if(empty($record)) {
                return true;
            }
            $record->out_time = time();

            if($record->status != EmployeeAccount::STATUS_LEAVED) {
                $record->status = EmployeeAccount::STATUS_LEAVED;
                $record->update();
            }
            // 将账号变成离职
            $employeeAccount = new EmployeeAccount();
            if(!$employeeAccount->dismiss($uuid)) {
                $transaction->rollBack();
                return true;
            }
            // 保存离职清单
            $employeeAttachment = new EmployeeBasicAttachmentInformation();
            if(!$employeeAttachment->updateRecord([
                'uuid'=>$uuid,
                'dismiss_list'=>Json::encode($formData),
            ])) {
                $transaction->rollBack();
                return true;
            }
            /**
             * 将相对应的职位的在职人数-1
             */
            $positionEmployeeMap = new EmployeePositionMap();
            $positionEmployeeMap->dismiss($uuid);
            /**
             * 原计划，想把离职员工的权限及职位解除，这样会导致该员工的
             * 遗留下来的一些信息无法被领导接受，所以不能把员工的权限及
             * 职位解除
             */
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function updateRecord($formData)
    {
        $this->uuid = $formData['uuid'];

        $account = new EmployeeAccount();
        $attachment = new EmployeeBasicAttachmentInformation();
        $map = new EmployeePositionMap();
        $family = new EmployeeFamily();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (($record = self::find()->andWhere(['uuid'=>$this->uuid])->one()) !== null
                && $this->updatePreHandler($formData, $record)) {
                // 附件处理
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
            }
            $account->updateRecord($formData);
            $attachment->updateRecord($formData);
            $map->updateRecord($formData);
            $family->updateRecord($formData);
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function deleteRecordByUuid($uuid)
    {
        // TODO: Implement deleteRecordByUuid() method.
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