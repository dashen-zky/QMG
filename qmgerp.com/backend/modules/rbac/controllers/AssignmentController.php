<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/3 0003
 * Time: 下午 4:22
 */

namespace backend\modules\rbac\controllers;

use backend\modules\rbac\model\Assignment;
use backend\modules\rbac\model\RoleManager;
use Yii;
use backend\modules\hr\models\EmployeeBasicInformation;
use backend\models\CompressHtml;
class AssignmentController extends RBACController
{
    public function actionIndex() {
        return $this->render('index');
    }

    public function actionIndex2() {
        return $this->render('index2');
    }

    public function actionInit() {
        $authManager = Yii::$app->authManager;
        $employeeUuids = EmployeeBasicInformation::find()
            ->select(['uuid'])
            ->asArray()->all();
        foreach($employeeUuids as $employeeUuid) {
            $authManager->revokeAll($employeeUuid['uuid']);
        }
        $this->redirect(['index2']);
    }

    public function actionAssign() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post('AssignForm');
            $assignment = new Assignment();
            if($assignment->updateRecord($formData)) {
                $backUrl = Yii::$app->request->post('backUrl');
                return $this->redirect([$backUrl]);
            }
        }
    }

    public function actionAssignment() {
        $name = Yii::$app->request->get('name');
        $roleManager = New RoleManager();
        $formData = $roleManager->getRecordFromName($name);
        $assignment = new Assignment();
        $employee = $assignment->getAssignmentsByRole($name);
        $formData['role_employee_uuid'] = $employee['uuid'];
        $formData['role_employee_name'] = $employee['name'];
        $backUrl = Yii::$app->request->get('backUrl');
        return $this->render('assign-container',[
            'backUrl'=>$backUrl,
            'formData'=>$formData,
        ]);
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
}