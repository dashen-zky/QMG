<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use backend\modules\fin\payment\models\PaymentConfig;
use yii\helpers\Url;
?>
<div class="payment-list-panel panel panel-body">
<?php Pjax::begin(); ?>
<?php
$Js = <<<JS
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
    
    // 查看付款申请详情
    $('.payment-list-panel').on('click', '.show-apply-payment', function() {
        var url = $(this).attr('name');
        var self = $(this);
        $.get(
        url,
        function(data,status) {
            if(status === 'success') {
                var panel = self.parents('.panel-body');
                var modal = panel.find('.apply-check-stamp-show');
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
JS;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
<?= $this->render('list-filter-form',[
    'action'=>['/daily/apply-check-stamp/list-filter'],
    'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
]);?>
    <div class="panel-body payment-list">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>选择</th>
                <th>#</th>
                <th>期望日期</th>
                <th>款项类型</th>
                <th>款项用途</th>
                <th>发票状态</th>
                <th>付款金额</th>
                <th>已验收发票金额</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php $paymentConfig = new PaymentConfig();?>
            <?php foreach ($paymentList['list'] as $item) :?>
                <tr>
                    <td>
                        <input hidden value="<?= $item['remind_message']?>" class="remind-message">
                        <input hidden value="<?= $item['actual_money']?>" class="actual-money">
                        <input hidden value="<?= $item['checked_stamp_money']?>"
                               class="checked-stamp-money">
                        <input hidden value="<?= $item['receiver_account']?>" class="receiver-account">
                        <?php if($item['stamp_status'] != PaymentConfig::StampChecked) :?>
                        <input type="checkbox" value="<?= $item['uuid']?>">
                        <?php endif?>
                    </td>
                    <td><?= PaymentConfig::CodePrefix . $item['code']?></td>
                    <td>
                        <?= (isset($item['expect_time'])
                            && $item['expect_time'] != 0)
                            ?date('Y-m-d', $item['expect_time']):''?>
                    </td>
                    <td>
                        <?= $paymentConfig->getAppointed('type', $item['type'])?>
                    </td>
                    <td>
                        <?= $paymentConfig->getAppointed(
                            $paymentConfig->getAppointed('type_purpose_map', $item['type']), $item['purpose']
                        )?>
                    </td>
                    <td><?= $paymentConfig->getAppointed(
                            'stamp_status', $item['stamp_status']
                        )?></td>
                    <td><?= $item['actual_money']?></td>
                    <td>
                        <?= $item['checked_stamp_money']?>
                    </td>
                    <td><?= $paymentConfig->getAppointed('status', $item['status'])?></td>
                    <td>
                        <div>
                            <a name="<?= Url::to([
                                '/daily/apply-check-stamp/show',
                                'uuid'=>$item['uuid'],
                            ])?>" class="show-apply-payment" href="javascript:;">查看</a>
                        </div>
                        <?php if($item['stamp_status'] != PaymentConfig::StampChecked) :?>
                        <div>
                            <a uuid="<?= $item['uuid']?>" href="#" class="apply-checking-stamp">申请验票</a>
                        </div>
                        <?php endif?>
                    </td>
                </tr>
            <?php endforeach?>
            </tbody>
        </table>
        <?php
        if(isset($ser_filter) && !empty($ser_filter)) {
            $pageParams = [
                'pagination' => $paymentList['pagination'],
                'ser_filter'=>$ser_filter,
            ];
        } else {
            $pageParams = [
                'pagination' => $paymentList['pagination'],
            ];
        }
        ?>
        <?= MyLinkPage::widget($pageParams); ?>
    </div>
<?php Pjax::end(); ?>
<span>
    <button class="btn btn-primary multi-apply-checking-stamp">申请验票</button>
</span>
<?= $this->render('show')?>
<?= $this->render('check-stamp',[
    'action'=>['/daily/apply-check-stamp/apply-checking-stamp'],
])?>
<?= $this->render('receiver-account-error')?>
</div>
