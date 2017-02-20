<?php
use yii\helpers\Json;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-config',
    'menu-2-payment-assess',
    'menu-3-pay'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'step-2')?'active':''?>"><a href="#pay-config-tab-1" data-toggle="tab">付款配置</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'step-2')?'active in':''?>" id="pay-config-tab-1">
            <?= $this->render('/payment-assess-config/add',[
                'action'=>['/system/pay-config/add'],
                'entrance'=>'daily',
                'step'=>'step-2',
                'choose_condition_type_url'=>'/system/pay-config/choose-condition-type',
            ])?>
            <?= $this->render('/payment-assess-config/list',[
                'config'=>$config,
                'step'=>'step-2',
                'itemDel'=>'/system/pay-config/del',
            ])?>
        </div>
        <div style="clear:left;"></div>
    </div>
</div>
