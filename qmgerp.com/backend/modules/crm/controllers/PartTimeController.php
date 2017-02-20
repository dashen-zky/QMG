<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/15 0015
 * Time: 下午 7:20
 */

namespace backend\modules\crm\controllers;

use backend\models\interfaces\controller\ControllerCommon;
use backend\modules\crm\models\part_time\model\PartTimeConfig;
use backend\modules\crm\models\part_time\record\PartTime;
use backend\modules\fin\models\account\models\FINAccountForm;
use backend\modules\fin\models\account\record\FINAccount;
use Yii;
use backend\modules\crm\models\part_time\model\PartTimeForm;
use yii\web\UploadedFile;
use backend\modules\crm\models\part_time\record\PartTimeFinAccountMap;
use yii\helpers\Json;
use backend\modules\rbac\model\PermissionManager;

class PartTimeController extends CRMBaseController implements ControllerCommon
{
    public function actionIndex() {
        $model = new PartTimeForm();
        // 生成兼职的编码
        $model->code = $model->generateCode();
        $partTime = new PartTime();
        $partTimeList = $partTime->myPartTimeList('manager');
        return $this->render('index',[
            'partTimeList'=>$partTimeList,
            'model'=>$model,
        ]);
    }

    public function actionRecommend() {
        $model = new PartTimeForm();
        // 生成兼职的编码
        $model->code = $model->generateCode();
        $partTime = new PartTime();
        $partTimeList = $partTime->myPartTimeList('created');
        $tab = $this->getTab(Yii::$app->request->get('tab'), 'add-part-time');
        return $this->render('recommend',[
            'partTimeList'=>$partTimeList,
            'model'=>$model,
            'tab'=>$tab,
        ]);
    }

    public function actionIncrease() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post();
            $backUrl = isset($formData['backUrl'])?Json::decode($formData['backUrl']):['index'];
            $model = new PartTimeForm();
            if($model->load($formData) && $model->validate()) {
                $formData = $formData['PartTimeForm'];
                $partTime = new PartTime();
                $formData['attachment'] = UploadedFile::getInstances($model, 'attachment');
                if($partTime->insertRecord($formData)) {
                    $this->redirect($backUrl);
                }
            } else {
                $model->code = $model->generateCode();
                return $this->render('add-container',[
                    'model'=>$model,
                ]);
            }
        }
    }

    public function actionEdit() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return false;
        }

        $partTime = new PartTime();
        $partTimeRecord = $partTime->getRecordByUuid($uuid);
        $model = new PartTimeForm();
        // 生成收款账户模型
        $finAccountModel = new FINAccountForm();
        // 收款账户列表
        $partTimeFinAccountMap = new PartTimeFinAccountMap();
        $finAccountList = $partTimeFinAccountMap->finAccountList($uuid);
        $tab = $this->getTab(Yii::$app->request->get('tab'),'edit-part-time');
        $error = Yii::$app->request->get('error');
        if(!empty($error)) {
            if($tab === 'edit-part-time') {
                $model->setError(unserialize($error));
            } elseif($tab === 'add-account') {
                $finAccountModel->setError(unserialize($error));
            }
        }
        // 查看是否可以编辑供应商
        // 传入的参数是来判定是不是管理者
        $enableEditPartTime = Yii::$app->authManager->canAccess(PermissionManager::EditSupplierAndPartTime, [
            'manager_uuid'=>$partTimeRecord['manager_uuid'],
        ]);
        return $this->render('edit',[
            'model'=>$model,
            'partTime'=>$partTimeRecord,
            'tab'=>$tab,
            'finAccountModel'=>$finAccountModel,
            'finAccountList' => $finAccountList,
            'enableEditPartTime' => $enableEditPartTime,
        ]);
    }

    public function actionRecommendEdit() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return false;
        }

        $partTime = new PartTime();
        $partTimeRecord = $partTime->getRecordByUuid($uuid);
        $model = new PartTimeForm();

        $tab = $this->getTab(Yii::$app->request->get('tab'),'edit-part-time');
        return $this->render('recommend-edit',[
            'model'=>$model,
            'partTime'=>$partTimeRecord,
            'tab'=>$tab,
        ]);
    }

    public function actionUpdate() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post();
            $backUrl = isset($formData['backUrl'])?Json::decode($formData['backUrl']):['/crm/part-time/index'];
            $model = new PartTimeForm();
            // 将唯一性字段的规则去掉
            $model->ignoreUniqueRules();

            if($model->load($formData) && $model->validate()) {
                $formData = $formData['PartTimeForm'];
                $partTime = new PartTime();
                $formData['attachment'] = UploadedFile::getInstances($model, 'attachment');
                if ($partTime->updateRecord($formData)) {
                    $this->redirect($backUrl);
                } else {

                }
            } else {
                $error = serialize($model->errors);
                return $this->redirect([
                    'edit',
                    'uuid'=>$formData['PartTimeForm']['uuid'],
                    'error'=>$error,
                    'tab'=>'edit-part-time',
                ]);
            }
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
        if(Yii::$app->request->isAjax) {
            $uuid = Yii::$app->request->get('uuid');
            $path = Yii::$app->request->get('path');
            $partTime = new PartTime();
            return $partTime->deleteAttachment($uuid, $path);
        }
    }

    public function actionListFilter()
    {
        $partTime = new PartTime();
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $partTime->clearEmptyField($filter);
        $partTimeList = $partTime->listFilter($filter);
        $model = new PartTimeForm();
        return $this->render('index',[
            'partTimeList'=>$partTimeList,
            'model'=>$model,
            'ser_filter'=>serialize($filter),
        ]);
    }

    public function actionCodeUpdate() {
        return ;
        $partTimeConfig = new PartTimeConfig();
        $config = $partTimeConfig->generateConfig();
        unset($config['code']);
        unset($config['customer_code']);
        $partTimeConfig->updateDateConfigByJsonString(Json::encode($config));
//        var_dump($config);
//        die;
        $partTimeList = PartTime::find()->asArray()->all();
        for($i = 0; $i < count($partTimeList); $i++) {
            $record = PartTime::find()->andWhere(['uuid'=>$partTimeList[$i]['uuid']])->one();
            $config = $partTimeConfig->generateConfig();
            // 获取当前的code
            if(isset($config['code'])) {
                $priCode = date('y',time()) . '0001';
                $configCode = $config['code'];
                $code = $priCode >= $configCode
                    ? $priCode:$configCode;
            } else {
                $code = $config['code'] = date('y',time()) . '0001';
            }
            $record->code = $code;
            var_dump($code);
            $record->update();
            $config['code'] =$code + 1;
            $partTimeConfig->updateDateConfigByJsonString(Json::encode($config));
        }
    }

    public function actionRebuildStatus() {
        return   'sssss';
        $partTimeConfig = new PartTimeConfig();
        $config = $partTimeConfig->generateConfig();
        $status = $config['check_status'];
        $list = PartTime::find()->all();
        foreach($list as $item) {
            if(!isset($status[$item->check_status]) || $status[$item->check_status] == '待审核') {
                $item->check_status = PartTimeConfig::StatusWaitForAssess;
            } elseif($status[$item->check_status] == '审核通过') {
                $item->check_status = PartTimeConfig::StatusWaitForAssess;
            } elseif($status[$item->check_status] == '审核不通过') {
                $item->check_status = PartTimeConfig::StatusAssessFailed;
            }
            $item->update();
        }
        unset($config['check_status']);
        $partTimeConfig->updateDateConfigByJsonString(Json::encode($config));
        return $this->redirect(['index']);
    }
}
