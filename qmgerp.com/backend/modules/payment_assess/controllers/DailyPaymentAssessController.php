<?php
namespace backend\modules\payment_assess\controllers;
use backend\modules\payment_assess\models\DailyPaymentAssess;
use Yii;
use backend\modules\daily\models\payment\ApplyPayment;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/30 0030
 * Time: 上午 11:48
 */
class DailyPaymentAssessController extends PaymentAssessController
{
    public function init()
    {
        $this->paymentAssess = new DailyPaymentAssess();
        $this->applyPayment = new ApplyPayment();
        parent::init();
    }

    public function actionAssessing() {
        $action = ['/payment_assess/daily-payment-assess/assess'];
        return parent::assessing($action);
    }

    public function actionShow() {
        return parent::actionShow();
    }
}