<?php
use backend\modules\hr\controllers\EmployeeController;
use backend\modules\hr\models\Department;
?>
<?php use yii\helpers\Json;?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-hr',
    'menu-2-employee',
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#default-tab-1" data-toggle="tab">基本信息</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="default-tab-1">
        <div class="panel-body">
            <?= $this->render('employee-form',[
                'model'=>$model,
                'formData' => $formData,
                'action'=>['/hr/employee/update'],
                'edit'=>true,
                'add'=>false,
                'familyList'=>$familyList,
            ])?>
            <?php
            $department = new Department();
            // level 1表示是公司
            $company = $department->getDepartmentsForDropDownList(1);
            ?>
            <?= $this->render('/position/position-select-list-panel',[
                'edit'=>true,
                'filters'=>[
                    'department'=>[
                        1=>$company,
                    ],
                    'position_uuid'=>$formData['position_uuid'],
                    'position_name'=>$formData['position_name'],
                ]
            ])?>
        </div>
    </div>
</div>
