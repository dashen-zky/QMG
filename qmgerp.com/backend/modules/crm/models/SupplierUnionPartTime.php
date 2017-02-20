<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/27 0027
 * Time: 上午 10:44
 */

namespace backend\modules\crm\models;

use backend\models\BaseRecord;
use backend\models\MyPagination;
use backend\modules\crm\models\part_time\model\PartTimeConfig;
use backend\modules\crm\models\part_time\model\PartTimeForm;
use backend\modules\crm\models\part_time\record\PartTime;
use backend\modules\crm\models\supplier\model\SupplierConfig;
use backend\modules\crm\models\supplier\model\SupplierForm;
use backend\modules\crm\models\supplier\record\Supplier;
use backend\modules\crm\models\supplier\record\SupplierPaymentMap;
use Yii;

class SupplierUnionPartTime
{
    public function supplierUnionPartTimeList($conditions = null, $type = null) {
        // 供应商里面用created_uuid
        // 兼职里面用gender
        // 保证字段一致，但是同时作了很好的区分
        $supplier_query = Supplier::find()
            ->select(['uuid','code','name','status', 'from'])->andWhere(['=', 'status', SupplierConfig::StatusAssessSuccess]);
        $part_time_query = PartTime::find()
            ->select(['uuid','code','name','status', 'gender'])->andWhere(['=', 'check_status', PartTimeConfig::StatusAssessSuccess]);

        if(!empty($conditions)) {
            $part_time_query->andWhere($conditions);
            $supplier_query->andWhere($conditions);
        }

        switch($type) {
            case SupplierPaymentMap::PartTime:
                $query = $part_time_query;
                break;
            case SupplierPaymentMap::Supplier:
                $query = $supplier_query;
                break;
            default:
                $query = $supplier_query->union($part_time_query);
                break;
        }

        $pagination = new MyPagination([
            'totalCount'=>$query->count(),
            'pageSize'=>BaseRecord::PageSize,
        ]);
        $list = $query->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();

        if(empty($type)) {
            // 将结果集区分开来
            foreach($list as $index=>$item) {
                if(strlen($item['from']) < 3) {
                    $list[$index]['type'] = SupplierPaymentMap::PartTime;
                    $list[$index]['type_name'] = '兼职';
                    $list[$index]['code'] = PartTimeForm::codePrefix . $list[$index]['code'];
                    unset($list[$index]['from']);
                    continue;
                }

                $list[$index]['type'] = SupplierPaymentMap::Supplier;
                $list[$index]['type_name'] = '供应商';
                $list[$index]['code'] = SupplierForm::codePrefix . $list[$index]['code'];
                unset($list[$index]['from']);
            }
        } else if($type == SupplierPaymentMap::PartTime) {
            // 兼职
            foreach($list as $index => $item) {
                $list[$index]['type'] = SupplierPaymentMap::PartTime;
                $list[$index]['type_name'] = '兼职';
                $list[$index]['code'] = PartTimeForm::codePrefix . $list[$index]['code'];
                unset($list[$index]['gender']);
            }
        } elseif($type == SupplierPaymentMap::Supplier) {
            // 供应商
            foreach($list as $index => $item) {
                $list[$index]['type'] = SupplierPaymentMap::Supplier;
                $list[$index]['type_name'] = '供应商';
                $list[$index]['code'] = SupplierForm::codePrefix . $list[$index]['code'];
                unset($list[$index]['from']);
            }
        }

        return [
            'pagination'=>$pagination,
            'list'=>$list
        ];
    }

    public function allSupplierUnionPartTimeList() {
        return $this->supplierUnionPartTimeList();
    }

    public function listFilter($filter) {
        if(empty($filter)) {
            return $this->allSupplierUnionPartTimeList();
        }

        $condition = [
            'and',
        ];
        $type = null;
        foreach($filter as $key => $item) {
            if($key === 'type') {
                $type = $item;
                continue;
            }

            $condition[] = [
                'like',
                $key,
                $item,
            ];
        }
        return $this->supplierUnionPartTimeList($condition, $type);
    }
}