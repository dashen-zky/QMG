<?php
use backend\modules\fin\payment\models\PaymentConfig;
use backend\models\MyLinkPage;
use yii\helpers\Url;
use backend\modules\fin\payment\models\Payment;
use backend\modules\payment_assess\models\ProjectPaymentAssess;
use backend\modules\crm\models\project\model\ProjectForm;
?>
<table class="table table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th>申请日期</th>
        <th>项目信息</th>
        <th>款项类型</th>
        <th>款项用途</th>
        <th>付款金额</th>
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
            <td><?= $item['id']?></td>
            <td>
                <?= (isset($item['created_time'])
                    && $item['created_time'] != 0)
                    ?date('Y-m-d', $item['created_time']):''?>
            </td>
            <td>
                <div>项目编号:<?= ProjectForm::codePrefix.$item['project_code']?></div>
                <div>项目名称:<?= $item['project_name']?></div>
            </td>
            <td>
                <?= $paymentConfig->getAppointed('type', $item['type'])?>
            </td>
            <td>
                <?= $paymentConfig->getAppointed(
                    $paymentConfig->getAppointed('type_purpose_map', $item['type']), $item['purpose']
                )?>
            </td>
            <td><?= $item['actual_money']?></td>
            <td><?= $paymentConfig->getAppointed('status', $item['status'])?></td>
            <td>
                <?= (isset($item['expect_time'])
                    && $item['expect_time'] != 0)
                    ?date('Y-m-d', $item['expect_time']):''?>
            </td>
            <td><?= $item['created_name']?></td>
            <td>
        <?php if($entrance != Payment::AssessSucceed
            || ProjectPaymentAssess::canAssessInSucceedEntrance([
                $item['first_assess_uuid'],
                $item['second_assess_uuid'],
                $item['third_assess_uuid'],
            ], $item['status'])):?>
                <div>
                    <a url="<?= Url::to([
                        '/payment_assess/project-payment-assess/assessing',
                        'uuid'=>$item['uuid'],
                        'entrance'=>isset($entrance)?$entrance:'',
                    ])?>" class="assess-apply-payment" href="javascript:;">审核</a>
                </div>
            <?php else:?>
            <div>
                <a url="<?= Url::to([
                    '/payment_assess/project-payment-assess/show',
                    'uuid'=>$item['uuid'],
                    'entrance'=>isset($entrance)?$entrance:'',
                ])?>" class="assess-apply-payment" href="javascript:;">查看</a>
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