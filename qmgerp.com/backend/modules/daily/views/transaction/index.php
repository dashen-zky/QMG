<?php
use yii\helpers\Json;
use backend\modules\daily\models\transaction\Transaction;
$transaction = new Transaction();
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-com',
    'menu-2-transaction'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'unfinished-list')?'active':''?>"><a href="#unfinished-list" data-toggle="tab">待完成事项</a></li>
        <li class="<?= (isset($tab) && $tab === 'finished-list')?'active':''?>"><a href="#finished-list" data-toggle="tab">已完成事项</a></li>
        <li class="<?= (isset($tab) && $tab === 'add-transaction')?'active':''?>"><a href="#add-transaction" data-toggle="tab">添加事项</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'unfinished-list')?'active in':''?>" id="unfinished-list">
        <?= $this->render('unfinished-list', [
            'transactionList'=>isset($unfinishedTransactionList)?
                $unfinishedTransactionList:$transaction->unfinishedTransactionList(),
            'ser_filter'=>isset($unfinished_ser_filter)?$unfinished_ser_filter:'',
        ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'finished-list')?'active in':''?>" id="finished-list">
        <?= $this->render('finished-list', [
            'transactionList'=>isset($finishedTransactionList)?
                $finishedTransactionList:$transaction->finishedTransactionList(),
            'ser_filter'=>isset($finished_ser_filter)?$finished_ser_filter:'',
        ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-transaction')?'active in':''?>" id="add-transaction">
            <?= $this->render('add')?>
        </div>
    </div>
</div>
