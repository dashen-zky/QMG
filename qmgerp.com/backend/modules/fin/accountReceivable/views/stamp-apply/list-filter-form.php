<?php
use backend\models\ViewHelper;
use yii\helpers\Html;
use backend\modules\crm\models\project\record\ProjectApplyStamp;
?>
<?= Html::beginForm($action, 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
    <table class="table ">
        <tbody>
        <tr>
            <td class="col-md-1">项目编号</td>
            <td class="col-md-3">
                <?= Html::textInput('ListFilterForm[project_code]',
                    isset($formData['project_code'])?$formData['project_code']:'',
                    [
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td class="col-md-1">项目名称</td>
            <td class="col-md-3">
                <?= Html::textInput('ListFilterForm[project_name]',
                    isset($formData['project_name'])?$formData['project_name']:'',
                    [
                        'class' => 'form-control col-md-12'
                    ]) ?>
            </td>
            <td class="col-md-1">开票状态</td>
            <td class="col-md-3">
                <?= Html::dropDownList(
                    'ListFilterForm[status]',
                    null,
                    ViewHelper::appendElementOnDropDownList(ProjectApplyStamp::$status),
                    [
                        'class'=>'form-control department col-md-12',
                        'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'status'),
                    ]
                ) ?>
            </td>
        </tr>
        <tr>
            <td class="col-md-1">申请人</td>
            <td class="col-md-3">
                <?= Html::textInput('ListFilterForm[created_name]',
                    isset($formData['created_name'])?$formData['created_name']:'',
                    [
                        'class' => 'form-control col-md-12'
                    ]) ?>
            </td>
            <td>申请时间</td>
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
            <td>申请金额</td>
            <td>
                <span class="col-md-6" style="padding: 0px">
                <?= Html::textInput('ListFilterForm[min_money]',
                    isset($formData['min_money'])?$formData['min_money']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'placeholder'=>'最小金额'
                    ]) ?>
                </span>
                <span class="col-md-6" style="padding: 0px">
                    <?= Html::textInput('ListFilterForm[max_money]',
                        isset($formData['max_money'])?$formData['max_money']:'',
                        [
                            'class' => 'form-control col-md-12',
                            'placeholder'=>'最大金额'
                        ]) ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>已开票金额</td>
            <td>
                <span class="col-md-6" style="padding: 0px">
                <?= Html::textInput('ListFilterForm[min_checked_stamp_money]',
                    isset($formData['min_checked_stamp_money'])?$formData['min_checked_stamp_money']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'placeholder'=>'最小金额'
                    ]) ?>
                </span>
                <span class="col-md-6" style="padding: 0px">
                    <?= Html::textInput('ListFilterForm[max_checked_stamp_money]',
                        isset($formData['max_checked_stamp_money'])?$formData['max_checked_stamp_money']:'',
                        [
                            'class' => 'form-control col-md-12',
                            'placeholder'=>'最大金额'
                        ]) ?>
                </span>
            </td>
            <td>编号</td>
            <td>
                <?= Html::input('text', 'ListFilterForm[id]',
                    isset($formData['id'])?$formData['id']:'',
                    [
                        'class' => 'form-control'
                    ]) ?>
            </td>
            <td colspan="2"></td>
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