<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-12-21
 * Time: 下午9:38
 */

namespace backend\modules\crm\controllers;


use backend\models\BackEndBaseController;
use yii\filters\VerbFilter;
use Yii;
use yii\web\UploadedFile;
use backend\modules\crm\models\project\record\ProjectMediaBrief;
use backend\models\UploadForm;
use backend\models\CompressHtml;
use backend\modules\crm\models\project\record\ProjectMediaBriefConfig;

class ProjectMediaBriefController extends BackEndBaseController
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
                    'update-in-media-entrance'=>['post'],
                    'show-in-media-entrance'=>['get', 'AJAX'],
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

    public function actionMediaIndex() {
        if(Yii::$app->request->isAjax) {
            return $this->pagination();
        }

        return $this->render('/project-media-brief/media-index');
    }

    public function pagination() {
        $briefList = (new ProjectMediaBrief())->listForMedia();
        return CompressHtml::compressHtml($this->renderPartial('media-list', [
            'briefList'=>$briefList,
        ]));
    }

    public function actionListFilterForMedia() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $brief = new ProjectMediaBrief();
        $brief->clearEmptyField($filter);
        $briefList = $brief->listFilterFormMedia($filter);

        return CompressHtml::compressHtml($this->renderPartial('media-list',[
            'briefList'=>$briefList,
            'ser_filter'=>serialize($filter),
        ]));
    }

    public function actionAdd() {
        $formData = Yii::$app->request->post('ProjectMediaBriefForm');
        $formData['file'] = UploadedFile::getInstances(new UploadForm(), 'file');
        $ProjectMediaBrief = new ProjectMediaBrief();
        if ($ProjectMediaBrief->insertRecord($formData)) {
            return $this->redirect([
                '/crm/project/edit',
                'uuid'=>$formData['project_uuid'],
                'tab'=>'media-brief-list'
            ]);
        }
    }


    public function actionShowInMediaEntrance() {
        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return null;
        }

        $formData = (new ProjectMediaBrief())->getRecord($uuid);
        return CompressHtml::compressHtml($this->renderPartial('/project-media-brief/form', [
            'formData'=>$formData,
            'edit'=>$this->getParam('edit', false),
            'action'=>['/crm/project-media-brief/update-in-media-entrance'],
            'show'=>true,
        ]));
    }

    public function actionUpdateInMediaEntrance() {
        $formData = Yii::$app->request->post('ProjectMediaBriefForm');
        $formData['file'] = UploadedFile::getInstances(new UploadForm(), 'file');
        $brief = new ProjectMediaBrief();
        if ($brief->updateRecord($formData)) {
            return $this->redirect([
                'media-index'
            ]);
        }
    }

    public function actionShow() {
        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return null;
        }

        $formData = (new ProjectMediaBrief())->getRecord($uuid);
        return CompressHtml::compressHtml($this->renderPartial('/project-media-brief/form', [
            'formData'=>$formData,
            'edit'=>$this->getParam('edit', false) && in_array($formData['status'], [
                    ProjectMediaBriefConfig::StatusApplying,
                    ProjectMediaBriefConfig::StatusAssessRefused,
                ]),
            'action'=>['/crm/project-media-brief/update'],
            'show'=>true,
        ]));
    }

    public function actionUpdate() {
        $formData = Yii::$app->request->post('ProjectMediaBriefForm');
        $formData['file'] = UploadedFile::getInstances(new UploadForm(), 'file');
        $brief = new ProjectMediaBrief();
        if ($brief->updateRecord($formData)) {
            return $this->redirect([
                '/crm/project/edit',
                'uuid'=>$formData['project_uuid'],
                'tab'=>'media-brief-list',
            ]);
        }
    }

    public function actionDel() {
        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return $this->redirect([
                '/crm/project/edit',
                'uuid'=>Yii::$app->request->get('project_uuid'),
                'tab'=>'media-brief-list'
            ]);
        }

        $brief = new ProjectMediaBrief();
        if ($brief->deleteRecord($uuid)) {
            return $this->redirect([
                '/crm/project/edit',
                'uuid'=>Yii::$app->request->get('project_uuid'),
                'tab'=>'media-brief-list'
            ]);
        }
    }

    public function actionAssessSucceed() {
        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return $this->redirect(['media-index']);
        }

        $brief = new ProjectMediaBrief();
        if ($brief->updateRecord([
            'uuid'=>$uuid,
            'status'=>ProjectMediaBriefConfig::StatusAssessed,
            'assess_uuid'=>Yii::$app->getUser()->getIdentity()->getId()
        ])) {
            return $this->redirect(['media-index']);
        }
    }

    public function actionAssessRefused() {
        $formData = Yii::$app->request->post();

        $brief = new ProjectMediaBrief();
        if ($brief->updateRecord([
            'uuid'=>$formData['uuid'],
            'refuse_reason'=>$formData['refuse_reason'],
            'status'=>ProjectMediaBriefConfig::StatusAssessRefused,
            'assess_uuid'=>Yii::$app->getUser()->getIdentity()->getId()
        ])) {
            return $this->redirect(['media-index']);
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
        $brief = new ProjectMediaBrief();
        return $brief->deleteAttachment($uuid, $path);
    }
}