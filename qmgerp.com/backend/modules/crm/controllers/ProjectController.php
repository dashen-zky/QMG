<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/25 0025
 * Time: 下午 12:32
 */

namespace backend\modules\crm\controllers;


use backend\models\BackEndBaseController;
use backend\models\CompressHtml;
use backend\models\helper\file\UploadFileHelper;
use backend\models\interfaces\controller\ControllerCommon;
use backend\modules\crm\models\customer\model\CustomerConfig;
use backend\modules\crm\models\customer\record\Contact;
use backend\modules\crm\models\project\model\ProjectConfig;
use backend\modules\crm\models\project\model\ProjectContractForm;
use backend\modules\crm\models\project\model\ProjectForm;
use backend\modules\crm\models\project\record\Project;
use backend\modules\crm\models\project\record\ProjectContactMap;
use backend\modules\crm\models\project\record\ProjectContractMap;
use backend\modules\crm\models\touchrecord\TouchRecordForm;
use backend\modules\hr\models\EmployeeBasicInformation;
use backend\modules\rbac\model\RoleManager;
use yii\helpers\Json;
use backend\modules\crm\models\touchrecord\TouchRecord;
use Yii;
use backend\modules\fin\models\contract\ContractBaseRecord;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;

class ProjectController extends BackEndBaseController implements ControllerCommon
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'apply-active' => ['get'],
                    'apply-done' => ['get'],
                    'apply-active-validate'=>['ajax','get'],
                    'apply-done-validate'=>['ajax','get'],
                    'active-assess-passed'=>['get'],
                    'active-assess-refused'=>['post'],
                    'failed'=>['post'],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $model = new ProjectForm();
        $project = new Project();

        $ser_filter = $this->getParam('ser_filter', '');
        if(empty($ser_filter)) {
            $projectList = $project->myProjectList();
        } else {
            $projectList = $project->listFilter(unserialize($ser_filter));
        }

        return $this->render('index',[
            'model'=>$model,
            'projectList'=>$projectList,
            'tab'=>$this->getTab(Yii::$app->request->get('tab'), 'list'),
            'ser_filter'=>$ser_filter,
        ]);
    }

    public function actionContactList() {
        $customer_uuid = Yii::$app->request->get('customer_uuid');
        if(empty($customer_uuid)) {
            return '';
        }
        $uuids = Yii::$app->request->get('uuids');
        $uuids = explode(',', trim($uuids, ','));
        $contactList = (new Contact())->getContactListByObjectUuid($customer_uuid,'project');
        return CompressHtml::compressHtml($this->renderPartial('/contact/contact-select-list-panel',[
            'contactList'=>$contactList,
            'selectClass'=>Yii::$app->request->get('selectClass'),
            'uuids'=>$uuids,
        ]));
    }

    public function actionDel() {
        $uuid = Yii::$app->request->get('uuid');
        if(!empty($uuid)) {
            $project = new Project();
            if($project->deleteRecordByUuid($uuid)) {
                return $this->redirect(['index']);
            }
        }
    }

    public function actionEmployeeList() {
        $uuids = Yii::$app->authManager->getUserIdsByRoles([
            RoleManager::Project,
            RoleManager::ProjectManager,
            RoleManager::ProjectDirector,
        ]);
        $employeeList = (new EmployeeBasicInformation())->getEmployeeListByUuids($uuids);
        $uuids = Yii::$app->request->get('uuids');
        $uuids = explode(',', trim($uuids, ','));
        return CompressHtml::compressHtml($this->renderPartial('@hr/views/employee/employee-select-list.php',[
            'employeeList'=>$employeeList,
            'selectClass'=>Yii::$app->request->get('selectClass'),
            'uuids'=>$uuids,
        ]));
    }

    public function actionAdd() {
        $formData = Yii::$app->request->post('ProjectForm');
        if(empty($formData)) {
            $this->redirect(['index']);
        }

        $model = new ProjectForm();
        $formData['_active_attachment'] = UploadedFile::getInstances($model, '_active_attachment');
        $formData['_done_attachment'] = UploadedFile::getInstances($model, '_done_attachment');
        $formData['_budget_attachment'] = UploadedFile::getInstances($model, '_budget_attachment');
        $project = new Project();
        if ($project->insertRecord($formData)) {
            $this->redirect([
                '/crm/private-customer/edit',
                'uuid'=>$formData['customer_uuid'],
                'tab'=>'project-list'
            ]);
        }
    }

    public function actionEdit() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return true;
        }

        $formData = (new Project())->getRecordByUuid($uuid);
        $model = new ProjectForm();
        $touchRecordModel = new TouchRecordForm();
        $touchRecordModel->setConfig(((new CustomerConfig())->generateConfig()));
        // 合同表单模型
        $contractForm = new ProjectContractForm();
        $contractForm->code = $model->generateContractCode($uuid, $formData['code']);
        // 设置显示tab
        $tab = Yii::$app->request->get('tab');
        if(empty($tab)) {
            $tab = 'edit-project';
        }
        // 得到错误信息
        $error = Yii::$app->request->get('error');
        if(!empty($error)) {
            if ($tab === 'add-touch-record') {
                $touchRecordModel->setError(unserialize($error));
            }
        }
        return $this->render('edit',[
            'formData'=>$formData,
            'model'=>$model,
            'touchRecordModel'=>$touchRecordModel,
            'contractForm'=>$contractForm,
            'tab'=>$tab,
        ]);
    }

    public function actionUpdate() {
        $formData = Yii::$app->request->post();
        if(empty($formData)) {
            return true;
        }
        $model = new ProjectForm();
        $formData['ProjectForm']['_active_attachment'] = UploadedFile::getInstances($model, '_active_attachment');
        $formData['ProjectForm']['_done_attachment'] = UploadedFile::getInstances($model, '_done_attachment');
        $formData['ProjectForm']['_budget_attachment'] = UploadedFile::getInstances($model, '_budget_attachment');
        if(!$model->load($formData) || !$model->validate()) {
            $formData = (new Project())->getRecordByUuid($formData['ProjectForm']['uuid']);
            $touchRecordModel = new TouchRecordForm();
            $touchRecordModel->setConfig(((new CustomerConfig())->generateConfig()));
            // 合同表单模型
            $contractForm = new ProjectContractForm();
            $contractForm->code = $model->generateContractCode($formData['uuid'], $formData['code']);
            return $this->render('edit',[
                'formData'=>$formData,
                'model'=>$model,
                'touchRecordModel'=>$touchRecordModel,
                'contractForm'=>$contractForm,
                'tab'=>'edit-project',
            ]);
        }

        $project = new Project();
        $formData = $formData['ProjectForm'];
        if($project->updateRecord($formData)) {
            $this->redirect([
                '/crm/project/edit',
                'uuid'=>$formData['uuid'],
            ]);
        }
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
        $model = new projectForm();

        return $this->render('index',[
            'model'=>$model,
            'projectList'=>$projectList,
            'ser_filter'=>serialize($filter),
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

    public function actionActiveAssess() {
        return $this->render('/project-active-assess/index');
    }

    public function actionDoneAssess() {
        return $this->render('/project-done-assess/index');
    }

    public function actionActiveAssessListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $project = new Project();
        $project->clearEmptyField($filter);
        $projectList = $project->activeAssessListFilter($filter);

        return $this->render('/project-active-assess/index',[
            'projectList'=>$projectList,
            'ser_filter'=>serialize($filter),
        ]);
    }

    public function actionDoneAssessListFilter() {
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $project = new Project();
        $project->clearEmptyField($filter);
        $projectList = $project->doneAssessListFilter($filter);

        return $this->render('/project-done-assess/index',[
            'projectList'=>$projectList,
            'ser_filter'=>serialize($filter),
        ]);
    }

    // 申请立项验证
    public function actionApplyActiveValidate() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return -1;
        }

        $project = new Project();
        return $project->applyActiveValidate($uuid);
    }

    // 立项申请
    public function actionApplyActive() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
           return $this->redirect(['/crm/project/index']);
        }

        $project = new Project();
        if ($project->applyActive($uuid)) {
            return $this->redirect(['/crm/project/index']);
        }
    }

    // 结案申请验证
    public function actionApplyDoneValidate() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return -1;
        }

        $project = new Project();
        return $project->applyDoneValidate($uuid);
    }

    // 结案申请
    public function actionApplyDone() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['index']);
        }

        $project = new Project();
        $project->applyDone($uuid);
        return $this->redirect(['index']);
    }

    // 立项审核通过
    public function actionActiveAssessPassed() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['active-assess']);
        }

        $project = new Project();
        if ($project->activeAssessPassed($uuid)) {
            return $this->redirect(['active-assess']);
        }
    }

    // 立项审核不通过
    public function actionActiveAssessRefused() {
        $formData = Yii::$app->request->post();
        if(empty($formData)) {
            return $this->redirect(['active-assess']);
        }

        $project = new Project();
        if ($project->activeAssessRefused($formData)) {
            return $this->redirect(['active-assess']);
        }
    }

    // 结案审核通过
    public function actionDoneAssessPassed() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['done-assess']);
        }

        $project = new Project();
        if ($project->doneAssessPassed($uuid)) {
            return $this->redirect(['done-assess']);
        }
    }

    // 结案审核不通过
    public function actionDoneAssessRefused() {
        $formData = Yii::$app->request->post();
        if(empty($formData)) {
            return $this->redirect(['done-assess']);
        }

        $project = new Project();
        if ($project->doneAssessRefused($formData)) {
            return $this->redirect(['done-assess']);
        }
    }

    public function actionActiveAttachmentDelete() {
        if(Yii::$app->request->isAjax) {
            $uuid = Yii::$app->request->get('uuid');
            $path = Yii::$app->request->get('path');
            $project = new Project();
            return $project->activeAttachmentDelete($uuid, $path);
        }
    }

    public function actionDoneAttachmentDelete() {
        if(Yii::$app->request->isAjax) {
            $uuid = Yii::$app->request->get('uuid');
            $path = Yii::$app->request->get('path');
            $project = new Project();
            return $project->doneAttachmentDelete($uuid, $path);
        }
    }

    public function actionBudgetAttachmentDelete() {
        if(Yii::$app->request->isAjax) {
            $uuid = Yii::$app->request->get('uuid');
            $path = Yii::$app->request->get('path');
            $project = new Project();
            return $project->budgetAttachmentDelete($uuid, $path);
        }
    }

    public function actionFailed() {
        $formData = Yii::$app->request->post();
        if (empty($formData)) {
            return $this->redirect(['index']);
        }

        $project = new Project();
        if ($project->updateRecord([
            'uuid'=>$formData['uuid'],
            'status'=>ProjectConfig::StatusFailed,
            'failed_reason'=>$formData['failed_reason'],
        ])) {
            return $this->redirect(['index']);
        }
    }
}