<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/3 0003
 * Time: 下午 4:13
 */

namespace backend\modules\hr\controllers;


use backend\models\BackEndBaseController;
use backend\modules\hr\models\config\EmployeeDismissConfig;
use Yii;

class EmployeeDismissConfigController extends BackEndBaseController
{
    public function actionIndex() {
        $config = (new EmployeeDismissConfig())->generateConfig();
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
        $config = new EmployeeDismissConfig();

        if ($config->updateConfig($formDate)) {
            $this->redirect([
                'index',
                'tab'=>$tab,
            ]);
        }
    }
}