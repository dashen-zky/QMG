<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/21 0021
 * Time: 下午 3:37
 */

namespace backend\modules\system\controllers;
use backend\modules\system\models\payment_assess_config\ExpensePaymentAssessConfig;
use Yii;
class ExpensePaymentAssessConfigController extends PaymentAssessConfigController
{
    public function init()
    {
        $this->paymentAssessConfig = new ExpensePaymentAssessConfig();
        $this->viewDir = '/expense-payment-assess-config/';
        parent::init();
    }
}