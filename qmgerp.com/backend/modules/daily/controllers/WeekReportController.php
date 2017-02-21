<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-11-28
 * Time: 下午3:47
 */

namespace backend\modules\daily\controllers;

use backend\models\BackEndBaseController;
use backend\models\CompressHtml;
use backend\modules\daily\models\transaction\Transaction;
use backend\modules\daily\models\transaction\TransactionConfig;
use backend\modules\daily\models\week_report\WeekReport;
use Yii;
class WeekReportController extends BackEndBaseController
{
    public function actionIndex() {
        return $this->render('index', [
            'tab'=>$this->getParam('tab', 'list')
        ]);
    }

    public function actionAdd() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('WeekReportForm');
        // var_dump($formData);die;
        $weekReport = new WeekReport();
        if ($weekReport->insertRecord($formData)) {
            // 把 对应的事项 改变状态
            // if($transaction->updateTransactionStatus($formData['transaction_uuid'])){/
                return $this->redirect(['index']);
            // }
        }
    }

    public function actionDel() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return $this->redirect(['index']);
        }

        $weekReport = new WeekReport();
        if($weekReport->deleteRecord($uuid)) {
            return $this->redirect(['index']);
        }
    }

    public function actionShow() {
        if (!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return $this->redirect(['index']);
        }
        $weekReport = new WeekReport();
        $formData = $weekReport->getRecord($uuid);
        $transaction = new Transaction();
        $finishedTransactionList = $transaction->getTransactionListByWeekReportUuid($uuid, TransactionConfig::CurrentWeekTransaction);
        $unfinishedTransactionList = $transaction->getTransactionListByWeekReportUuid($uuid, TransactionConfig::NextWeekTransaction);
        return CompressHtml::compressHtml($this->renderPartial('show', [
            'formData'=>$formData,
            'finishedTransactionList'=>$finishedTransactionList['list'],
            'unfinishedTransactionList'=>$unfinishedTransactionList['list'],
        ]));
    }

    public function actionListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['/daily/week-report/index']);
            }
            $filter = unserialize($ser_filter);
        }
        $weekReport = new WeekReport();
        $weekReport->clearEmptyField($filter);
        $weekReportList = $weekReport->listFilter($filter);

        return $this->render('/week-report/index', [
            'weekReportList'=>$weekReportList,
            'ser_filter'=>serialize($filter),
        ]);
    }
}