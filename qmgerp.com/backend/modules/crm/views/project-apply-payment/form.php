<?php
use yii\helpers\Html;
use backend\modules\fin\payment\models\PaymentConfig;
use backend\models\ViewHelper;
use yii\helpers\Url;
use yii\web\View;
use backend\modules\crm\models\project\record\Project;
use backend\modules\crm\models\project\model\ProjectForm;
use yii\helpers\Json;
$paymentConfig = new PaymentConfig();
?>
<?= Html::beginForm($action, 'post', [
    'class' => 'form-horizontal ApplyPaymentForm',
    'data-parsley-validate' => "true",
])?>
<input hidden name="ApplyPaymentForm[uuid]" value="<?= isset($formData['uuid'])?$formData['uuid']:''?>">
    <table class="table">
        <tr>
            <td class="col-md-1">
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
                    isset($formData['type'])?$paymentConfig->getAppointed('type', $formData['type']):$paymentConfig->getAppointed('type', PaymentConfig::PaymentForProjectExecute),
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
                        $paymentConfig->getAppointed('type_purpose_map', PaymentConfig::PaymentForProjectExecute)
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
                    isset($formData['with_stamp'])?$formData['with_stamp']:null,
                    $paymentConfig->getList('with_stamp'),
                    [
                        'class'=>'form-control col-md-12',
                        'disabled'=>isset($show) && $show,
                    ]
                    )?>
            </td>
        </tr>
        <tr>
            <td>预算金额*</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[budget_money]',
                    isset($formData['budget_money'])?$formData['budget_money']:'',
                    [
                        'data-parsley-type'=>"number",
                        'data-parsley-required'=>"true",
                        'disabled'=>isset($show) && $show,
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td>实际金额*</td>
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
        </tr>
        <tr>
            <td>付款人</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[paied_name]',
                    isset($formData['paied_name'])?$formData['paied_name']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
            <td>支付时间</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[paied_time]',
                    (isset($formData['paied_time']) && $formData['paied_time'] !='0')?date("Y-m-d", $formData['paied_time']):'',
                    [
                        'disabled'=>true,
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td>已验票金额</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[checked_stamp_money]',
                    isset($formData['checked_stamp_money'])?$formData['checked_stamp_money']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
            <td>验票时间</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[checked_stamp_time]',
                    (isset($formData['created_time']) && $formData['checked_stamp_time'] !='0')
                        ?date("Y-m-d", $formData['checked_stamp_time']):'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
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
            <td>账号类型</td>
            <td>
                <?= Html::dropDownList(
                    'ApplyPaymentForm[receiver_account_type]',
                    isset($formData['receiver_account_type'])?$formData['receiver_account_type']:null,
                    $paymentConfig->getList('receiver_account_type'),
                    [
                        'class'=>'form-control col-md-12 receiver-account-type',
                        'disabled'=>isset($show) && $show,
                    ]
                )?>
            </td>
            <td>开户行</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[receiver_bank_of_deposit]',
                    isset($formData['receiver_bank_of_deposit'])?$formData['receiver_bank_of_deposit']:'',
                    [
                        'class' => 'form-control col-md-12 receiver-bank-of-deposit',
                        'disabled'=>isset($show) && $show,
                        'placeholder'=>'请输入开户行支行全部信息',
                    ]) ?>
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
            <td>项目*</td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[project_name]',
                    isset($formData['project_name'])?$formData['project_name']:'',
                    [
                        'data-parsley-required'=>"true",
                        'disabled'=>true,
                        'class' => 'form-control col-md-12 project-name',
                    ]) ?>
                <input  value="<?= isset($formData['project_uuid'])?$formData['project_uuid']:''?>" name="ApplyPaymentForm[project_uuid]" type="hidden" class="project-uuid">
            </td>
            <td>
                <?php if(!isset($show) || !$show) :?>
                    <a href="#" class="choose-project" name="<?= Url::to([
                        '/crm/project-apply-payment/project-list'
                    ])?>"><i class="fa fa-2x fa-edit"></i></a>
                <?php endif?>
            </td>
            <td colspan="3"></td>
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
        <tr>
            <td>
                四级审核人
            </td>
            <td>
                <?= Html::textInput('ApplyPaymentForm[fourth_assess_name]',
                    isset($formData['fourth_assess_name'])?$formData['fourth_assess_name']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
            <?php if(isset($show) || isset($edit)) :?>
                <td colspan="4">
                    <?= Html::textInput('ApplyPaymentForm[assessor_remind]',
                        isset($formData['assessor_remind'])?$formData['assessor_remind']:'',
                        [
                            'class' => 'form-control col-md-12',
                            'disabled'=>true,
                        ]) ?>
                </td>
            <?php endif;?>
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
            '/crm/project-apply-payment/save-apply'
        ])?>" value="保存申请" class="save-apply-payment form-control btn-primary">
    </span>
    <span class="col-md-<?= isset($edit) && $edit ? '4':'2'?>">
        <input type="submit" value="提交申请" class="form-control btn-primary">
    </span>
    <span class="col-md-4"></span>
</span>
<?php endif?>
<?= Html::endForm()?>
<?=  $this->render('/project-select/list-panel',[
    'entrance'=>'project-apply-payment',
    'fieldInformation'=>Json::encode([
        0=>['ApplyPaymentForm', 'project-name'],
        1=>['ApplyPaymentForm', 'project-uuid'],
    ]),
])?>
<?php
$Js = <<<Js
$(function() {
// 保存申请
    $('.ApplyPaymentForm').on('click', '.save-apply-payment', function() {
           var form = $(this).parents('form');
           form.attr('action', $(this).attr('name'));
           form.submit();
    });

    $('.ApplyPaymentForm').on('change', '.receiver-account-type', function() {
        var form = $(this).parents('form');
        if($(this).val() != 1) {
            form.find('.receiver-bank-of-deposit').attr("disabled",true);
            return ;
        }
        form.find('.receiver-bank-of-deposit').attr("disabled",false);
    });

    // 选择项目
    $('.ApplyPaymentForm').on('click', '.choose-project', function() {
        var self = $(this);
        var url = self.attr('name');
        $.get(
        url,
        function(data, status) {
            if(status !== 'success') {
                return ;
            }

            var panel = self.parents('.panel-body');
            var modal = panel.find('.project-list');
            modal.find('.modal-body').html(data);
            modal.modal('show');
        });
    });
});
Js;
$this->registerJs($Js, View::POS_END);
?>
