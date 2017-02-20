<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-11-22
 * Time: 下午9:13
 */

namespace backend\modules\statistic\models;


use backend\models\BaseRecord;
use Yii;
use backend\modules\rbac\model\RBACManager;
use backend\models\MyPagination;
class ProjectStatistic extends BaseRecord
{
    public static $aliasMap = [
        'statistic'=>'t1',
        'manager'=>'t2',
        'employee_position_map'=>'t3',
        'position'=>'t4',
        'department'=>'t5',
    ];

    public static function tableName()
    {
        return self::CRMProjectStatistic;
    }

    public function myStatisticList() {
        if(Yii::$app->getUser()->getIdentity()->getUserName() == 'admin') {
            return $this->statisticList();
        }


        return $this->statisticList([
            'in',
            't1.manager_uuid',
            $this->getOrdinateUuids(RBACManager::ProjectModule),
        ]);
    }

    public function statisticList($conditions = null) {
        $query = self::find()
            ->alias('t1')
            ->select(['t1.*',
                't2.name manager_name',
                'group_concat(t4.name) position_name',
                'group_concat(t5.name) department_name'])
            ->leftJoin(self::EmployeeBasicInformationTableName .' t2', 't2.uuid = t1.manager_uuid')
            ->leftJoin(self::EmployeePositionMapTableName . ' t3', 't2.uuid = t3.em_uuid')
            ->leftJoin(self::PositionTableName . ' t4', 't4.uuid = t3.position_uuid')
            ->leftJoin(self::DepartmentTableName . ' t5', 't5.uuid = t4.de_uuid')
            ->andWhere($conditions)
            ->groupBy('t1.manager_uuid');

        $pagination = new MyPagination([
            'totalCount'=>$query->count(),
            'pageSize' => self::PageSize,
        ]);
        $list = $query->orderBy('t1.id desc')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data = [
            'pagination' => $pagination,
            'list'=> $list,
        ];
        return $data;
    }

    public function listFilter($filter) {
        if(empty($filter)) {
            return $this->myStatisticList();
        }

        $map = [
            'manager_name'=>[
                'like',
                self::$aliasMap['manager'] . '.name',
            ],
            'department_uuid'=>[
                '=',
                self::$aliasMap['department'] . '.uuid'
            ]
        ];

        $condition = [
            'and',
            [
                'in',
                't1.manager_uuid',
                $this->getOrdinateUuids(RBACManager::ProjectModule),
            ]
        ];
        foreach ($filter as $index => $item) {
            $condition[] = [
                $map[$index][0],
                $map[$index][1],
                trim($item),
            ];
        }

        return $this->statisticList($condition);
    }
}