<?php
use yii\helpers\Json;
use backend\modules\fin\accountReceivable\models\ReceiveMoneyCompany;
$receiveMoneyCompany = new ReceiveMoneyCompany();
$receiveCompanyList = $receiveMoneyCompany->receiveMoneyCompanyList();
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-fin',
    'menu-2-account-receivable',
    'menu-3-receive-money'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= !isset($tab) || $tab == 'list'?'active':''?>"><a href="#default-tab-list" data-toggle="tab">收款记录</a></li>
        <li class="<?= isset($tab) && $tab == 'add'?'active':''?>"><a href="#default-tab-add" data-toggle="tab">录入收款</a></li>
        <li class="<?= isset($tab) && $tab == 'receive-company'?'active':''?>"><a href="#default-tab-receive-company" data-toggle="tab">收款公司</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= !isset($tab) || $tab == 'list'?'active in':''?>" id="default-tab-list">
            <?= $this->render('list',[
                'receiveMoneyList'=>$receiveMoneyList,
                'ser_filter'=>isset($ser_filter)?$ser_filter:null,
            ])?>
        </div>
        <div class="tab-pane fade <?= isset($tab) && $tab == 'add'?'active in':''?>" id="default-tab-add">
            <?= $this->render('add', [
                'formData'=>isset($formData)?$formData:null,
                'model'=>$model,
                'receiveCompanyList'=>$receiveMoneyCompany->transformForDropDownList($receiveCompanyList, 'uuid', 'name'),
            ])?>
        </div>
        <div class="tab-pane fade <?= isset($tab) && $tab == 'receive-company'?'active in':''?>" id="default-tab-receive-company">
            <?= $this->render('/receive-company/add')?>
            <?= $this->render('/receive-company/list', [
                'receiveCompanyList'=>$receiveCompanyList,
            ])?>
        </div>
    </div>
</div>