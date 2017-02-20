<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/13 0013
 * Time: 上午 11:22
 */

namespace backend\modules\crm\controllers;


use yii\web\Controller;
use Yii;
use backend\modules\crm\models\customer\model\CustomerConfig;
class CustomerConfigController extends Controller
{
    public function  actionIndex() {
        $config = new CustomerConfig();
        $config = $config->generateConfig();
        return $this->render('index',['config'=>$config]);
    }

    public function actionUpdate() {
        $formDate = Yii::$app->request->post('Config');
        $config = new CustomerConfig();

        if ($config->updateConfig($formDate)) {
            $this->redirect(['index']);
        } else {

        }
    }
}