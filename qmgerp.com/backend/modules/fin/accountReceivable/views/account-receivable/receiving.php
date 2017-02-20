<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<!-- #modal-without-animation -->
<div class="modal fade scroll receiving-modal" style="width: 35%; margin: 80px auto;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">收款</h4>
        </div>
        <div class="modal-body">
            <?= Html::beginForm(['/accountReceivable/account-receivable/receive-money'], 'post', [
                'class' => 'form-horizontal ReceiveMoneyForm',
                'data-parsley-validate' => "true",
            ])?>
            <?= Html::input('hidden', 'ReceiveMoneyForm[project_uuid]', null, [
                'class'=>'project-uuid'
            ])?>
            <input hidden name="ReceiveMoneyForm[account_receivable_uuid]" class="account-receivable-uuid">
            <table class="table">
                <tr>
                    <td>项目金额</td>
                    <td>
                        <input data-parsley-required="true"
                               class="form-control project-money"
                               type="text" disabled>
                    </td>
                </tr>
                <tr>
                    <td>已收金额</td>
                    <td>
                        <input data-parsley-required="true"
                               class="form-control received-money"
                               type="text" disabled>
                    </td>
                </tr>
                <tr>
                    <td>待收金额</td>
                    <td>
                        <input data-parsley-required="true"
                               class="form-control waiting-receive-money"
                               type="text" disabled>
                    </td>
                </tr>
                <tr>
                    <td>银行流水号*</td>
                    <td>
                        <div>
                            <input
                                class="form-control bank-series-number"
                                type="text" name="ReceiveMoneyForm[bank_series_number]"
                                data-parsley-required="true"
                                validate-url = "<?= Url::to([
                                    '/accountReceivable/account-receivable/validate-bank-series-number'
                                ])?>"
                            >
                        </div>
                        <span style="color: red" class="bank-series-number-error"></span>
                    </td>
                </tr>
                <tr>
                    <td>可匹配金额</td>
                    <td>
                        <input data-parsley-required="true"
                               class="form-control rest-money"
                               type="text" disabled>
                    </td>
                </tr>
                <tr>
                    <td>金额*</td>
                    <td>
                        <div>
                            <input class="form-control money"
                                    type="text"
                                    name="ReceiveMoneyForm[money]"
                                    data-parsley-required="true">
                        </div>
                        <span style="color: red" class="money-error"></span>
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
    $('.receiving-modal').on('blur', '.bank-series-number',function() {
        var self = $(this);
        var bank_series_number = $.trim(self.val());
        if(bank_series_number === '') {
            return false;
        }
        
        var form = self.parents('form');
        if(!validateSeriesNumber(bank_series_number)) {
            form.find('.bank-series-number-error').html('小盆友，输入的流水号格式不正确哦^^^');
            return false;
        }
        
        var validate_url = self.attr('validate-url') + '&bank_series_number=' + bank_series_number;
        $.get(
        validate_url,
        function(data, status) {
            if(status === 'success') {
                if(data == -1) {
                    form.find('.rest-money').val('');
                    form.find('.account-receivable-uuid').val('');
                    form.find('.bank-series-number-error').html('小盆友，你输入的流水号不可用哦^^^');
                    return ;
                }
                data = JSON.parse(data);
                form.find('.bank-series-number-error').html('');
                form.find('.rest-money').val(data.rest_money);
                form.find('.account-receivable-uuid').val(data.account_receivable_uuid);
            }
        }
        );
    });
    
    $('.receiving-modal').on('click', '.submit',function() {
        var form = $(this).parents('form');
        var money = form.find('.money').val();
        var rest_money = form.find('.rest-money').val();
        var waiting_receive_money = form.find('.waiting-receive-money').val();
        if(parseFloat(rest_money) < parseFloat(money) || parseFloat(waiting_receive_money) < parseFloat(money)) {
            form.find('.money-error').html('小盆友，输入的金额太大^^^');
            return false;
        }
        form.submit();
    });
})
JS;
$this->registerJs($Js, \yii\web\View::POS_END);
?>