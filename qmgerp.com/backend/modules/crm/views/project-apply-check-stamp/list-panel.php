<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use backend\modules\fin\payment\models\PaymentConfig;
use yii\helpers\Url;
use backend\modules\crm\models\project\model\ProjectForm;
?>
<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>$title,
    'panelClass'=>'payment-list-panel',
])?>

<?= $this->render('list-filter-form',[
    'action'=>['/crm/project-apply-check-stamp/list-filter'],
]);?>
<div class="payment-list list">
    <?= $this->render('list', [
        'paymentList'=>$paymentList,
    ])?>
</div>

<span>
    <button class="btn btn-primary multi-apply-checking-stamp">申请验票</button>
</span>
<?= $this->render('show')?>
<?= $this->render('check-stamp',[
    'action'=>['/crm/project-apply-check-stamp/apply-checking-stamp'],
])?>
<?= $this->render('receiver-account-error')?>
<?= $this->render('@webroot/../views/site/panel-footer')?>
<?php
$Js = <<<Js
$(function() {
    // 查看付款申请详情
    $('.payment-list-panel').on('click', '.show-apply-payment', function() {
        var url = $(this).attr('name');
        var self = $(this);
        $.get(
        url,
        function(data,status) {
            if(status === 'success') {
                var panel = self.parents('.panel-body');
                var modal = panel.find('.apply-payment-show');
                modal.find('.modal-body').html(data);
                modal.modal('show');
            }
        });
    });
    // 申请验收发票
    $('.payment-list-panel').on('click', '.apply-checking-stamp', function() {
        var panel = $(this).parents('.panel-body');
        var modal = panel.find('.check-stamp-modal');
        var form = modal.find('form');
        form.find('.payment_uuid').val($(this).attr('uuid'));
        var tr = $(this).parents('tr');
        var actual_money = tr.find('.actual-money').val();
        var stamp_check_money = tr.find('.checked-stamp-money').val();
        var remind_message = tr.find('.remind-message').val();
        form.find('.money_count').val(actual_money);
        form.find('.have_checked_stamp_money').val(stamp_check_money);
        form.find('.owe_stamp_money').val(parseFloat(actual_money-stamp_check_money).toFixed(2));
        form.find('.remind_message').val(remind_message);
        modal.modal('show');
    });
    
    // 多笔流水一起申请验收发票
    $('.payment-list-panel').on('click', '.multi-apply-checking-stamp', function() {
        var panel = $(this).parents('.panel-body');
        var checked_uuid = '';
        var actual_money = 0;
        var checked_stamp_money = 0;
        // 检查收款账号是否一致,如果不一致，则不能通过验证
        var receiver_account = '';
        panel.find('input[type=checkbox]:checked').each(function(i) {
            var tr = $(this).parents('tr');
            // 检查收款账号是否一致,如果不一致，则不能通过验证
            if(receiver_account === '') {
                receiver_account = tr.find('.receiver-account').val();
            } else if(tr.find('.receiver-account').val() != receiver_account){
                var error_modal = panel.find('.receiver-account-error');
                error_modal.modal('show');
                receiver_account = 'error';
                return false;
            }
            
            checked_uuid = ((checked_uuid == '')?(''):(checked_uuid + ',')) + $(this).val();
            actual_money = parseFloat(actual_money) + parseFloat(tr.find('.actual-money').val());
            checked_stamp_money = parseFloat(checked_stamp_money) + parseFloat(tr.find('.checked-stamp-money').val());
        });
        
        if(receiver_account === 'error') {
            return false;
        }
        
        var modal = panel.find('.check-stamp-modal');
        var form = modal.find('form');
        form.find('.payment_uuid').val(checked_uuid);
        form.find('.money_count').val(actual_money);
        form.find('.have_checked_stamp_money').val(checked_stamp_money);
        form.find('.owe_stamp_money').val(parseFloat(actual_money-checked_stamp_money).toFixed(2));
        modal.modal('show');
    });
});
Js;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
