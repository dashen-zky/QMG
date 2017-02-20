<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/23 0023
 * Time: 下午 3:27
 */

namespace backend\modules\crm\controllers;

use Yii;
use yii\web\Controller;
use backend\modules\crm\models\project\model\ProjectConfig;
class ProjectConfigController extends Controller
{
    public function actionIndex() {
        $config = new ProjectConfig();
        $config = $config->generateConfig();
        return $this->render('index',['config'=>$config]);
    }

    public function actionUpdate() {
        $formData = Yii::$app->request->post('Config');
        if(empty($formData)) {
            $this->redirect(['index']);
        }

        $config = new ProjectConfig();
        if ($config->updateConfig($formData)) {
            $this->redirect(['index']);
        } else {

        }
    }
}