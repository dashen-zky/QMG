<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/11 0011
 * Time: 上午 10:37
 */

namespace backend\modules\crm\controllers;

use backend\models\CompressHtml;
use backend\modules\crm\models\supplier\model\SupplierContractForm;
use backend\modules\crm\models\supplier\record\SupplierContactMap;
use backend\modules\crm\models\supplier\record\SupplierContractMap;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use backend\modules\fin\models\contract\ContractBaseRecord;
class SupplierContractController extends Controller
{
    public function actionAdd() {
        if(Yii::$app->request->isPost) {
            $model = new SupplierContractForm();
            $formData = Yii::$app->request->post();

            if($model->load($formData) && $model->validate()) {
                $formData['SupplierContractForm']['attachment'] = UploadedFile::getInstances($model, 'attachment');
                $supplier = new SupplierContractMap();
                if ($supplier->insertSingleRecord($formData['SupplierContractForm'])) {
                    $this->redirect([
                        '/crm/supplier/edit',
                        'uuid'=>$formData['SupplierContractForm']['supplier_uuid'],
                        'tab'=>'contract-list',
                    ]);
                } else {

                }
            } else {
                $error = serialize($model->errors);
                return $this->redirect([
                    '/crm/supplier/edit',
                    'uuid'=>$formData['SupplierContractForm']['supplier_uuid'],
                    'tab'=>'add-contract',
                    'error'=>$error,
                ]);
            }
        }
    }

    public function actionEdit() {
        if(Yii::$app->request->isAjax) {
            $uuid = Yii::$app->request->get('uuid');
            if(empty($uuid)) {
                return false;
            }

            $model = new SupplierContractForm();
            $contract = new SupplierContractMap();
            $supplierContract = $contract->getRecordByUuid($uuid);
            // 合同模板列表
            $templateList = $contract->templateList();
            return CompressHtml::compressHtml($this->renderPartial('form',[
                'formClass'=>'SupplierContractForm',
                'model'=>$model,
                'supplierContract'=>$supplierContract,
                'templateList'=>$templateList,
                'show'=>true,
                'action'=>['/crm/supplier-contract/update'],
            ]));
        }
    }

    public function actionUpdate() {
        if(Yii::$app->request->isPost) {
            $model = new SupplierContractForm();
            $formData = Yii::$app->request->post();
            if($model->load($formData) && $model->validate()) {
                $formData['SupplierContractForm']['attachment'] = UploadedFile::getInstances($model, 'attachment');
                $supplierContract = new SupplierContractMap();
                if ($supplierContract->updateSingleRecord($formData['SupplierContractForm'])) {
                    $this->redirect([
                        '/crm/supplier/edit',
                        'uuid'=>$formData['SupplierContractForm']['supplier_uuid'],
                        'tab'=>'contract-list'
                    ]);
                } else {

                }
            } else {
                // error handler,验证失败
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
        $customerContract = new SupplierContractMap();
        if($customerContract->deleteSingleRecord($uuid, $object_uuid)) {
            $this->redirect([
                '/crm/supplier/edit',
                'uuid'=>$object_uuid,
                'tab'=>'contract-list',
            ]);
        }
    }
}