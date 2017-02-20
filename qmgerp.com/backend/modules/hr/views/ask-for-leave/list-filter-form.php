<?php
use yii\bootstrap\Html;
use backend\models\ViewHelper;
use backend\modules\hr\models\config\EmployeeBasicConfig;
$config = new EmployeeBasicConfig();
?>
<?= Html::beginForm($action, 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
    <table class="table">
        <tbody>
        <tr>
            <td class="col-md-1">姓名</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::input('text', 'ListFilterForm[applied_name]',
                        isset($formData['applied_name'])?$formData['applied_name']:'',
                        ['class' => 'form-control col-md-12']) ?>
                </div>
            </td>
            <td class="col-md-1">类别</td>
            <td class="col-md-3">
                <div class="position col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[type]',
                        null,
                        ViewHelper::appendElementOnDropDownList($config->getList('ask_leave_type')),
                        [
                            'class'=>'form-control',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'type'),
                        ]
                    ) ?>
                </div>
            </td>
            <td class="col-md-1">部门</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::input('text', 'ListFilterForm[department]',
                        isset($formData['department'])?$formData['department']:'',
                        ['class' => 'form-control col-md-12']) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td class="col-md-1">状态</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[status]',
                        null,
                        ViewHelper::appendElementOnDropDownList($config->getList('ask_for_leave_status')),
                        [
                            'class'=>'form-control',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'status'),
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