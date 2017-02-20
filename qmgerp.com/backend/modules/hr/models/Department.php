<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3 0003
 * Time: 下午 6:20
 */

namespace backend\modules\hr\models;


use backend\models\MyPagination;
use Yii;
use backend\models\UUID;
use backend\modules\hr\models\HrBaseActiveRecord;
use yii\data\Pagination;
use backend\modules\hr\models\hrinterfaces\HrRecordOperator;
use backend\modules\hr\models\hrinterfaces\HrPrimaryTable;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * department model
 *
 * @property integer $id
 * @property string $uuid
 * @property string $name
 * @property string $description
 * @property string $level
 * @property string $remarks
 * @property string $attachment
 */
class Department extends HrBaseActiveRecord implements HrRecordOperator,HrPrimaryTable
{
    public $parent_uuid;
    public $parent; // parent name
    public $parentDescription;
    public $parentLevel;
    public $parentRemarks;
    public $parentAttachment;
    static function tableName()
    {
        return self::DepartmentTableName;
    }

    public function parentDepartmentList($level) {
        if (0 === $level) {
            return null;
        }

        $parentLevel = $level - 1;
        return $this->getDepartmentListFromLevel($parentLevel);
    }

    public function getDepartmentListFromLevel($level) {
        $selector = [
            'department'=>[
                '*'
            ],
            'duty'=>[
                'name',
            ]
        ];
        switch ($level) {
            case 2:
                $selector['parent'] = [
                    'uuid',
                    'name',
                    'description',
                ];
                break;
            case 3:
                $selector['parent'] = [
                    'uuid',
                    'name',
                    'description',
                ];
                $selector['grand_parent'] = [
                    'name',
                ];
                break;
        }
        return $this->departmentList(
            $selector,
            [
            'department'=>[
                'level='.$level,
            ]
        ]);
    }

    public function getRecordByUuid($uuid) {
        if(!isset($uuid) || empty($uuid)) {
            return false;
        }
        return $this->departmentList(
            [
                'department'=>[
                    '*'
                ],
                'parent'=>[
                    'uuid',
                    'name',
                    'description',
                ],
                'duty'=>[
                    'name',
                    'uuid',
                ]
            ],
            [
            'department'=>[
                "uuid='". $uuid . "'",
            ]
        ],true);
    }


    public function getDepartmentsForDropDownList($level, $parent_uuid = null) {
        $query = $this->find()
            ->alias('t1')
            ->leftJoin(self::DepartmentRelationTableName . ' t2', 't1.uuid = t2.child_uuid')
            ->andWhere(['level'=>$level]);
        if(!empty($parent_uuid)) {
            $list = $query->andWhere(['t2.parent_uuid'=>$parent_uuid])->asArray()->all();
        } else {
            $list = $query->asArray()->all();
        }
        $_list = [];
        foreach($list as $item) {
            $_list[$item['uuid']]=$item['name'];
        }
        return $_list;
    }

    public function formDataPreHandler(&$formData, $record)
    {
        if(empty($record)) {
            if(!isset($formData['uuid']) || empty($formData['uuid'])) {
                $formData['uuid'] = UUID::getUUID();
            }
        }
        // 将父部门存入到元素中
        if(isset($formData['parent_uuid']) && !empty($formData['parent_uuid'])) {
            $parent = self::find()->andWhere(['uuid'=>$formData['parent_uuid']])->asArray()->one();
            if(!empty($parent) && !empty($parent['parent_departments'])) {
                $parent_departments = Json::decode($parent['parent_departments']);
                $parent_departments[$parent['level']] = $parent['uuid'];
                $formData['parent_departments'] = Json::encode($parent_departments);
            } elseif(!empty($parent)) {
                // 当添加事业部的时候，上级部门的上级部门信息是没有的，所以只要把公司的信息录入即可
                $parent_departments[$parent['level']] = $parent['uuid'];
                $formData['parent_departments'] = Json::encode($parent_departments);
            }
        }
        parent::formDataPreHandler($formData, $record);
    }

