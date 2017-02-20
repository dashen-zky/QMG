<?php
use backend\models\MyLinkPage;
use backend\modules\crm\models\project\record\ProjectApplyStamp;
?>
<table class="table table-striped">
<thead>
    <tr>
        <th>#</th>
        <th>申请金额</th>
        <th>已开票金额</th>
        <th>申请人</th>
        <th>申请时间</th>
        <th>状态</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($projectApplyStampList['list'] as $item) :?>
        <tr>
            <td>
                <?= $item['id']?>
            </td>
            <td><?= $item['money']?></td>
            <td><?= $item['checked_stamp_money']?></td>
            <td><?= $item['created_name']?></td>
            <td><?= $item['created_time'] !='0'?date("Y-m-d", $item['created_time']):''?></td>
            <td><?= ProjectApplyStamp::$status[$item['status']]?></td>
        </tr>
    <?php endforeach?>
    </tbody>
</table>
<?= MyLinkPage::widget([
    'pagination' => $projectApplyStampList['pagination'],
]); ?>
