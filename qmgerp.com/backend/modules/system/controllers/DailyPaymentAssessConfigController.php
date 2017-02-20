<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/21 0021
 * Time: 下午 3:23
 * 日常付款申请审核流程配置控制器
 */
namespace backend\modules\system\controllers;
use backend\models\BackEndBaseController;
use backend\modules\fin\payment\controllers\PaymentConfigController;
use backend\modules\fin\payment\models\PaymentConfig;
use backend\modules\system\models\payment_assess_config\DailyPaymentAssessConfig;
use yii;
use backend\models\CompressHtml;
use backend\modules\hr\models\EmployeeBasicInformation;
use yii\helpers\Html;
use yii\helpers\Json;
class DailyPaymentAssessConfigController extends PaymentAssessConfigController
{
    public function init()
    {
        $this->paymentAssessConfig = new DailyPaymentAssessConfig();
        $this->viewDir = '/daily-payment-assess-config/';
        parent::init();
    }
}