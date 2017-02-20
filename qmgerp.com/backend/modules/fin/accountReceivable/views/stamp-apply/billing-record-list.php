<?php
use yii\helpers\Url;
?>
<table class="table table-striped">
    <thead>
    <tr>
        <th>发票编号</th>
        <th>开票方</th>
        <th>收票方</th>
        <th>发票金额</th>
        <th>匹配金额</th>
        <th>匹配时间</th>
        <th>匹配人</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($billingRecordList as $item) :?>
        <tr>
            <td><?= $item['stamp_series_number']?></td>
            <td><?= $item['stamp_provider']?></td>
            <td><?= $item['stamp_receiver']?></td>
            <td><?= $item['stamp_money']?></td>
            <td><?= $item['money']?></td>
            <td><?= $item['checked_time'] !='0'?date("Y-m-d", $item['checked_time']):''?></td>
            <td><?= $item['checked_name']?></td>
            <td>
                <div>
                    <a url="<?= Url::to([
                        '/stamp/export-stamp/show',
                        'uuid'=>$item['stamp_uuid'],
                    ])?>" class="show-export-stamp" href="#">查看</a>
                </div>
            </td>
        </tr>
    <?php endforeach?>
    </tbody>
</table>
