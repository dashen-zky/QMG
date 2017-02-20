<?php
use yii\helpers\Json;
use backend\modules\hr\recruitment\models\ApplyRecruit;
$applyRecruit = new ApplyRecruit();
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-com',
    'menu-2-recruitment'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'list')?'active':''?>"><a href="#list" data-toggle="tab">招聘列表</a></li>
        <li class="<?= (isset($tab) && $tab === 'add')?'active':''?>"><a href="#add" data-toggle="tab">添加招聘</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'list')?'active in':''?>" id="list">
            <?= $this->render('list', [
                'applyRecruitList'=>isset($applyRecruitList)?$applyRecruitList:$applyRecruit->myApplyRecruitList(),
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add')?'active in':''?>" id="add">
            <?= $this->render('add')?>
        </div>
    </div>
</div>
