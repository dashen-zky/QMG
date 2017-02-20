<?php
use backend\modules\hr\models\EmployeeForm;
use backend\models\ViewHelper;
use yii\helpers\Html;
use yii\web\View;
?>
<?= Html::beginForm($action, 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
    <table class="table ">
        <tbody>
        <tr>
            <td class="col-md-1">名称</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::textInput('ListFilterForm[name]',
                        isset($formData['name'])?$formData['name']:'',
                        [
                        'class' => 'form-control col-md-12'
                        ]) ?>
                </div>
            </td>
            <td class="col-md-1">职能</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[position]',
                        null,
                        ViewHelper::appendElementOnDropDownList($model->config->getList('position')),
                        [
                            'class'=>'form-control department col-md-12',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'position'),
                        ]
                    ) ?>
                </div>
            </td>
            <td class="col-md-1">性别</td>
            <td class="col-md-3">
                <div class="col-md-12">
                <?= Html::dropDownList(
                    'ListFilterForm[gender]',
                    null,
                    ViewHelper::appendElementOnDropDownList(\backend\models\BaseForm::genderList()),
                    [
                        'class'=>'form-control department col-md-12',
                        'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'gender'),
                    ]
                ) ?>
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
                        ViewHelper::appendElementOnDropDownList($model->config->getList('status')),
                        [
                            'class'=>'form-control department col-md-12',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'status'),
                        ]
                    ) ?>
                </div>
            </td>
            <td class="col-md-1">审核状态</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[check_status]',
                        null,
                        ViewHelper::appendElementOnDropDownList($model->config->getList('check_status')),
                        [
                            'class'=>'form-control department col-md-12',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'check_status'),
                        ]
                    ) ?>
                </div>
            </td>
            <td>编号</td>
            <td>
                <div class="col-md-12">
                    <?= Html::input('text', 'ListFilterForm[code]',
                        isset($formData['code'])?$formData['code']:'',
                        [
                            'class' => 'form-control'
                        ]) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?= Html::submitButton('搜索', ['class' => 'form-control btn btn-primary submit col-md-12']) ?>
            </td>
            <td colspan="5"></td>
        </tr>
        </tbody>
    </table>
<?= Html::endForm()?>