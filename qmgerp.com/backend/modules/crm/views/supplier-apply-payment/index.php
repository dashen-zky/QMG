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
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-supplier',
    'menu-2-apply-payment'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'list')?'active':''?>"><a href="#apply-payment-tab-list" data-toggle="tab">我的付款申请</a></li>
        <li class="<?= (isset($tab) && $tab === 'add-payment')?'active':''?>"><a href="#apply-payment-tab-add" data-toggle="tab">申请付款</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'list')?'active in':''?>" id="apply-payment-tab-list">
            <?= $this->render('list-panel', [
                'title'=>'我的付款申请',
                'paymentList'=>$paymentList,
                'ser_filter'=>isset($ser_filter)?$ser_filter:null,
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-payment')?'active in':''?>" id="apply-payment-tab-add">
            <?= $this->render('add', [
                'title'=>'申请付款'
            ])?>
        </div>
    </div>
</div>
