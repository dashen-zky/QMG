<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/2 0002
 * Time: 下午 9:06
 */

namespace backend\modules\crm\controllers;


use backend\modules\crm\models\supplier\model\SupplierConfig;
use yii\web\Controller;
use Yii;

class SupplierConfigController extends Controller
{
    public function actionIndex() {
        $config = new SupplierConfig();
        $config = $config->generateConfig();
        return $this->render('index',['config'=>$config]);
    }

    public function actionUpdate() {
        $formData = Yii::$app->request->post('Config');
        if(empty($formData)) {
            $this->redirect(['index']);
        }

        $config = new SupplierConfig();
        if ($config->updateConfig($formData)) {
            $this->redirect(['index']);
        } else {

        }
    }
}