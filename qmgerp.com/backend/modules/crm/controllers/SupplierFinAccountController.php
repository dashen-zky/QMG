<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/6 0006
 * Time: 上午 12:06
 */

namespace backend\modules\crm\controllers;

use backend\modules\crm\models\supplier\record\SupplierFinAccountMap;
use backend\modules\fin\models\account\models\FINAccountForm;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class SupplierFinAccountController extends Controller
{
    public function actionAdd() {
        if(!Yii::$app->request->isPost) {
            return ;
        }
        
        $formData = Yii::$app->request->post();
        $model = new FINAccountForm();
        if($model->load($formData) && $model->validate()) {
            $supplierFinAccountMap = new SupplierFinAccountMap();
            if($supplierFinAccountMap->insertSingleRecord($formData['FINAccountForm'])) {
                return $this->redirect([
                    '/crm/supplier/edit',
                    'uuid'=>$formData['FINAccountForm']['object_uuid'],
                    'tab'=>'add-account',
                ]);
            }
        } else {
            $error = serialize($model->errors);
            return $this->redirect([
                '/crm/supplier/edit',
                'uuid'=>$formData['FINAccountForm']['object_uuid'],
                'tab'=>'add-account',
                'error'=>$error,
            ]);
        }
    }

    public function actionDel() {
        $account_uuid = Yii::$app->request->get('uuid');
        if(empty($account_uuid)) {
            return false;
        }

        $supplier_uuid = Yii::$app->request->get('object_uuid');
        $supplierAccountMap = new SupplierFinAccountMap();
        if($supplierAccountMap->deleteSingleRecord($supplier_uuid,$account_uuid)) {
            return $this->redirect([
                '/crm/supplier/edit',
                'uuid'=>$supplier_uuid,
                'tab'=>'add-account',
            ]);
        }
    }
}