    public function insertRecord($formData)
    {
        if (empty($formData)) {
            return false;
        }
        // prepare department data
        if(!$this->updatePreHandler($formData)) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            parent::insert();
            $relation = new DepartmentRelation();
            if(isset($formData['parent_uuid']) && !empty($formData['parent_uuid'])) {
                $relation->insertSingleRecord([
                    'parent_uuid'=>$formData['parent_uuid'],
                    'child_uuid'=>$formData['uuid'],
                ]);
            }
            if(isset($formData['duty_uuid']) && !empty($formData['duty_uuid'])) {
                $departmentDutyMap = new DepartmentDutyMap();
                $departmentDutyMap->insertRecord([
                    'duty_uuid'=>$formData['duty_uuid'],
                    'department_uuid'=>$formData['uuid'],
                ]);
            }
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
        if (!isset($formData) || empty($formData)) {
            return false;
        }
        // prepare update data
        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(empty($record) || !$this->updatePreHandler($formData, $record)) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {

            if(isset($formData['parent_uuid']) && !empty($formData['parent_uuid'])) {
                $relation = new DepartmentRelation();
                $relation->updateSingleRecord([
                    'child_uuid'=>$formData['uuid'],
                    'parent_uuid'=>$formData['parent_uuid'],
                ]);

                $record->update();
            } else {
                // 如果没有设置该部门的上级部门，那么应该把上级部门的信息清空
                $relation = new DepartmentRelation();
                // 将上级部门的关系 清空
                $relation->deleteAll(['child_uuid'=>$formData['uuid']]);
                $record->parent_departments = '';
                $record->update();
            }
            $departmentDutyMap = new DepartmentDutyMap();
            $departmentDutyMap->updateRecord([
                'department_uuid'=>$formData['uuid'],
                'duty_uuid'=>isset($formData['duty_uuid'])?$formData['duty_uuid']:'',
            ]);
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

    }

    public function departmentListForDropDownList() {
        $list = $this->departmentList(
            [
                'department'=>[
                    'name',
                    'uuid',
                ]
            ],
            null,
            false,
            false
        );
        foreach($list as $item) {
            $_list[$item['uuid']] = $item['name'];
        }
        return $_list;
    }

    public function departmentList($selects, $conditions = null, $fetchOne = false, $pagination = true) {
        $aliasMap = [
            'department' => 't1',
            'relation' => 't2',
            'parent' => 't3',
            'duty_map'=>'t4',
            'duty'=>'t5',
            'paren_relation'=>'t6',
            'grand_parent'=>'t7',
        ];
        $selector = [];
        if (!empty($selects)) {
            foreach($aliasMap as $key=>$alias) {
                if (isset($selects[$key])) {
                    foreach($selects[$key] as $select) {
                        $select = trim($select);
                        if ($key === 'department') {
                            $selector[] = $alias ."." . $select;
                        } elseif($key === 'duty') {
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
            ->leftJoin(self::DepartmentRelationTableName . " t2", 't1.uuid = t2.child_uuid')
            ->leftJoin(self::DepartmentTableName." t3", "t3.uuid = t2.parent_uuid")
            ->leftJoin(self::DepartmentDutyMap . ' t4', 't4.department_uuid = t1.uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t5', 't4.duty_uuid = t5.uuid')
            ->leftJoin(self::DepartmentRelationTableName .' t6', 't6.child_uuid = t3.uuid')
            ->leftJoin(self::DepartmentTableName . ' t7','t7.uuid = t6.parent_uuid')
            ->groupBy('t1.uuid');

        if(!empty($conditions)) {
            foreach($aliasMap as $key=>$alias) {
                if (isset($conditions[$key])) {
                    foreach($conditions[$key] as $condition) {
                        $condition = trim($condition);
                        $query->andWhere(
                            $alias . "." . $condition
                        );
                    }
                }
            }
        }

        if ($fetchOne) {
            return $query->asArray()->one();
        }
        if (!$pagination) {
            return $query->orderBy('t1.code')->asArray()->all();
        }
        $pagination = New MyPagination([
            'totalCount' => $query->count(),
            'pageSize' => self::PageSize,
        ]);
        $departmentList = $query->orderBy('t1.code')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data = [
            'departmentList' => $departmentList,
            'pagination' => $pagination,
        ];
        return $data;
    }

    // 更新所有的部门的所有的父亲部门信息
    public function updateParentDepartments() {
        $list = self::find()->all();
        foreach($list as $department) {
            $level = $department->level;
            $parents = '';
            $uuid = $department->uuid;
            for($i = $level; $i > 0; $i--) {
                $parent_uuid = DepartmentRelation::find()
                    ->select(['parent_uuid'])
                    ->andWhere(['child_uuid'=>$uuid])
                    ->asArray()->one();
                var_dump($parent_uuid);
                echo '<br>';
                if(empty($parent_uuid['parent_uuid'])) {
                    break;
                }
                $parents[$i-1] = $parent_uuid['parent_uuid'];
                $uuid = $parent_uuid['parent_uuid'];
            }
            if(empty($parents)) {
                continue;
            }
            var_dump($parents);
            $department->parent_departments = Json::encode($parents);
            $department->update();
        }
    }

    public function updateChildDepartments() {
        $list = self::find()->all();
        foreach($list as $department) {
            $children = $this->childDepartments($department->uuid);
            $department->child_departments = Json::encode($children);
            $department->update();
        }
    }

    public function childDepartments($uuid) {
        $child_uuids = DepartmentRelation::find()
            ->select("child_uuid")
            ->andWhere(['parent_uuid'=>$uuid])
            ->asArray()->all();
        if(empty($child_uuids)) {
            return [$uuid];
        }
        $_return = [$uuid];
        foreach($child_uuids as $child_uuid) {
            $_return = array_merge($_return,$this->childDepartments($child_uuid['child_uuid']));
        }
        return $_return;
    }

    public function updateChildPositions() {
        $list = self::find()->all();
        foreach($list as $department) {
            $children = Json::decode($department->child_departments);
            $positions = [];
            foreach($children as $child) {
                $positionList = Position::find()
                    ->select(['uuid'])
                    ->andWhere(['de_uuid'=>$child])
                    ->asArray()->all();
                foreach($positionList as $position) {
                    $positions[] = $position['uuid'];
                }
            }
            $department->child_positions = Json::encode($positions);
            $department->update();
        }
    }

    public static function getDepartmentUuidsFromUserId($userId) {
        if(empty($userId)) {
            return null;
        }
        $list = self::find()
            ->select(['t1.uuid'])
            ->alias('t1')
            ->leftJoin(self::PositionTableName . ' t2', 't1.uuid = t2.de_uuid')
            ->leftJoin(self::EmployeePositionMapTableName . ' t3', 't2.uuid = t3.position_uuid')
            ->leftJoin(self::EmployeeBasicInformationTableName . ' t4', 't4.uuid = t3.em_uuid')
            ->andWhere(['t4.uuid'=>$userId])
            ->asArray()->all();
        $_list = [];
        foreach($list as $item) {
            $_list[] = $item['uuid'];
        }
        return $_list;
    }
}