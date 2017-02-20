<?php
namespace backend\modules\daily\controllers;

use backend\models\BackEndBaseController;
use backend\models\UploadForm;
use backend\modules\daily\models\regulation\Regulation;
use backend\modules\hr\models\EmployeeBasicInformation;
use backend\modules\rbac\model\RoleManager;
use Yii;
use backend\models\CompressHtml;
use yii\web\UploadedFile;
class RegulationController extends BackEndBaseController
{
    public function actionIndex() {
        $regulation = new Regulation();
        $ser_filter = $this->getParam('ser_filter',null);
        $list = $regulation->myRegulationList(unserialize($ser_filter));
        return $this->render('index',[
            'list'=>$list,
            'ser_filter'=>$ser_filter,
        ]);
    }

    public function actionAdd() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post();
            $regulation = new Regulation();
            $model = New UploadForm();
            $formData['RegulationForm']['content'] = $formData['editorValue'];
            $formData['RegulationForm']['attachment'] = UploadedFile::getInstances($model, 'file');
            if($regulation->insertRecord($formData['RegulationForm'])) {
                $this->redirect(['index']);
            }
        }
    }

    public function actionEdit() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return false;
        }

        $regulation = new Regulation();
        $formData = $regulation->getRecordByUuid($uuid);
        return $this->render('edit',[
            'formData'=>$formData,
        ]);
    }

    public function actionUpdate() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post();
            $regulation = new Regulation();
            $model = New UploadForm();
            $formData['RegulationForm']['content'] = $formData['editorValue'];
            $formData['RegulationForm']['attachment'] = UploadedFile::getInstances($model, 'file');
            if($regulation->updateRecord($formData['RegulationForm'])) {
                $this->redirect(['index']);
            }

        }
    }

    public function actionDisable() {
        if (!Yii::$app->request->isGet) {
            return true;
        }

        $uuid = Yii::$app->request->get('uuid');
        if (empty($uuid)) {
            return true;
        }

        $regulation = new Regulation();
        if ($regulation->updateRecord([
            'uuid'=>$uuid,
            'enable'=>Regulation::Disable,
        ])) {
            return $this->redirect(['index']);
        }
    }

    public function actionDel() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return false;
        }

        $regulation = new Regulation();
        if($regulation->deleteRecord($uuid)) {
            $this->redirect(['index']);
        }
    }

    public function actionEmployeeList() {
        $employeeList = (new EmployeeBasicInformation())->allEmployeeList(false);
        $uuids = Yii::$app->request->get('uuids');
        $uuids = explode(',', trim($uuids, ','));
        return CompressHtml::compressHtml($this->renderPartial('@hr/views/employee/employee-select-list-advance.php',[
            'employeeList'=>$employeeList,
            'uuids'=>$uuids,
        ]));
    }

    public function actionEditorList() {
        $uuids = Yii::$app->authManager->getUserIdsByRoles([
            RoleManager::Administer,
            RoleManager::AdministerManager,
            RoleManager::AdministerDirector,
            RoleManager::ceo,
        ]);
        $employeeList = (new EmployeeBasicInformation())->getEmployeeListByUuids($uuids);
        $uuids = Yii::$app->request->get('uuids');
        $uuids = explode(',', trim($uuids, ','));
        return CompressHtml::compressHtml($this->renderPartial('@hr/views/employee/employee-select-list-advance.php',[
            'employeeList'=>$employeeList,
            'uuids'=>$uuids,
        ]));
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
        $regulation = new Regulation();
        $regulation->clearEmptyField($filter);
        $regulationList = $regulation->listFilter($filter);

        return $this->render('index', [
            'list'=>$regulationList,
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

    public function actionAttachmentDelete() {
        if(Yii::$app->request->isAjax) {
            $uuid = Yii::$app->request->get('uuid');
            $path = Yii::$app->request->get('path');
            $partTime = new Regulation();
            return $partTime->deleteAttachment($uuid, $path);
        }
    }
}