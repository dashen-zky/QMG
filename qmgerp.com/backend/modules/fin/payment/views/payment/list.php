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
        <th>选择</th>
        <th>#</th>
        <th>申请日期</th>
        <th>款项类型</th>
        <th>付款金额</th>
        <th>已付金额</th>
        <th style="color: red">应付金额</th>
        <th>状态</th>
        <th>期望日期</th>
        <th>申请人</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php $paymentConfig = new PaymentConfig();?>
    <?php foreach ($paymentList['list'] as $item) :?>
        <tr>
            <td>
                <?php $canPay = $payment->canPay($item);?>
                <?php if($canPay) :?>
                    <input type="checkbox" value="<?= $item['uuid']?>">
                <?php endif?>
            </td>
            <td>
                <input hidden value="<?= $item['actual_money']?>" class="actual-money">
                <input hidden value="<?= $item['paied_money']?>" class="paied-money">
                <input hidden value="<?= $item['receiver_account']?>" class="receiver-account">
                <?= PaymentConfig::CodePrefix . $item['code']?>
            </td>
            <td>
                <?= (isset($item['created_time'])
                    && $item['created_time'] != 0)
                    ?date('Y-m-d', $item['created_time']):''?>
            </td>
            <td>
                <div><?= $paymentConfig->getAppointed('type', $item['type'])?></div>
                <div>
                    <?= $paymentConfig->getAppointed(
                        $paymentConfig->getAppointed('type_purpose_map', $item['type']), $item['purpose']
                    )?>
                </div>
            </td>
            <td><?= $item['actual_money']?></td>
            <td><?= $item['paied_money']?></td>
            <td style="color: red"><?= $item['actual_money'] - $item['paied_money']?></td>
            <td><?= $paymentConfig->getAppointed('status', $item['status'])?></td>
            <td>
                <?= (isset($item['expect_time'])
                    && $item['expect_time'] != 0)
                    ?date('Y-m-d', $item['expect_time']):''?>
            </td>
            <td><?= $item['created_name']?></td>
            <td>
            <?php if($canPay):?>
                <div>
                    <a class="paying" href="javascript:;" uuid="<?= $item['uuid']?>">付款</a>
                </div>
            <?php endif?>
            <div>
                <a url="<?= Url::to([
                    '/payment/payment/show',
                    'uuid'=>$item['uuid'],
                ])?>" class="payment-show" href="javascript:;">查看</a>
                <a url="<?= Url::to([
                    '/payment/payment/print',
                    'uuid'=>$item['uuid'],
                ])?>" class="show-new-tab" href="javascript:;">打印</a>
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
    
