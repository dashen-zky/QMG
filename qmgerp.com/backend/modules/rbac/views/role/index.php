<?php
use yii\helpers\Json;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-config',
    'menu-2-permission',
    'menu-3-role'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'role-tree')?'active':''?>"><a href="#role-tab-tree" data-toggle="tab">树状图</a></li>
        <li class="<?= (isset($tab) && $tab === 'add-role')?'active':''?>"><a href="#role-tab-add" data-toggle="tab">添加角色</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'role-tree')?'active in':''?>" id="role-tab-tree">
            <?= $this->render('tree',[

            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-role')?'active in':''?>" id="role-tab-add">
            <?= $this->render('add',[

            ])?>
        </div>
    </div>
</div>
