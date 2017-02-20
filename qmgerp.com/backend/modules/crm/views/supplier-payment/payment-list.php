<?php
use backend\models\MyLinkPage;
use backend\modules\fin\payment\models\PaymentConfig;
$paymentConfig = new PaymentConfig();
?>
<table class="table">
<thead>
    <tr>
        <th>项目名称</th>
        <th>项目编号</th>
        <th>状态</th>
        <th>金额</th>
        <th>申请人</th>
        <th>款项用途</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($paymentList['list'] as $item) :?>
        <tr>
            <td><?= $item['project_name']?></td>
            <td><?= \backend\modules\crm\models\project\model\ProjectForm::codePrefix . $item['project_code']?></td>
            <td><?= $paymentConfig->getAppointed('status', $item['status'])?></td>
            <td><?= $item['actual_money']?></td>
            <td><?= $item['created_name']?></td>
            <td><?= $paymentConfig->getAppointed(
                    $paymentConfig->getAppointed('type_purpose_map', $item['type']), $item['purpose']
                )?></td>
        </tr>
    <?php endforeach?>
    </tbody>
</table>
<?= MyLinkPage::widget([
    'pagination' => $paymentList['pagination'],
]); ?>
