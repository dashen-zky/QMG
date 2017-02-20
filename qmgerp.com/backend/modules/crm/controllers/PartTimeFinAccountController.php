<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/17 0017
 * Time: 下午 4:45
 */

namespace backend\modules\crm\controllers;
use backend\modules\crm\models\part_time\record\PartTimeFinAccountMap;
use backend\modules\fin\models\account\models\FINAccountForm;
use Yii;

class PartTimeFinAccountController extends CRMBaseController
{
    public function actionAdd() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post();
            $model = new FINAccountForm();
            if($model->load($formData) && $model->validate()) {
                $partTimeFinAccountMap = new PartTimeFinAccountMap();
                if($partTimeFinAccountMap->insertSingleRecord($formData['FINAccountForm'])) {
                    return $this->redirect([
                        '/crm/part-time/edit',
                        'uuid'=>$formData['FINAccountForm']['object_uuid'],
                        'tab'=>'add-account',
                    ]);
                }
            } else {
                $error = serialize($model->errors);
                return $this->goBack([
                    '/crm/part-time/edit',
                    'uuid'=>$formData['FINAccountForm']['object_uuid'],
                    'tab'=>'add-account',
                    'error'=>$error,
                ]);
            }
        }
    }

    public function actionDel() {
        $account_uuid = Yii::$app->request->get('uuid');
        if(empty($account_uuid)) {
            return false;
        }

        $partTime_uuid = Yii::$app->request->get('object_uuid');
        $partTimeAccountMap = new PartTimeFinAccountMap();
        if($partTimeAccountMap->deleteSingleRecord($partTime_uuid, $account_uuid)) {
            return $this->redirect([
                '/crm/part-time/edit',
                'uuid'=>$partTime_uuid,
                'tab'=>'add-account',
            ]);
        }
    }
}