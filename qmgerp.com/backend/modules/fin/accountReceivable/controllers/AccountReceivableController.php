<?php
namespace backend\modules\fin\accountReceivable\controllers;
use backend\models\BackEndBaseController;
use backend\models\CompressHtml;
use backend\modules\crm\models\project\record\Project;
use backend\modules\crm\models\project\record\ProjectAccountReceivableMap;
use backend\modules\fin\accountReceivable\models\AccountReceivable;
use Yii;
use yii\helpers\Json;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/25 0025
 * Time: 下午 11:43
 */
class AccountReceivableController extends BackEndBaseController
{
    public function actionIndex() {
        $project = new Project();
        $projectList = $project->accountReceivableList();
        return $this->render('index',[
            'projectList'=>$projectList,
        ]);
    }
    
    public function actionReceiveRecordList() {
        if(!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
        
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['index']);
        }
        
        $receiveRecordList = (new ProjectAccountReceivableMap())->getReceiveRecordListByProjectUuid($uuid);
        return CompressHtml::compressHtml($this->renderPartial('receive-record-list',[
            'receiveRecordList'=>$receiveRecordList,
        ]));
    }
    
    public function actionReceiveMoney() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }
        
        $formData = Yii::$app->request->post('ReceiveMoneyForm');
        $projectAccountReceivable = new ProjectAccountReceivableMap();
        if($projectAccountReceivable->receiveMoney($formData)) {
            return $this->redirect(['index']);
        }
    }
    
    public function actionValidateBankSeriesNumber() {
        if(!Yii::$app->request->isAjax) {
            return null;
        }

        $bankSeriesNumber = Yii::$app->request->get('bank_series_number');
        if(empty($bankSeriesNumber)) {
            return null;
        }

        $record = (new AccountReceivable())->getRecordByBankSeriesNumber($bankSeriesNumber);
        if(empty($record)) {
            return -1;
        }
        return Json::encode([
            'rest_money'=>$record->money - $record->distributed_money,
            'account_receivable_uuid'=>$record->uuid,
        ]);
    }

    public function actionListFilter() {
        $project = new Project();
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $project->clearEmptyField($filter);
        $projectList = $project->listFilter($filter);

        return $this->render('index',[
            'projectList'=>$projectList,
            'ser_filter'=>serialize($filter),
        ]);
    }
}