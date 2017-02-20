<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/2 0002
 * Time: 下午 7:57
 */

namespace backend\modules\crm\controllers;

use backend\models\interfaces\controller\ControllerCommon;
use backend\modules\crm\models\customer\model\ContactForm;
use backend\modules\crm\models\supplier\model\SupplierConfig;
use backend\modules\crm\models\supplier\model\SupplierContractForm;
use backend\modules\crm\models\supplier\model\SupplierForm;
use backend\modules\crm\models\supplier\record\Supplier;
use backend\modules\crm\models\supplier\record\SupplierContractMap;
use backend\modules\crm\models\supplier\record\SupplierFinAccountMap;
use backend\modules\fin\models\account\models\FINAccountForm;
use backend\modules\fin\models\account\record\FINAccount;
use backend\modules\rbac\model\PermissionManager;
use yii\db\Exception;
use yii\web\Controller;
use Yii;
use yii\web\UploadedFile;
use yii\helpers\Json;
use backend\modules\fin\models\contract\ContractBaseRecord;
class SupplierController extends CRMBaseController implements ControllerCommon
{
    public function actionIndex() {
        $model = new SupplierForm();
        $supplier = new Supplier();
        $supplierList = $supplier->allSupplierList();

        $contactModel = new ContactForm();
        return $this->render('index',[
            'model'=>$model,
            'supplierList'=>$supplierList,
            'contactModel'=>$contactModel,
        ]);
    }
    // 推荐供应商页面
    public function actionRecommend() {
        $model = new SupplierForm();
        $contactModel = new ContactForm();
        $model->code = $model->generateSupplierCode();

        $supplier = new Supplier();
        $supplierList = $supplier->mySupplierList('created');
        $tab = $this->getTab(Yii::$app->request->get('tab'),'add');
        return $this->render('recommend',[
            'model'=>$model,
            'contactModel'=>$contactModel,
            'backUrl'=>Json::encode([
                '/crm/supplier/recommend',
                'tab'=>'list',
            ]),
            'tab'=>$tab,
            'supplierList'=>$supplierList,
        ]);
    }

    public function actionIncrease() {
        if(Yii::$app->request->isPost) {
            $model = new SupplierForm();
            $formData = Yii::$app->request->post();
            $backUrl = Json::decode($formData['backUrl']);
            $attachment = UploadedFile::getInstance($model,'attachment');
            if (!empty($attachment)) {
                $model->file_name = $attachment->baseName;
            }
            if($model->load($formData) && $model->validate()) {
                if(!empty($attachment)) {
                    $formData['SupplierForm']['attachment'] = $attachment;
                }
                $supplier = new Supplier();
                if ($supplier->insertRecord($formData['SupplierForm'])) {
                    $this->redirect(!empty($backUrl)?$backUrl:['index']);
                } else {

                }
            } else {
                return $this->render('add-container',[
                    'model'=>$model,
                    'contactModel'=>(new ContactForm()),
                ]);
            }
        }
    }

