<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-12-20
 * Time: 上午11:10
 */

namespace backend\modules\crm\controllers;


use backend\models\BackEndBaseController;
use backend\models\CompressHtml;
use backend\models\UploadForm;
use backend\modules\crm\models\project\record\ProjectBrief;
use backend\modules\crm\models\project\record\ProjectBriefConfig;
use yii\filters\VerbFilter;
use Yii;
use yii\web\UploadedFile;

class ProjectBriefController extends BackEndBaseController
{
    public function behaviors()
    {
        return [
            'verbs'=> [
                'class'=>VerbFilter::className(),
                'actions'=>[
                    'add'=>['post'],
                    'update'=>['post'],
                    'show'=>['get', 'AJAX'],
                    'del'=>['get'],
                    'attachment-download'=>['get'],
                    'attachment-delete'=>['get', 'ajax'],
                    'assess'=>['get','ajax'],
                    'assess-succeed'=>['get'],
                    'assess-refused'=>['post']
                ],
            ],
        ];
    }

    public function actionAdd() {
        $formData = Yii::$app->request->post('ProjectBriefForm');
        $formData['file'] = UploadedFile::getInstances(new UploadForm(), 'file');
        $projectBrief = new ProjectBrief();
        if ($projectBrief->insertRecord($formData)) {
            return $this->redirect([
                '/crm/project/edit',
                'uuid'=>$formData['project_uuid'],
                'tab'=>'brief-list'
            ]);
        }
    }

    public function actionShow() {
        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return null;
        }

        $formData = (new ProjectBrief())->getRecord($uuid);
        return CompressHtml::compressHtml($this->renderPartial('/project-brief/form', [
            'formData'=>$formData,
            'edit'=>$this->getParam('edit', false) && in_array($formData['status'], [
                    ProjectBriefConfig::StatusApplying,
                    ProjectBriefConfig::StatusAssessRefused,
            ]),
            'action'=>['/crm/project-brief/update'],
            'show'=>true,
        ]));
    }

    public function actionUpdate() {
        $formData = Yii::$app->request->post('ProjectBriefForm');
        $formData['file'] = UploadedFile::getInstances(new UploadForm(), 'file');
        $brief = new ProjectBrief();
        if ($brief->updateRecord($formData)) {
            return $this->redirect([
                '/crm/project/edit',
                'uuid'=>$formData['project_uuid'],
                'tab'=>'brief-list',
            ]);
        }
    }

    public function actionDel() {
        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return $this->redirect([
                '/crm/project/edit',
                'uuid'=>Yii::$app->request->get('project_uuid'),
                'tab'=>'brief-list'
            ]);
        }

        $brief = new ProjectBrief();
        if ($brief->deleteRecord($uuid)) {
            return $this->redirect([
                '/crm/project/edit',
                'uuid'=>Yii::$app->request->get('project_uuid'),
                'tab'=>'brief-list'
            ]);
        }
    }

    public function actionAssess() {
        if(Yii::$app->request->isAjax) {
            return $this->pagination();
        }
        return $this->render('assess');
    }

    public function pagination() {
        $briefList = (new ProjectBrief())->myAssessList();
        return CompressHtml::compressHtml($this->renderPartial('assess-list', [
            'briefList'=>$briefList,
        ]));
    }

    public function actionAssessListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $brief = new ProjectBrief();
        $brief->clearEmptyField($filter);
        $briefList = $brief->assessListFilter($filter);

        return CompressHtml::compressHtml($this->renderPartial('assess-list',[
            'briefList'=>$briefList,
            'ser_filter'=>serialize($filter),
        ]));
    }

    public function actionAssessSucceed() {
        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return $this->redirect(['assess']);
        }

        $brief = new ProjectBrief();
        if ($brief->updateRecord([
            'uuid'=>$uuid,
            'status'=>ProjectBriefConfig::StatusAssessed,
            'assess_uuid'=>Yii::$app->getUser()->getIdentity()->getId()
        ])) {
            return $this->redirect(['assess']);
        }
    }

    public function actionAssessRefused() {
        $formData = Yii::$app->request->post();

        $brief = new ProjectBrief();
        if ($brief->updateRecord([
            'uuid'=>$formData['uuid'],
            'refuse_reason'=>$formData['refuse_reason'],
            'status'=>ProjectBriefConfig::StatusAssessRefused,
            'assess_uuid'=>Yii::$app->getUser()->getIdentity()->getId()
        ])) {
            return $this->redirect(['assess']);
        }
    }

    public function actionAttachmentDownload() {
        $path = Yii::$app->request->get("path");
        $path = iconv("UTF-8", "GBK", $path);
        if (empty($path)) {
            $this->redirect(['index']);
        }
        $file_name = Yii::$app->request->get('file_name');
        $path = Yii::getAlias("@app") . $path;
        Yii::$app->response->sendFile($path, $file_name);
    }

    public function actionAttachmentDelete() {
        $uuid = Yii::$app->request->get('uuid');
        $path = Yii::$app->request->get('path');
        $brief = new ProjectBrief();
        return $brief->deleteAttachment($uuid, $path);
    }
}