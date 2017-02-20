<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-12-9
 * Time: 下午8:51
 */

namespace backend\modules\hr\recruitment\controllers;


use backend\models\BackEndBaseController;
use backend\modules\hr\recruitment\models\Candidate;
use backend\modules\hr\recruitment\models\CandidateConfig;
use Yii;
use backend\models\UploadForm;
use yii\web\UploadedFile;
use backend\models\CompressHtml;
class CandidateController extends BackEndBaseController
{
    public function actionIndex() {
        if(Yii::$app->request->isAjax) {
            return $this->pagination();
        }

        return $this->render('index',[
            'tab'=>$this->getParam('tab', 'candidate-list'),
        ]);
    }

    public function pagination() {
        $ser_filter = $this->getParam('ser_filter', null);
        $filter = empty($ser_filter)?null:unserialize($ser_filter);
        if(!Yii::$app->request->isAjax) {
            return null;
        }

        $candidateList = (new Candidate())->candidateList([
            'candidate'=>[
                '*'
            ]
        ], $filter);

        if (isset($filter['location']) && $filter['location'] == CandidateConfig::LocateBlackList) {
            return CompressHtml::compressHtml($this->renderPartial('black-list', [
                'candidateList'=>$candidateList,
                'ser_filter'=>$ser_filter,
            ]));
        }

        return CompressHtml::compressHtml($this->renderPartial('list', [
            'candidateList'=>$candidateList,
            'ser_filter'=>$ser_filter,
        ]));
    }

    public function actionAdd() {
        if (!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('CandidateForm');
        $model = New UploadForm();
        $formData['attachment'] = UploadedFile::getInstances($model, 'file');
        $candidate = new Candidate();
        if ($candidate->insertRecord($formData)) {
            return $this->redirect(['index']);
        }
    }

    public function actionUpdate() {
        if (!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('CandidateForm');
        $model = New UploadForm();
        $formData['attachment'] = UploadedFile::getInstances($model, 'file');
        $candidate = new Candidate();
        if ($candidate->updateRecord($formData)) {
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

        $candidate = new Candidate();
        $formData = $candidate->getRecord($uuid);
        return CompressHtml::compressHtml($this->renderPartial('form',[
            'formData'=>$formData,
            'action'=>['/recruitment/candidate/update'],
            'edit'=>$this->getParam('edit', false),
            'show'=>true,
        ]));
    }

    public function actionValidatePhone() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }

        $phone = Yii::$app->request->get('phone');
        if (empty($phone)) {
            return 1;
        }

        $candidate = new Candidate();
        return $candidate->validatePhone($phone);
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
        $candidate = new Candidate();
        $candidate->clearEmptyField($filter);
        $candidateList = $candidate->listFilter($filter);

        return CompressHtml::compressHtml($this->renderPartial('list',[
            'candidateList'=>$candidateList,
            'ser_filter'=>serialize($filter),
        ]));
    }

    public function actionBlackListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index','tab'=>'black-list']);
            }
            $filter = unserialize($ser_filter);
        }
        $candidate = new Candidate();
        $candidate->clearEmptyField($filter);
        $candidateList = $candidate->blackListFilter($filter);

        return CompressHtml::compressHtml($this->renderPartial('black-list',[
            'candidateList'=>$candidateList,
            'ser_filter'=>serialize($filter),
        ]));
    }

    public function actionAddBlackList() {
        if (!Yii::$app->request->isGet) {
            return null;
        }

        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return null;
        }

        $candidate = new Candidate();
        if($candidate->updateRecord([
            'uuid'=>$uuid,
            'location'=>CandidateConfig::LocateBlackList
        ])) {
            return $this->redirect(['index']);
        }
    }

    public function actionResumeDownload() {
        $path = Yii::$app->request->get("path");
        $path = iconv("UTF-8", "GBK", $path);
        if (empty($path)) {
            $this->redirect(['index']);
        }
        $file_name = Yii::$app->request->get('file_name');
        $path = Yii::getAlias("@app") . $path;
        Yii::$app->response->sendFile($path, $file_name);
    }

    public function actionResumeDelete() {
        if(Yii::$app->request->isAjax) {
            $uuid = Yii::$app->request->get('uuid');
            $path = Yii::$app->request->get('path');
            $stamp = new Candidate();
            return $stamp->deleteAttachment($uuid, $path);
        }
    }
}