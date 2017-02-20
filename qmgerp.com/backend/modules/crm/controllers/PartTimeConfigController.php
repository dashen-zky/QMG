<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/15 0015
 * Time: 下午 3:57
 */

namespace backend\modules\crm\controllers;

use Yii;
use backend\modules\crm\models\part_time\model\PartTimeConfig;

class PartTimeConfigController extends CRMBaseController
{
    public function  actionIndex() {
        $config = new PartTimeConfig();
        $config = $config->generateConfig();
        $tab = $this->getTab(Yii::$app->request->get('tab'),'status');
        return $this->render('index',[
            'config'=>$config,
            'tab'=>$tab,
        ]);
    }

    public function actionUpdate() {
        if(Yii::$app->request->isPost) {
            $formDate = Yii::$app->request->post('Config');
            $config = new PartTimeConfig();
            $tab = key($formDate);
            if ($config->updateConfig($formDate)) {
                $this->redirect([
                    'index',
                    'tab'=>$tab,
                ]);
            } else {

            }
        }
    }
}