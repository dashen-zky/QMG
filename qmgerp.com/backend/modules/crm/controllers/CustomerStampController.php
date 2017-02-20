<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/29 0029
 * Time: 下午 4:47
 */

namespace backend\modules\crm\controllers;

use backend\models\CompressHtml;
use backend\modules\crm\models\customer\record\CustomerStampMap;
use backend\modules\crm\models\stamp\Stamp;
use Yii;
use yii\helpers\Json;
class CustomerStampController extends CRMBaseController
{
    public function actionAdd() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post('Stamp');
            $stamp = new CustomerStampMap();
            $_return = $stamp->insertSingleRecord($formData);
            if($_return === true) {
                $this->redirect([
                    '/crm/private-customer/edit',
                    'uuid'=>$formData['object_uuid'],
                    'tab'=>'stamp',
                ]);
            } elseif ($_return !== false) {
                $this->redirect([
                    '/crm/private-customer/edit',
                    'uuid'=>$formData['object_uuid'],
                    'tab'=>'stamp',
                    'error'=>$_return,
                ]);
            }
        }
    }

    public function actionDel() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return false;
        }
        $customer_uuid = Yii::$app->request->get('object_uuid');
        $customerContact = New CustomerStampMap();
        if($customerContact->deleteSingleRecord($uuid, $customer_uuid)) {
            $this->redirect([
                '/crm/private-customer/edit',
                'tab'=>'stamp',
                'uuid'=>$customer_uuid,
            ]);
        }
    }

    public function actionEdit() {
        if(Yii::$app->request->isAjax) {
            $uuid1 = Yii::$app->request->get('uuid');
            $uuid2 = Yii::$app->request->get('object_uuid');
            $contactMap = new CustomerStampMap();
            $record = $contactMap->getRecordByUuid($uuid1, $uuid2);
            return Json::encode($record);
        }
    }

    public function actionUpdate() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post('Stamp');
            $customerContact = new CustomerStampMap();
            if($customerContact->updateSingleRecord($formData)) {
                $this->redirect([
                    '/crm/private-customer/edit',
                    'tab'=>'stamp',
                    'uuid'=>$formData['object_uuid'],
                ]);
            }
        }
    }
}