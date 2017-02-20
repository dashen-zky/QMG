<?php

namespace backend\modules\crm\models\customer\record;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/13 0013
 * Time: 上午 12:34
 */
use backend\models\interfaces\PrimaryTable;
use backend\models\interfaces\RecordOperator;
use backend\modules\crm\models\customer\model\ContactForm;
use backend\modules\crm\models\customer\model\CustomerBaseForm;
use backend\modules\crm\models\customer\model\CustomerConfig;
use backend\modules\crm\models\customer\model\PublicCustomerForm;
use backend\modules\crm\models\customer\record\CustomerBaseRecord;
use backend\modules\hr\models\EmployeeBasicInformation;
use yii\db\Exception;
use Yii;
use backend\models\UUID;
use backend\models\MyPagination;
use yii\helpers\Json;
use backend\modules\rbac\model\PermissionManager;

class PublicCustomer extends Customer implements PrimaryTable,RecordOperator
{

    public $employee_name;
    public $contact_name;
    public $customer_business_business_id;
    public static $aliasMap = [
        'customer'=>'t1',
        'customer_business'=>'t2',
        'customer_contact_map'=>'t3',
        'contact'=>'t4',
        'employee'=>'t5',
        'customer_advance'=>'t6',
        'sales'=>'t7',
    ];
    static public function tableName()
    {
        return self::CRMCustomerBasic;
    }

    public function getRecordByUuid($uuid)
    {
        $publicCustomer = self::find()->andWhere(['uuid'=>$uuid])->asArray()->one();
        $business = new CustomerBusinessMap();
        $requireList = $business->getRecordListByCustomerUuid($uuid);
        $contact = new Contact();
        $contactList = $contact->getContactListByObjectUuid($uuid);
        // 处理required域
        $config = (new CustomerConfig())->generateConfig();
        $businessList = $config['business'];
        $publicCustomer['requireIds'] = '';
        $publicCustomer['requireAnalyse'] = '';
        for($i = 0; $i < count($requireList); $i++) {
            $publicCustomer['requireIds'] .= " " . $requireList[$i]['business_id'];
            if(isset($businessList[$requireList[$i]['business_id']])) {
                $publicCustomer['requireAnalyse'] .= " " .$businessList[$requireList[$i]['business_id']];
            }
        }
        $publicCustomer['requireIds'] = trim($publicCustomer['requireIds']);
        $publicCustomer['requireAnalyse'] = trim($publicCustomer['requireAnalyse']);
        return [
            'publicCustomer'=>$publicCustomer,
            'requireList'=>$requireList,
            'contactList'=>$contactList,
        ];
    }


    public function allList() {
        $userName = Yii::$app->user->getIdentity()->getUserName();
        $condition = [];
        if($userName !== 'admin') {
            $condition = [
                '=',
                self::$aliasMap['customer'] . '.enable',
                self::Enable,
            ];
        }

        $list = $this->publicCustomerList(
            [
                'customer'=>[
                    '*'
                ],
                'customer_business'=>[
                    'business_id',
                ],
                'contact'=>[
                    'uuid',
                    'name'
                ],
                'customer_contact_map' =>[
                  'type',
                ],
                'employee'=>[
                  'name',
                ],
                'customer_advance'=>[
                    'level',
                ],
                'sales'=>[
                    'name',
                ]
            ],
            $condition
        );
        $publicCustomerList = &$list['publicCustomerList'];
        // 查出来的contact name 以及business id 会重复，程序上进行一下过滤
        for ($i = 0; $i < count($publicCustomerList); $i++) {
            if (isset($publicCustomerList[$i]['customer_business_business_id'])
                && !empty($publicCustomerList[$i]['customer_business_business_id'])) {
                $publicCustomerList[$i]['customer_business_business_id'] =
                    $this->getDistinctValueAsArray(
                        explode(",",$publicCustomerList[$i]['customer_business_business_id'])
                    );
            }
            if (isset($publicCustomerList[$i]['contact_name'])
                && !empty($publicCustomerList[$i]['contact_name'])) {
                $publicCustomerList[$i]['contact'] =
                    $this->filterRepeatFieldAsArray($publicCustomerList[$i]['contact_uuid'],$publicCustomerList[$i]['contact_name']);
            }
        }
        return $list;
    }

