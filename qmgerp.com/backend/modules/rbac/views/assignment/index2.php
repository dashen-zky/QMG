<?php
use yii\helpers\Json;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-config',
    'menu-2-permission',
    'menu-3-assignment2'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs active">
        <li><a href="#role-assign-tab-2" data-toggle="tab">角色分配树状图</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="role-assign-tab-2">
            <?= $this->render('assignment-tree',[

            ])?>
        </div>
    </div>
</div>
