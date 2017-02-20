<?php
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\web\View;
?>
<div class="payment-list-panel panel panel-body">
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
    'action'=>['/payment_assess/project-payment-assess/list-filter'],
    'formData'=>isset($ser_filter)?unserialize($ser_filter):[],
    'entrance'=>isset($entrance)?$entrance:null,
]);?>
<div class="panel-body payment-list list">
    <?= $this->render('list', [
        'paymentList'=>$paymentList,
        'entrance'=>isset($entrance)?$entrance:null,
        'ser_filter'=>isset($ser_filter)?$ser_filter:null,
    ])?>
</div>
<?php Pjax::end()?>
<?= $this->render('assess')?>
</div>
<?php
$Js = <<<Js
$(function() {
    // 查看付款申请详情
    $('.payment-list-panel').on('click', '.assess-apply-payment', function() {
        var url = $(this).attr('url');
        var self = $(this);
        $.get(
        url,
        function(data,status) {
            if(status === 'success') {
                var panel = self.parents('.panel-body');
                var modal = panel.find('.apply-payment-assess');
                modal.find('.modal-body').html(data);
                modal.modal('show');

                // 状态变化控制审核不通过原因的显示
                $('.ApplyPaymentForm').on('change', '.assess-status', function() {
                   var value = $(this).val();
                   if(value == 2) {
                        $(this).parents('form').find('.refuse-reason').attr('readOnly', true);
                        return ;
                   }

                   $(this).parents('form').find('.refuse-reason').attr('readOnly', false);
                });
            }
        });
    });
});
Js;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
