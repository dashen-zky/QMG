<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/27 0027
 * Time: 下午 4:05
 */

namespace backend\modules\fin\accountReceivable\controllers;


use backend\models\BackEndBaseController;
use backend\modules\crm\models\project\record\ProjectApplyStamp;
use backend\modules\crm\models\project\record\ProjectApplyStampMap;
use backend\modules\fin\stamp\models\ExportStamp;
use Yii;
use yii\helpers\Json;
use backend\models\CompressHtml;
use backend\modules\crm\models\stamp\Stamp;
class StampApplyController extends BackEndBaseController
{
    public function actionIndex() {
        $projectApplyStamp = new ProjectApplyStamp();
        $projectApplyStampList = $projectApplyStamp->allList();
        return $this->render('index',[
            'projectApplyStampList'=>$projectApplyStampList,
        ]);
    }

    public function actionValidateStampSeriesNumber() {
        if(!Yii::$app->request->isAjax) {
            return null;
        }

        $stampSeriesNumber = Yii::$app->request->get('stamp_series_number');
        if(empty($stampSeriesNumber)) {
            return null;
        }

        $record = (new ExportStamp())->getRecordBySeriesNumber($stampSeriesNumber);
        if(empty($record)) {
            return -1;
        }
        return Json::encode([
            'rest_money'=>$record->money - $record->checked_money,
            'stamp_uuid'=>$record->uuid,
        ]);
    }

    public function actionBilling() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('BillingForm');
        $projectApplyStampMap = new ProjectApplyStampMap();
        if($projectApplyStampMap->insertSingleRecord($formData)) {
            return $this->redirect(['index']);
        }
    }

    public function actionDisable() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['index']);
        }

        $projectApplyStamp = new ProjectApplyStamp();
        if($projectApplyStamp->updateRecord([
            'uuid'=>$uuid,
            'enable'=>ProjectApplyStamp::Disable
        ])) {
            return $this->redirect(['index']);
        }
    }

    public function actionLoadStampMessage() {
        if(!Yii::$app->request->isAjax) {
            return ;
        }

        $uuid = Yii::$app->request->get('uuid');
        $stamp = new Stamp();
        return Json::encode($stamp->getRecord($uuid));
    }
    
    public function actionBillingRecordList() {
        if(!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['index']);
        }

        $billingRecordList = (new ProjectApplyStampMap())->getBillingRecordListByApplyStampUuid($uuid);
        return CompressHtml::compressHtml($this->renderPartial('billing-record-list',[
            'billingRecordList'=>$billingRecordList,
        ]));
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
        $projectApplyStamp = new ProjectApplyStamp();
        $projectApplyStamp->clearEmptyField($filter);
        $projectApplyStampList = $projectApplyStamp->listFilter($filter);
        return $this->render('index',[
            'projectApplyStampList'=>$projectApplyStampList,
            'ser_filter'=>serialize($filter),
        ]);
    }
}