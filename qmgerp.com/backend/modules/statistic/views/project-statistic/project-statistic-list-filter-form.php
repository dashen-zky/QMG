<?php
use yii\helpers\Html;
use backend\modules\hr\models\Department;
use backend\models\ViewHelper;
$departmentList = (new Department())->departmentListForDropDownList();
?>
<?= Html::beginForm(['/statistic/project-statistic/project-statistic-list-filter'], 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
<table class="table">
    <tbody>
    <tr>
        <td class="col-md-1">姓名</td>
        <td class="col-md-3">
            <div class="col-md-12">
            <?= Html::input('text', 'ListFilterForm[manager_name]',
                isset($formData['manager_name'])?$formData['manager_name']:'',
                [
                    'class' => 'form-control'
                ]) ?>
            </div>
        </td>
        <td class="col-md-1">部门</td>
        <td class="col-md-3">
            <div class="col-md-12">
            <?= Html::dropDownList('ListFilterForm[department_uuid]',
                isset($formData['department_uuid'])?$formData['department_uuid']:'',
                ViewHelper::appendElementOnDropDownList($departmentList),
                [
                    'class' => 'form-control'
                ])?>
            </div>
        </td>
        <td class="col-md-1">
            <?= Html::submitButton('搜索', ['class' => 'form-control btn btn-primary submit col-md-12']) ?>
        </td>
        <td class="col-md-3"></td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>
    </tbody>
</table>
<?= Html::endForm()?>