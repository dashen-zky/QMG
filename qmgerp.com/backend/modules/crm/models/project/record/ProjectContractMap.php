<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/29 0029
 * Time: 下午 4:58
 */

namespace backend\modules\crm\models\project\record;

use backend\models\interfaces\DeleteMapRecord;
use backend\models\interfaces\PrimaryTable;
use backend\models\MyPagination;
use backend\modules\crm\models\project\model\ProjectForm;
use backend\modules\fin\models\contract\ContractBaseRecord;
use backend\modules\fin\models\FINBaseRecord;
use backend\modules\hr\models\EmployeeBasicInformation;
use backend\modules\rbac\model\RBACManager;
use Yii;
use backend\models\interfaces\RecordOperator;
use backend\models\UUID;
use backend\modules\fin\models\contract\ContractTemplateRecord;
use yii\db\Exception;

class ProjectContractMap extends ProjectBaseRecord implements DeleteMapRecord,RecordOperator,PrimaryTable
{
    public static $aliasMap = [
        'project_contract_map'=>'t1',
        'contract'=>'t2',
        'project'=>'t3',
        'project_customer_map' => 't4',
        'customer'=>'t5',
        'duty'=>'t6',
        'project_manager'=>'t7',
        'customer_advance' => 't8',
        'project_member_map'=>'t9',

    ];
    public static function tableName()
    {
        return self::CRMProjectContractMap;
    }

