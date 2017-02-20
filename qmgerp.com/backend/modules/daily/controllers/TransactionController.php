<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-11-28
 * Time: 下午3:48
 */

namespace backend\modules\daily\controllers;

use backend\models\BackEndBaseController;
use backend\models\CompressHtml;
use backend\modules\daily\models\transaction\Transaction;
use backend\modules\daily\models\transaction\TransactionConfig;
use backend\modules\hr\models\EmployeeBasicInformation;
use backend\modules\rbac\model\RBACManager;
use Yii;
class TransactionController extends BackEndBaseController
{
    public function actionIndex() {
        return $this->render('index', [
            'tab'=>$this->getParam('tab', 'unfinished-list')
        ]);
    }

    public function actionAdd() {
        if(!Yii::$app->request->isPost) {
            return false;
        }

        $formData = Yii::$app->request->post('TransactionForm');
        if(empty($formData)) {
            return true;
        }

        $transaction = new Transaction();
        if($transaction->add($formData)) {
            return $this->redirect(['index']);
        }
    }

    public function actionShow() {
        if(!Yii::$app->request->isAjax) {
            return '';
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return '';
        }

        $transaction = new Transaction();
        $formData = $transaction->getRecord($uuid);
        return CompressHtml::compressHtml($this->renderPartial('form',[
            'action'=>['/daily/transaction/update'],
            'show'=>true,
            'formData'=>$formData,
            'back_tab'=>$this->getParam('back_tab','unfinished-list')
        ]));
    }

    public function actionSetTop() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['index']);
        }

        $transaction = new Transaction();
        if($transaction->setTop($uuid)) {
            $this->redirect(['index']);
        }
    }

    public function actionUpdate() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('TransactionForm');
        if (empty($formData)) {
            return $this->redirect(['index']);
        }

        $transaction = new Transaction();
        if($transaction->updateRecord($formData)) {
            return $this->redirect([
                'index',
                'tab'=>$formData['back_tab'],
            ]);
        }
    }

    public function actionDrop() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['index']);
        }

        $transaction = new Transaction();
        if($transaction->updateRecord([
            'uuid'=>$uuid,
            'status'=>TransactionConfig::StatusDropped,
            'finished_time'=>time(),
        ])) {
            return $this->redirect([
                'index',
                'tab'=>'finished-list'
            ]);
        }
    }

    public function actionDel() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['index']);
        }

        $transaction = new Transaction();
        if($transaction->deleteRecord($uuid)) {
            return $this->redirect([
                'index',
            ]);
        }
    }

    public function actionFinish() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['index']);
        }

        $transaction = new Transaction();
        if($transaction->updateRecord([
            'uuid'=>$uuid,
            'status'=>TransactionConfig::StatusFinished,
            'finished_time'=>time(),
        ])) {
            return $this->redirect([
                'index',
                'tab'=>'finished-list'
            ]);
        }
    }

    public function actionEffectiveListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['/daily/week-report/index']);
            }
            $filter = unserialize($ser_filter);
        }
        $transaction = new Transaction();
        $transaction->clearEmptyField($filter);
        $transactionList = $transaction->effectiveListFilter($filter);

        return $this->render('/week-report/index', [
            'transactionList'=>$transactionList,
            'transaction_ser_filter'=>serialize($filter),
        ]);
    }

    public function actionFinishedListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $transaction = new Transaction();
        $transaction->clearEmptyField($filter);
        $finishedTransactionList = $transaction->finishedListFilter($filter);

        return $this->render('index', [
            'finishedTransactionList'=>$finishedTransactionList,
            'finished_ser_filter'=>serialize($filter),
        ]);
    }

    public function actionUnfinishedListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $transaction = new Transaction();
        $transaction->clearEmptyField($filter);
        $unfinishedTransactionList = $transaction->unfinishedListFilter($filter);

        return $this->render('index', [
            'unfinishedTransactionList'=>$unfinishedTransactionList,
            'unfinished_ser_filter'=>serialize($filter),
        ]);
    }

    public function actionEmployeeList() {
        $employee = new EmployeeBasicInformation();
        $employeeList = $employee->getEmployeeListByUuids($employee->getOrdinateUuidsWithoutSelf(RBACManager::Common));
        $uuids = Yii::$app->request->get('uuids');
        $uuids = explode(',', trim($uuids, ','));
        return CompressHtml::compressHtml($this->renderPartial('@hr/views/employee/employee-select-list-advance.php',[
            'employeeList'=>$employeeList,
            'uuids'=>$uuids,
        ]));
    }
}