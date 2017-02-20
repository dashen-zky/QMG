<?php
use yii\helpers\Html;
use backend\models\ViewHelper;
use backend\modules\hr\models\EmployeeBasicInformation;
$employee = new EmployeeBasicInformation();
$uuids = $employee->getOrdinateUuids(\backend\modules\rbac\model\RBACManager::Common);
$employeeList = $employee->getEmployeeListByUuidsForDropDown($uuids);
?>

<?= Html::beginForm(['/daily/week-report/list-filter'], 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
    <table class="table">
        <tbody>
        <tr>
            <td class="col-md-1">标题</td>
            <td class="col-md-3">
                <?= Html::input('text', 'ListFilterForm[title]',
                    isset($formData['title'])?$formData['title']:'',
                    [
                        'class' => 'form-control'
                    ]) ?>
            </td>
            <td>创建时间</td>
            <td>
                <span class="col-md-6" style="padding: 0px">
                <?= Html::textInput('ListFilterForm[min_created_time]',
                    isset($formData['min_created_time'])?$formData['min_created_time']:'',
                    [
                        'class' => 'input-section datetimepicker form-control col-md-12',
                        'placeholder'=>'开始日期'
                    ]) ?>
                </span>
                <span class="col-md-6" style="padding: 0px">
                    <?= Html::textInput('ListFilterForm[max_created_time]',
                        isset($formData['max_created_time'])?$formData['max_created_time']:'',
                        [
                            'class' => 'input-section datetimepicker form-control col-md-12',
                            'placeholder'=>'截止日期'
                        ]) ?>
                </span>
            </td>
            <td>创建人</td>
            <td>
                <?= Html::dropDownList('ListFilterForm[created_uuid]',
                    isset($formData['created_uuid'])?$formData['created_uuid']:'',
                    ViewHelper::appendElementOnDropDownList($employeeList),[
                        'class' => 'form-control'
                    ])?>
            </td>
            <td>
                <?= Html::submitButton('搜索', ['class' => 'form-control btn btn-primary submit']) ?>
            </td>
        </tr>

        </tbody>
    </table>
<?= Html::endForm()?>