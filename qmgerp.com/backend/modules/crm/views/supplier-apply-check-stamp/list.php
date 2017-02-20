<?php
use backend\models\AjaxLinkPage;
use backend\modules\fin\payment\models\PaymentConfig;
use backend\modules\crm\models\project\model\ProjectForm;
use yii\helpers\Url;
use backend\modules\crm\models\supplier\model\SupplierForm;
use backend\modules\crm\models\part_time\model\PartTimeForm;
?>
<table class="table table-striped">
    <thead>
    <tr>
        <th>选择</th>
        <th>#</th>
        <th class="col-md-1">项目信息</th>
        <th>供应商/兼职信息</th>
        <th>验票状态</th>
        <th>款项用途</th>
        <th>付款金额</th>
        <th>已验票金额</th>
        <th>状态</th>
        <th>期望日期</th>
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
                <div><?= $item['project_name']?></div>
                <div><?= ProjectForm::codePrefix.$item['project_code']?></div>
            </td>
            <td>
                <?php if(!empty($item['supplier_name'])) :?>
                    <div>供应商</div>
                    <div><?= $item['supplier_name']?></div>
                    <div><?= SupplierForm::codePrefix.$item['supplier_code']?></div>
                <?php else:?>
                    <div>兼职</div>
                    <div><?= $item['part_time_name']?></div>
                    <div><?= PartTimeForm::codePrefix.$item['part_time_code']?></div>
                <?php endif?>
            </td>
            <td>
                <?= $paymentConfig->getAppointed('stamp_status', $item['stamp_status'])?>
            </td>
            <td>
                <?= $paymentConfig->getAppointed(
                    $paymentConfig->getAppointed('type_purpose_map', $item['type']), $item['purpose']
                )?>
            </td>
            <td><?= $item['actual_money']?></td>
            <td><?= $item['checked_stamp_money']?></td>
            <td><?= $paymentConfig->getAppointed('status', $item['status'])?></td>
            <td>
                <?= (isset($item['expect_time'])
                    && $item['expect_time'] != 0)
                    ?date('Y-m-d', $item['expect_time']):''?>
            </td>
            <td>
                <div>
                    <a name="<?= Url::to([
                        '/crm/supplier-apply-check-stamp/show',
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
<?= AjaxLinkPage::widget($pageParams); ?>
<?php
$Js = <<<Js
$(function() {
    $('.list .pagination').on('click', 'li', function() {
            pagination($(this));
    });

    function pagination(self) {
        var url = self.attr('url');
        $.get(
        url,
        function(data, status) {
            if(status !== 'success') {
                return ;
            }
            var container = self.parents('.list');
            container.html(data);
            $('.pagination').on('click', 'li', function() {
                pagination($(this));
            });
        });
    }
});
Js;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
