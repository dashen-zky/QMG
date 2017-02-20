<?php
use yii\helpers\Html;
use backend\modules\fin\payment\models\PaymentConfig;
use backend\models\ViewHelper;
use yii\helpers\Url;
use yii\web\View;
$paymentConfig = new PaymentConfig();
?>
<?= Html::beginForm($action, 'post', [
    'class' => 'form-horizontal ApplyPaymentForm',
    'data-parsley-validate' => "true",
])?>
<input hidden name="ApplyPaymentForm[uuid]" value="<?= isset($formData['uuid'])?$formData['uuid']:''?>">
    <table class="table">
        <tr>
            <td>
                申请日期
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[created_time]',
                    (isset($formData['created_time']) && $formData['created_time'] !='0')?date("Y-m-d", $formData['created_time']):'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
            <td>
                 申请人
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[created_name]',
                    isset($formData['created_name'])?$formData['created_name']:Yii::$app->user->getIdentity()->getEmployeeName(),
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
            <td>
                款项类型
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[type_name]',
                    isset($formData['type'])?$paymentConfig->getAppointed('type', $formData['type']):$paymentConfig->getAppointed('type', PaymentConfig::PaymentForManage),
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>款项用途</td>
            <td>
                <?= Html::dropDownList(
                    'ApplyPaymentForm[purpose]',
                    isset($formData['purpose'])?$formData['purpose']:null,
                    isset($formData['type'])?
                        $paymentConfig->getList(
                            $paymentConfig->getAppointed('type_purpose_map', $formData['type'])
                        )
                    :$paymentConfig->getList(
                        $paymentConfig->getAppointed('type_purpose_map', PaymentConfig::PaymentForManage)
                    ),
                    [
                        'class'=>'form-control col-md-12',
                        'disabled'=>isset($show) && $show,
                        'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'purpose'),
                    ]
                    )?>
            </td>
            <td>
                期望付款时间*
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[expect_time]',
                    (isset($formData['expect_time']) && $formData['expect_time'] !='0')?date("Y-m-d", $formData['expect_time']):'',
                    [
                        'data-parsley-required'=>"true",
                        'disabled'=>isset($show) && $show,
                        'class' => 'input-section datetimepicker form-control col-md-12',
                    ]) ?>
            </td>
            <td>发票</td>
            <td>
                <?= Html::dropDownList(
                    'ApplyPaymentForm[with_stamp]',
                    null,
                    $paymentConfig->getList('with_stamp'),
                    [
                        'class'=>'form-control col-md-12',
                        'disabled'=>isset($show) && $show,
                    ]
                    )?>
            </td>
        </tr>
        <tr>
            <td>金额*</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[actual_money]',
                    isset($formData['actual_money'])?$formData['actual_money']:'',
                    [
                        'data-parsley-type'=>"number",
                        'data-parsley-required'=>"true",
                        'disabled'=>isset($show) && $show,
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td>已支付金额</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[paied_money]',
                    isset($formData['paied_money'])?$formData['paied_money']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
            <td>付款人</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[paied_name]',
                    isset($formData['paied_name'])?$formData['paied_name']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>支付时间</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[paied_time]',
                    (isset($formData['paied_time']) && $formData['paied_time'] !='0')?date("Y-m-d", $formData['paied_time']):'',
                    [
                        'data-parsley-required'=>"true",
                        'disabled'=>true,
                        'class' => 'input-section datetimepicker form-control col-md-12',
                    ]) ?>
            </td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td>发票验收状态</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[stamp_status]',
                    isset($formData['stamp_status'])?$paymentConfig->getAppointed('stamp_status', $formData['stamp_status']):null,
                    [
                        'disabled'=>true,
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td>已验收发票金额</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[checked_stamp_money]',
                    isset($formData['checked_stamp_money'])?$formData['checked_stamp_money'] : null,
                    [
                        'disabled'=>true,
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td>发票编号</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[stamp_series_number]',
                    isset($formData['stamp_series_number'])?$formData['stamp_series_number'] : null,
                    [
                        'disabled'=>true,
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>发票验收人</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[checked_stamp_name]',
                    isset($formData['checked_stamp_name'])?$formData['checked_stamp_name']:'',
                    [
                        'disabled'=>true,
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td>发票验收时间</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[checked_stamp_time]',
                    (isset($formData['checked_stamp_time']) && $formData['checked_stamp_time'] !='0')?date("Y-m-d", $formData['checked_stamp_time']):'',
                    [
                        'disabled'=>true,
                        'class' => 'input-section datetimepicker form-control col-md-12',
                    ]) ?>
            </td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td>收款人*</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[receiver]',
                    isset($formData['receiver'])?$formData['receiver']:'',
                    [
                        'data-parsley-required'=>"true",
                        'disabled'=>isset($show) && $show,
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td>联系人*</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[receiver_contact_name]',
                    isset($formData['receiver_contact_name'])?$formData['receiver_contact_name']:'',
                    [
                        'data-parsley-required'=>"true",
                        'class' => 'form-control col-md-12',
                        'disabled'=>isset($show) && $show,
                    ]) ?>
            </td>
            <td>联系电话*</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[receiver_contact_phone]',
                    isset($formData['receiver_contact_phone'])?$formData['receiver_contact_phone']:'',
                    [
                        'data-parsley-required'=>"true",
                        'class' => 'form-control col-md-12',
                        'disabled'=>isset($show) && $show,
                    ]) ?>
            </td>

        </tr>
        <tr>
            <td>开户行</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[receiver_bank_of_deposit]',
                    isset($formData['receiver_bank_of_deposit'])?$formData['receiver_bank_of_deposit']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>isset($show) && $show,
                    ]) ?>
            </td>
            <td>账号类型</td>
            <td>
                <?= Html::dropDownList(
                    'ApplyPaymentForm[receiver_account_type]',
                    isset($formData['receiver_account_type'])?$formData['receiver_account_type']:null,
                    $paymentConfig->getList('receiver_account_type'),
                    [
                        'class'=>'form-control col-md-12',
                        'disabled'=>isset($show) && $show,
                    ]
                )?>
            </td>
            <td>收款账号*</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[receiver_account]',
                    isset($formData['receiver_account'])?$formData['receiver_account']:'',
                    [
                        'data-parsley-required'=>"true",
                        'class' => 'form-control col-md-12',
                        'disabled'=>isset($show) && $show,
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>付款内容</td>
            <td colspan="5">
                <?= Html::textarea('ApplyPaymentForm[description]',
                    isset($formData['description'])?$formData['description']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>isset($show) && $show,
                        'rows'=>3,
                    ]
                )?>
            </td>
        </tr>
        <tr>
            <td>备注</td>
            <td colspan="5">
                <?= Html::textarea('ApplyPaymentForm[remarks]',
                    isset($formData['remarks'])?$formData['remarks']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>isset($show) && $show,
                        'rows'=>3,
                    ]
                )?>
            </td>
        </tr>
        <tr>
            <td>审核不通过原因</td>
            <td colspan="5">
                <?= Html::textarea('ApplyPaymentForm[refuse_reason]',
                    isset($formData['refuse_reason'])?$formData['refuse_reason']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                        'rows'=>3,
                    ]
                )?>
            </td>
        </tr>
        <tr>
            <td>
                一级审核人
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[first_assess_name]',
                    isset($formData['first_assess_name'])?$formData['first_assess_name']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
            <td>
                二级审核人
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[second_assess_name]',
                    isset($formData['second_assess_name'])?$formData['second_assess_name']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
            <td>
                三级审核人
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[third_assess_name]',
                    isset($formData['third_assess_name'])?$formData['third_assess_name']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
        </tr>
        <?php if(isset($show) && $show) :?>
        <tr>
            <td  colspan="6">付款凭证</td>
        </tr>
        <?php endif;?>
    </table>
