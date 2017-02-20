<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/23 0023
 * Time: 上午 11:25
 */

namespace backend\modules\daily\controllers;

use backend\models\BackEndBaseController;
use backend\models\CompressHtml;
use backend\modules\daily\models\payment\ApplyPayment;
use backend\modules\fin\payment\models\PaymentConfig;
use Yii;
use yii\helpers\Json;

class ApplyPaymentController extends BackEndBaseController
{
    public function actionIndex() {
        $applyPayment = new ApplyPayment();
        $paymentList = $applyPayment->myPaymentList();
        return $this->render('index', [
            'paymentList'=>$paymentList,
            'tab'=>$this->getParam('tab','list')
        ]);
    }

    public function actionSubmitApply() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }
        $formData = Yii::$app->request->post('ApplyPaymentForm');
        $formData['status'] = PaymentConfig::StatusWaitFirstAssess;
        $applyPayment = new ApplyPayment();
        if($applyPayment->insertRecord($formData)) {
            return $this->redirect(['index']);
        }
    }

    public function actionSaveApply() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }
        $formData = Yii::$app->request->post('ApplyPaymentForm');
        $applyPayment = new ApplyPayment();
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
        $applyPayment = new ApplyPayment();
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

        $applyPayment = new ApplyPayment();
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
        $applyPayment = new ApplyPayment();
        if($applyPayment->updateRecord($formData)) {
            return $this->redirect(['index']);
        }
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

        $applyPayment = new ApplyPayment();
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
        $applyPayment = new ApplyPayment();
        foreach($uuids as $uuid) {
            $applyPayment->updateRecord([
                'uuid'=>$uuid,
                'status'=>PaymentConfig::StatusWaitFirstAssess,
            ]);
        }
        return $this->redirect(['index']);
    }

    public function actionListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $applyPayment = new ApplyPayment();
        $applyPayment->clearEmptyField($filter);
        $paymentList = $applyPayment->listFilter($filter);

        return $this->render('index',[
            'paymentList'=>$paymentList,
            'ser_filter'=>serialize($filter),
        ]);
    }
}