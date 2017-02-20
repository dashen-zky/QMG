<?php
use backend\modules\hr\models\EmployeeForm;
use backend\models\ViewHelper;
use yii\helpers\Html;
use yii\web\View;
use backend\modules\hr\models\Department;
$departmentList = (new Department())->departmentListForDropDownList();
?>
<?php
$updatePositionList = \yii\helpers\Url::to(['/hr/employee/update-position-list']);
$JS = <<<JS
$(document).ready(function() {
    $('.ListFilterForm .employee-list-filter-table').on('change','.department',function() {
        var url = "$updatePositionList&department_uuid="+$(this).val();
        var form = $(this).parents('form');
        $.get(
            url,
            function(data,status) {
                if('success' == status) {
                    form.find('.employee-list-filter-table .position').html(data);
                }
            });
    });
});
JS;
$this->registerJs($JS, View::POS_END);
?>
<?php
    switch ($entrance) {
        case 'working':
            $action = ['/hr/employee/working-list-filter'];
            break;
        case 'disabled':
            $action = ['/hr/employee/disabled-list-filter'];
            break;
        case 'waiting':
            $action = ['/hr/employee/waiting-list-filter'];
            break;
        default:
            $action = ['/hr/employee/list-filter'];
            break;
    }
?>
<?= Html::beginForm($action, 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
<input hidden value="<?= $entrance?>" name="ListFilterForm[entrance]">
    <table class="table employee-list-filter-table">
        <tbody>
        <tr>
            <td class="col-md-1">姓名</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::input('text', 'ListFilterForm[name]',
                        isset($formData['name'])?$formData['name']:'',
                        ['class' => 'form-control col-md-12']) ?>
                </div>
            </td>
            <td class="col-md-1">部门</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[department_uuid]',
                        null,
                        ViewHelper::appendElementOnDropDownList($departmentList),
                        [
                            'class'=>'form-control department col-md-12',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'department_uuid'),
                        ]
                    ) ?>
                </div>
            </td>
            <td class="col-md-1">职位</td>
            <td class="col-md-3">
                <div class="position col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[position_uuid]',
                        null,
                        ViewHelper::appendElementOnDropDownList($positionList),
                        [
                            'class'=>'form-control',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'position_uuid'),
                        ]
                    ) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>性别</td>
            <td>
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[gender]',
                        null,
                        ViewHelper::appendElementOnDropDownList(EmployeeForm::genderList()),
                        [
                            'class'=>'form-control col-md-12',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'gender'),
                        ]
                    ) ?>
                </div>
            </td>
            <td>
                <?= Html::submitButton('搜索', ['class' => 'form-control btn btn-primary submit']) ?>
            </td>
            <td colspan="3"></td>
        </tr>
        </tbody>
    </table>
<?= Html::endForm()?>