<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/2 0002
 * Time: 下午 12:31
 */

namespace backend\modules\crm\models\customer\record;

use backend\models\interfaces\DeleteMapRecord;
use backend\models\MyPagination;
use backend\modules\crm\models\customer\model\PublicCustomerForm;
use backend\modules\hr\models\EmployeeBasicInformation;
use backend\modules\rbac\model\RBACManager;
use Yii;
use backend\models\interfaces\Map;
use backend\models\interfaces\PrimaryTable;
use backend\models\interfaces\RecordOperator;
use backend\modules\fin\models\contract\ContractTemplateRecord;
use backend\models\UUID;
use backend\modules\fin\models\contract\ContractBaseRecord;
use backend\modules\fin\models\FINBaseRecord;
use yii\data\Pagination;
use backend\modules\crm\models\customer\model\CustomerConfig;
use yii\db\Exception;
use yii\helpers\Json;

class CustomerContractMap extends CustomerBaseRecord implements RecordOperator,DeleteMapRecord,PrimaryTable
{
    public static $aliasMap = [
        'customer_contract_map'=>'t1',
        'contract'=>'t2',
        'customer'=>'t3',
        'duty'=>'t4',
        'customer_advance'=>'t5',
        'sales'=>'t6',
    ];
    public static function tableName()
    {
        return self::CRMCustomerContractMap;
    }

