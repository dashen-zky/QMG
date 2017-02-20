<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3 0003
 * Time: 上午 1:40
 */

namespace backend\modules\hr\controllers;

use backend\models\BackEndBaseController;
use backend\models\interfaces\controller\ControllerCommon;
use backend\modules\hr\models\config\EmployeeConfig;
use backend\modules\hr\models\config\EmployeeDismissConfig;
use backend\modules\hr\models\config\EmployeeEntryConfig;
use backend\modules\hr\models\Department;
use backend\modules\hr\models\EmployeeBasicAttachmentInformation;
use backend\modules\hr\models\EmployeeFamily;
use backend\modules\hr\models\HrConfig;
use backend\modules\hr\models\Position;
use Yii;
use backend\modules\hr\models\EmployeeForm;
use yii\helpers\Json;
use backend\modules\hr\models\EmployeeBasicInformation;
use backend\models\CompressHtml;
use yii\helpers\Html;
use backend\models\ViewHelper;
use yii\web\UploadedFile;

class EmployeeController extends BackEndBaseController implements ControllerCommon
{
    const AddEmployeeError = "输入的数据格式有误，，点击返回按钮返回";
    const EditEmployee = "编辑员工信息";
    function actionIndex() {
        $employeeModel = new EmployeeForm();
        $tab = $this->getTab(Yii::$app->request->get('tab'), 'working-list');
        $error = Yii::$app->request->get('error');
        if(!empty($error)) {
            if($tab === 'add-employee') {
                $employeeModel->setError(unserialize($error));
            }
        }
        $data = [
            'model'=>$employeeModel,
            'tab'=>$tab,
        ];
        return $this->render('index', $data);
    }

    public function actionAdd() {
        if (!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post("EmployeeForm");
        $model = new EmployeeForm();
        $formData['attachment'] = UploadedFile::getInstances($model, 'attachment');
        $employee = new EmployeeBasicInformation();
        $message = $employee->insertRecord($formData);
        if(!is_bool($message)) {
            return $this->redirect([
                'index',
                'error'=>$message,
                'tab'=>'add-employee',
            ]);
        }
        if ($message) {
            return $this->redirect(['index']);
        } else {
            return $this->render('/site/error',[
                'errorMessage'=> self::AddEmployeeError,
                'backUrl'=>"/hr/employee/index",
            ]);
        }
    }

    public function actionEdit() {
        if (($uuid = Yii::$app->request->get('uuid')) !== null) {
            $employee = new EmployeeBasicInformation();
            $formData = $employee->getRecordByUuid($uuid);
            $formData['entry_time'] =
                (!isset($formData['entry_time']) || $formData['entry_time'] == 0
                    || $formData['entry_time'] == '0')?
                    '':date("Y-m-d",$formData['entry_time']);
            $formData['become_full_member_time'] =
                (!isset($formData['become_full_member_time'])
                    || $formData['become_full_member_time'] == 0
                    || $formData['become_full_member_time'] == '0')?
                    '':date("Y-m-d",$formData['become_full_member_time']);
            $formData['out_time'] =
                (!isset($formData['out_time']) || $formData['out_time'] == 0
                || $formData['out_time'] == '0')?
                    '':date("Y-m-d",$formData['out_time']);
            $family = new EmployeeFamily();
            $familyList = $family->getFamilyListFromEmUuid($uuid);
            $model = new EmployeeForm();
            return $this->render('edit',[
                'model'=>$model,
                'formData'=>$formData,
                'familyList'=>$familyList,
            ]);
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
            $employee = new EmployeeBasicInformation();
            return $employee->deleteAttachment($uuid, $path);
        }
    }

    public function actionUpdate() {
        $formData = Yii::$app->request->post('EmployeeForm');
        $employee = new EmployeeBasicInformation();
        $model = new EmployeeForm();
        $formData['attachment'] = UploadedFile::getInstances($model, 'attachment');
        $result = $employee->updateRecord($formData);
        if($result || $result === 0) {
            $this->redirect(['index']);
        } else {
            return  $this->render('/site/error',[
                'errorMessage'=>'更新员工信息错误',
                'backUrl'=>'/hr/employee/list',
            ]);
        }
    }

    public function actionPositionList() {
        $position = new Position();
        $positionList = $position->allPositionList();
        $position_uuid = Yii::$app->request->get('position_uuid');
        $checked = [];
        if(!empty($position_uuid)) {
            $checked = explode(',', $position_uuid);
        }
        $positionListHtml = CompressHtml::compressHtml(
            $this->renderPartial(
                '/position/position-select-list',
                [
                    'position'=>$positionList,
                    'checked'=>$checked,
                ]
            )
        );
        return $positionListHtml;
    }

    public function actionPositionListFilter() {
        if(Yii::$app->request->isAjax) {
            $formData = Yii::$app->request->post('ListFilterForm');
            if (empty($formData) || empty($formData['department'])) {
                return [];
            }
            $position = new Position();
            $position->clearEmptyField($formData['department']);
            $positionList['positionList'] = $position->listFilter($formData, false);
            // checked 表示已经被选中的元素
            $checked = explode(',', $formData['position_uuid']);
            $positionListHtml = CompressHtml::compressHtml(
                $this->renderPartial(
                    '/position/position-select-list',
                    [
                        'position' => $positionList,
                        'checked' => $checked,
                    ]
                )
            );
            return $positionListHtml;
        }
    }

    public function actionDismissList() {
        if(!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['index']);
        }

        $record = (new EmployeeBasicAttachmentInformation())->getRecord($uuid);
        $config = new EmployeeDismissConfig();
        return $this->renderPartial('/employee-dismiss/dismiss-list',[
            'dismiss_list'=>empty($record['dismiss_list'])?null:Json::decode($record['dismiss_list']),
            'config'=>$config,
            'uuid'=>$uuid,
        ]);
    }

