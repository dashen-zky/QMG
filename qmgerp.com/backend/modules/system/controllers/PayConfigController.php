<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11 0011
 * Time: 上午 11:52
 */

namespace backend\modules\system\controllers;


use backend\modules\system\models\payment_assess_config\PayConfig;

class PayConfigController extends PaymentAssessConfigController
{
    public function init()
    {
        $this->paymentAssessConfig = new PayConfig();
        $this->viewDir = '/pay-config/';
        parent::init();
    }
}