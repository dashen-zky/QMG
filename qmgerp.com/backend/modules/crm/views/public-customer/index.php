<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3 0003
 * Time: 上午 1:43
 */
use yii\helpers\Json;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-customer',
    'menu-2-public-customer'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'customer-list')?'active':''?>"><a href="#public-customer-tab-list" data-toggle="tab">客户列表</a></li>
        <li class="<?= (isset($tab) && $tab === 'add-customer')?'active':''?>"><a href="#public-customer-tab-add" data-toggle="tab">推荐客户</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab !== 'customer-list')?'':'active in'?>" id="public-customer-tab-list">
            <?= $this->render('public-customer-list',[
                'title'=>'客户池',
                'publicCustomerList' => $publicCustomerList,
                'model'=>$model,
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
                'contactModel'=>$contactModel,
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-customer')?'active in':''?>" id="public-customer-tab-add">
            <?= $this->render('add',[
                'model'=>$model,
                'contactModel'=>$contactModel,
            ])?>
        </div>
    </div>
</div>
