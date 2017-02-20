
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
    'menu-1-project',
    'menu-2-apply-payment'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'edit-apply-payment')?'active':''?>"><a href="#edit-apply-payment-tab-1" data-toggle="tab">编辑付款申请</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'edit-apply-payment')?'active in':''?>" id="edit-apply-payment-tab-1">
            <?= $this->render('@webroot/../views/site/panel-header',[
                'title'=>'编辑付款申请',
            ])?>
            <?= $this->render('form',[
                'action'=>['/crm/project-apply-payment/update'],
                'formData'=>$formData,
                'edit'=>true,
            ])?>
            <?= $this->render('@webroot/../views/site/panel-footer')?>
        </div>
    </div>
</div>

