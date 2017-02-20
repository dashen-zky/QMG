<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/3 0003
 * Time: 上午 10:38
 */

namespace backend\modules\hr\controllers;


use backend\models\BackEndBaseController;
use backend\modules\hr\models\config\EmployeeEntryConfig;
use Yii;

class EmployeeEntryConfigController extends BackEndBaseController
{
    public function actionIndex() {
        $config = (new EmployeeEntryConfig())->generateConfig();
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
        $config = new EmployeeEntryConfig();

        if ($config->updateConfig($formDate)) {
            $this->redirect([
                'index',
                'tab'=>$tab,
            ]);
        }
    }
}