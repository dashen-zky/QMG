<?php

namespace backend\modules\crm\models\project\record;
use backend\models\helper\file\UploadFileHelper;
use backend\models\interfaces\PrimaryTable;
use backend\models\interfaces\RecordOperator;
use backend\models\UUID;
use backend\modules\crm\models\customer\record\Contact;
use backend\modules\crm\models\project\model\ProjectConfig;
use backend\modules\crm\models\project\model\ProjectForm;
use backend\modules\hr\models\EmployeeBasicInformation;
use yii\db\Exception;
use Yii;
use yii\helpers\Json;
use backend\models\MyPagination;
use backend\modules\rbac\model\RBACManager;
use backend\modules\rbac\model\RoleManager;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/2 0002
 * Time: 下午 3:45
 */
class Project extends ProjectBaseRecord implements RecordOperator,PrimaryTable
{
    const Enable = 1;
    const Disable = 2;
    const AccountReceivableEntrance = 3;
    public static $aliasMap = [
        'project'=>'t1',
        'customer_project_map'=>'t2',
        'customer'=>'t3',
        'customer_advance'=>'t4',
        'sales'=>'t5',
        'project_manager'=>'t6',
        'business_map'=>'t7',
        'contact_map'=>'t8',
        'contact'=>'t9',
        'member_map'=>'t10',
        'member'=>'t11',
        'created'=>'t12',
        'apply_active'=>'t13',
        'apply_done'=>'t14',
    ];
    public static function tableName()
    {
        return self::CRMProject;
    }

    // 对formData在插入和修改之前做的数据预处理
    public function formDataPreHandler(&$formData, $record)
    {
        if(empty($record)) {
            $formData['create_time'] = time();
            $formData['uuid'] = UUID::getUUID();
            $formData['status'] = ProjectConfig::StatusTouching;
        }
        $this->handlerFormDataTime($formData,'start_time');
        $this->handlerFormDataTime($formData, 'end_time');
        $this->handlerFormDataTime($formData, 'sign_time');
        parent::formDataPreHandler($formData, $record);
    }

    // 发票作废
    public function stampDisable($uuid, $money) {
        if(empty($uuid)) {
            return true;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return true;
        }

        $record->checked_stamp_money -= $money;
        return $record->update();
    }

    public function receiveMoney($formData) {
        if(empty($formData)) {
            return true;
        }
        
        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(empty($record)) {
            return true;
        }
        $record->received_money += $formData['received_money'];
        return $record->update();
    }
    
    public function billing($formData) {
        if(empty($formData) || !isset($formData['uuid']) || empty($formData['uuid'])) {
            return true;
        }

        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(empty($record)) {
            return true;
        }
        
        $record->checked_stamp_money += $formData['checked_stamp_money'];
        return $record->update();
    }
    
