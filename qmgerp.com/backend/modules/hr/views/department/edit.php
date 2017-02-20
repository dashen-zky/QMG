<?php use yii\helpers\Json;?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-hr',
    'menu-2-department',
])?>'>
<div class="col-md-12">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#department-tab-edit" data-toggle="tab">编辑部门</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="department-tab-edit">
            <div class="panel-body">
                <div class="panel panel-body department-form">
                <?= $this->render('department-form',[
                    'model'=>$model,
                    'action'=>['/hr/department/update'],
                    'edit'=>true,
                    'formData'=>$formData
                ])?>
                <?= $this->render('/department/department-select-list-panel',[
                    'selectId' => "parentDepartmentSelect",
                ])?>
                </div>
            </div>
        </div>
    </div>
</div>
