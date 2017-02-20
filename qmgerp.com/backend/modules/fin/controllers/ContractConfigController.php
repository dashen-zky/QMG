<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/29 0029
 * Time: 下午 5:31
 */
namespace backend\modules\fin\controllers;
use backend\modules\fin\models\contract\ContractConfig;
use yii\web\Controller;
use Yii;
class ContractConfigController extends Controller
{
    public function actionIndex() {
        $config = new ContractConfig();
        $config = $config->generateConfig();
        return $this->render('index',['config'=>$config]);
    }

    public function actionUpdate() {
        $formData = Yii::$app->request->post('Config');
        if(empty($formData)) {
            $this->redirect(['index']);
        }

        $config = new ContractConfig();
        if ($config->updateConfig($formData)) {
            $this->redirect(['index']);
        } else {

        }
    }
}