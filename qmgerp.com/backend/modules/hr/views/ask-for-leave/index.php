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
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-com',
    'menu-2-ask-for-leave'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'list')?'active':''?>"><a href="#apply-payment-tab-list" data-toggle="tab">我的请假申请</a></li>
        <li class="<?= (isset($tab) && $tab === 'add')?'active':''?>"><a href="#apply-payment-tab-add" data-toggle="tab">请假</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'list')?'active in':''?>" id="apply-payment-tab-list">
            <?= $this->render('list',[
                'askForLeaveList'=>$askForLeaveList,
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add')?'active in':''?>" id="apply-payment-tab-add">
            <?= $this->render('add')?>
        </div>
    </div>
</div>
