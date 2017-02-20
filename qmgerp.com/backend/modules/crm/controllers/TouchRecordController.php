<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22 0022
 * Time: 上午 10:08
 */

namespace backend\modules\crm\controllers;

use backend\modules\crm\models\customer\record\CustomerTouchRecordMap;
use backend\modules\crm\models\project\record\ProjectTouchRecordMap;
use backend\modules\crm\models\touchrecord\TouchRecord;
use backend\modules\crm\models\touchrecord\TouchRecordForm;
use Yii;
use backend\modules\crm\models\customer\model\PrivateCustomerForm;
use yii\web\Controller;
use backend\modules\crm\models\customer\model\CustomerConfig;

class TouchRecordController extends Controller
{
    public function actionAdd() {
        if(!Yii::$app->request->isPost) {
            return ;
        }

        $model = new TouchRecordForm();
        $formData = Yii::$app->request->post();
        if($model->load($formData) && $model->validate()) {
            $formData = $formData['TouchRecordForm'];
            $touchRecord = new TouchRecord();
            if($touchRecord->insertRecord($formData)) {
                if($formData['category'] === 'customer') {
                    $this->redirect([
                        '/crm/private-customer/edit',
                        'uuid'=>$formData['customer_uuid'],
                        'tab'=>'add-touch-record'
                    ]);
                } elseif($formData['category'] === 'project') {
                    $this->redirect([
                        '/crm/project/edit',
                        'uuid'=>$formData['project_uuid'],
                        'tab'=>'add-touch-record',
                    ]);
                }
            }
        } else {
            $error = serialize($model->errors);
            $formData = $formData['TouchRecordForm'];
            if($formData['category'] === 'customer') {
                $this->redirect([
                    '/crm/private-customer/edit',
                    'uuid'=>$formData['customer_uuid'],
                    'tab'=>'add-touch-record',
                    'error'=>$error,
                ]);
            } elseif($formData['category'] === 'project') {
                $this->redirect([
                    '/crm/project/edit',
                    'uuid'=>$formData['project_uuid'],
                    'tab'=>'add-touch-record',
                    'error'=>$error,
                ]);
            }
        }
    }
    
    public function actionCustomerTouchRecordListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['/crm/private-customer/index']);
            }
            $filter = unserialize($ser_filter);
        }
        $customerTouchRecord = new CustomerTouchRecordMap();
        $customerTouchRecord->clearEmptyField($filter);
        $touchRecordList = $customerTouchRecord->listFilter($filter);

        $model = new PrivateCustomerForm();
        $model->setConfig((new CustomerConfig())->generateConfig());

        return $this->render('/private-customer/index',[
            'model'=>$model,
            'touchRecordList'=>$touchRecordList,
            'tab'=>'touch-record-list',
            'touch_record_list_ser_filter'=>serialize($filter),
        ]);
    }

    public function actionProjectTouchRecordListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['/crm/project/index']);
            }
            $filter = unserialize($ser_filter);
        }
        $projectTouchRecord = new ProjectTouchRecordMap();
        $projectTouchRecord->clearEmptyField($filter);
        $touchRecordList = $projectTouchRecord->listFilter($filter);

        $model = new PrivateCustomerForm();
        $model->setConfig((new CustomerConfig())->generateConfig());

        return $this->render('/project/index',[
            'touchRecordList'=>$touchRecordList,
            'tab'=>'touch-record-list',
            'touch_record_list_ser_filter'=>serialize($filter),
        ]);
    }
}