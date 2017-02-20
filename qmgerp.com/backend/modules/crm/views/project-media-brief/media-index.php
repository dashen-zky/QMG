<?php
use yii\helpers\Json;
$brief = new \backend\modules\crm\models\project\record\ProjectMediaBrief();
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-supplier',
    'menu-2-project-media-brief',
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'list')?'active':''?>"><a href="#list" data-toggle="tab">brief列表</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'list')?'active in':''?>" id="list">
            <?= $this->render('media-list-panel', [
                'briefList'=>isset($briefList)?$briefList:$brief->listForMedia(),
            ])?>
        </div>
    </div>
</div>