    public function updateRecord($formData)
    {
        if(empty($formData) || !isset($formData['uuid']) || empty($formData['uuid'])) {
            return true;
        }

        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(!empty($record) && !parent::updatePreHandler($formData, $record)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            // 上传立项资料和结案资料
            UploadFileHelper::uploadWhileUpdate($record,
                isset($formData['_active_attachment'])?$formData['_active_attachment']:null,
                '/upload/project/'.$formData['uuid'] . '/active_attachment',
                'active_attachment');
            UploadFileHelper::uploadWhileUpdate($record,
                isset($formData['_done_attachment'])?$formData['_done_attachment']:null,
                '/upload/project/'.$formData['uuid'] . '/done_attachment',
                'done_attachment');
            UploadFileHelper::uploadWhileUpdate($record,
                isset($formData['_budget_attachment'])?$formData['_budget_attachment']:null,
                '/upload/project/'.$formData['uuid'] . '/budget_attachment',
                'budget_attachment');

            // 更新项目基本资料
            $record->update();

            // 更新业务板块
            if(isset($formData['business_id'])) {
                (new ProjectBusinessMap())->updateSingleRecord([
                    'project_uuid'=>$formData['uuid'],
                    'business_id'=>$formData['business_id'],
                ]);
            }

            // 跟新联系人
            $contactMap = new ProjectContactMap();
            if(isset($formData['project_contact_uuid'])) {
                $contactMap->updateRecord([
                    'project_uuid'=>$formData['uuid'],
                    'contact_uuid'=>$formData['project_contact_uuid'],
                    'duty'=>ProjectContactMap::ProjectContact,
                ]);
            }

            // 更新负责人
            if(isset($formData['project_duty_uuid'])) {
                $contactMap->updateRecord([
                    'project_uuid'=>$formData['uuid'],
                    'contact_uuid'=>$formData['project_duty_uuid'],
                    'duty'=>ProjectContactMap::ProjectDuty,
                ]);
            }

            // 跟新项目成员
            if(isset($formData['project_member_uuid'])) {
                $projectMemberMap = new ProjectMemberMap();
                $projectMemberMap->updateRecord([
                    'project_uuid'=>$formData['uuid'],
                    'member_uuid'=>$formData['project_member_uuid'],
                ]);
            }
        }catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function applyPaymentProjectList() {
        $uuids = $this->getOrdinateUuids(RBACManager::ProjectModule);

        // 销售看到的所有的客户的项目
        $salesRole = [
            RoleManager::Sales,
            RoleManager::SalesManager,
            RoleManager::SalesDirector,
        ];
        $auth = Yii::$app->authManager;
        $userId = Yii::$app->user->getIdentity()->getId();
        // 看看这个人有没有销售的角色，有销售的角色才能查看下面所有客户的项目
        $interact = array_intersect($salesRole, array_keys($auth->getRolesByUser($userId)));
        $union = null;
        // 在设计里面union的字段是一样的，只是条件不一样
        if(!empty($interact)) {
            $uuids = $this->getOrdinateUuids(RBACManager::ProjectModule);
            $union = [
                'and',
                [
                    'in',
                    self::$aliasMap['sales'] . '.uuid',
                    $uuids,
                ],
                [
                    '<>',
                    self::$aliasMap['project'] . '.enable',
                    Project::Disable,
                ],
                [
                    'in',
                    self::$aliasMap['project'] . '.status',
                    [
                        ProjectConfig::StatusExecuting,
                        ProjectConfig::StatusDone,
                    ]
                ],
            ];
        }
        $condition = empty($union)?[
            'and',
            [
                '<>',
                self::$aliasMap['project'] . '.enable',
                Project::Disable,
            ],
            [
                'in',
                self::$aliasMap['project'] . '.status',
                [
                    ProjectConfig::StatusExecuting,
                    ProjectConfig::StatusDone,
                    ProjectConfig::StatusDoneApplying,
                ]
            ],
            [
                'in',
                self::$aliasMap['project'] . '.project_manager_uuid',
                $uuids
            ],
        ]
            :
        [
            'or',
            [
                'and',
                [
                    '<>',
                    self::$aliasMap['project'] . '.enable',
                    Project::Disable,
                ],
                [
                    'in',
                    self::$aliasMap['project'] . '.status',
                    [
                        ProjectConfig::StatusExecuting,
                        ProjectConfig::StatusDone,
                        ProjectConfig::StatusDoneApplying,
                    ]
                ],
                [
                    'in',
                    self::$aliasMap['project'] . '.project_manager_uuid',
                    $uuids
                ],
            ],
            $union
        ];
        $list = $this->projectList(
            [
                'project'=>[
                    'id',
                    'uuid',
                    'name',
                    'code',
                    'status'
                ],
                'sales'=>[
                  'name'
                ],
                'project_manager'=>[
                    'name'
                ],
                'customer'=>[
                    'name',
                ]
            ],
            $condition,
            true,
            10
        );
        return $list;
    }

    public function myProjectList($enablePage = true) {
        $userName = Yii::$app->user->getIdentity()->getUserName();
        if($userName === 'admin') {
            $condition = null;
        } else {
            $uuids = $this->getOrdinateUuids(RBACManager::ProjectModule);
            $condition = [
                'or',
                [
                    'and',
                    [
                        '<>',
                        self::$aliasMap['project'] . '.enable',
                        self::Disable,
                    ],
                    [
                        'in',
                        self::$aliasMap['project'] .'.project_manager_uuid',
                        $uuids
                    ],
                ],
                [
                    'and',
                    [
                        '<>',
                        self::$aliasMap['project'] . '.enable',
                        self::Disable,
                    ],
                    [
                        '=',
                        self::$aliasMap['member_map'] .'.member_uuid',
                        Yii::$app->user->getIdentity()->getId(),
                    ],
                ]
            ];

        }

        // 销售看到的所有的客户的项目
        $salesRole = [
            RoleManager::Sales,
            RoleManager::SalesManager,
            RoleManager::SalesDirector,
        ];
        $auth = Yii::$app->authManager;
        $userId = Yii::$app->user->getIdentity()->getId();
        // 看看这个人有没有销售的角色，有销售的角色才能查看下面所有客户的项目
        $interact = array_intersect($salesRole, array_keys($auth->getRolesByUser($userId)));
        // 在设计里面union的字段是一样的，只是条件不一样
        if(!empty($interact)) {
            $uuids = $this->getOrdinateUuids(RBACManager::ProjectModule);
            $condition[] = [
                'and',
                [
                    'in',
                    self::$aliasMap['sales'].'.uuid',
                    $uuids,
                ],
                [
                    '<>',
                    self::$aliasMap['project'] . '.enable',
                    self::Disable,
                ],
            ];
        }

        $list =  $this->projectList(
            [
                'project'=>[
                    '*',
                ],
                'customer'=>[
                    'name'
                ],
                'sales'=>[
                    'name'
                ],
                'project_manager'=>[
                    'name'
                ],
                'business_map'=>[
                    'business_id',
                ],
            ],
            $condition,
            $enablePage
        );

        if($enablePage) {
            // 将个数组里面重复的数据去除掉
            $list['projectList'] = $this->filterRepeatItemByUuid($list['projectList']);
        } else {
            $list = $this->filterRepeatItemByUuid($list);
        }

        return $list;
    }
    
    public function accountReceivableList() {
        return $this->projectList(
            [
                'project'=>[
                    '*',
                ],
                'customer'=>[
                    'uuid',
                    'name',
                    'full_name'
                ],
                'sales'=>[
                    'name'
                ],
                'project_manager'=>[
                    'name'
                ],
            ],
            [
                '<>',
                self::$aliasMap['project'] . '.enable',
                self::Disable,
            ]
        );
    }

    // 通过project的uuid过滤掉projectList重复的元素
    public function filterRepeatItemByUuid($list) {
        $exist_uuid = [];
        for($i = 0; $i < count($list); $i++) {
            if(isset($exist_uuid[$list[$i]['uuid']])) {
                unset($list[$i]);
                continue;
            }
            $exist_uuid[$list[$i]['uuid']] = true;
        }

        return $list;
    }

    public function insertRecord($formData)
    {
        if (empty($formData) || !isset($formData['name']) || empty($formData['name'])) {
            return true;
        }

        if (!$this->updatePreHandler($formData)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            // 上传立项资料和结案资料
            UploadFileHelper::uploadWhileInsert($this,
                isset($formData['_active_attachment'])?$formData['_active_attachment']:null,
                '/upload/project/'.$formData['uuid'] . '/active_attachment',
                'active_attachment');
            UploadFileHelper::uploadWhileInsert($this,
                isset($formData['_done_attachment'])?$formData['_done_attachment']:null,
                '/upload/project/'.$formData['uuid'] . '/done_attachment',
                'done_attachment');
            UploadFileHelper::uploadWhileInsert($this,
                isset($formData['_budget_attachment'])?$formData['_budget_attachment']:null,
                '/upload/project/'.$formData['uuid'] . '/budget_attachment',
                'budget_attachment');

            $projectConfig = new ProjectConfig();
            $this->code = $projectConfig->generateProjectCode();
            self::insert();

            // 将项目和客户关联起来
            $projectCustomerMap = new ProjectCustomerMap();
            $projectCustomerMap->insertSingleRecord([
                'project_uuid'=>$formData['uuid'],
                'customer_uuid'=>$formData['customer_uuid']
            ]);
            // 添加项目联系人
            $projectContactMap = new ProjectContactMap();
            if (!empty($formData['project_contact_uuid'])) {
                $projectContactMap->insertRecord([
                    'project_uuid'=>$formData['uuid'],
                    'contact_uuid'=>$formData['project_contact_uuid'],
                    'duty'=>ProjectContactMap::ProjectContact,
                ]);
            }
            //添加项目负责人
            if (!empty($formData['project_duty_uuid'])) {
                $projectContactMap->insertRecord([
                    'project_uuid'=>$formData['uuid'],
                    'contact_uuid'=>$formData['project_duty_uuid'],
                    'duty'=>ProjectContactMap::ProjectDuty,
                ]);
            }
            // 添加项目成员
            if(!empty($formData['project_member_uuid'])) {
                $projectMemberMap = new ProjectMemberMap();
                $projectMemberMap->insertRecord([
                    'project_uuid'=>$formData['uuid'],
                    'member_uuid'=>$formData['project_member_uuid'],
                ]);
            }
            // 添加项目业务板块
            if(!empty($formData['business_id'])) {
                $projectBusinessMap = new ProjectBusinessMap();
                $projectBusinessMap->insertSingleRecord([
                    'project_uuid'=>$formData['uuid'],
                    'business_id'=>$formData['business_id'],
                ]);
            }
            // 将项目的编号+1放入到配置文件里面

            $config = $projectConfig->generateConfig();
            $config['project_code'] = intval($this->code) + 1;
            $projectConfig->updateDateConfigByJsonString(Json::encode($config));
        }catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function getProjectListFromCustomer($uuid) {
        return $this->projectList(
            [
                'project'=>[
                    '*',
                ],
                'customer'=>[
                  'name'
                ],
                'sales'=>[
                  'name'
                ],
                'project_manager'=>[
                    'name'
                ],
                'business_map'=>[
                    'business_id',
                ],
            ],
            [
                '=',
                self::$aliasMap['customer'] . '.uuid',
                $uuid,
            ]
        );
    }

    public function projectList($selects, $conditions = null, $enablePage = true, $pageSize = self::PageSize) {
        $selector = [];

        if (!empty($selects)) {
            foreach(self::$aliasMap as $key=>$alias) {
                if (isset($selects[$key])) {
                    foreach($selects[$key] as $select) {
                        $select = trim($select);
                        if ($key === 'project') {
                            $selector[] = $alias ."." . $select;
                        } elseif($key === 'member' || $key === 'member_map'
                            || $key === 'contact' || $key === 'contact_map') {
                            $selector[] = "group_concat(".$alias ."." . $select .") " . $key . "_" .$select;
                        }else {
                            $selector[] = $alias ."." . $select . " " . $key . "_" .$select;
                        }
                    }
                }
            }
        }

        $query = self::find()
            ->alias('t1')
            ->select($selector)
            ->leftJoin(self::CRMCustomerProjectMap . ' t2','t1.uuid=t2.project_uuid')
            ->leftJoin(self::CRMCustomerBasic . ' t3','t3.uuid = t2.customer_uuid')
            ->leftJoin(self::CRMCustomerAdvance. ' t4','t4.customer_uuid=t3.uuid')
            ->leftJoin(EmployeeBasicInformation::$tableName.' t5','t5.uuid = t4.sales_uuid')
            ->leftJoin(EmployeeBasicInformation::$tableName . ' t6','t1.project_manager_uuid = t6.uuid')
            ->leftJoin(self::CRMProjectBusinessMap . ' t7','t1.uuid = t7.project_uuid')
            ->leftJoin(self::CRMProjectContactMap . ' t8','t1.uuid = t8.project_uuid')
            ->leftJoin(self::CRMContact . ' t9', 't9.uuid = t8.contact_uuid')
            ->leftJoin(self::CRMProjectMemberMap .' t10', 't1.uuid = t10.project_uuid')
            ->leftJoin(EmployeeBasicInformation::$tableName . " t11", 't11.uuid = t10.member_uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t12','t12.uuid = t1.created_uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t13', 't1.apply_active_uuid = t13.uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t14', 't1.apply_done_uuid = t14.uuid')
            ->groupBy('t1.uuid');
        //
        if(!empty($conditions)) {
            $query->andWhere($conditions);
        }

        if(!$enablePage) {
            return $query->asArray()->all();
        }

        $pagination = new MyPagination([
            'totalCount'=>$query->count(),
            'pageSize' => $pageSize,
        ]);
        $publicCustomerList = $query->orderBy('t1.id DESC')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data = [
            'pagination' => $pagination,
            'projectList'=> $publicCustomerList,
        ];
        return $data;
    }



    public function getRecordByUuid($uuid)
    {
        $list =  $this->projectList(
            [
                'project'=>[
                    '*',
                ],
                'customer'=>[
                    'uuid',
                    'name',
                    'type',
                ],
                'sales'=>[
                    'name'
                ],
                'created'=>[
                    'name'
                ],
                'project_manager'=>[
                    'name'
                ],
                'business_map'=>[
                    'business_id',
                ],
                'contact'=> [
                    'uuid',
                    'name',
                ],
                'contact_map'=>[
                    'duty'
                ],
                'member_map'=>[
                  'member_uuid',
                ],
                'member'=>[
                    'name'
                ]
            ],
            [
                '=',
                self::$aliasMap['project'] . '.uuid',
                $uuid,
            ],
            false
        );
        $record = $this->handlerListFieldToShow($list);
        $record[0]['business_id'] = $record[0]['business_map_business_id'];
        return $record[0];
    }
//处理重复的字段，因为在group_concat查询出来，会出现一些重复的内容，将其处理一下
    public function handlerListFieldToShow(&$projectList) {
        for($i = 0; $i < count($projectList); $i++) {
            // 处理重复的字段
            $projectList[$i]['contact_list'] = [];
            if (isset($projectList[$i]['contact_name'])
                && !empty($projectList[$i]['contact_name'])) {
                $projectList[$i]['project_contact_name'] =
                    $this->filterRepeatField(
                        $projectList[$i]['contact_map_duty'],
                        $projectList[$i]['contact_name'],
                        ProjectContactMap::ProjectContact
                    );
                $projectList[$i]['project_duty_name'] =
                    $this->filterRepeatField(
                        $projectList[$i]['contact_map_duty'],
                        $projectList[$i]['contact_name'],
                        ProjectContactMap::ProjectDuty
                    );
            }

            if (isset($projectList[$i]['contact_uuid'])
                && !empty($projectList[$i]['contact_uuid'])) {
                $projectList[$i]['project_contact_uuid'] =
                    $this->filterRepeatField(
                        $projectList[$i]['contact_map_duty'],
                        $projectList[$i]['contact_uuid'],
                        ProjectContactMap::ProjectContact
                    );
                $projectList[$i]['project_duty_uuid'] =
                    $this->filterRepeatField(
                        $projectList[$i]['contact_map_duty'],
                        $projectList[$i]['contact_uuid'],
                        ProjectContactMap::ProjectDuty
                    );
                // 获取contact list ，在跟进记录里面使用

                $projectList[$i]['contact_list'] =
                    $this->getContactListForTouchRecord($projectList[$i]['contact_uuid'],$projectList[$i]['contact_name']);

            }
            if (isset($projectList[$i]['member_name'])
                && !empty($projectList[$i]['member_name'])) {
                $projectList[$i]['project_member_name'] =
                    $this->getDistinctValue(explode(",",$projectList[$i]['member_name']));
            }

            if (isset($projectList[$i]['member_map_member_uuid'])
                && !empty($projectList[$i]['member_map_member_uuid'])) {
                $projectList[$i]['project_member_uuid'] =
                    $this->getDistinctValue(explode(",",$projectList[$i]['member_map_member_uuid']));
            }

        }
        return $projectList;
    }
    //根据我们查出来的数值，将联系人（联系人，负责人）的信息提取出来，
    // ['uuid'=>'contact_name']
    protected function getContactListForTouchRecord($uuidStr, $nameStr) {
        $uuids = explode(",", trim($uuidStr));
        $names = explode(",", trim($nameStr));
        $_return = [];
        foreach ($uuids as $index => $uuid) {
            if(!empty($uuid)) {
                $_return[$uuid] = $names[$index];
            }
        }
        return $this->getDistinctValueAsArray($_return);
    }

    // 逻辑删除
    public function deleteRecordByUuid($uuid)
    {
        if(empty($uuid)) {
            return true;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return true;
        }

        $record->enable = self::Disable;
        return $record->update();
    }

    // 可以选择所有的项目
    public function listFilterForWithoutBelong($filter, $pageSize = self::PageSize) {
        if(empty($filter)) {
            return $this->projectList(
                [
                    'project'=>[
                        '*',
                    ],
                    'customer'=>[
                        'name'
                    ],
                    'sales'=>[
                        'name'
                    ],
                    'project_manager'=>[
                        'name'
                    ],
                ],
                [
                    '<>',
                    self::$aliasMap['project'] . '.enable',
                    self::Disable,
                ],
                true,
                $pageSize
            );
        }

        if(isset($filter['code']) && !empty($filter['code'])) {
            preg_match('/([a-zA-Z]*)(\d+)/',$filter['code'],$match);
            if($match[1] === ProjectForm::codePrefix) {
                $filter['code'] = $match[2];
            }
        }

        $map = [
            'code'=>[
                'like',
                self::$aliasMap['project'] .'.code',
            ],
            'customer_name'=>[
                'like',
                self::$aliasMap['customer'] .'.name',
            ],
            'name'=>[
                'like',
                self::$aliasMap['project'] .'.name',
            ],
            'project_manager_name'=>[
                'like',
                self::$aliasMap['project_manager'] .'.name',
            ],
            'sales_name'=>[
                'like',
                self::$aliasMap['sales'] .'.name',
            ],
            'status'=>[
                '=',
                self::$aliasMap['project'] . '.status',
            ],
            'contract_status'=>[
                '=',
                self::$aliasMap['project'] . '.contract_status'
            ],
            'receive_money_status'=>[
                '=',
                self::$aliasMap['project'] . '.receive_money_status',
            ],
            'stamp_status'=>[
                '=',
                self::$aliasMap['project'] . '.stamp_status'
            ],
        ];

        $condition = [
            'and',
            [
                '<>',
                self::$aliasMap['project'] . '.enable',
                self::Disable,
            ]
        ];

        foreach ($filter as $key => $value) {
            $condition[] = [
                $map[$key][0],
                $map[$key][1],
                trim($value),
            ];
        }

        $list = $this->projectList(
            [
                'project'=>[
                    '*',
                ],
                'customer'=>[
                    'name'
                ],
                'sales'=>[
                    'name'
                ],
                'project_manager'=>[
                    'name'
                ],
            ],
            $condition,
            true,
            $pageSize
        );
        return $list;
    }

    public function listFilter($filter, $pageSize = self::PageSize) {
        $entrance = isset($filter['entrance'])?$filter['entrance']:0;
        unset($filter['entrance']);
        if(empty($filter)) {
            if ($entrance == self::AccountReceivableEntrance) {
                return $this->accountReceivableList();
            }
            return $this->myProjectList();
        }
        if(isset($filter['code']) && !empty($filter['code'])) {
            preg_match('/([a-zA-Z]*)(\d+)/',$filter['code'],$match);
            if($match[1] === ProjectForm::codePrefix) {
                $filter['code'] = $match[2];
            }
        }


        $map = [
            'code'=>[
                'like',
                self::$aliasMap['project'] .'.code',
            ],
            'customer_name'=>[
                'like',
                self::$aliasMap['customer'] .'.name',
            ],
            'customer_full_name'=>[
                'like',
                self::$aliasMap['customer'] .'.full_name',
            ],
            'name'=>[
                'like',
                self::$aliasMap['project'] .'.name',
            ],
            'project_manager_name'=>[
                'like',
                self::$aliasMap['project_manager'] .'.name',
            ],
            'sales_name'=>[
                'like',
                self::$aliasMap['sales'] .'.name',
            ],
            'status'=>[
                '=',
                self::$aliasMap['project'] . '.status',
            ],
            'contract_status'=>[
                '=',
                self::$aliasMap['project'] . '.contract_status'
            ],
            'receive_money_status'=>[
                '=',
                self::$aliasMap['project'] . '.receive_money_status',
            ],
            'stamp_status'=>[
                '=',
                self::$aliasMap['project'] . '.stamp_status'
            ],
        ];

        $entrance_condition = $this->buildConditionByEntrance($entrance);

        $filterCondition = [
            'and'
        ];
        foreach ($filter as $key => $value) {
            $filterCondition[] = [
                $map[$key][0],
                $map[$key][1],
                trim($value)
            ];
        }

        $condition = [
            'and',
            $filterCondition,
        ];
        if(!empty($entrance_condition)) {
            $condition[] = $entrance_condition;
        }



        $list = $this->projectList(
            [
                'project'=>[
                    '*',
                ],
                'customer'=>[
                    'uuid',
                    'name',
                    'full_name'
                ],
                'sales'=>[
                    'name'
                ],
                'project_manager'=>[
                    'name'
                ],
                'business_map'=>[
                    'business_id',
                ],
            ],
            $condition,
            true,
            $pageSize
        );

        $list['projectList'] = $this->filterRepeatItemByUuid($list['projectList']);
        return $list;
    }
    
    protected function buildConditionByEntrance($entrance = 0) {
        if ($entrance == self::AccountReceivableEntrance) {
            return [
                '<>',
                self::$aliasMap['project'] . '.enable',
                self::Disable
            ];
        }

        $userName = Yii::$app->user->getIdentity()->getUserName();
        if($userName === 'admin') {
            $condition = null;
        } else {
            $uuids = $this->getOrdinateUuids(RBACManager::ProjectModule);
            $condition = [
                'or',
                [
                    'and',
                    [
                        '<>',
                        self::$aliasMap['project'] . '.enable',
                        self::Disable
                    ],
                    [
                        'in',
                        self::$aliasMap['project'] . '.project_manager_uuid',
                        $uuids,
                    ],
                ],
                [
                    'and',
                    [
                        '<>',
                        self::$aliasMap['project'] . '.enable',
                        self::Disable
                    ],
                    [
                        '=',
                        self::$aliasMap['member_map'] . '.member_uuid',
                        Yii::$app->getUser()->getIdentity()->getId(),
                    ],
                ]
            ];

            // 销售看到的所有的客户的项目
            $salesRole = [
                RoleManager::Sales,
                RoleManager::SalesManager,
                RoleManager::SalesDirector,
            ];
            $auth = Yii::$app->authManager;
            $userId = Yii::$app->user->getIdentity()->getId();
            // 看看这个人有没有销售的角色，有销售的角色才能查看下面所有客户的项目
            $interact = array_intersect($salesRole, array_keys($auth->getRolesByUser($userId)));
            // 在设计里面union的字段是一样的，只是条件不一样
            if(!empty($interact)) {
                $uuids = $this->getOrdinateUuids(RBACManager::ProjectModule);
                $condition[] = [
                    'and',
                    [
                        'in',
                        self::$aliasMap['sales'] . '.uuid',
                        $uuids,
                    ],
                    [
                        '<>',
                        self::$aliasMap['project'] . '.enable',
                        self::Disable,
                    ],
                ];
            }
        }
        
        return $condition;
    }

    public function doneAssessListFilter($filter) {
        if (empty($filter)) {
            return $this->doneAssessList();
        }

        if(isset($filter['code']) && !empty($filter['code'])) {
            preg_match('/([a-zA-Z]*)(\d+)/',$filter['code'],$match);
            if($match[1] === ProjectForm::codePrefix) {
                $filter['code'] = $match[2];
            }
        }


        $map = [
            'code'=>[
                'like',
                self::$aliasMap['project'] .'.code',
            ],
            'customer_name'=>[
                'like',
                self::$aliasMap['customer'] .'.name',
            ],
            'name'=>[
                'like',
                self::$aliasMap['project'] .'.name',
            ],
            'project_manager_name'=>[
                'like',
                self::$aliasMap['project_manager'] .'.name',
            ],
            'sales_name'=>[
                'like',
                self::$aliasMap['sales'] .'.name',
            ],
            'status'=>[
                '=',
                self::$aliasMap['project'] . '.status',
            ],
            'contract_status'=>[
                '=',
                self::$aliasMap['project'] . '.contract_status'
            ],
            'receive_money_status'=>[
                '=',
                self::$aliasMap['project'] . '.receive_money_status',
            ],
            'stamp_status'=>[
                '=',
                self::$aliasMap['project'] . '.stamp_status'
            ],
        ];

        $condition = [
            'and',
            [
                '<>',
                self::$aliasMap['project'] . '.enable',
                self::Disable,
            ],
            [
                '=',
                self::$aliasMap['project'] . '.status',
                ProjectConfig::StatusDoneApplying,
            ],
        ];

        foreach ($filter as $index => $value) {
            $condition[] = [
                $map[$index][0],
                $map[$index][1],
                trim($value)
            ];
        }

        return $this->projectList([
            'project'=>[
                '*',
            ],
            'customer'=>[
                'name'
            ],
            'sales'=>[
                'name'
            ],
            'project_manager'=>[
                'name'
            ],
            'apply_done'=>[
                'name'
            ]
        ],$condition);
    }

    public function activeAssessListFilter($filter) {
        if (empty($filter)) {
            return $this->activeAssessList();
        }

        if(isset($filter['code']) && !empty($filter['code'])) {
            preg_match('/([a-zA-Z]*)(\d+)/',$filter['code'],$match);
            if($match[1] === ProjectForm::codePrefix) {
                $filter['code'] = $match[2];
            }
        }


        $map = [
            'code'=>[
                'like',
                self::$aliasMap['project'] .'.code',
            ],
            'customer_name'=>[
                'like',
                self::$aliasMap['customer'] .'.name',
            ],
            'name'=>[
                'like',
                self::$aliasMap['project'] .'.name',
            ],
            'project_manager_name'=>[
                'like',
                self::$aliasMap['project_manager'] .'.name',
            ],
            'sales_name'=>[
                'like',
                self::$aliasMap['sales'] .'.name',
            ],
            'status'=>[
                '=',
                self::$aliasMap['project'] . '.status',
            ],
            'contract_status'=>[
                '=',
                self::$aliasMap['project'] . '.contract_status'
            ],
            'receive_money_status'=>[
                '=',
                self::$aliasMap['project'] . '.receive_money_status',
            ],
            'stamp_status'=>[
                '=',
                self::$aliasMap['project'] . '.stamp_status'
            ],
        ];

        $condition = [
            'and',
            [
                '<>',
                self::$aliasMap['project'] . '.enable',
                self::Disable,
            ],
            [
                '=',
                self::$aliasMap['project'] . '.status',
                ProjectConfig::StatusExecuteApplying,
            ],
        ];

        foreach ($filter as $index => $value) {
            $condition[] = [
                $map[$index][0],
                $map[$index][1],
                trim($value)
            ];
        }

        return $this->projectList([
            'project'=>[
                '*',
            ],
            'customer'=>[
                'name'
            ],
            'sales'=>[
                'name'
            ],
            'project_manager'=>[
                'name'
            ],
            'apply_active'=>[
                'name'
            ]
        ],$condition);
    }

    public function doneAssessList() {
        return $this->projectList(
            [
                'project'=>[
                    '*',
                ],
                'customer'=>[
                    'name'
                ],
                'sales'=>[
                    'name'
                ],
                'project_manager'=>[
                    'name'
                ],
                'apply_done'=>[
                    'name'
                ]
            ],
            [
                'and',
                [
                    '<>',
                    self::$aliasMap['project'] . '.enable',
                    self::Disable,
                ],
                [
                    '=',
                    self::$aliasMap['project'] . '.status',
                    ProjectConfig::StatusDoneApplying,
                ]
            ]
        );
    }

    public function activeAssessList() {
        return $this->projectList(
            [
                'project'=>[
                    '*',
                ],
                'customer'=>[
                    'name'
                ],
                'sales'=>[
                    'name'
                ],
                'project_manager'=>[
                    'name'
                ],
                'apply_active'=>[
                    'name'
                ]
            ],
            [
                'and',
                [
                    '<>',
                    self::$aliasMap['project'] . '.enable',
                    self::Disable,
                ],
                [
                    '=',
                    self::$aliasMap['project'] . '.status',
                    ProjectConfig::StatusExecuteApplying,
                ]
            ]
        );
    }

    public function activeAttachmentDelete($uuid, $path) {
        return $this->deleteAttachment($uuid, $path, 'active_attachment');
    }

    public function doneAttachmentDelete($uuid, $path) {
        return $this->deleteAttachment($uuid, $path, 'done_attachment');
    }

    public function budgetAttachmentDelete($uuid, $path) {
        return $this->deleteAttachment($uuid, $path, 'budget_attachment');
    }

    public function deleteAttachment($uuid, $path, $filed) {
        if(empty($uuid) || empty($path)) {
            return false;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return false;
        }

        $attachments = unserialize($record->$filed);
        foreach($attachments as $index => $item) {
            if($item === $path) {
                unset($attachments[$index]);
                break;
            }
        }

        $record->$filed = serialize($attachments);
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

    public function applyActiveValidate($uuid) {
        if (empty($uuid)) {
            return false;
        }

        $record = self::find()->alias('t1')->select([
            't1.active_attachment','t4.contact_uuid','t3.stamp_uuid stamp_uuid','t6.contract_uuid'
        ])->leftJoin(self::CRMCustomerProjectMap . ' t2', 't1.uuid = t2.project_uuid')
            ->leftJoin(self::CRMCustomerStampMap . ' t3', 't3.customer_uuid = t2.customer_uuid')
            ->leftJoin(self::CRMProjectContactMap . ' t4', 't4.project_uuid = t1.uuid')
            ->leftJoin(self::CRMContact . ' t5', 't5.uuid = t4.contact_uuid and t5.enable=' . Contact::Enable)
            ->leftJoin(self::CRMProjectContractMap . ' t6', 't6.project_uuid = t1.uuid')
            ->andWhere([
                '=',
                't1.uuid',
                $uuid
            ])->asArray()->one();
        if (empty($record)) {
            return -1;
        }

        $_return = null;
        if (empty($record['active_attachment']) || empty(unserialize($record['active_attachment']))) {
            $_return = '项目立项资料尚未上传';
        }

        if (empty($record['contact_uuid'])) {
            $_return .= '<br>项目没有联系人或是联系人都已离职';
        }

        if (empty($record['stamp_uuid'])) {
            $_return .= '<br>项目对应的客户没有开票信息';
        }

        if (empty($record['contract_uuid'])) {
            $_return .= '<br>该项目没有创建合同';
        }

        if (!empty($_return)) {
            return $_return;
        }
        return 1;
    }

    public function applyActive($uuid) {
        if (empty($uuid)) {
            return true;
        }

        return $this->updateRecord([
            'uuid'=>$uuid,
            'status'=>ProjectConfig::StatusExecuteApplying,
            'active_time'=>time(),
            'apply_active_uuid'=>Yii::$app->getUser()->getIdentity()->getId(),
        ]);
    }

    public function applyDoneValidate($uuid) {
        if (empty($uuid)) {
            return 1;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if (empty($record)) {
            return 1;
        }

        if(empty($record->done_attachment) || empty(unserialize($record->done_attachment))) {
            return '结案失败！结案资料缺失';
        }

        return 1;
    }

    public function applyDone($uuid) {
        return $this->updateRecord([
            'uuid'=>$uuid,
            'status'=>ProjectConfig::StatusDoneApplying,
            'apply_done_uuid'=>Yii::$app->getUser()->getIdentity()->getId(),
            'done_time'=>time(),
        ]);
    }

    public function activeAssessPassed($uuid) {
        if (empty($uuid)) {
            return true;
        }

        return $this->updateRecord([
            'uuid'=>$uuid,
            'status'=>ProjectConfig::StatusExecuting,
            'active_assess_uuid'=>Yii::$app->getUser()->getIdentity()->getId(),
        ]);
    }

    public function activeAssessRefused($formData) {
        return $this->updateRecord([
            'uuid'=>$formData['uuid'],
            'status'=>ProjectConfig::StatusTouching,
            'active_assess_uuid'=>Yii::$app->getUser()->getIdentity()->getId(),
            'active_assess_refuse_reason'=>$formData['refuse_reason'],
        ]);
    }

    public function doneAssessPassed($uuid) {
        if (empty($uuid)) {
            return true;
        }

        return $this->updateRecord([
            'uuid'=>$uuid,
            'status'=>ProjectConfig::StatusDone,
            'done_assess_uuid'=>Yii::$app->getUser()->getIdentity()->getId(),
        ]);
    }

    public function doneAssessRefused($formData) {
        return $this->updateRecord([
            'uuid'=>$formData['uuid'],
            'status'=>ProjectConfig::StatusExecuting,
            'done_assess_uuid'=>Yii::$app->getUser()->getIdentity()->getId(),
            'done_assess_refuse_reason'=>$formData['refuse_reason'],
        ]);
    }
}