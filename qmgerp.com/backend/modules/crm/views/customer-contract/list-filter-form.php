<?php
use yii\helpers\Html;
use backend\modules\fin\models\contract\ContractConfig;
use backend\models\ViewHelper;
$config = new ContractConfig();
?>
<?= Html::beginForm(['/crm/customer-contract/list-filter'], 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
    <table class="table">
        <tbody>
        <tr>
            <td>客户</td>
            <td>
                <div class="col-md-12">
                    <?= Html::input('text', 'ListFilterForm[customer_name]',
                        isset($formData['customer_name'])?$formData['customer_name']:'',
                        [
                            'class' => 'form-control'
                        ]) ?>
                </div>
            </td>
            <td class="col-md-1">合同编号</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::textInput('ListFilterForm[code]',
                        isset($formData['code'])?$formData['code']:'',
                        [
                            'class' => 'form-control col-md-12'
                        ]) ?>
                </div>
            </td>
            <td class="col-md-1">签订日期</td>
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
        </tr>
        <tr>
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
                    <?= Html::dropDownList('ListFilterForm[status]',
                        isset($formData['status'])?$formData['status']:null,
                        ViewHelper::appendElementOnDropDownList($config->getList('status')),
                        [
                            'class' => 'form-control col-md-12'
                        ])?>
                </div>
            </td>
            <td class="col-md-1">合同金额</td>
            <td class="col-md-3">
                <div class="col-md-12">
                <span class="col-md-6" style="padding: 0px">
                <?= Html::textInput('ListFilterForm[min_money]',
                    isset($formData['min_money'])?$formData['min_money']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'placeholder'=>'最大金额'
                    ]) ?>
                </span>
                <span class="col-md-6" style="padding: 0px">
                    <?= Html::textInput('ListFilterForm[max_money]',
                        isset($formData['max_money'])?$formData['max_money']:'',
                        [
                            'class' => 'form-control col-md-12',
                            'placeholder'=>'最小金额'
                        ]) ?>
                </span>
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