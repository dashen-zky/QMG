<?php
use backend\models\ViewHelper;
use yii\helpers\Html;
use backend\modules\fin\models\contract\ContractConfig;
$config = new ContractConfig();
?>
<?= Html::beginForm(['/crm/project-contract/list-filter'], 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
    <table class="table ">
        <tbody>
        <tr>
            <td class="col-md-1">编号</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::input('text', 'ListFilterForm[code]',
                        isset($formData['code'])?$formData['code']:'',
                        [
                            'class' => 'form-control'
                        ]) ?>
                </div>
            </td>
            <td class="col-md-1">客户简称</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::textInput('ListFilterForm[customer_name]',
                        isset($formData['customer_name'])?$formData['customer_name']:'',
                        [
                            'class' => 'form-control col-md-12',
                            'disabled'=>isset($customer_uuid) && !empty($customer_uuid)?true:false,
                        ]) ?>
                </div>
            </td>
            <td class="col-md-1">项目名称</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::textInput('ListFilterForm[project_name]',
                        isset($formData['project_name'])?$formData['project_name']:'',
                        [
                            'class' => 'form-control col-md-12'
                        ]) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td class="col-md-1">项目编号</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::textInput('ListFilterForm[project_code]',
                        isset($formData['project_code'])?$formData['project_code']:'',
                        [
                            'class' => 'form-control col-md-12'
                        ]) ?>
                </div>
            </td>
            <td class="col-md-1">合同负责人</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::textInput('ListFilterForm[duty_name]',
                        isset($formData['duty_name'])?$formData['duty_name']:'',
                        [
                            'class' => 'form-control col-md-12'
                        ]) ?>
                </div>
            </td>
            <td class="col-md-1">合同状态</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[status]',
                        null,
                        ViewHelper::appendElementOnDropDownList($config->getList('status')),
                        [
                            'class'=>'form-control department col-md-12',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'status'),
                        ]
                    ) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td class="col-md-1">金额</td>
            <td class="col-md-3">
                <div class="col-md-12">
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
                </div>
            </td>
            <td>
                <?= Html::submitButton('搜索', ['class' => 'form-control btn btn-primary submit col-md-12']) ?>
            </td>
            <td colspan="3"></td>
        </tr>
        </tbody>
    </table>
<?= Html::endForm()?>