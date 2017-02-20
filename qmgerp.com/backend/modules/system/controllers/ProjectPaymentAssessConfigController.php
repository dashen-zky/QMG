<?php
/**
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/21 0021
 * Time: 下午 3:24
 * 项目付款审核流程配置控制器
 */

namespace backend\modules\system\controllers;


use backend\models\BackEndBaseController;
use backend\modules\system\models\payment_assess_config\ProjectPaymentAssessConfig;

class ProjectPaymentAssessConfigController extends PaymentAssessConfigController
{
    public function init() {
        $this->paymentAssessConfig = new ProjectPaymentAssessConfig();
        $this->viewDir = '/project-payment-assess-config/';
        parent::init();
    }
}