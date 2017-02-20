<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/21 0021
 * Time: 上午 11:04
 */

namespace backend\modules\crm\controllers;

use backend\modules\crm\models\supplier\record\SupplierPaymentMap;
use Yii;
use backend\modules\fin\payment\models\PaymentConfig;
use backend\models\CompressHtml;
use backend\modules\fin\payment\models\PaymentStampMap;
class SupplierApplyCheckStampController extends CRMBaseController
{
    public function actionIndex() {
        if(Yii::$app->request->isAjax) {
            return $this->pagination();
        }
        $applyPayment = new SupplierPaymentMap();
        $paymentList = $applyPayment->myPaymentList([
            'and',
            [
                '=',
                't1.with_stamp',
                PaymentConfig::WithStamp,
            ],
            [
                'in',
                't1.status',
                [
                    PaymentConfig::StatusWithoutPaied,
                    PaymentConfig::StatusPartPaied,
                    PaymentConfig::StatusSuccess,
                ],
            ]
        ]);
        return $this->render('index', [
            'paymentList'=>$paymentList,
        ]);
    }

    public function pagination() {
        if(!Yii::$app->request->isAjax) {
            return null;
        }
        $applyPayment = new SupplierPaymentMap([
            'and',
            [
                '=',
                't1.with_stamp',
                PaymentConfig::WithStamp,
            ],
            [
                'in',
                't1.status',
                [
                    PaymentConfig::StatusWithoutPaied,
                    PaymentConfig::StatusPartPaied,
                    PaymentConfig::StatusSuccess,
                ],
            ]
        ]);
        $paymentList = $applyPayment->myPaymentList();
        return CompressHtml::compressHtml($this->renderPartial('list', [
            'paymentList'=>$paymentList,
        ]));
    }

    public function actionShow() {
        if(!Yii::$app->request->isAjax) {
            return false;
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return false;
        }
        $applyPayment = new SupplierPaymentMap();
        $formData = $applyPayment->getRecordByUuid($uuid);
        return CompressHtml::compressHtml($this->renderPartial('form',[
            'formData'=>$formData,
            'show'=>true,
            'action'=>null,
        ]));
    }

    public function actionApplyCheckingStamp() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('ApplyCheckStampForm');
        $applyPayment = new SupplierPaymentMap();
        if($applyPayment->applyCheckStamp($formData)) {
            return $this->redirect(['index']);
        }
    }

    public function actionListFilter() {
        if(!Yii::$app->request->isAjax) {
            return null;
        }

        $filter = Yii::$app->request->post('ListFilterForm');
        if(empty($filter)) {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $applyPayment = new SupplierPaymentMap();
        $applyPayment->clearEmptyField($filter);
        $paymentList = $applyPayment->listFilter($filter);

        return CompressHtml::compressHtml($this->renderPartial('list',[
            'paymentList'=>$paymentList,
            'ser_filter'=>serialize($filter),
        ]));
    }

    // 查看供应商/兼职的开票记录
    public function actionCheckedStampList() {
        if(!Yii::$app->request->isAjax) {
            return  null;
        }

        $paymentStamp = new PaymentStampMap();
        $stampList = $paymentStamp->getStampListBySupplierUuid(Yii::$app->request->get('uuid'));
        return CompressHtml::compressHtml($this->renderPartial('/supplier-stamp/stamp-list',[
            'stampList'=>$stampList,
        ]));
    }
}