<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/31 0031
 * Time: 上午 10:56
 */

namespace backend\modules\fin\controllers;


use backend\modules\fin\models\contract\ContractTemplateForm;
use backend\modules\fin\models\contract\ContractTemplateRecord;
use yii\web\Controller;
use Yii;
use yii\web\UploadedFile;

class ContractTemplateController extends Controller
{
    public function actionIndex() {
        $model = new ContractTemplateForm();
        $contractTemplate = new ContractTemplateRecord();
        $contractTemplateList = $contractTemplate->contractTemplateList();
        return $this->render('index',[
            'model'=>$model,
            'contractTemplateList'=>$contractTemplateList,
        ]);
    }

    public function actionAttachmentDownload() {
        $path = Yii::$app->request->get("path");
        if (empty($path)) {
            $this->redirect(['index']);
        }

        $path = Yii::getAlias("@app") . $path;
        Yii::$app->response->sendFile($path);
    }

    public function actionEdit() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            $this->redirect(['index']);
        }

        $contractTemplate = (new ContractTemplateRecord())->getRecordByUuid($uuid);
        $model = new ContractTemplateForm();
        return $this->render('edit',[
            'model'=>$model,
            'contractTemplate'=>$contractTemplate,
        ]);
    }

    public function actionUpdate() {
        $model = new ContractTemplateForm();
        if (Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post();
            if ($model->load($formData) && $model->validate()) {
                $contractTemplate = new ContractTemplateRecord();
                if ($contractTemplate->updateRecord($formData['ContractTemplateForm'])) {
                    $this->redirect(['index']);
                }
            } else {
                return $this->render('index',[
                    'model'=>$model,
                    'validateError'=>true,
                ]);
            }
        }
    }

    public function actionAdd() {
        $model = new ContractTemplateForm();
        if (Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post();
            $attachment = UploadedFile::getInstance($model,'attachment');
            $model->file_name = $attachment->baseName;
            if ($model->load($formData) && $model->validate()) {
                $contractTemplate = new ContractTemplateRecord();
                $formData['ContractTemplateForm']['attachment'] = $attachment;
                if ($contractTemplate->insertRecord($formData['ContractTemplateForm'])) {
                    $this->redirect(['index']);
                }
            } else {
                return $this->render('index',[
                    'model'=>$model,
                    'validateError'=>true,
                ]);
            }
        }
    }
}