    public function publicCustomerList($selects, $conditions = null,$fetchOne = false) {

        $selector = [];

        if (!empty($selects)) {
            foreach(self::$aliasMap as $key=>$alias) {
                if (isset($selects[$key])) {
                    foreach($selects[$key] as $select) {
                        if (in_array($key, [
                            'employee',
                            'customer_advance',
                            'sales'
                        ])) {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select . " " . $key . "_" .$select;
                        } elseif ($key === 'customer') {
                            $select = trim($select);
                            $selector[] = $alias ."." . $select;
                        } else {
                            $select = trim($select);
                            $selector[] = "group_concat(".$alias ."." . $select .") " . $key . "_" .$select;
                        }
                    }
                }
            }
        }


        $query = self::find()
            ->alias('t1')
            ->select($selector)
            ->leftJoin(self::CRMCustomerBusinessMap . ' t2', 't1.uuid = t2.customer_uuid')
            ->leftJoin(self::CRMCustomerContactMap . ' t3', 't1.uuid = t3.customer_uuid')
            ->leftJoin(self::CRMContact . ' t4','t3.contact_uuid = t4.uuid')
            ->leftJoin(EmployeeBasicInformation::$tableName . " t5", "t5.uuid = t1.em_uuid")
            ->leftJoin(self::CRMCustomerAdvance . ' t6', 't1.uuid = t6.customer_uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t7', 't6.sales_uuid = t7.uuid')
            ->groupBy('t1.uuid');
        //
        if(!empty($conditions)) {
            $query->andWhere($conditions);
        }
        if ($fetchOne) {
            return $query->asArray()->one();
        }
        $pagination = new MyPagination([
            'totalCount'=>$query->count(),
            'pageSize' => self::PageSize,
        ]);
        $publicCustomerList = $query->orderBy('id DESC')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data = [
            'pagination' => $pagination,
            'publicCustomerList'=> $publicCustomerList,
        ];
        return $data;
    }

    public function formDataPreHandler(&$formData, $record)
    {
        if(empty($record)) {
            if(!isset($formData['uuid']) || empty($formData['uuid'])) {
                $formData['uuid'] = UUID::getUUID();
            }
            $formData['enable'] = self::Enable;
        }
        parent::formDataPreHandler($formData, $record);
    }

