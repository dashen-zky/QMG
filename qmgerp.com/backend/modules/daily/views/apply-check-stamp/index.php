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
    'menu-1-com',
    'menu-2-apply-check-stamp'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#apply-check-stamp-list" data-toggle="tab">我的付款申请</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="apply-check-stamp-list">
            <?= $this->render('list', [
                'paymentList'=>$paymentList,
                'ser_filter'=>isset($ser_filter)?$ser_filter:null,
            ])?>
        </div>
    </div>
</div>
