<?php
use backend\models\ViewHelper;
use yii\helpers\Html;
use backend\modules\fin\payment\models\PaymentConfig;
$paymentConfig = new PaymentConfig();
?>
<?= Html::beginForm($action, 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
<input value="<?= isset($entrance)?$entrance:null?>" name="ListFilterForm[entrance]" hidden>
    <table class="table ">
        <tbody>
        <tr>
            <td class="col-md-1">款项用途</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[payment_purpose]',
                        isset($formData['payment_purpose'])?$formData['payment_purpose']:null,
                        ViewHelper::appendElementOnDropDownList(isset($formData['type'])?
                            $paymentConfig->getList(
                                $paymentConfig->getAppointed('type_purpose_map', $formData['type'])
                            )
                            :$paymentConfig->getList(
                                $paymentConfig->getAppointed('type_purpose_map', PaymentConfig::PaymentForManage)
                            )),
                        [
                            'class'=>'form-control col-md-12',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'purpose'),
                        ]
                    )?>
                </div>
            </td>
            <td class="col-md-1">状态</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[payment_status]',
                        isset($formData['payment_status'])?$formData['payment_status']:null,
                        ViewHelper::appendElementOnDropDownList($paymentConfig->getList('status')),
                        [
                            'class'=>'form-control department col-md-12',
                        ]
                    ) ?>
                </div>
            </td>
            <td class="col-md-1">发票</td>
            <td class="col-md-3">
                <div class="col-md-12">
                <?= Html::dropDownList(
                    'ListFilterForm[payment_with_stamp]',
                    isset($formData['payment_with_stamp'])?$formData['payment_with_stamp']:null,
                    ViewHelper::appendElementOnDropDownList($paymentConfig->getList('with_stamp')),
                    [
                        'class'=>'form-control department col-md-12',
                    ]
                ) ?>
                </div>
            </td>
        </tr>
        <tr>
<!--            receiver_account_type-->
            <td class="col-md-1">付款方式</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[payment_receiver_account_type]',
                        isset($formData['payment_receiver_account_type'])?$formData['payment_receiver_account_type']:null,
                        ViewHelper::appendElementOnDropDownList($paymentConfig->getList('receiver_account_type')),
                        [
                            'class'=>'form-control department col-md-12',
                        ]
                    ) ?>
                </div>
            </td>
            <td>申请人</td>
            <td>
                <div class="col-md-12">
                    <?= Html::textInput(
                        'ListFilterForm[created_name]',
                        isset($formData['created_name'])?$formData['created_name']:'',
                        [
                            'class'=>'form-control',
                        ]
                    )?>
                </div>
            </td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td>期望日期</td>
            <td>
                <div class="col-md-12">
                    <?= Html::textInput(
                        'ListFilterForm[payment_expect_time_min]',
                        isset($formData['payment_expect_time_min'])?$formData['payment_expect_time_min']:'',
                        [
                            'class' => 'input-section datetimepicker form-control col-md-12',
                        ]
                    )?>
                </div>
            </td>
            <td>------</td>
            <td>
                <div class="col-md-12">
                    <?= Html::textInput(
                        'ListFilterForm[payment_expect_time_max]',
                        isset($formData['payment_expect_time_max'])?$formData['payment_expect_time_max']:'',
                        [
                            'class' => 'input-section datetimepicker form-control col-md-12',
                        ]
                    )?>
                </div>
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