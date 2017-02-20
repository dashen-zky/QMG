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
            <td class="col-md-1">类型</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[type]',
                        null,
                        ViewHelper::appendElementOnDropDownList($model->config->getList('type')),
                        [
                            'class'=>'form-control department col-md-12',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'type'),
                        ]
                    ) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td class="col-md-1">性质</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[feature]',
                        null,
                        ViewHelper::appendElementOnDropDownList($model->config->getList('feature')),
                        [
                            'class'=>'form-control department col-md-12',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'feature'),
                        ]
                    ) ?>
                </div>
            </td>
            <td class="col-md-1">审核状态</td>
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
            <td class="col-md-1">来源</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[from]',
                        null,
                        ViewHelper::appendElementOnDropDownList($model->config->getList('from')),
                        [
                            'class'=>'form-control department col-md-12',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'from'),
                        ]
                    ) ?>
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