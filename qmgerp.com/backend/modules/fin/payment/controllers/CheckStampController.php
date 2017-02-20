<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/21 0021
 * Time: 下午 3:01
 */

namespace backend\modules\fin\payment\controllers;


use backend\models\BackEndBaseController;
use backend\modules\fin\payment\models\PaymentList;
use backend\modules\fin\payment\models\PaymentStampMap;
use backend\modules\fin\stamp\models\StampConfig;
use Yii;
use backend\modules\crm\models\supplier\record\SupplierPaymentMap;
use backend\models\CompressHtml;
use backend\models\BaseRecord;
use backend\modules\fin\stamp\models\ImportStamp;

class CheckStampController extends BackEndBaseController
{
    public function actionIndex() {
        $paymentList = (new PaymentList())->checkStampList();
        return $this->render('index', [
            'paymentList'=>$paymentList,
        ]);
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
    
    public function actionCheckStamp() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('StampCheckForm');
        $paymentStamp = new PaymentStampMap();
        if($paymentStamp->checkStamp($formData)) {
            return $this->redirect(['index']);
        }
    }
    
   public function actionStampList() {
       if(!Yii::$app->request->isAjax) {
           return  null;
       }

       $importStamp = new ImportStamp();
       $stampList = $importStamp->importStampList([
           '=',
           $importStamp->aliasMap['stamp'] . '.enable',
           StampConfig::Enable,
       ]);
       $paymentStamp = new PaymentStampMap();
       $checked = $paymentStamp->getStampUuidsByPaymentUuid(Yii::$app->request->get('uuid'));
       return CompressHtml::compressHtml($this->renderPartial('@stamp/views/import-stamp/stamp-select-list',[
           'stampList'=>$stampList,
           'checked'=>$checked,
       ]));
   }

    public function actionCheckedStampList() {
        if(!Yii::$app->request->isAjax) {
            return  null;
        }

        $paymentStamp = new PaymentStampMap();;
        $stampList = $paymentStamp->getStampListByPaymentUuid(Yii::$app->request->get('uuid'));
        return CompressHtml::compressHtml($this->renderPartial('checked-stamp-list',[
            'stampList'=>$stampList,
        ]));
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