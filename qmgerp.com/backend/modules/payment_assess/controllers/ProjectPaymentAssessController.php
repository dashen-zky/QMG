<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/10 0010
 * Time: 下午 4:07
 */

namespace backend\modules\payment_assess\controllers;


use backend\modules\payment_assess\models\ProjectPaymentAssess;
use Yii;
use backend\modules\crm\models\supplier\record\SupplierPaymentMap;
use backend\modules\fin\payment\models\Payment;
use backend\models\BaseRecord;
class ProjectPaymentAssessController extends PaymentAssessController
{
    public function init()
    {
        $this->paymentAssess = new ProjectPaymentAssess();
        $this->applyPayment = new SupplierPaymentMap();
        parent::init();
    }

    public function actionAssessing() {
        $action = ['/payment_assess/project-payment-assess/assess'];
        return parent::assessing($action);
    }

    public function actionShow() {
        return parent::actionShow();
    }
}