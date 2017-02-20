<?php
namespace backend\modules\payment_assess\controllers;
use backend\models\BackEndBaseController;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/30 0030
 * Time: 上午 11:49
 */
use backend\modules\fin\payment\models\Payment;
use Yii;
use backend\models\CompressHtml;
use backend\models\BaseRecord;
class PaymentAssessController extends BackEndBaseController
{
    protected $paymentAssess;
    protected $applyPayment;

    public function actionWaiting() {
        $paymentList = $this->paymentAssess->assessList();
        return $this->render('waiting',[
            'paymentList' => $paymentList,
        ]);
    }

    public function actionSucceed() {
        $paymentList = $this->paymentAssess->assessList(Payment::AssessSucceed);
        return $this->render('succeed',[
            'paymentList' => $paymentList,
        ]);
    }

    public function actionRefused() {
        $paymentList = $this->paymentAssess->assessList(Payment::AssessRefused);
        return $this->render('refused',[
            'paymentList' => $paymentList,
        ]);
    }

    public function assessing($action) {
        if(!Yii::$app->request->isAjax) {
            return false;
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return false;
        }

        $formData = $this->applyPayment->getRecordByUuid($uuid);
        return CompressHtml::compressHtml($this->renderPartial('form',[
            'formData'=>$formData,
            'entrance'=>Yii::$app->request->get('entrance'),
            'action'=>$action,
        ]));
    }

    public function actionShow() {
        if(!Yii::$app->request->isAjax) {
            return false;
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return false;
        }

        $formData = $this->applyPayment->getRecordByUuid($uuid);
        return CompressHtml::compressHtml($this->renderPartial('form',[
            'formData'=>$formData,
            'show'=>true,
            'entrance'=>Yii::$app->request->get('entrance'),
            'action'=>null,
        ]));
    }

    public function actionAssess() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['waiting']);
        }

        $formData = Yii::$app->request->post('ApplyPaymentForm');
        if($this->applyPayment->assess($formData)) {
            $backUrl = [
                Payment::AssessSucceed => 'succeed',
                Payment::AssessRefused => 'refused',
                Payment::WaitingAssess => 'waiting',
            ];
            return $this->redirect([$backUrl[$formData['entrance']]]);
        }
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

        $paymentList = $this->paymentAssess->listFilter($filter);

        $backUrl = [
            Payment::AssessSucceed => 'succeed',
            Payment::AssessRefused => 'refused',
            Payment::WaitingAssess => 'waiting',
        ];
        return $this->render($backUrl[$filter['entrance']],[
            'paymentList'=>$paymentList,
            'ser_filter'=>serialize($filter),
        ]);
    }
}