<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/24 0024
 * Time: 下午 3:36
 */

namespace backend\modules\crm\controllers;

use backend\modules\crm\models\customer\record\CustomerContactMap;
use Yii;
use yii\helpers\Json;

class PublicCustomerContactController extends CRMBaseController
{
    public function actionAdd() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post('ContactForm');
            $customerContact = new CustomerContactMap();
            if($customerContact->insertContact($formData)) {
                $this->redirect([
                    '/crm/public-customer/edit',
                    'tab'=>'contact-list',
                    'uuid'=>$formData['object_uuid'],
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
        $customerContact = New CustomerContactMap();
        if($customerContact->deleteSingleRecord($uuid, $customer_uuid)) {
            $this->redirect([
                '/crm/public-customer/edit',
                'tab'=>'contact-list',
                'uuid'=>$customer_uuid,
            ]);
        }
    }

    public function actionEdit() {
        if(Yii::$app->request->isAjax) {
            $uuid1 = Yii::$app->request->get('uuid');
            $uuid2 = Yii::$app->request->get('object_uuid');
            $contactMap = new CustomerContactMap();
            $record = $contactMap->getRecordByUuid($uuid1, $uuid2);
            return Json::encode($record);
        }
    }

    public function actionUpdate() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post('ContactForm');
            $customerContact = new CustomerContactMap();
            if($customerContact->updateContact($formData)) {
                $this->redirect([
                    '/crm/public-customer/edit',
                    'tab'=>'contact-list',
                    'uuid'=>$formData['object_uuid'],
                ]);
            }
        }
    }
}