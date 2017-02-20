<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/20 0020
 * Time: 上午 12:47
 */

namespace backend\modules\daily\controllers;


use backend\models\BackEndBaseController;
use backend\modules\daily\models\payment\ApplyPayment;
use backend\modules\fin\payment\models\Payment;
use backend\modules\fin\payment\models\PaymentConfig;
use Yii;
use backend\models\CompressHtml;
class ApplyCheckStampController extends BackEndBaseController
{
    public function actionIndex() {
        $applyPayment = new ApplyPayment();
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

    public function actionShow() {
        if(!Yii::$app->request->isAjax) {
            return false;
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return false;
        }
        $applyPayment = new ApplyPayment();
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
        $applyPayment = new ApplyPayment();
        if($applyPayment->applyCheckStamp($formData)) {
            return $this->redirect(['index']);
        }
    }

    public function actionListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
            $filter['is_apply_check_stamp_list'] = true;
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $applyPayment = new ApplyPayment();
        $applyPayment->clearEmptyField($filter);
        $paymentList = $applyPayment->listFilter($filter);

        return $this->render('index',[
            'paymentList'=>$paymentList,
            'ser_filter'=>serialize($filter),
        ]);
    }
}