<?php
use yii\helpers\Json;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-config',
    'menu-2-permission',
    'menu-3-assignment'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'assign')?'active':''?>"><a href="#assign-tab-1" data-toggle="tab">角色分配</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'assign')?'active in':''?>" id="assign-tab-1">
            <?= $this->render('assign',[
                'formData'=>$formData,
                'backUrl'=>$backUrl,
            ])?>
        </div>
    </div>
</div>
