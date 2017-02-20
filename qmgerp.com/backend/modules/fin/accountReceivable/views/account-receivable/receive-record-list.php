<?php 
use yii\helpers\Url;
?>
<table class="table table-striped">
    <thead>
    <tr>
        <th>银行流水号</th>
        <th>付款方</th>
        <th>金额</th>
        <th>支付金额</th>
        <th>录入人</th>
        <th>录入时间</th>
        <th>收款时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($receiveRecordList as $item) :?>
    <tr>
        <td><?= $item['bank_series_number']?></td>
        <td><?= $item['payment']?></td>
        <td><?= $item['money']?></td>
        <td><?= $item['paied_money']?></td>
        <td><?= $item['created_name']?></td>
        <td><?= $item['time'] !='0'?date("Y-m-d", $item['time']):''?></td>
        <td><?= $item['receive_time'] !='0'?date("Y-m-d", $item['receive_time']):''?></td>
        <td>
            <div>
                <a url="<?= Url::to([
                    '/accountReceivable/receive-money/show',
                    'uuid'=>$item['uuid'],
                ])?>" class="show-receive-money" href="#">查看</a>
            </div>
        </td>
    </tr>
    <?php endforeach?>
    </tbody>
</table>
