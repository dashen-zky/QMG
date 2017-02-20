<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/26 0026
 * Time: 上午 11:45
 */

namespace backend\modules\fin\accountReceivable\controllers;


use backend\models\BackEndBaseController;
use backend\models\CompressHtml;
use backend\modules\fin\accountReceivable\models\AccountReceivable;
use Yii;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\helpers\Json;

class ReceiveMoneyController extends BackEndBaseController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'evidence-delete'=>['get','ajax'],
                ],
            ],
        ]);
    }

    public function actionIndex() {
        $accountReceivable = new AccountReceivable();
        return $this->render('index', [
            'model'=>$accountReceivable,
            'receiveMoneyList'=>$accountReceivable->allList(),
            'tab'=>$this->getTab(Yii::$app->request->get('tab'), 'list'),
        ]);
    }
    
    public function actionShow() {
        if(!Yii::$app->request->isGet) {
            return $this->redirect(['index']);
        }
        
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['index']);
        }
        
        $accountReceivable = new AccountReceivable();
        $formData = $accountReceivable->getRecordByUuid($uuid);
        return CompressHtml::compressHtml($this->renderPartial('form',[
            'formData'=>$formData,
            'model'=>$accountReceivable,
            'action'=>['/accountReceivable/receive-money/update'],
            'show'=>true,
        ]));
    }

    public function actionAdd() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('AccountReceivable');
        $accountReceivable = new AccountReceivable();
        $formData['file'] = UploadedFile::getInstances($accountReceivable, 'file');
        if($accountReceivable->insertRecord($formData)) {
            return $this->redirect(['index']);
        } else {
            return $this->render('index',[
                'tab'=>'add',
                'formData'=>$formData,
                'model'=>$accountReceivable,
                'receiveMoneyList'=>$accountReceivable->allList(),
            ]);
        }
    }

    public function actionFormValidate() {
        if (!Yii::$app->request->isAjax) {
            return null;
        }

        $formData = Yii::$app->request->post();
        if (empty($formData)) {
            return null;
        }

        $accountReceivable = new AccountReceivable();
        $accountReceivable->setScenario($formData['scenario']);
        if ($accountReceivable->load($formData) && $accountReceivable->validate()) {
            return 1;
        }

        return Json::encode($accountReceivable->getErrors());
    }

    public function actionUpdate() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('AccountReceivable');
        $accountReceivable = new AccountReceivable();
        $formData['file'] = UploadedFile::getInstances($accountReceivable, 'file');
        if($accountReceivable->updateRecord($formData)) {
            return $this->redirect(['index']);
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

        $accountReceivable = new AccountReceivable();
        if($accountReceivable->deleteRecord($uuid)) {
            return $this->redirect(['index']);
        }
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
        $accountReceivable = new AccountReceivable();
        $accountReceivable->clearEmptyField($filter);
        $receiveMoneyList = $accountReceivable->listFilter($filter);
        return $this->render('index',[
            'receiveMoneyList'=>$receiveMoneyList,
            'ser_filter'=>serialize($filter),
            'model'=>$accountReceivable,
        ]);
    }

    public function actionEvidenceDelete() {
        $uuid = Yii::$app->request->get('uuid');
        $path = Yii::$app->request->get('path');
        $record = new AccountReceivable();
        return $record->deleteAttachment($uuid, $path);
    }
}