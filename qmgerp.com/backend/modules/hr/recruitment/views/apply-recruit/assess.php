<?php
use yii\helpers\Json;
use backend\modules\hr\recruitment\models\ApplyRecruit;
$applyRecruit = new ApplyRecruit();
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-assess',
    'menu-2-apply-recruitment-assess',
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'list')?'active':''?>"><a href="#list" data-toggle="tab">招聘列表</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'list')?'active in':''?>" id="list">
            <?= $this->render('assess-list', [
                'applyRecruitList'=>isset($applyRecruitList)?$applyRecruitList:$applyRecruit->myAssessApplyRecruitList(),
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
            ])?>
        </div>
    </div>
</div>
