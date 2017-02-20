<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/25 0025
 * Time: 下午 3:45
 */

namespace backend\modules\crm\controllers;

use backend\modules\crm\models\supplier\record\SupplierContactMap;
use Yii;
use yii\helpers\Json;

class SupplierContactController extends CRMBaseController
{
    public function actionAdd() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post('ContactForm');
            $customerContact = new SupplierContactMap();
            if($customerContact->insertContact($formData)) {
                $this->redirect([
                    '/crm/supplier/edit',
                    'tab'=>'contact-list',
                    'uuid'=>$formData['object_uuid'],
                ]);
            }
        }
    }

    public function actionRecommendAdd() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post('ContactForm');
            $customerContact = new SupplierContactMap();
            if($customerContact->insertContact($formData)) {
                $this->redirect([
                    '/crm/supplier/recommend-edit',
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
        $object_uuid = Yii::$app->request->get('object_uuid');
        $customerContact = New SupplierContactMap();
        if($customerContact->deleteSingleRecord($uuid, $object_uuid)) {
            $this->redirect([
                '/crm/supplier/edit',
                'tab'=>'contact-list',
                'uuid'=>$object_uuid,
            ]);
        }
    }

    public function actionRecommendDel() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return false;
        }
        $object_uuid = Yii::$app->request->get('object_uuid');
        $customerContact = New SupplierContactMap();
        if($customerContact->deleteSingleRecord($uuid, $object_uuid)) {
            $this->redirect([
                '/crm/supplier/recommend-edit',
                'tab'=>'contact-list',
                'uuid'=>$object_uuid,
            ]);
        }
    }

    public function actionEdit() {
        if(Yii::$app->request->isAjax) {
            $uuid1 = Yii::$app->request->get('uuid');
            $uuid2 = Yii::$app->request->get('object_uuid');
            $contactMap = new SupplierContactMap();
            $record = $contactMap->getRecordByUuid($uuid1, $uuid2);
            return Json::encode($record);
        }
    }

    public function actionUpdate() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post('ContactForm');
            $customerContact = new SupplierContactMap();
            if($customerContact->updateContact($formData)) {
                $this->redirect([
                    '/crm/supplier/edit',
                    'tab'=>'contact-list',
                    'uuid'=>$formData['object_uuid'],
                ]);
            }
        }
    }

    public function actionRecommendUpdate() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post('ContactForm');
            $customerContact = new SupplierContactMap();
            if($customerContact->updateContact($formData)) {
                $this->redirect([
                    '/crm/supplier/recommend-edit',
                    'tab'=>'contact-list',
                    'uuid'=>$formData['object_uuid'],
                ]);
            }
        }
    }
}