    // 推荐供应商的编辑功能
    public function actionRecommendEdit() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return true;
        }

        $supplier = new Supplier();
        $formData = $supplier->getRecordByUuid($uuid);
        $contactModel = new ContactForm();
        $model = new SupplierForm();

        $tab = $this->getTab(Yii::$app->request->get('tab'),'edit-supplier');
        return $this->render('recommend-edit',[
            'formData'=>$formData,
            'contactModel'=>$contactModel,
            'model'=>$model,
            'tab'=>$tab,
        ]);
    }

    public function actionEdit() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return true;
        }

        $supplier = new Supplier();
        $formData = $supplier->getRecordByUuid($uuid);
        $contactModel = new ContactForm();
        $model = new SupplierForm();
        // 收款账户管理
        $finAccountModel = new FINAccountForm();
        $tab = Yii::$app->request->get('tab');
        if(empty($tab)) {
            $tab = 'edit-supplier';
        }
        // 收款账户列表
        $supplierFinAccountMap = new SupplierFinAccountMap();
        $finAccountList = $supplierFinAccountMap->finAccountList($uuid);
        //合同模块
        $contractForm = new SupplierContractForm();
        //合同
        $contract = new SupplierContractMap();
        // 生成合同编号
        $model->contract_code = $model->generateContractCode($uuid,$formData['supplier']['code']);
        // 合同模板列表
        $templateList = $contract->templateList();
        // 当前供应商下面的合同列表
        $contractList = $contract->getContractListBySupplierUud($uuid);
        // 查看验证的错误信息
        $error = Yii::$app->request->get('error');
        // 查看是否可以编辑供应商
        // 传入的参数是来判定是不是管理者
        $enableEditSupplier = Yii::$app->authManager->canAccess(PermissionManager::EditSupplierAndPartTime, [
            'manager_uuid'=>$formData['supplier']['manager_uuid'],
        ]);
        if(!empty($error)) {
            if($tab === 'add-account') {
                $finAccountModel->setError(unserialize($error));
            } else if($tab === 'add-contract') {
                $errors = unserialize($error);
                if(isset($errors['file_name'])) {
                    $errors['attachment'] = $errors['file_name'];
                    unset($errors['file_name']);
                }
                $contractForm->setError($errors);
            } else if($tab === 'edit-supplier') {
                $model->setError(unserialize($error));
            }
        }

        return $this->render('edit',[
            'formData'=>$formData,
            'contactModel'=>$contactModel,
            'model'=>$model,
            'tab'=>$tab,
            'finAccountModel'=>$finAccountModel,
            'finAccountList'=>$finAccountList,
            'contractForm'=>$contractForm,
            'templateList'=>$templateList,
            'contractList'=>$contractList,
            'enableEditSupplier'=>$enableEditSupplier,
        ]);
    }

    public function actionUpdate() {
        if(Yii::$app->request->isPost) {
            $model = new SupplierForm();
            $formData = Yii::$app->request->post();
            // $backUrl
            $backUrl = Json::decode($formData['backUrl']);
//            unset($formData['backUrl']);
            $attachment = UploadedFile::getInstance($model,'attachment');
            if (!empty($attachment)) {
                $model->file_name = $attachment->baseName;
            }
            if($model->load($formData) && $model->validate()) {
                if(!empty($attachment)) {
                    $formData['SupplierForm']['attachment'] = $attachment;
                }
                $supplier = new Supplier();
                if ($supplier->updateRecord($formData['SupplierForm'])) {
                    $this->redirect(!empty($backUrl)?$backUrl:['/crm/supplier/index']);
                } else {

                }
            } else {
                $error = serialize($model->errors);
                return $this->redirect([
                    'edit',
                    'uuid'=>$formData['SupplierForm']['uuid'],
                    'error'=>$error,
                    'tab'=>'edit-supplier',
                ]);
            }
        }
    }
    
    public function actionListFilter()
    {
        $supplier = new Supplier();
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $supplier->clearEmptyField($filter);
        $supplierList = $supplier->listFilter($filter);
        $model = new SupplierForm();
        $contactModel = new ContactForm();
        return $this->render('index',[
            'model'=>$model,
            'supplierList'=>$supplierList,
            'contactModel'=>$contactModel,
            'ser_filter'=>serialize($filter),
        ]);
    }

    public function actionCodeUpdate() {
        return ;
        $supplierConfig = new SupplierConfig();
        $config = $supplierConfig->generateConfig();
        unset($config['supplierCode']);
        unset($config['supplier_code']);
        unset($config['contract_code']);
        unset($config['code']);
        $supplierConfig->updateDateConfigByJsonString(Json::encode($config));
        $supplier = new Supplier();
        $supplierList = $supplier->find()->asArray()->all();
        for($i = 0; $i < count($supplierList); $i++) {
            $record = $supplier->find()->andWhere(['uuid'=>$supplierList[$i]['uuid']])->one();
            $config = $supplierConfig->generateConfig();
            // 获取当前的code
            if(isset($config['supplier_code'])) {
                $priCode = date('y',time()) . '0001';
                $configCode = $config['supplier_code'];
                $code = $priCode >= $configCode
                    ? $priCode:$configCode;
            } else {
                $code = $config['supplier_code'] = date('y',time()) . '0001';
            }
            $record->code = $code;
            var_dump($code);
            $record->update();
            $config['supplier_code'] =$code + 1;
            $supplierConfig->updateDateConfigByJsonString(Json::encode($config));
        }
    }

    public function actionContractCodeUpdate() {
        return ;
        $supplierList = Supplier::find()->asArray()->all();
        foreach($supplierList as $supplier) {
            $contractMapList = SupplierContractMap::find()->andWhere(['supplier_uuid'=>$supplier['uuid']])->asArray()->all();
            if(empty($contractMapList)) {
                continue;
            }
            $tail = 1;
            $code = $supplier['code'];
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
                $record->type = SupplierForm::codePrefix;
                $record->update();
            }
        }
    }

    public function actionRebuildStatus() {
        return ;
        $supplierConfig = new SupplierConfig();
        $config = $supplierConfig->generateConfig();
        $status = $config['status'];
        $list = Supplier::find()->all();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach($list as $item) {
                if(!isset($status[$item->status])) {
                    $item->status = SupplierConfig::StatusWaitForAssess;
                } elseif($status[$item->status] == '待审核') {
                    $item->status = SupplierConfig::StatusWaitForAssess;
                } elseif($status[$item->status] == '审核通过') {
                    $item->status = SupplierConfig::StatusWaitForAssess;
                } elseif($status[$item->status] == '审核不通过') {
                    $item->status = SupplierConfig::StatusAssessFailed;
                }
                $item->update();
            }
            unset($config['status']);
            $supplierConfig->updateDateConfigByJsonString(Json::encode($config));
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::error('rebuild status failed');
            return false;
        }
        $transaction->commit();
        return $this->redirect(['index']);
    }
}