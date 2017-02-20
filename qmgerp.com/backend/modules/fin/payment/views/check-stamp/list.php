<?php
use backend\modules\fin\payment\models\PaymentConfig;
use backend\models\MyLinkPage;
use yii\helpers\Url;
use backend\modules\fin\payment\models\PaymentList;
$payment = new PaymentList();
?>
<table class="table table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th>验票状态</th>
        <th>款项类型</th>
        <th>款项用途</th>
        <th>付款金额</th>
        <th>验收金额</th>
        <th>状态</th>
        <th>验收备注</th>
        <th>申请人</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php $paymentConfig = new PaymentConfig();?>
    <?php foreach ($paymentList['list'] as $item) :?>
        <tr>
            <td>
                <input hidden value="<?= $item['actual_money']?>" class="actual-money">
                <input hidden value="<?= $item['paied_money']?>" class="paied-money">
                <input hidden value="<?= $item['uuid']?>" class="payment-uuid">
                <input hidden value="<?= $item['stamp_status']?>" class="stamp-status">
                <input hidden value="<?= $item['receiver_account']?>" class="receiver-account">
                <?= PaymentConfig::CodePrefix . $item['code']?>
            </td>
            <td><?= $paymentConfig->getAppointed('stamp_status', $item['stamp_status'])?></td>
            <td>
                <?= $paymentConfig->getAppointed('type', $item['type'])?>
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
                <?= $item['remind_message']?>
            </td>
            <td><?= $item['created_name']?></td>
            <td>
                <div>
                    <a url="<?= Url::to([
                        '/payment/check-stamp/show',
                        'uuid'=>$item['uuid'],
                    ])?>" class="payment-show" href="javascript:;">查看</a>
                </div>
                <div>
                    <a uuid="<?= $item['uuid']?>" url="<?= Url::to([
                        '/payment/check-stamp/stamp-list',
                        'uuid'=>$item['uuid']
                    ])?>" href="#" class="checking-stamp">验票</a>
                </div>
                <div>
                    <a url="<?= Url::to([
                        '/payment/check-stamp/checked-stamp-list',
                        'uuid'=>$item['uuid']
                    ])?>" href="#" class="checked-stamp-list">发票记录</a>
                </div>
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