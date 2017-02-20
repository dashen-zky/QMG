<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/27 0027
 * Time: 上午 10:37
 */

namespace backend\modules\crm\controllers;


use backend\models\BaseRecord;
use backend\models\CompressHtml;
use backend\modules\crm\models\SupplierUnionPartTime;
use Yii;
class SupplierUnionPartTimeController extends CRMBaseController
{
    public function actionSupplierUnionPartTimeList() {
        $supplierUnionPartTime = new SupplierUnionPartTime();
        $list = $supplierUnionPartTime->allSupplierUnionPartTimeList();
        return CompressHtml::compressHtml($this->renderPartial('select-list',[
            'list'=>$list,
        ]));
    }

    public function actionListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                $supplierUnionPartTime = new SupplierUnionPartTime();
                $list = $supplierUnionPartTime->allSupplierUnionPartTimeList();
                return CompressHtml::compressHtml($this->renderPartial('select-list',[
                    'list'=>$list,
                ]));
            }
            $filter = unserialize($ser_filter);
        }
        $supplierUnionPartTime = new SupplierUnionPartTime();
        (new BaseRecord())->clearEmptyField($filter);
        $list = $supplierUnionPartTime->listFilter($filter);

        return CompressHtml::compressHtml($this->renderPartial('select-list',[
            'list'=>$list,
            'ser_filter'=>serialize($filter),
        ]));
    }
}