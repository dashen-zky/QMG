<?php
namespace backend\modules\statistic\models;
use backend\models\BaseRecord;
use backend\models\MyPagination;
use backend\modules\rbac\model\RBACManager;
use Yii;
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-11-21
 * Time: 下午1:15
 */
class SalesCustomerStatistic extends BaseRecord
{
    public static $aliasMap = [
        'statistic'=>'t1',
        'employee_position_map'=>'t2',
        'position'=>'t3',
        'department'=>'t4',
    ];
    public static function tableName()
    {
        return self::CRMSalesCustomerStatistic;
    }

    public function myStatisticList() {
        if(Yii::$app->getUser()->getIdentity()->getUserName() == 'admin') {
            return $this->statisticList();
        }


        return $this->statisticList([
            'in',
            't1.sales_uuid',
            $this->getOrdinateUuids(RBACManager::CustomerModule),
        ]);
    }

    public function statisticList($conditions = null) {
        $query = self::find()
            ->alias('t1')
            ->select(['t1.*',
                'group_concat(t3.name) position_name',
                'group_concat(t4.name) department_name'])
            ->leftJoin(self::EmployeePositionMapTableName . ' t2', 't1.sales_uuid = t2.em_uuid')
            ->leftJoin(self::PositionTableName . ' t3', 't3.uuid = t2.position_uuid')
            ->leftJoin(self::DepartmentTableName . ' t4', 't4.uuid = t3.de_uuid')
            ->andWhere($conditions)
            ->groupBy('t1.sales_uuid');

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
            'sales_name'=>[
                'like',
                self::$aliasMap['statistic'] . '.sales_name',
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
                't1.sales_uuid',
                $this->getOrdinateUuids(RBACManager::CustomerModule),
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