<?php
use yii\helpers\Json;
$brief = new \backend\modules\crm\models\project\record\ProjectBrief();
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-assess',
    'menu-2-project-brief-assess',
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'list')?'active':''?>"><a href="#list" data-toggle="tab">brief列表</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'list')?'active in':''?>" id="list">
            <?= $this->render('assess-list-panel', [
                'briefList'=>isset($briefList)?$briefList:$brief->myAssessList(),
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
            ])?>
        </div>
    </div>
</div>
