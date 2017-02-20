<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\fin\stamp\models\StampConfig;
$config = new StampConfig();
?>
<!-- #modal-without-animation -->
<div class="modal fade scroll billing-modal">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">开票</h4>
        </div>
        <div class="modal-body">
            <?= Html::beginForm(['/accountReceivable/stamp-apply/billing'], 'post', [
                'class' => 'form-horizontal BillingForm',
                'data-parsley-validate' => "true",
            ])?>
            <?= Html::input('hidden', 'BillingForm[project_apply_stamp_uuid]', null, [
                'class'=>'project-apply-stamp-uuid'
            ])?>
            <input hidden name="BillingForm[stamp_uuid]" class="stamp-uuid">
            <table class="table">
                <tr>
                    <td class="col-md-1">申请开票金额</td>
                    <td class="col-md-3">
                        <input data-parsley-required="true"
                               class="form-control project-apply-stamp-money"
                               type="text" disabled>
                    </td>
                    <td class="col-md-1">已开票金额</td>
                    <td class="col-md-3">
                        <input data-parsley-required="true"
                               class="form-control checked-stamp-money"
                               type="text" disabled>
                    </td>
                    <td class="col-md-1">待开票金额</td>
                    <td class="col-md-3">
                        <input data-parsley-required="true"
                               class="form-control wait-checking-stamp-money"
                               type="text" disabled>
                    </td>
                </tr>
                <tr>
                    <td>发票编号*</td>
                    <td>
                        <div>
                            <input
                                class="form-control stamp-series-number"
                                type="text" name="BillingForm[stamp_series_number]"
                                data-parsley-required="true"
                                validate-url = "<?= Url::to([
                                    '/accountReceivable/stamp-apply/validate-stamp-series-number'
                                ])?>"
                            >
                        </div>
                        <span style="color: red" class="stamp-series-number-error"></span>
                    </td>
                    <td>可匹配金额</td>
                    <td>
                        <input data-parsley-required="true"
                               class="form-control rest-money"
                               type="text" disabled>
                    </td>
                    <td>金额*</td>
                    <td>
                        <div>
                            <input class="form-control money"
                                   type="text"
                                   name="BillingForm[money]"
                                   data-parsley-required="true">
                        </div>
                        <span style="color: red" class="money-error"></span>
                    </td>
                </tr>
                <tr>
                    <td>发票类型</td>
                    <td>
                        <?= Html::textInput('BillingForm[type]',
                            isset($formData['type'])?
                            $config->getAppointed('service_type',$formData['type']):null, [
                                'class'=>'type form-control',
                                'disabled'=>true,
                            ])?>
                    </td>
                    <td>发票性质</td>
                    <td>
                        <?= Html::textInput('BillingForm[feature]',
                            isset($formData['feature'])?
                                $config->getAppointed('feature',$formData['feature']):null, [
                                'class'=>'feature form-control',
                                'disabled'=>true,
                            ])?>
                    </td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>备注</td>
                    <td colspan="5">
                        <?= Html::textInput('BillingForm[remarks]',
                            isset($formData['remarks'])?$formData['remarks']:null,[
                                'class'=>'form-control remarks',
                                'disabled'=>true,
                        ])?>
                    </td>
                </tr>
                <tr>
                    <td>开票信息</td>
                    <td colspan="5"><?= Html::textarea('stamp-message', '',[
                            'class'=>"form-control stamp-message",
                            'disabled'=>true,
                            'rows'=>2,
                        ])?></td>
                </tr>
                <tr>
                    <td colspan="6"></td>
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
            <?= Html::endForm()?>
        </div>
        <div class="modal-footer">
        </div>
    </div>
</div>
<?php
$Js = <<<JS
$(function() {
    // 失去焦点获取，可以匹配的金额
    $('.billing-modal').on('blur', '.stamp-series-number',function() {
        var self = $(this);
        var stamp_series_number = $.trim(self.val());
        if(stamp_series_number === '') {
            return false;
        }
        
        var form = self.parents('form');
        if(!validateSeriesNumber(stamp_series_number)) {
            form.find('.rest-money').val('');
            form.find('.stamp-uuid').val('');
            form.find('.stamp-series-number-error').html('小盆友，输入的流水号格式不正确哦^^^');
            return false;
        }
        
        var validate_url = self.attr('validate-url') + '&stamp_series_number=' + stamp_series_number;
        $.get(
        validate_url,
        function(data, status) {
            if(status === 'success') {
                if(data == -1) {
                    form.find('.rest-money').val('');
                    form.find('.stamp-uuid').val('');
                    form.find('.stamp-series-number-error').html('小盆友，你输入的流水号不可用哦^^^');
                    return ;
                }
                data = JSON.parse(data);
                form.find('.stamp-series-number-error').html('');
                form.find('.rest-money').val(data.rest_money);
                form.find('.stamp-uuid').val(data.stamp_uuid);
            }
        }
        );
    });
    
    $('.billing-modal').on('click', '.submit',function() {
        var form = $(this).parents('form');
        var money = form.find('.money').val();
        var rest_money = form.find('.rest-money').val();
        var wait_checking_money = form.find('.wait-checking-stamp-money').val();
        if(parseFloat(rest_money) < parseFloat(money) 
        || parseFloat(wait_checking_money) < parseFloat(money)) {
            form.find('.money-error').html('小盆友，输入的金额不可以大于可匹配或待开票金额哦^^^');
            return false;
        }
        form.submit();
    });
})
JS;
$this->registerJs($Js, \yii\web\View::POS_END);
?>