<?php if(isset($formData['evidence']) && !empty($formData['evidence'])) :?>
    <?php $evidence = unserialize($formData['evidence']);?>
    <?php $i = 0;?>
    <?php foreach ($evidence as $index=>$item) :?>
        <?php if($i % 3 === 0) :?>
            <span class="col-md-12">
        <?php endif;?>
        <span class="col-md-4"><img width="100%" src="<?= Yii::getAlias('@web').'/../'.$item?>"></span>
        <?php if($i % 3 === 2) :?>
            </span>
        <?php endif;?>
        <?php $i++?>
    <?php endforeach;?>
<?php endif;?>
<?php if(!isset($show) || !$show) :?>
    <span class="col-md-12">
    <span class="col-md-4"></span>
    <span class="col-md-2" style="display: <?= isset($edit) && $edit ? 'none':'block'?>;">
        <input type="button" name="<?= Url::to([
            '/daily/apply-payment/save-apply'
        ])?>" value="保存" class="save-apply-payment form-control btn-primary">
    </span>
    <span class="col-md-<?= isset($edit) && $edit ? '4':'2'?>">
        <input type="submit" value="提交申请" class="form-control btn-primary">
    </span>
    <span class="col-md-4"></span>
</span>
<?php endif?>
<?= Html::endForm()?>
<?php
$Js = <<<Js
$(function() {
    $('.ApplyPaymentForm').on('click', '.save-apply-payment', function() {
           var form = $(this).parents('form');
           form.attr('action', $(this).attr('name'));
           form.submit();
    });
});
Js;
$this->registerJs($Js, View::POS_END);
?>
