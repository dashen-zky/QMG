<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11 0011
 * Time: 下午 5:10
 */

namespace backend\modules\fin\payment\controllers;


use backend\modules\fin\payment\models\Payment;
use backend\modules\fin\payment\models\PaymentList;
use yii\web\Controller;
use backend\modules\crm\models\supplier\record\SupplierPaymentMap;
use backend\models\CompressHtml;
use backend\models\BaseRecord;
use Yii;
use backend\models\UploadForm;
use yii\web\UploadedFile;
class PaymentController extends Controller
{
    public function actionIndex() {
        $paymentList = new PaymentList();
        $assessList = $paymentList->assessList();
        $moneyStatistic = $paymentList->moneyStatistic();
        return $this->render('index', [
            'paymentList'=>$assessList,
            'moneyStatistic'=>$moneyStatistic,
        ]);
    }

    public function actionPaying() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post();
        $model = New UploadForm();
        $formData['PayingForm']['attachment'] = UploadedFile::getInstances($model, 'file');
        $payment = new Payment();
        if($payment->pay($formData['PayingForm'])) {
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
            'action'=>null,
        ]));
    }

    public function actionPrint() {
        $this->layout = '@webroot/../views/layouts/base';
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return false;
        }
        $applyPayment = new SupplierPaymentMap();
        $formData = $applyPayment->getRecordByUuid($uuid);
        return $this->render('print',[
            'formData'=>$formData,
        ]);
    }

    public function actionListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['waiting']);
            }
            $filter = unserialize($ser_filter);
        }

        (new BaseRecord())->clearEmptyField($filter);
        $paymentList = (new PaymentList())->listFilter($filter);

        return $this->render('index',[
            'paymentList'=>$paymentList,
            'ser_filter'=>serialize($filter),
        ]);
    }
}