<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/26 0026
 * Time: 下午 3:30
 */

namespace backend\modules\crm\controllers;


use backend\modules\crm\models\project\record\ProjectPaymentMap;
use Yii;
use backend\modules\fin\payment\models\PaymentConfig;
use yii\helpers\Json;
use backend\models\CompressHtml;
use backend\modules\crm\models\project\record\Project;
use backend\modules\rbac\model\RBACManager;
use backend\modules\crm\models\project\model\ProjectConfig;
use backend\modules\crm\models\project\model\ProjectForm;
class ProjectApplyPaymentController extends CRMBaseController
{
    public function actionIndex() {
        if(Yii::$app->request->isAjax) {
            return $this->pagination();
        }
        $applyPayment = new ProjectPaymentMap();
        $paymentList = $applyPayment->myPaymentList();
        return $this->render('index', [
            'paymentList'=>$paymentList,
        ]);
    }
    
    public function actionSupplierPaymentList() {
        
    }

    public function pagination() {
        if(!Yii::$app->request->isAjax) {
            return null;
        }
        $applyPayment = new ProjectPaymentMap();
        $paymentList = $applyPayment->myPaymentList();
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
        $applyPayment = new ProjectPaymentMap();
        if($applyPayment->insertRecord($formData)) {
            return $this->redirect(['index']);
        }
    }

    public function actionSaveApply() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }
        $formData = Yii::$app->request->post('ApplyPaymentForm');
        $applyPayment = new ProjectPaymentMap();
        $formData['status'] = PaymentConfig::StatusSave;
        if($applyPayment->insertRecord($formData)) {
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
        $applyPayment = new ProjectPaymentMap();
        $formData = $applyPayment->getRecordByUuid($uuid);
        return CompressHtml::compressHtml($this->renderPartial('form',[
            'formData'=>$formData,
            'show'=>true,
            'action'=>null,
        ]));
    }

    public function actionEdit() {
        if(!Yii::$app->request->isGet) {
            return false;
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return false;
        }

        $applyPayment = new ProjectPaymentMap();
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
        $applyPayment = new ProjectPaymentMap();
        if($applyPayment->updateRecord($formData)) {
            return $this->redirect(['index']);
        }
    }

    public function actionProjectList() {
        $project = new Project();
        $list = $project->applyPaymentProjectList();

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
        $list = $project->listFilter($filter, 10);

        return CompressHtml::compressHtml($this->renderPartial('/project-select/select-list',[
            'list'=>$list,
            'ser_filter'=>serialize($filter),
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

        $applyPayment = new ProjectPaymentMap();
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
        $applyPayment = new ProjectPaymentMap();
        foreach($uuids as $uuid) {
            $applyPayment->updateRecord([
                'uuid'=>$uuid,
                'status'=>PaymentConfig::StatusWaitFirstAssess,
            ]);
        }
        return $this->redirect(['index']);
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
        $applyPayment = new ProjectPaymentMap();
        $applyPayment->clearEmptyField($filter);
        $paymentList = $applyPayment->listFilter($filter);

        return CompressHtml::compressHtml($this->renderPartial('list',[
            'paymentList'=>$paymentList,
            'ser_filter'=>serialize($filter),
        ]));
    }
}