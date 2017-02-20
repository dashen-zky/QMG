<?php
use yii\helpers\Json;
use backend\modules\hr\models\Department;
use backend\modules\hr\models\DepartmentForm;
$department = new Department();
$model = new DepartmentForm();
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-hr',
    'menu-2-department',
])?>'>
<div class="col-md-12">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'department-1')?'active':''?>"><a href="#default-tab-1" data-toggle="tab">公司</a></li>
        <li class="<?= (isset($tab) && $tab === 'department-2')?'active':''?>"><a href="#default-tab-2" data-toggle="tab">事业部</a></li>
        <li class="<?= (isset($tab) && $tab === 'department-3')?'active':''?>"><a href="#default-tab-3" data-toggle="tab">部门</a></li>
        <li class="<?= (isset($tab) && $tab === 'add-department')?'active':''?>"><a href="#default-tab-add" data-toggle="tab">添加部门</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'department-1')?'active in':''?>" id="default-tab-1">
            <?= $this->render('department-1-list',[
                'department' => $department->getDepartmentListFromLevel(1),
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'department-2')?'active in':''?>" id="default-tab-2">
            <?= $this->render('department-2-list',[
                'department' => $department->getDepartmentListFromLevel(2),
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'department-3')?'active in':''?>" id="default-tab-3">
            <?= $this->render('department-3-list',[
                'department' => $department->getDepartmentListFromLevel(3),
            ])?>
        </div>
        <div class="tab-pane fade" id="default-tab-add">
            <div class="panel-body">
            <?= $this->render('add')?>
            </div>
        </div>
    </div>
</div>