    public function updateSingleRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }
        $record = self::find()->andWhere(['contract_uuid'=>$formData['uuid']])->one();
        if(!parent::updatePreHandler($formData,$record)) {
            return true;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $contract = new ContractBaseRecord();
            $contract->updateRecord($formData);
            $record->update();

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
        // TODO: Implement updateRecord() method.
    }

    public function insertRecord($formData)
    {
        // TODO: Implement insertRecord() method.
    }

    public function formDataPreHandler(&$formData,$record)
    {
        if (!isset($formData['uuid']) || empty($formData['uuid'])) {
            $formData['uuid'] = UUID::getUUID();
        }
        $formData['contract_uuid'] = $formData['uuid'];
        $formData['path_dir'] = "/upload/contract/customer/".$formData['uuid'];
        // 表示销售合同
        $formData['type'] = PublicCustomerForm::codePrefix;
        if (empty($record)) {
            parent::clearEmptyField($formData);
        }
        parent::formDataPreHandler($formData, $record);
    }

    public function insertSingleRecord($formData)
    {
        if(empty($formData) || empty($formData['customer_uuid'])) {
            return true;
        }

        if (!parent::updatePreHandler($formData)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            $contract = new ContractBaseRecord();
            if ($contract->insertRecord($formData)) {
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

    public function getRecordByUuid($uuid)
    {
        if(empty($uuid)) {
            return null;
        }

        return $this->contractList(
            [
                'contract'=>[
                    '*'
                ],
                'customer'=>[
                    'name',
                    'uuid',
                ],
                'duty'=>[
                    'name',
                ],
                'sales'=>[
                    'name'
                ],
            ],
            [
                '=',
                self::$aliasMap['contract'] . '.uuid',
                $uuid
            ],
            false
        );
    }

    public function deleteRecordByUuid($uuid)
    {
        // TODO: Implement deleteRecordByUuid() method.
    }

    public function templateList() {
        $templateList = (new ContractTemplateRecord())->contractTemplateList();
        $templateList = $templateList['list'];
        $list = parent::dropDownListDataBuilder($templateList, 'uuid', 'name');
        $list[0] = '无';
        return $list;
    }

    public function listFilter($filter) {
        if(empty($filter)) {
            return $this->myContractList();
        }

        $this->handlerFormDataTime($filter,'min_time');
        $this->handlerFormDataTime($filter,'max_time');

        $map = [
            'customer_name'=>[
                'like',
                'customer',
            ],
            'code'=>[
                'like',
                'contract',
                'code'
            ],
            'min_time'=>[
                '>=',
                'contract',
                'sign_time'
            ],
            'max_time'=>[
                '<=',
                'contract',
                'sign_time'
            ],
            'duty_name'=>[
                'like',
                'duty',
                'name'
            ],
            'status'=>[
                '=',
                'contract',
                'status'
            ],
            'min_money'=>[
                '>=',
                'contract',
                'money',
            ],
            'max_money'=>[
                '<=',
                'contract',
                'money',
            ],
        ];
        $condition = [
            'and'
        ];
        if(Yii::$app->getUser()->getIdentity()->getUserName() != 'admin') {
            $uuids = $this->getOrdinateUuids(RBACManager::CustomerModule);
            $condition = [
                'in',
                self::$aliasMap['customer_advance'] . '.sales_uuid',
                $uuids
            ];
        }

        foreach ($filter as $key => $value) {
            $condition[] = [
                $map[$key][0],
                self::$aliasMap[$map[$key][1]] . '.' . $map[$key][2],
                $value
            ];
        }
        
        return $this->contractList(
            [
                'contract'=>[
                    '*'
                ],
                'customer'=>[
                    'name',
                    'uuid',
                ],
                'duty'=>[
                    'name',
                ],
            ], $condition);
    }

    public function myContractList() {
        $condition = [];
        if(Yii::$app->user->getIdentity()->getUserName() != 'admin') {
            $uuids = $this->getOrdinateUuids(RBACManager::CustomerModule);
            $condition = [
                'in',
                self::$aliasMap['customer_advance'] . '.sales_uuid',
                $uuids
            ];
        }
        return $this->contractList(
            [
                'contract'=>[
                    '*'
                ],
                'customer'=>[
                    'name',
                    'uuid',
                ],
                'duty'=>[
                    'name',
                ],
            ],
            $condition
        );
    }
    
    public function contractListByCustomerUuid($uuid) {
        return $this->contractList(
            [
                'contract'=>[
                    '*'
                ],
                'customer'=>[
                    'name',
                    'uuid',
                ],
                'duty'=>[
                    'name',
                ]
            ],
            [
                '=',
                self::$aliasMap['customer'] . '.uuid',
                $uuid
            ]
        );
    }

    public function contractList($selects, $conditions = null, $enablePage = true) {
        
        $selector = [];

        if (!empty($selects)) {
            foreach(self::$aliasMap as $key=>$alias) {
                if (isset($selects[$key])) {
                    foreach($selects[$key] as $select) {
                        $select = trim($select);
                        if ($key === 'project_contract_map' || $key === 'contract') {
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
            ->leftJoin(FINBaseRecord::FinContract . " t2", 't2.uuid = t1.contract_uuid')
            ->leftJoin(self::CRMCustomerBasic . " t3", 't1.customer_uuid = t3.uuid')
            ->leftJoin(EmployeeBasicInformation::$tableName . " t4", 't2.duty_uuid = t4.uuid')
            ->leftJoin(self::CRMCustomerAdvance . ' t5', 't5.customer_uuid = t3.uuid')
            ->leftJoin(EmployeeBasicInformation::$tableName . ' t6','t6.uuid = t5.sales_uuid');
        //
        if(!empty($conditions)) {
            $query->andWhere($conditions);
//            foreach(self::$aliasMap as $key=>$alias) {
//                if (isset($conditions[$key])) {
//                    foreach($conditions[$key] as $condition) {
//                        $condition = trim($condition);
//                        $query->andWhere(
//                            $alias . "." . $condition
//                        );
//                    }
//                }
//            }
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

    public function deleteSingleRecord($uuid1, $uuid2)
    {
        if(empty($uuid2) || empty($uuid1)) {
            return false;
        }

        $record = self::find()->andWhere(['customer_uuid'=>$uuid2, 'contract_uuid'=>$uuid1])->one();
        if(empty($record)) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            $record->delete();
            $contract = new ContractBaseRecord();
            $contract->deleteRecordByUuid($uuid1);
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }
}