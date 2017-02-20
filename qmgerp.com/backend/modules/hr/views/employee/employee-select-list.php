<?php
use yii\widgets\LinkPager;
use backend\modules\hr\models\EmployeeForm;
use backend\modules\hr\models\EmployeeAccount;
?>
<!-- begin panel -->
<div class="panel-heading">
    <div class="panel-heading-btn">
        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></a>
    </div>
    <h4 class="panel-title">员工列表</h4>
</div>

<div class="panel-body">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>选择</th>
            <th>部门</th>
            <th>名称</th>
            <th>性别</th>
            <th>学位</th>
            <th>职位</th>
            <th>手机</th>
            <th>qq</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $genderList = EmployeeForm::genderList();
        $degreeList = EmployeeForm::educationDegreeList();
        ?>
        <?php foreach ($employeeList['employeeList'] as $item) :?>
            <tr>
                <td>
                    <input type = "<?= $selectClass === 'selectProjectManager'?'radio':'checkbox'?>"
                           name="uuid"
                           <?= in_array($item['uuid'], $uuids)?'checked':''?>
                           value="<?= $item['uuid']?>">
                </td>
                <td><?= $item['department_name']?></td>
                <td class="name">
                    <?php if ($item['status'] == EmployeeAccount::STATUS_LEAVED) :?>
                        <s><?= $item['name']?></s>
                    <?php else :?>
                        <?= $item['name']?>
                    <?php endif;?>
                </td>
                <td><?= $genderList[$item['gender']]?></td>
                <td><?= $degreeList[$item['education_degree']]?></td>
                <td><?= $item['position_name']?></td>
                <td><?= $item['phone_number']?></td>
                <td><?= $item['qq_number']?></td>
            </tr>
        <?php endforeach?>
        </tbody>
    </table>
    <?php if(isset($employeeList['pagination'])) :?>
    <?= LinkPager::widget(['pagination' => $employeeList['pagination']]); ?>
    <?php endif?>
</div>
<a href="#" class="<?= $selectClass?>"><button class="btn btn-primary col-md-3" style="float: right">选择</button></a>
<!-- end panel -->