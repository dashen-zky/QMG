<?php
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\web\View;
?>
<div class="panel panel-body payment-list-panel">
<?php Pjax::begin()?>
<?php
$Js = <<<Js
$(function() {
$(".datetimepicker").datetimepicker({
        lang:"ch",           //语言选择中文
        format:"Y-m-d",      //格式化日期H:i
        i18n:{
          // 以中文显示月份
          de:{
            months:["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月",],
            // 以中文显示每周（必须按此顺序，否则日期出错）
            dayOfWeek:["日","一","二","三","四","五","六"]
          }
        }
        // 显示成年月日，时间--
    });
});
Js;
$this->registerJs($Js, View::POS_END);
?>
<?= $this->render('list-filter-form',[
    'action'=>$list_filter_action,
    'formData'=>isset($ser_filter)?unserialize($ser_filter):[],
    'entrance'=>$entrance,
]);?>
<div class="payment-list list">
    <?= $this->render('list', [
        'paymentList'=>$paymentList,
        'ser_filter'=>isset($ser_filter)?$ser_filter:null,
    ])?>
</div>
<?php Pjax::end()?>
<span>
    <button class="btn btn-primary multi-paying">付款</button>
</span>
<?= $this->render('show')?>
<?= $this->render('receiver-account-error')?>
<?= $this->render('paying',[
    'action'=>['/payment/payment/paying'],
])?>
</div>
<?php
$Js = <<<Js
$(function() {
    // 查看付款申请详情
    $('.payment-list-panel').on('click', '.payment-show', function() {
        var url = $(this).attr('url');
        var self = $(this);
        $.get(
        url,
        function(data,status) {
            if(status === 'success') {
                var panel = self.parents('.panel-body');
                var modal = panel.find('.payment-show-modal');
                modal.find('.modal-body').html(data);
                modal.modal('show');
            }
        });
    });
    
    // 付款
    $('.payment-list-panel').on('click', '.paying', function() {
        var panel = $(this).parents('.panel-body');
        var modal = panel.find('.paying-modal');
        var form = modal.find('.PayingForm');
        form.find('.payment_uuid').val($(this).attr('uuid'));
        var actual_money = $(this).parents('tr').find('.actual-money').val();
        var paied_money = $(this).parents('tr').find('.paied-money').val();
        form.find('.money_count').val(actual_money);
        form.find('.money_paied').val(paied_money);
        form.find('.should_paied').val(parseFloat(actual_money-paied_money).toFixed(2));
        modal.modal('show');
    });
    // 多笔付款一起付
    $('.payment-list-panel').on('click', '.multi-paying', function() {
        var panel = $(this).parents('.panel-body');
        var checked_uuid = '';
        var actual_money = 0;
        var paied_money = 0;
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
            paied_money = parseFloat(paied_money) + parseFloat(tr.find('.paied-money').val());
        });
        
        if(receiver_account === 'error') {
            return ;
        }
        
        var modal = panel.find('.paying-modal');
        var form = modal.find('.PayingForm');
        form.find('.payment_uuid').val(checked_uuid);
        form.find('.money_count').val(actual_money);
        form.find('.money_paied').val(paied_money);
        form.find('.should_paied').val(parseFloat(actual_money-paied_money).toFixed(2));
        modal.modal('show');
    });
});
Js;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
