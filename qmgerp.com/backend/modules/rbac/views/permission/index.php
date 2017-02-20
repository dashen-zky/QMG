<?php
use yii\helpers\Json;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-config',
    'menu-2-permission',
    'menu-3-permission'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#role-permission-tab-1" data-toggle="tab">权限与角色图</a></li>
        <li class=""><a href="#role-permission-tab-2" data-toggle="tab">添加权限</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="role-permission-tab-1">
            <?= $this->render('role-permission-tree',[

            ])?>
        </div>
        <div class="tab-pane fade" id="role-permission-tab-2">
            <?= $this->render('add',[

            ])?>
        </div>
    </div>
</div>