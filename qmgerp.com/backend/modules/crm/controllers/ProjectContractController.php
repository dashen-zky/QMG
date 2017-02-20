<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/29 0029
 * Time: 下午 4:49
 */

namespace backend\modules\crm\controllers;


use backend\models\CompressHtml;
use backend\modules\crm\models\customer\model\CustomerContractForm;
use backend\modules\crm\models\project\model\ProjectContractForm;
use backend\modules\crm\models\project\record\ProjectContractMap;
use yii\web\Controller;
use Yii;
use yii\web\UploadedFile;
use backend\modules\fin\models\contract\ContractBaseRecord;
use backend\modules\crm\models\project\record\Project;
use backend\modules\crm\models\project\model\ProjectForm;

class ProjectContractController extends Controller
{
    public function actionAdd() {
        if(!Yii::$app->request->isPost) {
            return false;
        }

        $model = new ProjectContractForm();
        $formData = Yii::$app->request->post();
        if($model->load($formData) && $model->validate()) {
            $formData['ProjectContractForm']['attachment'] = UploadedFile::getInstances($model, 'attachment');
            $projectContract = new ProjectContractMap();
            if ($projectContract->insertSingleRecord($formData['ProjectContractForm'])) {
                $this->redirect([
                    '/crm/project/edit',
                    'uuid'=>$formData['ProjectContractForm']['project_uuid'],
                    'tab'=>'contract-list'
                ]);
            } else {

            }
        } else {
            // error handler,验证失败
        }
    }
    
    public function actionEdit() {
        if(!Yii::$app->request->isAjax) {
            return ;
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return false;
        }
        $model = new ProjectContractForm();
        $contract = new ProjectContractMap();
        $projectContract = $contract->getRecordByUuid($uuid);
        // 合同模板列表
        $templateList = $contract->templateList();
        return CompressHtml::compressHtml($this->renderPartial('form',[
                'formClass'=>'ProjectContractForm',
                'model'=>$model,
                'projectContract'=>$projectContract,
                'templateList'=>$templateList,
                'show'=>true,
                'back_url'=>Yii::$app->request->get('back_url'),
                'action'=>['/crm/project-contract/update'],
        ]));
    }

    public function actionUpdate() {
        if(!Yii::$app->request->isPost) {
            return ;
        }

        $model = new ProjectContractForm();
        $formData = Yii::$app->request->post();
        $back_url = $formData['ProjectContractForm']['back_url'];
        unset($formData['ProjectContractForm']['back_url']);
        if($model->load($formData) && $model->validate()) {
            $formData['ProjectContractForm']['attachment'] = UploadedFile::getInstances($model, 'attachment');
            $projectContract = new ProjectContractMap();
            if ($projectContract->updateSingleRecord($formData['ProjectContractForm'])) {

                $this->redirect(empty($back_url)?[
                    '/crm/project/edit',
                    'uuid'=>$formData['ProjectContractForm']['project_uuid'],
                    'tab'=>'contract-list'
                ] : $back_url);
            }
        }
    }

    public function actionListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['/crm/project/index','tab'=>'contact-list']);
            }
            $filter = unserialize($ser_filter);
        }
        $projectContract = new ProjectContractMap();
        $projectContract->clearEmptyField($filter);
        $contractList = $projectContract->listFilter($filter);

        $model = new ProjectForm();
        $project = new Project();
        $projectList = $project->myProjectList();
        return $this->render('/project/index',[
            'contractList'=>$contractList,
            'contract_list_ser_filter'=>serialize($filter),
            'tab'=>'contract-list',
            'model'=>$model,
            'projectList'=>$projectList,
        ]);
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
        if(Yii::$app->request->isAjax) {
            $uuid = Yii::$app->request->get('uuid');
            $path = Yii::$app->request->get('path');
            $contract = new ContractBaseRecord();
            return $contract->deleteAttachment($uuid, $path);
        }
    }

    public function actionDel() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return null;
        }

        $object_uuid = Yii::$app->request->get('object_uuid');
        $customerContract = new ProjectContractMap();
        if($customerContract->deleteSingleRecord($uuid, $object_uuid)) {
            $back_url = Yii::$app->request->get('back_url');
            $this->redirect(empty($back_url)?[
                '/crm/project/edit',
                'uuid'=>$object_uuid,
                'tab'=>'contract-list',
            ]:$back_url);
        }
    }
}