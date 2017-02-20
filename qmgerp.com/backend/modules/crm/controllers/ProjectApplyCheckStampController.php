<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/20 0020
 * Time: 下午 8:24
 */

namespace backend\modules\crm\controllers;


use backend\models\BackEndBaseController;
use backend\models\ViewHelper;
use backend\modules\crm\models\project\record\ProjectCustomerMap;
use backend\modules\crm\models\project\record\ProjectPaymentMap;
use backend\modules\crm\models\stamp\Stamp;
use backend\modules\fin\payment\models\PaymentConfig;
use Yii;
use backend\models\CompressHtml;


class ProjectApplyCheckStampController extends BackEndBaseController
{
    public function actionIndex() {
        if(Yii::$app->request->isAjax) {
            return $this->pagination();
        }
        $applyPayment = new ProjectPaymentMap();
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
        $applyPayment = new ProjectPaymentMap([
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
        $applyPayment = new ProjectPaymentMap();
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
        $applyPayment = new ProjectPaymentMap();
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
        $applyPayment = new ProjectPaymentMap();
        $applyPayment->clearEmptyField($filter);
        $paymentList = $applyPayment->listFilter($filter);

        return CompressHtml::compressHtml($this->renderPartial('list',[
            'paymentList'=>$paymentList,
            'ser_filter'=>serialize($filter),
        ]));
    }
}