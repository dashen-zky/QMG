<?php use yii\helpers\Json;?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-fin',
    'menu-2-account-receivable',
    'menu-3-stamp-apply-manage'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#default-tab-list" data-toggle="tab">开票申请列表</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="default-tab-list">
            <?= $this->render('list',[
                'projectApplyStampList' => $projectApplyStampList,
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
            ])?>
        </div>
    </div>
</div>