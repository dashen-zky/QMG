<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-11-17
 * Time: 下午12:13
 */

namespace backend\modules\hr\controllers;


use backend\models\BackEndBaseController;
use backend\modules\hr\models\config\EmployeeBasicConfig;
use Yii;

class EmployeeBasicConfigController extends BackEndBaseController
{
    public function actionIndex() {
        $config = (new EmployeeBasicConfig())->generateConfig();
        return $this->render('index', [
            'config'=>$config,
            'tab'=>$this->getTab(Yii::$app->request->get('tab'), 'tab-1'),
        ]);
    }

    public function actionUpdate() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formDate = Yii::$app->request->post('Config');
        $tab = $formDate['tab'];
        unset($formDate['tab']);
        $config = new EmployeeBasicConfig();

        if ($config->updateConfig($formDate)) {
            $this->redirect([
                'index',
                'tab'=>$tab,
            ]);
        }
    }
}