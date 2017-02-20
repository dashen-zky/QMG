<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3 0003
 * Time: 上午 1:43
 */
?>
<?php
use yii\helpers\Json;
use backend\modules\rbac\model\PermissionManager;
use backend\modules\payment_assess\models\DailyPaymentAssess;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-fin',
    'menu-2-payment',
    'menu-3-payment'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'list')?'active':''?>"><a href="#waiting-payment-tab-list" data-toggle="tab">待付款</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'list')?'active in':''?>" id="waiting-payment-tab-list">
            <?= $this->render('/payment/list-panel', [
                'title'=>'我的付款申请',
                'paymentList'=>$paymentList,
                'moneyStatistic'=>isset($moneyStatistic)?$moneyStatistic:'',
                'ser_filter'=>isset($ser_filter)?$ser_filter:null,
                'list_filter_action'=>['/payment/payment/list-filter'],
                'entrance'=>0,
            ])?>
        </div>
    </div>
</div>
