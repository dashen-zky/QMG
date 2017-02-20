<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/26 0026
 * Time: 下午 11:50
 */

namespace backend\modules\crm\controllers;

use backend\models\BaseRecord;
use backend\models\CompressHtml;
use backend\modules\crm\models\customer\record\Contact;
use backend\modules\crm\models\part_time\record\PartTime;
use backend\modules\crm\models\part_time\record\PartTimeFinAccountMap;
use backend\modules\crm\models\project\model\ProjectConfig;
use backend\modules\crm\models\project\model\ProjectForm;
use backend\modules\crm\models\project\record\Project;
use backend\modules\crm\models\supplier\record\SupplierPaymentMap;
use backend\modules\crm\models\SupplierUnionPartTime;
use backend\modules\fin\models\account\models\FINAccountForm;
use backend\modules\fin\models\account\record\FINAccount;
use yii;
use yii\bootstrap\Html;
use backend\models\ViewHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use backend\modules\crm\models\supplier\record\SupplierFinAccountMap;
use backend\modules\fin\payment\models\PaymentConfig;
class SupplierApplyPaymentController extends CRMBaseController
{
    public function actionIndex() {
        if(Yii::$app->request->isAjax) {
            return $this->pagination();
        }
        $supplierPaymentMap = new SupplierPaymentMap();
        $paymentList = $supplierPaymentMap->myPaymentList();
        return $this->render('index', [
            'paymentList'=>$paymentList,
        ]);
    }

    public function actionPartTimePaymentList() {
        if(!Yii::$app->request->isGet) {
            return ;
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return ;
        }

        $supplierPaymentMap = new SupplierPaymentMap();
        $paymentList = $supplierPaymentMap->getPaymentListByPartTimeUuid($uuid);
        return CompressHtml::compressHtml($this->renderPartial('/supplier-payment/payment-list', [
            'paymentList'=>$paymentList,
        ]));
    }
    
    public function actionSupplierPaymentList() {
        if(!Yii::$app->request->isGet) {
            return ;
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return ;
        }

        $supplierPaymentMap = new SupplierPaymentMap();
        $paymentList = $supplierPaymentMap->getPaymentListBySupplierUuid($uuid);
        return CompressHtml::compressHtml($this->renderPartial('/supplier-payment/payment-list', [
            'paymentList'=>$paymentList,
        ]));
    }

    public function pagination() {
        if(!Yii::$app->request->isAjax) {
            return null;
        }
        $supplierPaymentMap = new SupplierPaymentMap();
        $paymentList = $supplierPaymentMap->myPaymentList();
        return CompressHtml::compressHtml($this->renderPartial('list', [
            'paymentList'=>$paymentList,
        ]));
    }

    public function actionSubmitApply() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }
        $formData = Yii::$app->request->post('ApplyPaymentForm');
        $formData['status'] = PaymentConfig::StatusWaitFirstAssess;
        $applyPayment = new SupplierPaymentMap();

        if($applyPayment->insertSingleRecord($formData)) {
            return $this->redirect(['index']);
        }
    }

    public function actionSaveApply() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }
        $formData = Yii::$app->request->post('ApplyPaymentForm');
        $applyPayment = new SupplierPaymentMap();
        $formData['status'] = PaymentConfig::StatusSave;
        if($applyPayment->insertSingleRecord($formData)) {
            return $this->redirect(['index']);
        }
    }

    public function actionShow() {
        if(!Yii::$app->request->isAjax) {
            return false;
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return false;
        }
        $applyPayment = new SupplierPaymentMap();
        $formData = $applyPayment->getRecordByUuid($uuid);
        return CompressHtml::compressHtml($this->renderPartial('form',[
            'formData'=>$formData,
            'show'=>true,
            'action'=>null,
        ]));
    }

    // 单个申请
    public function actionSingleApply() {
        if(!Yii::$app->request->isGet) {
            return false;
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return false;
        }

        $applyPayment = new SupplierPaymentMap();
        if($applyPayment->updateRecord([
            'uuid'=>$uuid,
            'status'=>PaymentConfig::StatusWaitFirstAssess,
        ])) {
            return $this->redirect(['index']);
        }
    }
