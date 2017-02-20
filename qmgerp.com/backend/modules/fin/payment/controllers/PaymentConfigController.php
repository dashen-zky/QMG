<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/21 0021
 * Time: 上午 11:55
 */

namespace backend\modules\fin\payment\controllers;


use backend\models\BackEndBaseController;
use backend\modules\fin\payment\models\PaymentConfig;
use Yii;
class PaymentConfigController extends BackEndBaseController
{
    public function actionIndex() {
        $config = new PaymentConfig();
        $config = $config->generateConfig();
        return $this->render('index',['config'=>$config]);
    }

    public function actionUpdate() {
        $formData = Yii::$app->request->post('Config');
        if(empty($formData)) {
            $this->redirect(['index']);
        }

        $config = new PaymentConfig();
        if ($config->updateConfig($formData)) {
            $this->redirect(['index']);
        } else {

        }
    }
}