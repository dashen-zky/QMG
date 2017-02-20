<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17 0017
 * Time: 上午 11:13
 */

namespace backend\modules\fin\stamp\controllers;


use backend\models\BackEndBaseController;
use backend\modules\fin\stamp\models\StampConfig;
use Yii;
class StampConfigController extends BackEndBaseController
{
    public function actionIndex() {
        $config = new StampConfig();
        $config = $config->generateConfig();
        $tab = $this->getTab(Yii::$app->request->get('tab'), 'tab-1');
        return $this->render('index',[
            'config'=>$config,
            'tab'=>$tab,
        ]);
    }

    public function actionUpdate() {
        $formData = Yii::$app->request->post('Config');
        if(empty($formData)) {
            $this->redirect(['index']);
        }
        $tab = $formData['tab'];
        unset($formData['tab']);
        $config = new StampConfig();
        if ($config->updateConfig($formData)) {
            $this->redirect([
                'index',
                'tab'=>$tab,
            ]);
        } else {

        }
    }
}