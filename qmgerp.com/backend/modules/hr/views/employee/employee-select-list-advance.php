<?php
use yii\widgets\LinkPager;
use backend\modules\hr\models\EmployeeForm;
use backend\modules\hr\models\EmployeeAccount;
?>
<table class="table table-striped employee-list-table">
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
                <input type = "<?= isset($selectType)?$selectType:'checkbox'?>"
                       class="employee-uuid"
                       name="<?= $item['name']?>"
                       id="<?= $item['uuid']?>"
                    <?= in_array($item['uuid'], $uuids)?'checked':''?>
                       value="<?= $item['uuid']?>">
            </td>
            <td><?= $item['department_name']?></td>
            <td class="employee-name">
                <?php if ($item['status'] == EmployeeAccount::STATUS_LEAVED) :?>
                    <s><?= $item['name']?></s>
                <?php else :?>
                    <?= $item['name']?>
                <?php endif;?>
            </td>
            <td><?= isset($genderList[$item['gender']])?$genderList[$item['gender']]:null?></td>
            <td><?= $degreeList[$item['education_degree']]?></td>
            <td><?= $item['position_name']?></td>
            <td><?= $item['phone_number']?></td>
            <td><?= $item['qq_number']?></td>
        </tr>
    <?php endforeach?>
    </tbody>
</table>