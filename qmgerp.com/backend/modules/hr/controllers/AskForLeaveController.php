<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-11-17
 * Time: 上午11:49
 */

namespace backend\modules\hr\controllers;


use backend\models\BackEndBaseController;
use backend\models\CompressHtml;
use backend\modules\hr\models\ask_leave\AskForLeave;
use backend\modules\hr\models\config\EmployeeBasicConfig;
use Yii;
class AskForLeaveController extends BackEndBaseController
{
    public function actionIndex() {
        $askForLeave = new AskForLeave();
        $askForLeaveList = $askForLeave->myAskForLeaveList();
        return $this->render('index', [
            'askForLeaveList'=>$askForLeaveList,
            'tab'=>$this->getParam('tab','list'),
        ]);
    }

    public function actionHrIndex() {
        $askForLeave = new AskForLeave();
        $askForLeaveList = $askForLeave->askForLeaveListForHumanResource();
        return $this->render('hr-index', [
            'askForLeaveList'=>$askForLeaveList,
        ]);
    }

    public function actionAssess() {
        $askForLeave = new AskForLeave();
        $askForLeaveList = $askForLeave->myAssessAskForLeaveList();
        return $this->render('assess', [
            'askForLeaveList'=>$askForLeaveList,
        ]);
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
        $askForLeave = new AskForLeave();
        $askForLeave->clearEmptyField($filter);
        $askForLeaveList = $askForLeave->listFilter($filter);

        return $this->render('index', [
            'askForLeaveList'=>$askForLeaveList,
            'ser_filter'=>serialize($filter),
        ]);
    }

    public function actionHrListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['assess']);
            }
            $filter = unserialize($ser_filter);
        }
        $askForLeave = new AskForLeave();
        $askForLeave->clearEmptyField($filter);
        $askForLeaveList = $askForLeave->hrListFilter($filter);

        return $this->render('hr-index', [
            'askForLeaveList'=>$askForLeaveList,
            'ser_filter'=>serialize($filter),
        ]);
    }

    public function actionAssessListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['assess']);
            }
            $filter = unserialize($ser_filter);
        }
        $askForLeave = new AskForLeave();
        $askForLeave->clearEmptyField($filter);
        $askForLeaveList = $askForLeave->assessListFilter($filter);

        return $this->render('assess', [
            'askForLeaveList'=>$askForLeaveList,
            'ser_filter'=>serialize($filter),
        ]);
    }

    /**
     * 通过审核
     */
    public function actionAssessPassed() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['assess']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['assess']);
        }
        $askForLeave = new AskForLeave();
        if($askForLeave->assessPassed($uuid)) {
            return $this->redirect(['assess']);
        }
    }

    // 审核不通过
    public function actionAssessRefused() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['assess']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['assess']);
        }
        $askForLeave = new AskForLeave();
        if($askForLeave->assessRefused($uuid)) {
            return $this->redirect(['assess']);
        }
    }

    public function actionAdd() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('AskLeaveForm');
        $askForLeave = new AskForLeave();
        if($askForLeave->insertRecord($formData)) {
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

        $formData = (new AskForLeave())->getRecord($uuid);
        return CompressHtml::compressHtml($this->renderPartial('form', [
            'formData'=>$formData,
            'show'=>true,
            'action'=>['/hr/ask-for-leave/update'],
            'edit'=>$this->getParam('edit', false) && $formData['status'] == EmployeeBasicConfig::AskLeaveApplying,
        ]));
    }

    public function actionUpdate() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('AskLeaveForm');
        $askForLeave = new AskForLeave();
        if($askForLeave->updateRecord($formData)) {
            return $this->redirect(['index']);
        }
    }

    public function actionDel() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        $askForLeave = New AskForLeave();
        if($askForLeave->deleteRecord($uuid)) {
            return $this->redirect(['index']);
        }
    }
}