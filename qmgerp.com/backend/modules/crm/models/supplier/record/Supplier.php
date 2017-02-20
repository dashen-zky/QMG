<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/3 0003
 * Time: 下午 7:06
 */

namespace backend\modules\crm\models\supplier\record;


use backend\models\interfaces\PrimaryTable;
use backend\models\interfaces\RecordOperator;
use backend\models\UUID;
use backend\modules\crm\models\customer\record\Contact;
use backend\modules\crm\models\supplier\model\SupplierConfig;
use backend\modules\crm\models\supplier\model\SupplierForm;
use Yii;
use yii\db\Exception;
use backend\models\MyPagination;
use yii\helpers\Json;
use backend\modules\rbac\model\PermissionManager;

class Supplier extends SupplierBaseRecord implements RecordOperator,PrimaryTable
{
    public static function tableName()
    {
        return self::CRMSupplier;
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

        $record = SupplierContactMap::find()->andWhere(['supplier_uuid'=>$uuid])->one();
        if(empty($record)) {
            return false;
        }

        $record = SupplierFinAccountMap::find()->andWhere(['supplier_uuid'=>$uuid])->one();
        if(empty($record)) {
            return false;
        }

        return Yii::$app->authManager->canAccess(PermissionManager::SupplierAndPartTimeAccess);
    }

    public function updateRecord($formData)
    {
        if(empty($formData) || !isset($formData['name']) || empty($formData['name'])) {
            return true;
        }
        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(empty($record) || !parent::updatePreHandler($formData, $record)) {
            return true;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $record->update();
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
        if(!isset($formData['uuid']) || empty($formData['uuid'])) {
            $formData['uuid'] = UUID::getUUID();
        }
        if(empty($record)) {
            parent::clearEmptyField($formData);
        }
        parent::formDataPreHandler($formData, $record);
        // 如果manager_uuid不为空的话，表示已经被分配了
        if(isset($formData['manager_uuid'])
            && !empty($formData['manager_uuid'])
            && $formData['manager_uuid'] != 0) {
            $formData['allocate'] = SupplierConfig::Allocated;
        }
    }

    public function insertRecord($formData)
    {
        if(empty($formData) || !isset($formData['name']) || empty($formData['name'])) {
            return true;
        }
        if(!parent::updatePreHandler($formData)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->code = (new SupplierForm())->generateSupplierCode();
            $this->insert();

            $supplierConfig = new SupplierConfig();
            $config = $supplierConfig->generateConfig();
            $config['supplier_code'] = $this->code + 1;
            $supplierConfig->updateDateConfigByJsonString(Json::encode($config));
            if(isset($formData['contactUuids']) && !empty($formData['contactUuids'])) {
                $supplierContactMap = new SupplierContactMap();
                $supplierContactMap->insertRecord([
                    'contactUuids'=>$formData['contactUuids'],
                    'supplier_uuid'=>$formData['uuid'],
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

    public function getRecordByUuid($uuid)
    {
        if(empty($uuid)) {
            return null;
        }

        $supplier = $this->supplierList(
            [
                'supplier'=>[
                    '*'
                ],
                'manager'=>[
                    'name',
                    'uuid',
                ]
            ],
            [
                'supplier'=>
                    [
                        'uuid="'.$uuid.'"',
                    ]
            ],false
        );
        $contact = new Contact();
        $contactList = $contact->getContactListByObjectUuid($uuid, 'supplier');
        return [
            'supplier'=>$supplier,
            'contactList'=>$contactList,
        ];
    }

    public function deleteRecordByUuid($uuid)
    {
        // TODO: Implement deleteRecordByUuid() method.
    }

    public function allSupplierList() {

        return $this->supplierList(
            [
                'supplier'=>[
                    '*',
                ],
                'manager'=>[
                    'name',
                    'uuid',
                ]
            ],
            [

            ]
        );
    }
    //  我管理的供应商,
    // manager是我管理的供应商
    // created是我创建的供应商
    public function mySupplierList($filed) {
        // 既不是创建者，又不是管理者，其他字符串不认
        if(empty($filed) || ($filed !== 'created' && $filed !== 'manager')) {
            return [];
        }

        $condition = [];
        $map = [
            'created'=>'created_uuid',
            'manager'=>'manager_uuid',
        ];

        $userId = Yii::$app->user->getIdentity()->getId();
        if($filed === 'created') {
            $condition = [
                'supplier'=> [
                    $map[$filed] => [
                        '=',
                        $userId,
                    ]
                ]
            ];
        } else {

        }
        return $this->supplierList(
            [
                'supplier'=>[
                    '*'
                ],
                'manager'=>[
                    'name',
                    'uuid',
                ]
            ],
            $condition
        );
    }



    public function supplierList($selects, $conditions = null, $enablePage = true) {
        $aliasMap = [
            'supplier'=>'t1',
            'manager'=>'t2'
        ];
        $selector = [];

        if (!empty($selects)) {
            foreach($aliasMap as $key=>$alias) {
                if (isset($selects[$key])) {
                    foreach($selects[$key] as $select) {
                        $select = trim($select);
                        if ($key === 'supplier') {
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
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t2', 't2.uuid = t1.manager_uuid');
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
        $list = $query->orderBy('id DESC')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data = [
            'pagination' => $pagination,
            'list'=> $list,
        ];
        return $data;
    }

    public function listFilter($filter)
    {
        if(empty($filter)) {
            return $this->allSupplierList();
        }

        if(isset($filter['code']) && !empty($filter['code'])) {
            preg_match('/([a-zA-Z]*)(\d+)/',$filter['code'],$match);
            if($match[1] === SupplierForm::codePrefix) {
                $filter['code'] = $match[2];
            }
        }
        $condition = [];
        foreach($filter as $key => $value) {
            if($key === 'name' || $key === 'code') {
                $condition['supplier'][] = $key . " like '%" . $value . "%'";
            } else {
                $condition['supplier'][] = $key . " = '" . $value . "'";
            }
        }
        $list = $this->supplierList(
            [
                'supplier'=>[
                    '*',
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
}