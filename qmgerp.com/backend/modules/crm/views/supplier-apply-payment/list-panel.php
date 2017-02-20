<?php
use yii\helpers\Url;
?>
<div class="panel panel-body payment-list-panel">
<?= $this->render('list-filter-form',[
    'action'=>['/crm/supplier-apply-payment/list-filter'],
]);?>
    <div class="panel-body payment-list list">
        <?= $this->render('list', [
            'paymentList'=>$paymentList,
        ])?>
    </div>
<div>
    <button class="btn btn-primary apply-payment" name="<?= Url::to([
        '/crm/supplier-apply-payment/multi-apply',
    ])?>">申请付款</button>
</div>
<?= $this->render('show')?>
</div>
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
    // 提交申请
    $('.payment-list-panel').on('click', '.apply-payment', function() {
        var panel = $(this).parents('.panel-body');
        var checked = new Array();
        panel.find('input[type=checkbox]:checked').each(function(i) {
            checked[i] = $(this).val();
        });
        checked = JSON.stringify(checked);
        var url = $(this).attr('name') + "&uuids=" + checked;
        window.location.href = url;
    });
});
Js;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
