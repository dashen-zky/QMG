<?php
use yii\helpers\Json;
use backend\modules\daily\models\week_report\WeekReport;
use backend\modules\daily\models\transaction\Transaction;
$transaction = new Transaction();
$weekReport = new WeekReport();
// echo "<pre>";
// var_dump($transaction->effectiveTransactionList());
// die;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-com',
    'menu-2-week-report'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'list')?'active':''?>"><a href="#list" data-toggle="tab">周报列表</a></li>
        <li class="<?= (isset($tab) && $tab === 'add')?'active':''?>"><a href="#add" data-toggle="tab">写周报</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'list')?'active in':''?>" id="list">
            <?= $this->render('list', [
                'weekReportList'=>isset($weekReportList)?$weekReportList:$weekReport->myWeekReportList(),
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add')?'active in':''?>" id="add">
            <?= $this->render('add', [
                'transaction_ser_filter'=>isset($transaction_ser_filter)?$transaction_ser_filter:'',
                'transactionList'=>isset($transactionList)?$transactionList:$transaction->effectiveTransactionList(),
            ])?>
        </div>
    </div>
</div>
