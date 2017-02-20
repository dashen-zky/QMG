<?php

namespace backend\modules\crm\controllers;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/13 0013
 * Time: 上午 12:14
 */
use backend\models\interfaces\controller\ControllerCommon;
use backend\modules\crm\models\customer\model\ContactForm;
use backend\modules\crm\models\customer\model\CustomerConfig;
use backend\modules\crm\models\customer\record\Customer;
use backend\modules\crm\models\customer\record\CustomerContractMap;
use backend\modules\crm\models\customer\record\PublicCustomer;
use backend\modules\fin\models\contract\ContractBaseRecord;
use Yii;
use yii\web\Controller;
use backend\modules\crm\models\customer\model\PublicCustomerForm;
use yii\helpers\Json;
class PublicCustomerController extends CRMBaseController implements ControllerCommon
{
    public function actionIndex() {
        $model = new PublicCustomerForm();
        $config = (new CustomerConfig())->generateConfig();
        $model->setConfig($config);
        $contactModel = new ContactForm();
        $publicCustomer = new PublicCustomer();
        $publicCustomerList = $publicCustomer->allList();
        // 查看该显示那个tab
        $tab = $this->getTab(Yii::$app->request->get('tab'), 'customer-list');
        // 查看有没有错误信息
        $error = Yii::$app->request->get('error');
        if(!empty($error)) {
            if($tab === 'add-customer') {
                $model->setError(unserialize($error));
            }
        }
        return $this->render('index',[
            'publicCustomerList'=>$publicCustomerList,
            'model'=>$model,
            'contactModel'=>$contactModel,
            'tab'=>$tab,
        ]);
    }

    public function actionAdd() {
        if (Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post();
            $model = new PublicCustomerForm();
            if($model->load($formData) && $model->validate()) {
                $formData = $formData['PublicCustomerForm'];
                $publicCustomer = new PublicCustomer();
                $result = $publicCustomer->insertRecord($formData);
                if ($result) {
                    $this->redirect(['index']);
                } else {

                }
            } else {
                $error = serialize($model->errors);
                $this->redirect([
                    'index',
                    'error'=>$error,
                    'tab'=>'add-customer',
                ]);
            }
        }
    }

    public function actionDel() {
        $uuid = Yii::$app->request->get('uuid');
        if(!empty($uuid)) {
            $customer = new PublicCustomer();
            if($customer->deleteRecordByUuid($uuid)) {
                return $this->redirect(['index']);
            }
        }
    }
    // admin使用恢复已删除数据
    public function actionRecover() {
        return ;
    }

    public function actionEdit() {
        if (($uuid = Yii::$app->request->get('uuid')) !== null) {
            $publicCustomer = new PublicCustomer();
            $formData = $publicCustomer->getRecordByUuid($uuid);
            $model = new PublicCustomerForm();
            $config = (new CustomerConfig())->generateConfig();
            $model->setConfig($config);
            $contactModel = new ContactForm();
            // 检查显示那个tab
            $tab = $this->getTab(Yii::$app->request->get('tab'), 'edit-customer');
            // 检查有没有错误数据
            $error = Yii::$app->request->get('error');
            if(!empty($error)) {
                if($tab === 'edit-customer') {
                    $model->setError(unserialize($error));
                }
            }
            return $this->render('edit',[
                'model'=>$model,
                'formData'=>$formData,
                'contactModel'=>$contactModel,
                'tab'=>$tab,
            ]);
        }
    }

    public function actionUpdate() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post();
            $model = new PublicCustomerForm();
            if($model->load($formData) && $model->validate()) {
                $formData = $formData['PublicCustomerForm'];
                $publicCustomer = new PublicCustomer();
                if($publicCustomer->updateRecord($formData)) {
                    $this->redirect([
                        'edit',
                        'uuid'=>$formData['uuid'],
                        'tab'=>'edit-customer',
                    ]);
                } else {

                }
            } else {
                $error = serialize($model->errors);
                $this->redirect([
                    'edit',
                    'uuid'=>$formData['PublicCustomerForm']['uuid'],
                    'tab'=>'edit-customer',
                    'error'=>$error,
                ]);
            }
        }
    }

    public function actionListFilter() {
        $publicCustomer = new PublicCustomer();
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $publicCustomer->clearEmptyField($filter);
        $publicCustomerList = $publicCustomer->listFilter($filter);
        $model = new PublicCustomerForm();
        $config = (new CustomerConfig())->generateConfig();
        $model->setConfig($config);

        return $this->render('index',[
            'publicCustomerList'=>$publicCustomerList,
            'model'=>$model,
            'contactModel'=>new ContactForm(),
            'tab'=>'customer-list',
            'ser_filter'=>serialize($filter),
        ]);
    }


    public function actionCodeUpdate() {
        return ;
        $customerConfig = new CustomerConfig();
$config = $customerConfig->generateConfig();
//        var_dump($config);die;
//        unset($config['customer_code']);
//        $customerConfig->updateDateConfigByJsonString(Json::encode($config));die;
        $customer = new PublicCustomer();
        $customerList = $customer->find()->asArray()->all();
        for($i = 0; $i < count($customerList); $i++) {
            $record = $customer->find()->andWhere(['uuid'=>$customerList[$i]['uuid']])->one();
            $config = $customerConfig->generateConfig();
            // 获取当前的code
            if(isset($config['customer_code'])) {
                $priCode = intval(date('y',time()) . '0001');
                $configCode = intval($config['customer_code']);
                $code = $priCode >= $configCode
                    ? $priCode:$configCode;
            } else {
                $code = $config['customer_code'] = intval(date('y',time()) . '0001');
            }
            $record->code = $code;
            var_dump($code);
            $record->update();
            $config['customer_code'] =$code + 1;
            $customerConfig->updateDateConfigByJsonString(Json::encode($config));
        }
    }

    public function actionContractCodeUpdate() {
        return ;
        $customerList = (new PublicCustomer())->find()->asArray()->all();
        foreach($customerList as $customer) {
            $contractMapList = CustomerContractMap::find()->andWhere(['customer_uuid'=>$customer['uuid']])->asArray()->all();
            if(empty($contractMapList)) {
                continue;
            }
            $tail = 1;
            $code = $customer['code'];
            foreach($contractMapList as $contractMap) {
                $record = ContractBaseRecord::find()->andWhere(['uuid'=>$contractMap['contract_uuid']])->one();
                if (empty($record)) {
                    continue;
                }
                if($tail < 10) {
                    $record->code = $code.'0'.$tail;
                } else {
                    $record->code = $code.$tail;
                }
                var_dump($record->code);
                $tail += 1;
                $record->type = PublicCustomerForm::codePrefix;
                $record->update();
            }
        }
    }
}