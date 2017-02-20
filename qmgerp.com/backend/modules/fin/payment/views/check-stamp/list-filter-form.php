<?php
use backend\models\ViewHelper;
use yii\helpers\Html;
use backend\modules\fin\payment\models\PaymentConfig;
use backend\modules\fin\payment\models\PaymentList;
$paymentConfig = new PaymentConfig();
?>
<?= Html::beginForm($action, 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
    <input hidden name="ListFilterForm[entrance]" value="<?= PaymentList::IsCheckStampEntrance?>">
    <table class="table ">
        <tbody>
        <tr>
            <td class="col-md-1">编号</td>
            <td class="col-md-3">
                <?= Html::textInput('ListFilterForm[code]',
                    isset($formData['code'])?$formData['code']:null,
                    [
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td class="col-md-1">款项用途</td>
            <td class="col-md-3">
                <?= Html::dropDownList(
                    'ListFilterForm[purpose]',
                    isset($formData['purpose'])?$formData['purpose']:null,
                    ViewHelper::appendElementOnDropDownList(array_merge(
                        $paymentConfig->getList(
                            $paymentConfig->getAppointed('type_purpose_map', PaymentConfig::PaymentForManage)
                        ),
                        $paymentConfig->getList(
                            $paymentConfig->getAppointed('type_purpose_map', PaymentConfig::PaymentForProjectExecute)
                        ),
                        $paymentConfig->getList(
                            $paymentConfig->getAppointed('type_purpose_map', PaymentConfig::PaymentForProjectMedia)
                        )
                    )),
                    [
                        'class'=>'form-control col-md-12',
                        'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'purpose'),
                    ]
                )?>
            </td>
            <td class="col-md-1">状态</td>
            <td class="col-md-3">
                    <?= Html::dropDownList(
                        'ListFilterForm[status]',
                        isset($formData['status'])?$formData['status']:null,
                        ViewHelper::appendElementOnDropDownList($paymentConfig->getList('status')),
                        [
                            'class'=>'form-control department col-md-12',
                        ]
                    ) ?>
            </td>
        </tr>
        <tr>
            <td class="col-md-1">验票状态</td>
            <td class="col-md-3">
                <?= Html::dropDownList(
                    'ListFilterForm[stamp_status]',
                    isset($formData['stamp_status'])?$formData['stamp_status']:null,
                    ViewHelper::appendElementOnDropDownList($paymentConfig->getList('stamp_status')),
                    [
                        'class'=>'form-control department col-md-12',
                    ]
                ) ?>
            </td>
            <td>项目编号</td>
            <td>
                <?= Html::textInput('ListFilterForm[project_code]',
                    isset($formData['project_code'])?$formData['project_code']:null,
                    [
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td>项目名称</td>
            <td>
                <?= Html::textInput('ListFilterForm[project_name]',
                    isset($formData['project_name'])?$formData['project_name']:null,
                    [
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>申请人</td>
            <td>
                <?= Html::textInput(
                    'ListFilterForm[created_name]',
                    isset($formData['created_name'])?$formData['created_name']:'',
                    [
                        'class'=>'form-control',
                    ]
                )?>
            </td>
            <td>供应商/兼职编号</td>
            <td>
                <?= Html::textInput('ListFilterForm[supplier_code]',
                    isset($formData['supplier_code'])?$formData['supplier_code']:null,
                    [
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td>供应商/兼职名称</td>
            <td>
                <?= Html::textInput('ListFilterForm[supplier_name]',
                    isset($formData['supplier_name'])?$formData['supplier_name']:null,
                    [
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>金额</td>
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
            <td>验收金额</td>
            <td>
                <span class="col-md-6" style="padding: 0px">
                    <?= Html::textInput('ListFilterForm[min_checked_stamp_money]',
                        isset($formData['min_check_stamp_money'])?$formData['min_check_stamp_money']:'',
                        [
                            'class' => 'form-control col-md-12',
                            'placeholder'=>'最小金额'
                        ]) ?>
                </span>
                <span class="col-md-6" style="padding: 0px">
                    <?= Html::textInput('ListFilterForm[max_checked_stamp_money]',
                        isset($formData['max_check_stamp_money'])?$formData['max_check_stamp_money']:'',
                        [
                            'class' => 'form-control col-md-12',
                            'placeholder'=>'最大金额'
                        ]) ?>
                </span>
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