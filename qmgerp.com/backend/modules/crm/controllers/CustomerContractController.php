<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/2 0002
 * Time: 下午 3:09
 */

namespace backend\modules\crm\controllers;

use backend\models\CompressHtml;
use backend\modules\crm\models\customer\model\CustomerContractForm;
use backend\modules\fin\models\contract\ContractBaseRecord;
use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\UploadedFile;
use backend\modules\crm\models\customer\record\CustomerContractMap;
use backend\modules\crm\models\customer\model\PrivateCustomerForm;
use backend\modules\crm\models\customer\model\CustomerConfig;

class CustomerContractController extends Controller
{
    public function actionAdd() {
        if(Yii::$app->request->isPost) {
            $model = new CustomerContractForm();
            $formData = Yii::$app->request->post();
            if($model->load($formData) && $model->validate()) {
                $formData['CustomerContractForm']['attachment'] = UploadedFile::getInstances($model, 'attachment');
                $customerContract = new CustomerContractMap();
                if ($customerContract->insertSingleRecord($formData['CustomerContractForm'])) {
                    $this->redirect([
                        '/crm/private-customer/edit',
                        'uuid'=>$formData['CustomerContractForm']['customer_uuid'],
                        'tab'=>'contract-list'
                    ]);
                } else {

                }
            } else {
                // error handler,验证失败
            }
        }
    }

    public function actionEdit() {
        if(Yii::$app->request->isAjax) {
            $uuid = Yii::$app->request->get('uuid');
            if(empty($uuid)) {
                return null;
            }
            $model = new CustomerContractForm();
            $contract = new CustomerContractMap();
            $customerContract = $contract->getRecordByUuid($uuid);
            // 合同模板列表
            $templateList = $contract->templateList();
            return CompressHtml::compressHtml($this->renderPartial('form',[
                        'formClass'=>'CustomerContractForm',
                        'model'=>$model,
                        'customerContract'=>$customerContract,
                        'templateList'=>$templateList,
                        'show'=>true,
                        'action'=>['/crm/customer-contract/update'],
                        'back_url'=>Yii::$app->request->get('back_url'),
                        'enableEdit'=>Yii::$app->request->get('enableEdit'),
            ]));
        }
    }

    public function actionUpdate() {
        if(!Yii::$app->request->isPost) {
            return ;
        }
        
        $model = new CustomerContractForm();
        $formData = Yii::$app->request->post();
        if($model->load($formData) && $model->validate()) {
            $formData['CustomerContractForm']['attachment'] = UploadedFile::getInstances($model, 'attachment');
            $customerContract = new CustomerContractMap();
            $back_url = empty($formData['CustomerContractForm']['back_url'])?Url::to([
                '/crm/private-customer/edit',
                'uuid'=>$formData['CustomerContractForm']['customer_uuid'],
                'tab'=>'contract-list',
            ]):$formData['CustomerContractForm']['back_url'];
            unset($formData['CustomerContractForm']['back_url']);
            if ($customerContract->updateSingleRecord($formData['CustomerContractForm'])) {
                $this->redirect($back_url);
            }
        }
    }

    public function actionAttachmentDownload() {
        $path = Yii::$app->request->get("path");
        $path = iconv("UTF-8", "GBK", $path);
        if (empty($path)) {
            $this->redirect(['index']);
        }
        $file_name = Yii::$app->request->get('file_name');
        $path = Yii::getAlias("@app") . $path;
        Yii::$app->response->sendFile($path, $file_name);
    }

    public function actionAttachmentDelete() {
        if(Yii::$app->request->isAjax) {
            $uuid = Yii::$app->request->get('uuid');
            $path = Yii::$app->request->get('path');
            $contract = new ContractBaseRecord();
            return $contract->deleteAttachment($uuid, $path);
        }
    }

    public function actionDel() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return null;
        }

        $object_uuid = Yii::$app->request->get('object_uuid');
        $customerContract = new CustomerContractMap();
        if($customerContract->deleteSingleRecord($uuid, $object_uuid)) {
            $back_url = empty(Yii::$app->request->get('back_url'))?Url::to([
                '/crm/private-customer/edit',
                'uuid'=>$object_uuid,
                'tab'=>'contract-list',
            ]):Yii::$app->request->get('back_url');
            $this->redirect($back_url);
        }
    }

    public function actionListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['/crm/private-customer/index']);
            }
            $filter = unserialize($ser_filter);
        }
        $customerContractMap = new CustomerContractMap();
        $customerContractMap->clearEmptyField($filter);
        $contractList = $customerContractMap->listFilter($filter);

        $model = new PrivateCustomerForm();
        $model->setConfig((new CustomerConfig())->generateConfig());

        return $this->render('/private-customer/index',[
            'model'=>$model,
            'contractList'=>$contractList,
            'tab'=>'contract-list',
            'contract_list_ser_filter'=>serialize($filter),
        ]);
    }

    // 将原先合同的单文件支持的变成多文件支持
    public function actionUpdateContractPath() {
        return ;
        $contractList = ContractBaseRecord::find()->all();
        foreach($contractList as $contract) {
            if(!empty($contract['path'])) {
                preg_match('/\w+\.\w+/',$contract['path'],$match);
                if(!empty($match)) {
                    $fileName = $match[0];
                    $contract['path'] = serialize([
                        $fileName=>$contract['path']
                    ]);
                    $contract->update();
                }
            }
        }
    }
}