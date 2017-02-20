<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/20 0020
 * Time: 下午 4:31
 */

namespace backend\modules\crm\controllers;

use backend\models\interfaces\controller\Common;
use backend\modules\crm\models\customer\model\ContactForm;
use backend\modules\crm\models\customer\model\CustomerConfig;
use backend\modules\crm\models\customer\model\CustomerContractForm;
use backend\modules\crm\models\customer\model\PrivateCustomerForm;
use backend\modules\crm\models\customer\model\PublicCustomerForm;
use backend\modules\crm\models\customer\record\CustomerContactMap;
use backend\modules\crm\models\customer\record\CustomerContractMap;
use backend\modules\crm\models\customer\record\CustomerStampMap;
use backend\modules\crm\models\customer\record\CustomerTouchRecordMap;
use backend\modules\crm\models\customer\record\PrivateCustomer;
use backend\modules\crm\models\project\model\ProjectForm;
use backend\modules\crm\models\project\record\Project;
use backend\modules\crm\models\stamp\Stamp;
use backend\modules\crm\models\touchrecord\TouchRecord;
use backend\modules\crm\models\touchrecord\TouchRecordForm;
use Yii;
use backend\models\interfaces\controller\ControllerCommon;
use yii\filters\VerbFilter;

class PrivateCustomerController extends CRMBaseController implements ControllerCommon
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'drop' => ['post'],
                ],
            ],
        ]);
    }

    public function actionIndex() {
        $model = new PrivateCustomerForm();
        $model->setConfig((new CustomerConfig())->generateConfig());
        $privateCustomer = new PrivateCustomer();

        $ser_filter = $this->getParam('ser_filter', '');
        if(empty($ser_filter)) {
            $privateCustomerList = $privateCustomer->allList();
        } else {
            $privateCustomerList = $privateCustomer->listFilter(unserialize($ser_filter));
        }
        $touchRecord = new CustomerTouchRecordMap();
        $touch_record_list_ser_filter = $this->getParam('touch_record_list_ser_filter', '');
        if(empty($touch_record_list_ser_filter)) {
            $touchRecordList = $touchRecord->allTouchRecord();
        } else {
            $touchRecordList = $touchRecord->listFilter(unserialize($touch_record_list_ser_filter));
        }
        $contactModel = new ContactForm();
        //获取显示那个tab
        $tab = $this->getTab(Yii::$app->request->get('tab'),'customer-list');
        $error = Yii::$app->request->get('error');
        if(!empty($error)) {
            if($tab === 'add-customer') {
                $model->setError(unserialize($error));
            }
        }


        return $this->render('index',[
            'model'=>$model,
            'privateCustomerList'=>$privateCustomerList,
            'contactModel'=>$contactModel,
            'tab'=>$tab,
            'ser_filter'=>$ser_filter,
            'touchRecordList'=>$touchRecordList,
            'touch_record_list_ser_filter'=>$touch_record_list_ser_filter,
        ]);
    }
    // 获取客户
    public function actionObtain() {
        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            $this->redirect(['index']);
        }
        $customer = new PrivateCustomer();
        if ($customer->obtain($uuid)) {
            $this->redirect(['index']);
        }
    }
    // 放弃客户
    public function actionDrop() {
        $formData = Yii::$app->request->post();
        if (empty($formData)) {
            return $this->redirect(['index']);
        }

        $customer = new PrivateCustomer();
        if($customer->drop($formData)) {
            return $this->redirect(['index']);
        }
    }

    public function actionAdd() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post();
            $model = new PrivateCustomerForm();
            if($model->load($formData) && $model->validate()) {
                $formData = $formData['PrivateCustomerForm'];
                $privateCustomer = new PrivateCustomer();
                if($privateCustomer->insertRecord($formData)) {
                    $this->redirect(['index']);
                } else {

                }
            } else {
                $error = serialize($model->errors);
                $this->redirect([
                    'index',
                    'error'=>$error,
                    'tab'=>'add-customer',
                ]);
            }
        }
    }

    public function actionDel() {
        $uuid = Yii::$app->request->get('uuid');
        if(!empty($uuid)) {
            $customer = new PrivateCustomer();
            if($customer->deleteRecordByUuid($uuid)) {
                return $this->redirect(['index']);
            }
        }
    }

    public function actionEdit() {
        if(($uuid = Yii::$app->request->get('uuid')) == null) {
            $filter = unserialize(Yii::$app->request->get('ser_filter'));
            if(!isset($filter['uuid'])) {
                return $this->redirect(['index']);
            }

            $uuid = $filter['uuid'];
        }


        $privateCustomer = new PrivateCustomer();
        $formData = $privateCustomer->getRecordByUuid($uuid);
        $model = new PrivateCustomerForm();
        // 配置文件
        $config = new CustomerConfig();
        $config->config = $config->generateConfig();
        $model->setConfig($config->config);

        // 联系人Model
        $contactModel = new ContactForm();
        // 跟进记录model
        $touchRecordModel = new TouchRecordForm();
        $touchRecordModel->setConfig($config->config);
        // 联系人列表
        $contactList = $touchRecordModel->handlerContactList($formData['contactList']);
        // 跟进记录列表
        $touchRecordList = (new TouchRecord())->getRecordFromObjectUuid($uuid,'customer');
        // 项目model
        $projectModel = new ProjectForm();
        // 项目列表
        $projectList = (new Project())->getProjectListFromCustomer($uuid);
        // 合同model
        $customerContractModel = new CustomerContractForm();
        // 生成客户合同编码
        $customerContractModel->code = $model->generateContractCode($uuid, $formData['privateCustomer']['code']);
        $customerContractModel->type = PublicCustomerForm::codePrefix;
        // 合同模板列表
        $contract = new CustomerContractMap();
        $templateList = $contract->templateList();
        // 合同列表
        $contractList = $contract->contractListByCustomerUuid($uuid);
        // 开票信息
        $stamp = new Stamp();
        $customerStampMap = new CustomerStampMap();
        $stampList = $customerStampMap->stampList($uuid);
        // 设置显示的tab
        $tab = $this->getTab(Yii::$app->request->get('tab'), 'edit-customer');
        // 检查form的错误信息
        $error = Yii::$app->request->get('error');
        if(!empty($error)) {
            if($tab === 'edit-customer') {
                $model->setError(unserialize($error));
            } elseif($tab === 'add-touch-record') {
                $touchRecordModel->setError(unserialize($error));
            } elseif ($tab === 'stamp') {
                $stamp->setError(unserialize($error));
            }
        }
        return $this->render('edit',[
            'model'=>$model,
            'formData'=>$formData,
            'contactModel'=>$contactModel,
            'touchRecordModel' => $touchRecordModel,
            'contactList'=>$contactList,
            'touchRecordList'=>$touchRecordList,
            'projectModel' => $projectModel,
            'projectList'=>$projectList,
            'customerContractModel'=>$customerContractModel,
            'templateList'=>$templateList,
            'contractList'=>$contractList,
            'tab'=>$tab,
            'stamp'=>$stamp,
            'stampList'=>$stampList,
        ]);
    }

    public function actionUpdate() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post();
            $model = new PrivateCustomerForm();
            if($model->load($formData) && $model->validate()) {
                $formData = $formData['PrivateCustomerForm'];
                $privateCustomer = new PrivateCustomer();
                if ($privateCustomer->updateRecord($formData)) {
                    $this->redirect([
                        'edit',
                        'uuid'=>$formData['uuid'],
                        'tab'=>'edit-customer',
                    ]);
                } else {

                }
            } else {
                $error = serialize($model->errors);
                $this->redirect([
                    'edit',
                    'error'=>$error,
                    'uuid'=>$formData['PrivateCustomerForm']['uuid'],
                    'tab'=>'edit-customer',
                ]);
            }
        }
    }
    // 客户列表过滤器
    public function actionListFilter() {
        $privateCustomer = new PrivateCustomer();
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $privateCustomer->clearEmptyField($filter);
        $privateCustomerList = $privateCustomer->listFilter($filter);
        $model = new PrivateCustomerForm();
        $config = (new CustomerConfig())->generateConfig();
        $model->setConfig($config);

        return $this->render('index',[
            'privateCustomerList'=>$privateCustomerList,
            'model'=>$model,
            'contactModel'=>new ContactForm(),
            'tab'=>'customer-list',
            'ser_filter'=>serialize($filter),
        ]);
    }
    // 客户里面的项目列表的过滤器
    public function actionProjectListFilter() {
        $project = new Project();
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect([
                    'edit',
                    'tab'=>'project-list',
                ]);
            }
            $filter = unserialize($ser_filter);
        }
        $project->clearEmptyField($filter);
        $projectList = $project->listFilter($filter);
        //
        $uuid = $filter['customer_uuid'];
        $privateCustomer = new PrivateCustomer();
        $formData = $privateCustomer->getRecordByUuid($uuid);
        $model = new PrivateCustomerForm();
        // 配置文件
        $config = new CustomerConfig();
        $config->config = $config->generateConfig();
        $model->setConfig($config->config);
        // 生成客户合同编码
        $model->generateCode();
        // 联系人Model
        $contactModel = new ContactForm();
        // 跟进记录model
        $touchRecordModel = new TouchRecordForm();
        $touchRecordModel->setConfig($config->config);
        // 联系人列表
        $contactList = $touchRecordModel->handlerContactList($formData['contactList']);
        // 跟进记录列表
        $touchRecordList = (new TouchRecord())->getRecordFromObjectUuid($uuid,'customer');
        // 项目model
        $projectModel = new ProjectForm();
        // 生成项目编码
        $projectModel->generateProjectCode();
        // 合同model
        $customerContractModel = new CustomerContractForm();
        // 合同模板列表
        $contract = new CustomerContractMap();
        $templateList = $contract->templateList();
        // 合同列表
        $contractList = $contract->contractListByCustomerUuid($uuid);
        // 设置显示的tab
        $tab = 'project-list';
        return $this->render('edit',[
            'model'=>$model,
            'formData'=>$formData,
            'contactModel'=>$contactModel,
            'touchRecordModel' => $touchRecordModel,
            'contactList'=>$contactList,
            'touchRecordList'=>$touchRecordList,
            'projectModel' => $projectModel,
            'projectList'=>$projectList,
            'customerContractModel'=>$customerContractModel,
            'templateList'=>$templateList,
            'contractList'=>$contractList,
            'tab'=>$tab,
            'ser_filter'=>serialize($filter),
        ]);
    }
}