//多个一起申请
    public function actionMultiApply() {
        if(!Yii::$app->request->isGet) {
            return false;
        }

        $uuids = Yii::$app->request->get('uuids');
        if(empty($uuids)) {
            return false;
        }
        $uuids = Json::decode($uuids);
        $applyPayment = new SupplierPaymentMap();
        foreach($uuids as $uuid) {
            $applyPayment->updateRecord([
                'uuid'=>$uuid,
                'status'=>PaymentConfig::StatusWaitFirstAssess,
            ]);
        }
        return $this->redirect(['index']);
    }

    public function actionEdit() {
        if(!Yii::$app->request->isGet) {
            return false;
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return false;
        }

        $applyPayment = new SupplierPaymentMap();
        $formData = $applyPayment->getRecordByUuid($uuid);
        return $this->render('edit',[
            'formData'=>$formData,
        ]);
    }

    public function actionUpdate() {
        if(!Yii::$app->request->isPost) {
            return false;
        }

        $formData = Yii::$app->request->post('ApplyPaymentForm');
        $applyPayment = new SupplierPaymentMap();
        if($applyPayment->updateRecord($formData)) {
            return $this->redirect(['index']);
        }
    }

    public function actionProjectList() {
        $project = new Project();
        $list = $project->projectList(
            [
                'project'=>[
                    'uuid',
                    'name',
                    'code',
                    'status',
                ],
                'customer'=>[
                    'name',
                ],
                'sales'=>[
                    'name'
                ],
                'project_manager'=>[
                    'name'
                ],
            ],
            [
                '<>',
                Project::$aliasMap['project'] . '.enable',
                Project::Disable,
            ],
            true,
            8
        );

        return CompressHtml::compressHtml($this->renderPartial('/project-select/select-list', [
            'list'=>$list,
        ]));
    }

    public function actionProjectListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->actionProjectList();
            }
            $filter = unserialize($ser_filter);
        }
        $project = new Project();
        $project->clearEmptyField($filter);
        $list = $project->listFilterForWithoutBelong($filter, 8);

        return CompressHtml::compressHtml($this->renderPartial('/project-select/select-list',[
            'list'=>$list,
            'ser_filter'=>serialize($filter),
        ]));
    }

    public function actionSupplierChange() {
        if(!Yii::$app->request->isAjax) {
            return null;
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return null;
        }

        $type = Yii::$app->request->get('type');
        if(empty($type)) {
            return null;
        }

        $_return = [];
        // 获取联系人列表
        $helper = new BaseRecord();
        if($type == SupplierPaymentMap::Supplier) {
            $contact = new Contact();
            $contactList = $contact->getContactListByObjectUuid($uuid, 'supplier');
            //
            $list1 = $helper->transformForDropDownList($contactList['contactList'],'uuid', 'name');
            $list2 = $helper->transformForDropDownList($contactList['customerDutyList'],'uuid', 'name');
            $list = array_merge($list1, $list2);
            $_return['html']['contact'] = CompressHtml::compressHtml(Html::dropDownList(
                'ApplyPaymentForm[receiver_contact_uuid]',
                null,
                ViewHelper::appendElementOnDropDownList($list),
                [
                    'class'=>'form-control col-md-12 contact-uuid',
                    'data-parsley-required'=>"true",
                    'url'=>Url::to([
                        '/crm/supplier-apply-payment/get-contact-information',
                    ]),
                ]
            ));
        } elseif ($type == SupplierPaymentMap::PartTime) {
            $partTime = new PartTime();
            $record = $partTime->getRecordByUuid($uuid);
            $_return['html']['contact'] = Html::textInput(
                'ApplyPaymentForm[receiver_contact_name_1]',
                $record['name'], [
                'data-parsley-required'=>"true",
                'class'=>'form-control col-md-12',
                'readOnly'=>true,
                ]);
            $_return['value']['contact-phone'] = $record['phone'];
            $_return['value']['contact-name'] = $record['name'];
        }

        // 根据供应商或是兼职获取收款账号列表
        if($type == SupplierPaymentMap::Supplier) {
            // 收款账户列表
            $finAccountMap = new SupplierFinAccountMap();
        } else if($type == SupplierPaymentMap::PartTime) {
            $finAccountMap = new PartTimeFinAccountMap();
        }
        $finAccountList = $finAccountMap->finAccountList($uuid);
        $finAccountList = $helper->transformForDropDownList($finAccountList['list'], 'uuid', 'account', 'name');
        $_return['html']['receiver'] = CompressHtml::compressHtml(Html::dropDownList(
            'ApplyPaymentForm[account_uuid]',
            null,
            ViewHelper::appendElementOnDropDownList($finAccountList),
            [
                'class'=>'form-control col-md-12 receiver-account-uuid',
                'data-parsley-required'=>"true",
                'url'=>Url::to([
                    '/crm/supplier-apply-payment/get-receiver-account-information'
                ]),
            ]
        ));

        return Json::encode($_return);
    }

    public function actionGetContactInformation() {
        if(!Yii::$app->request->isAjax) {
            return false;
        }

        $uuid = Yii::$app->request->get('uuid');
        $contact = new Contact();
        $record = $contact->getRecordByUuid($uuid);
        return Json::encode($record);
    }

    public function actionGetReceiverAccountInformation() {
        if(!Yii::$app->request->isAjax) {
            return false;
        }

        $uuid = Yii::$app->request->get('uuid');
        $contact = new FINAccount();
        $record = $contact->getRecordByUuid($uuid);
        $record['type_name'] = (new FINAccountForm())->getType($record['type']);
        return Json::encode($record);
    }

    public function actionListFilter() {
        if(!Yii::$app->request->isAjax) {
            return null;
        }

        $filter = Yii::$app->request->post('ListFilterForm');
        if(empty($filter)) {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $applyPayment = new SupplierPaymentMap();
        $applyPayment->clearEmptyField($filter);
        $paymentList = $applyPayment->listFilter($filter);

        return CompressHtml::compressHtml($this->renderPartial('list',[
            'paymentList'=>$paymentList,
            'ser_filter'=>serialize($filter),
        ]));
    }
}