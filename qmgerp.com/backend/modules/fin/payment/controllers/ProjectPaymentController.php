<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11 0011
 * Time: 下午 5:42
 */

namespace backend\modules\fin\payment\controllers;

use Yii;
use backend\modules\fin\payment\models\PaymentList;
class ProjectPaymentController extends PaymentController
{
    public function actionIndex() {
        $paymentList = (new PaymentList())->assessList(PaymentList::ProjectPaymentEntrance);
        return $this->render('index', [
            'paymentList'=>$paymentList,
        ]);
    }
}