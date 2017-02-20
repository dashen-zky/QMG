<?php
use backend\modules\hr\models\Department;
?>
<!-- begin panel -->
<div class="panel panel-body">
    <?= $this->render('employee-form',[
        'model'=>$model,
        'action'=>['/hr/employee/add'],
        'edit'=>false,
        'formData'=>[],
        'familyList'=>$familyList,
    ])?>
    <?php
    $department = new Department();
    // level 1表示是公司
    $company = $department->getDepartmentsForDropDownList(1);
    ?>
    <?= $this->render('/position/position-select-list-panel',[
        'add'=>true,
        'filters'=>[
            'department'=>[
                1=>$company,
            ],
        ]
    ])?>
</div>
<!-- end panel -->