    public function insertRecord($formData)
    {
        if (empty($formData) || empty($formData['full_name'])) {
            return true;
        }

        if (!$this->updatePreHandler($formData)) {
            return true;
        }
        $businessMap = new CustomerBusinessMap();
        $contactMap = new CustomerContactMap();
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->time = time();
            // 添加人
            $this->em_uuid = Yii::$app->user->getIdentity()->getId();
            // 客户编号
            $customerConfig = new CustomerConfig();
            $this->code = $customerConfig->generateCustomerCode();
            $this->insert();
            // type 1表示是需求板块 type 2表示业务板块,新增客户的时候只能新增需求板块
            if(isset($formData['business']) && !empty($formData['business'])) {
                $businessMap->insertRecord($formData);
            }
            // 添加联系人
            if(isset($formData['contactUuids']) && !empty($formData['contactUuids'])) {
                $contactMap->insertRecord([
                    'customer_uuid'=>$formData['uuid'],
                    'contactUuids'=>$formData['contactUuids'],
                ]);
            }
            //将编号+1存入数据库

            $config = $customerConfig->generateConfig();
            $config['customer_code'] = intval($this->code) + 1;
            $customerConfig->updateDateConfigByJsonString(Json::encode($config));
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
        if (empty($formData) || empty($formData['uuid'])) {
            return true;
        }

        // 在编辑联系人的时候，我们已经更新过customer_contact_map了，所以不需要在更新map表了
        $businessMap = new CustomerBusinessMap();
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
            if (!empty($record) && $this->updatePreHandler($formData, $record)) {
                $record->update();
            }
            // 更新需求模块
            if(isset($formData['business']) && !empty($formData['business'])) {
                $businessMap->updateRecord($formData);
            }
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    // 逻辑删除客户
    public function deleteRecordByUuid($uuid)
    {
        if(empty($uuid)) {
            return false;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return true;
        }

        $record->enable = self::Disable;
        $value = $record->getDirtyAttributes();
        if(empty($value)) {
            return true;
        }

        return $record->update();
    }

    public function obtain($uuid) {
        if (empty($uuid) || !$uuid) {
            return false;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return false;
        }

        $record->public_tag = self::privateTag;
        return $record->update();
    }

    public function listFilter($filter) {
        if(empty($filter)) {
            return $this->allList();
        }
        if(isset($filter['code']) && !empty($filter['code'])) {
            preg_match('/([a-zA-Z]*)(\d+)/',$filter['code'],$match);
            if($match[1] === PublicCustomerForm::codePrefix) {
                $filter['code'] = $match[2];
            }
        }
        $this->handlerFormDataTime($filter,'min_time');
        $this->handlerFormDataTime($filter,'max_time');

        $map = [
            'code'=>[
                'like',
                'customer',
                'code',
            ],
            'name'=>[
                'like',
                'customer',
                'name',
            ],
            'level'=>[
                '=',
                'customer_advance',
                'level',
            ],
            'require'=>[
                '=',
                'customer_business',
                'business_id',
            ],
            'status'=>[
                '=',
                'customer',
                'status',
            ],
            'sales_name'=>[
                'like',
                'sales',
                'name',
            ],
            'type'=>[
                '=',
                'customer',
                'type',
            ],
            'min_time'=>[
                '>=',
                'customer',
                'last_touch_time',
            ],
            'max_time'=>[
                '<=',
                'customer',
                'last_touch_time',
            ],
            'intent_level'=>[
                '=',
                'customer',
                'intent_level',
            ],
            'industry'=>[
                '=',
                'customer',
                'industry',
            ],
        ];

        $condition = [
            'and',
        ];
        if(Yii::$app->user->getIdentity()->getEmployeeName() != 'admin') {
            $condition[] = [
                '=',
                self::$aliasMap['customer'] . '.enable',
                self::Enable,
            ];
        }

        foreach($filter as $key=>$value) {
            $condition[] = [
                $map[$key][0],
                self::$aliasMap[$map[$key][1]] . '.' . $map[$key][2],
                $value
            ];
        }
        $list = $this->publicCustomerList(
            [
                'customer'=>[
                    '*'
                ],
                'customer_business'=>[
                    'business_id',
                ],
                'contact'=>[
                    'uuid',
                    'name'
                ],
                'customer_contact_map' =>[
                    'type',
                ],
                'employee'=>[
                    'name',
                ],
                'customer_advance'=>[
                    'level',
                ],
                'sales'=>[
                    'name',
                ]
            ],
            $condition
        );
        $publicCustomerList = &$list['publicCustomerList'];
        // 查出来的contact name 以及business id 会重复，程序上进行一下过滤
        for ($i = 0; $i < count($publicCustomerList); $i++) {
            if (isset($publicCustomerList[$i]['customer_business_business_id'])
                && !empty($publicCustomerList[$i]['customer_business_business_id'])) {
                $publicCustomerList[$i]['customer_business_business_id'] =
                    $this->getDistinctValueAsArray(
                        explode(",",$publicCustomerList[$i]['customer_business_business_id'])
                    );
            }
            if (isset($publicCustomerList[$i]['contact_name'])
                && !empty($publicCustomerList[$i]['contact_name'])) {
                $publicCustomerList[$i]['contact'] =
                    $this->filterRepeatFieldAsArray($publicCustomerList[$i]['contact_uuid'],$publicCustomerList[$i]['contact_name']);
            }
        }
        return $list;
    }
}