    // 离职清单跟新
    public function actionDismissListUpdate() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('EmployeeDismissForm');
        $formData['created_uuid'] = Yii::$app->user->getIdentity()->getId();
        $employee = new EmployeeBasicInformation();
        if($employee->dismiss($formData)) {
            return $this->redirect(['index']);
        }
    }

    // 入职清单
    public function actionEntryList() {
        if(!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }

        $uuid = Yii::$app->request->get('uuid');
        if(empty($uuid)) {
            return $this->redirect(['index']);
        }

        $record = (new EmployeeBasicAttachmentInformation())->getRecord($uuid);
        $config = new EmployeeEntryConfig();
        return $this->renderPartial('/employee-entry/entry-list',[
            'entry_list'=>empty($record['entry_list'])?null:Json::decode($record['entry_list']),
            'config'=>$config,
            'uuid'=>$uuid,
        ]);
    }

    // 入职清单跟新
    public function actionEntryListUpdate() {
        if(!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $formData = Yii::$app->request->post('EmployeeEntryListForm');
        $uuid = $formData['uuid'];
        unset($formData['uuid']);
        $formData['created_uuid'] = Yii::$app->user->getIdentity()->getId();
        $employeeAttachment = new EmployeeBasicAttachmentInformation();
        if($employeeAttachment->updateRecord([
            'uuid'=>$uuid,
            'entry_list'=>Json::encode($formData),
        ])) {
            return $this->redirect(['index']);
        }
    }

    // 过滤选择人员表
    public function actionSelectListFilter() {
        if(Yii::$app->request->isAjax) {
            $filter = Yii::$app->request->post('ListFilterForm');
            // 将已经checked的uuid记录下来
            $uuids = $filter['employee_uuid'];
            unset($filter['employee_uuid']);
            $employee = new EmployeeBasicInformation();
            $employee->clearEmptyField($filter);
            // 不需要分页
            $employeeList = $employee->listFilter($filter, false);
            $uuids = explode(',', trim($uuids, ','));
            return CompressHtml::compressHtml($this->renderPartial('@hr/views/employee/employee-select-list-advance.php',[
                'employeeList'=>$employeeList,
                'uuids'=>$uuids,
            ]));
        }
    }

    public function actionWorkingListFilter() {

        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('working_ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $employee = new EmployeeBasicInformation();
        $employee->clearEmptyField($filter);
        $departments = [];
        $employeeList = $employee->listFilter($filter, true, $departments);
        $model = new EmployeeForm();
        $positionList = [];
        if(isset($filter['department_uuid']) && !empty($filter['department_uuid'])) {
            $positionList = (new Position())->positionDropDownList($filter['department_uuid'], $departments);
        }
        $data = [
            'model'=>$model,
            'workingEmployeeList' => $employeeList,
            'working_ser_filter'=>serialize($filter),
            'workingPositionList'=>$positionList,
            'tab'=>'working-list'
        ];
        return $this->render('index', $data);
    }

    public function actionDisabledListFilter() {
        $employee = new EmployeeBasicInformation();
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('disabled_ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $employee->clearEmptyField($filter);
        $departments = [];
        $employeeList = $employee->listFilter($filter, true, $departments);
        $model = new EmployeeForm();
        $positionList = [];
        if(isset($filter['department_uuid']) && !empty($filter['department_uuid'])) {
            $positionList = (new Position())->positionDropDownList($filter['department_uuid'], $departments);
        }

        $data = [
            'model'=>$model,
            'disabledEmployeeList' => $employeeList,
            'disabled_ser_filter'=>serialize($filter),
            'disabledPositionList'=>$positionList,
            'tab'=>'disabled-list'
        ];
        return $this->render('index', $data);
    }

    public function actionWaitingListFilter() {
        $employee = new EmployeeBasicInformation();
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('waiting_ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $employee->clearEmptyField($filter);
        $departments = [];
        $employeeList = $employee->listFilter($filter, true, $departments);
        $model = new EmployeeForm();
        $positionList = [];
        if(isset($filter['department_uuid']) && !empty($filter['department_uuid'])) {
            $positionList = (new Position())->positionDropDownList($filter['department_uuid'], $departments);
        }
        $data = [
            'model'=>$model,
            'waitingEmployeeList' => $employeeList,
            'waiting_ser_filter'=>serialize($filter),
            'waitingPositionList'=>$positionList,
            'tab'=>'waiting-list'
        ];
        return $this->render('index', $data);
    }

    public function actionListFilter() {
        $employee = new EmployeeBasicInformation();
        if(Yii::$app->request->isPost) {
            $filter = Yii::$app->request->post('ListFilterForm');
        } else {
            $ser_filter = Yii::$app->request->get('ser_filter');
            if(empty($ser_filter)) {
                return $this->redirect(['index']);
            }
            $filter = unserialize($ser_filter);
        }
        $employee->clearEmptyField($filter);
        $departments = [];
        $employeeList = $employee->listFilter($filter, true, $departments);
        $model = new EmployeeForm();
        $positionList = [];
        if(isset($filter['department_uuid']) && !empty($filter['department_uuid'])) {
            $positionList = (new Position())->positionDropDownList($filter['department_uuid'], $departments);
        }
        $data = [
            'model'=>$model,
            'employeeList' => $employeeList,
            'familyList'=>'',
            'ser_filter'=>serialize($filter),
            'positionList'=>$positionList,
        ];
        return $this->render('index', $data);
    }

    public function actionUpdatePositionList() {
        if(Yii::$app->request->isAjax) {
            $department_uuid = Yii::$app->request->get('department_uuid');
            $positionList = (new Position())->positionDropDownList($department_uuid);
            return Html::dropDownList(
                'ListFilterForm[position_uuid]',
                null,
                ViewHelper::appendElementOnDropDownList($positionList),
                [
                    'class'=>'form-control',
                ]
            );
        }
    }

    // 更新员工的系统编码
    public function actionUpdateSystemCode() {
        return ;
        $employeeConfig = new EmployeeConfig();
        $config = $employeeConfig->generateConfig();
        if(isset($config['system_code'])) {
            unset($config['system_code']);
        }
        $employeeConfig->updateDateConfigByJsonString(Json::encode($config));

        $employeeList = EmployeeBasicInformation::find()->all();
        $config['system_code'] = '0001';
        foreach($employeeList as $employee) {
            $employee->system_code = $config['system_code'];
            $employee->update();
            $config['system_code'] += 1;
            if($config['system_code'] < 10) {
                $config['system_code'] = '000'.$config['system_code'];
            } else if($config['system_code']  < 100 && $config['system_code'] > 9) {
                $config['system_code'] = '00'.$config['system_code'];
            }else if($config['system_code']  < 1000 && $config['system_code'] > 99) {
                $config['system_code'] = '0'.$config['system_code'];
            }
            var_dump($config['system_code']);
            $employeeConfig->updateDateConfigByJsonString(Json::encode($config));
        }
    }
}