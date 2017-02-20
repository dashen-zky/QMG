<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
    <!-- #modal-without-animation -->
    <div class="modal fade scroll check-stamp-modal" style="width: 35%; margin: 80px auto;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">申请验收发票</h4>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin([
                    'options'=>[
                        'enctype'=>'multipart/form-data',
                        'class' => 'form-horizontal ApplyCheckStampForm',
                        'data-parsley-validate' => "true",
                    ],
                    'method' => 'post',
                    'action' => $action,
                    'fieldConfig' => [
                        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
                        'labelOptions' => ['class' => 'col-md-3 control-label'],
                    ],
                ])?>
                <?= Html::input('hidden', 'ApplyCheckStampForm[uuid]', null, [
                    'class'=>'payment_uuid',
                ])?>
                <table class="table">
                    <tr>
                        <td class="col-md-4">总金额</td>
                        <td class="col-md-8">
                            <input class="form-control money_count" type="text" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td>已验收发票金额</td>
                        <td>
                            <input value="0.00" class="form-control have_checked_stamp_money" type="text" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td>欠验收发票金额</td>
                        <td>
                            <input value="0.00" class="form-control owe_stamp_money" type="text" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td>验收发票金额*</td>
                        <td>
                            <?= Html::textInput('ApplyCheckStampForm[checked_stamp_money]', null, [
                                'class'=>'form-control checked_stamp_money',
                                'data-parsley-required'=>true,
                                'data-parsley-type'=>"number",
                            ])?>
                            <span style="color: red" class="payment-money-error"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>备注</td>
                        <td>
                            <?= Html::textarea('ApplyCheckStampForm[remind_message]',
                                null,
                                [
                                    'class' => 'form-control col-md-12 remind_message',
                                    'rows'=>3,
                                ]
                            )?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                </table>
            <span class="col-md-12">
                <span class="col-md-4"></span>
                <span class="col-md-4">
                    <?= Html::button('提交', [
                        'class'=>'form-control btn-primary submit'
                    ])?>
                </span>
                <span class="col-md-4"></span>
            </span>
                <?php ActiveForm::end()?>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
<?php
$Js = <<<JS
$(function() {
    $('.ApplyCheckStampForm').on('click', '.submit', function() {
        var form = $(this).parents('form');
        
        if(!validate_payment_money(form)) {
            form.find('.payment-money-error').html('小盆友，输入的金额有误哦^^^');
            return false;
        }

        form.submit();
    });
    
    function validate_payment_money(form) {
        var payment_uuid = form.find('.payment_uuid').val();
        payment_uuid = trim(payment_uuid).split(',');
        
        if(payment_uuid.length === 1) {
            return true;
        }
        
        var owe_stamp_money = form.find('.owe_stamp_money').val();
        var checked_stamp_money = form.find('.checked_stamp_money').val();
        
        if(parseFloat(owe_stamp_money) > parseFloat(checked_stamp_money)) {
            return false;
        }
        
        return true;
    }
})
JS;
$this->registerJs($Js, \yii\web\View::POS_END);
?>