<?php
use backend\modules\hr\models\EmployeeForm;
use backend\models\ViewHelper;
use yii\helpers\Html;
use yii\web\View;
use backend\modules\crm\models\customer\model\CustomerConfig;
$config = new CustomerConfig();
?>
<?= Html::beginForm($action, 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
    <table class="table">
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
            <td class="col-md-1">简称</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::textInput('ListFilterForm[name]',
                        isset($formData['name'])?$formData['name']:'',
                        [
                        'class' => 'form-control col-md-12'
                        ]) ?>
                </div>
            </td>
            <td class="col-md-1">级别</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[level]',
                        null,
                        ViewHelper::appendElementOnDropDownList($config->getList('level')),
                        [
                            'class'=>'form-control col-md-12',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'level'),
                        ]
                    ) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>业务板块</td>
            <td>
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[require]',
                        null,
                        ViewHelper::appendElementOnDropDownList($model->getList('business')),
                        [
                            'class'=>'form-control col-md-12',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'require'),
                        ]
                    ) ?>
                </div>
            </td>
            <td>状态</td>
            <td>
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[status]',
                        null,
                        ViewHelper::appendElementOnDropDownList($config->getList('status')),
                        [
                            'class'=>'form-control col-md-12',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'status'),
                        ]
                    ) ?>
                </div>
            </td>
            <td class="col-md-1">销售</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::textInput('ListFilterForm[sales_name]',
                        isset($formData['sales_name'])?$formData['sales_name']:'',
                        [
                            'class' => 'form-control col-md-12'
                        ]) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td class="col-md-1">类别</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[type]',
                        null,
                        ViewHelper::appendElementOnDropDownList($model->typeList()),
                        [
                            'class'=>'form-control department col-md-12',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'type'),
                        ]
                    ) ?>
                </div>
            </td>
            <td class="col-md-1">最后跟进时间</td>
            <td class="col-md-3">
                <div class="col-md-12">
                <span class="col-md-6" style="padding: 0px">
                <?= Html::textInput('ListFilterForm[min_time]',
                    isset($formData['min_time'])?$formData['min_time']:'',
                    [
                        'class' => 'input-section datetimepicker form-control col-md-12',
                        'placeholder'=>'开始日期'
                    ]) ?>
                </span>
                <span class="col-md-6" style="padding: 0px">
                    <?= Html::textInput('ListFilterForm[max_time]',
                        isset($formData['max_time'])?$formData['max_time']:'',
                        [
                            'class' => 'input-section datetimepicker form-control col-md-12',
                            'placeholder'=>'截止日期'
                        ]) ?>
                </span>
                </div>
            </td>
            <td>推荐级别</td>
            <td>
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[intent_level]',
                        null,
                        ViewHelper::appendElementOnDropDownList($model->intentLevelList()),
                        [
                            'class'=>'form-control',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'intent_level'),
                        ]
                    ) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td class="col-md-1">行业</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[industry]',
                        null,
                        ViewHelper::appendElementOnDropDownList($config->getList('industry')),
                        [
                            'class'=>'form-control department col-md-12',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'industry'),
                        ]
                    ) ?>
                </div>
            </td>
            <td>
                <?= Html::submitButton('搜索', ['class' => 'form-control btn btn-primary submit col-md-12']) ?>
            </td>
            <td colspan="5"></td>
        </tr>
        </tbody>
    </table>
<?= Html::endForm()?>