    public function updateRecord($formData)
    {
        // TODO: Implement updateRecord() method.
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

    public function insertRecord($formData)
    {
        // TODO: Implement insertRecord() method.
    }

    public function formDataPreHandler(&$formData, $record)
    {
        if (!isset($formData['uuid']) || empty($formData['uuid'])) {
            $formData['uuid'] = UUID::getUUID();
        }
        $formData['contract_uuid'] = $formData['uuid'];
        $formData['path_dir'] = "/upload/contract/project/".$formData['uuid'];
        // 表示销售合同
        $formData['type'] = ProjectForm::codePrefix;
        parent::formDataPreHandler($formData, $record);
    }

    public function recordPreHandler(&$formData, $record = null)
    {

    }

    public function insertSingleRecord($formData)
    {
        if(empty($formData) || empty($formData['project_uuid'])) {
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

    public function templateList() {
        $templateList = (new ContractTemplateRecord())->contractTemplateList();
        $templateList = $templateList['list'];
        $list = parent::dropDownListDataBuilder($templateList, 'uuid', 'name');
        $list[0] = '无';
        return $list;
    }

    // 获取项目的合同列表
    public function contractListByProjectUuid($uuid) {
        $list = $this->contractList(
            [
                'contract'=>[
                    '*'
                ],
                'customer'=>[
                  'name',
                ],
                'project'=>[
                    'name',
                    'uuid',
                    'code',
                ],
                'duty'=>[
                    'name',
                ],
                'project_manager'=>[
                    'name',
                ],
            ],
            [
                '=',
                self::$aliasMap['project'] . '.uuid',
                $uuid
            ]
        );
        return $list;
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
            ->leftJoin(self::CRMProject . " t3", 't3.uuid = t1.project_uuid')
            ->leftJoin(self::CRMCustomerProjectMap . " t4", 't4.project_uuid = t3.uuid')
            ->leftJoin(self::CRMCustomerBasic . ' t5', 't5.uuid = t4.customer_uuid')
            ->leftJoin(self::CRMCustomerAdvance . ' t8', 't5.uuid = t8.customer_uuid')
            ->leftJoin(EmployeeBasicInformation::$tableName . " t6", 't6.uuid = t2.duty_uuid')
            ->leftJoin(EmployeeBasicInformation::$tableName . " t7",'t3.project_manager_uuid = t7.uuid')
            ->leftJoin(self::CRMProjectMemberMap . ' t9', 't3.uuid = t9.project_uuid');
        //
        if(!empty($conditions)) {
            $query->andWhere($conditions);
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

    public function getRecordByUuid($uuid)
    {
        if(empty($uuid)) {
            return true;
        }

        return $this->contractList(
            [
                'customer'=>[
                    'name',
                ],
                'project'=>[
                    'name',
                    'uuid',
                    'code',
                ],
                'project_manager'=>[
                    'name',
                ],
                'duty'=>[
                    'name'
                ],
                'contract'=>[
                    '*'
                ],
            ],
            [
                '=',
                self::$aliasMap['contract'] . '.uuid',
                $uuid,
            ],false
        );
    }

    public function listFilter($filter) {
        if(empty($filter)) {
            return $this->myProjectContractList();
        }
        if(isset($filter['code']) && !empty($filter['code'])) {
            preg_match('/([a-zA-Z]*)(\d+)/',$filter['code'],$match);
            if(isset($match[1]) && $match[1] === 'P') {
                $filter['code'] = $match[2];
            }
        }

        if(isset($filter['project_code']) && !empty($filter['project_code'])) {
            preg_match('/([a-zA-Z]*)(\d+)/',$filter['project_code'],$match);
            if(isset($match[1]) && $match[1] === ProjectForm::codePrefix) {
                $filter['project_code'] = $match[2];
            }
        }

        $map = [
            'code'=>[
                'like',
                'contract',
                'code',
            ],
            'project_code'=>[
                'like',
                'project',
                'code',
            ],
            'customer_name'=>[
                'like',
                'customer',
                'name',
            ],
            'project_name'=>[
                'like',
                'project',
                'name',
            ],
            'duty_name'=>[
                'like',
                'duty',
                'name',
            ],
            'status'=>[
                '=',
                'contract',
                'status',
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
            'and',
        ];
        if(Yii::$app->user->getIdentity()->getUserName() !== 'admin') {
            $condition[] = [
                'or',
                [
                    'in',
                    self::$aliasMap['project'] . '.project_manager_uuid',
                    $this->getOrdinateUuids(RBACManager::ProjectModule),
                ],
                [
                    'in',
                    self::$aliasMap['customer_advance'] . '.sales_uuid',
                    $this->getOrdinateUuids(RBACManager::CustomerModule),
                ],
                [
                    '=',
                    self::$aliasMap['project_member_map'] . '.member_uuid',
                    Yii::$app->getUser()->getIdentity()->getId(),
                ]
            ];
        }

        foreach ($filter as $index => $value) {
            $condition[] = [
                $map[$index][0],
                self::$aliasMap[$map[$index][1]] . '.' . $map[$index][2],
                trim($value),
            ];
        }

        $list = $this->contractList(
            [
                'contract'=>[
                    '*'
                ],
                'customer'=>[
                    'name',
                ],
                'project'=>[
                    'name',
                    'uuid',
                    'code',
                ],
                'duty'=>[
                    'name',
                ],
                'project_manager'=>[
                    'name',
                ],
            ],
            $condition
        );

        return $list;
    }

    public function myProjectContractList() {
        $condition = null;

        if(Yii::$app->user->getIdentity()->getUserName() !== 'admin') {
            $condition = [
                'or',
                [
                    'in',
                    self::$aliasMap['project'] . '.project_manager_uuid',
                    $this->getOrdinateUuids(RBACManager::ProjectModule),
                ],
                [
                    'in',
                    self::$aliasMap['customer_advance'] . '.sales_uuid',
                    $this->getOrdinateUuids(RBACManager::CustomerModule),
                ],
                [
                    '=',
                    self::$aliasMap['project_member_map'] . '.member_uuid',
                    Yii::$app->getUser()->getIdentity()->getId(),
                ]
            ];
        }

        return $this->contractList(
            [
                'contract'=>[
                    '*'
                ],
                'customer'=>[
                    'name',
                ],
                'project'=>[
                    'name',
                    'uuid',
                    'code',
                ],
                'duty'=>[
                    'name',
                ],
                'project_manager'=>[
                    'name',
                ],
            ],
            $condition
        );
    }

    public function deleteRecordByUuid($uuid)
    {
        // TODO: Implement deleteRecordByUuid() method.
    }

    // uuid1 是合同的uuid,uuid2是项目的uuid
    public function deleteSingleRecord($uuid1, $uuid2)
    {
        if(empty($uuid2) || empty($uuid1)) {
            return false;
        }

        $record = self::find()->andWhere(['project_uuid'=>$uuid2, 'contract_uuid'=>$uuid1])->one();
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