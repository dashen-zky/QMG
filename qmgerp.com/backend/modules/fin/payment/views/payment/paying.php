<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<!-- #modal-without-animation -->
<div class="modal fade scroll paying-modal" style="width: 35%; margin: 80px auto;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">付款</h4>
        </div>
        <div class="modal-body">
            <?php $form = ActiveForm::begin([
                'options'=>[
                    'enctype'=>'multipart/form-data',
                    'class' => 'form-horizontal PayingForm',
                    'data-parsley-validate' => "true",
                ],
                'method' => 'post',
                'action' => $action,
                'fieldConfig' => [
                    'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
                    'labelOptions' => ['class' => 'col-md-3 control-label'],
                ],
            ])?>
            <?= Html::input('hidden', 'PayingForm[uuid]', null, [
                'class'=>'payment_uuid',
            ])?>
            <table class="table">
                <tr>
                    <td>总金额</td>
                    <td>
                        <input class="form-control money_count" type="text" disabled>
                    </td>
                </tr>
                <tr>
                    <td>已付金额</td>
                    <td>
                        <input value="0.00" class="form-control money_paied" type="text" disabled>
                    </td>
                </tr>
                <tr>
                    <td>应付金额</td>
                    <td>
                        <input value="0.00" class="form-control should_paied" type="text" disabled>
                    </td>
                </tr>
                <tr>
                    <td class="col-md-6">支付金额*</td>
                    <td>
                        <?= Html::textInput('PayingForm[paied_money]', null, [
                            'class'=>'form-control payment-money',
                            'data-parsley-required'=>true,
                            'data-parsley-type'=>"number",
                        ])?>
                        <span style="color: red" class="payment-money-error"></span>
                    </td>
                </tr>
                <tr>
                    <td>支付凭证*</td>
                    <td>
                        <?= $form->field(new \backend\models\UploadForm([
                            'fileRules'=>[
                                'extensions'=>'bmp,jpg,jpeg,png,gif',
                            ]
                        ]),'file[]')->fileInput([
                            'multiple' => '',
                            'data-parsley-required'=>true,
                        ])?>
                        <span style="color: red" class="payment-evidence-error"></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4"></td>
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
    $('.PayingForm').on('click', '.submit', function() {
        var form = $(this).parents('form');
        
        if(!validate_upload_file(form)) {
            form.find('.payment-evidence-error').html('小盆友，附件名称只允许数字，字母，下划线组成哦^^^');
            return false;
        }
        
        if(!validate_payment_money(form)) {
            form.find('.payment-money-error').html('小盆友，输入的金额有误哦^^^');
            return false;
        }

        form.submit();
    });
    
    function validate_payment_money(form) {
        var payment_uuid = form.find('.payment_uuid').val();
        payment_uuid = trim(payment_uuid).split(',');
        
        var should_paied = form.find('.should_paied').val();
        var payment_money = form.find('.payment-money').val();
        if(payment_money > should_paied) {
            return false;
        }
        
        if(payment_uuid.length === 1) {
            return true;
        }
        
        if(parseFloat(payment_money) != parseFloat(should_paied)) {
            return false;
        }
        
        return true;
    }
    
    function validate_upload_file(form) {
        var evidence = document.getElementById('uploadform-file');
        var files = evidence.files;
        var error_file_name = false;
        var reg = /^\w+\.\w+$/;
        for( var i = 0; i < files.length; i++) {
            file = files[i];
            if(!reg.test(file.name)) {
                return false;
            }
        }
       
        return true;
    }
})
JS;
$this->registerJs($Js, \yii\web\View::POS_END);
?>