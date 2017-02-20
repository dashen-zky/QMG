<?php
use yii\helpers\Json;
use backend\modules\crm\models\part_time\model\PartTimeConfig;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-com',
    'menu-2-add-part-time'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'edit-part-time')?'active':''?>">
            <a href="#part-time-edit-tab-1" data-toggle="tab">编辑兼职</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'edit-part-time')?'active in':''?>" id="part-time-edit-tab-1">
            <?= $this->render('@webroot/../views/site/panel-header',[
                'title'=>'编辑兼职',
                'panelClass'=>'part-time-panel'
            ])?>
            <?= $this->render('form',[
                'model'=>$model,
                'formClass'=>'PartTimeForm',
                'action'=>['/crm/part-time/update'],
                'partTime'=>$partTime,
                'show'=>true,
                'enableEdit'=>($partTime['allocate'] == PartTimeConfig::UnAllocate),
                'backUrl'=>Json::encode([
                    '/crm/part-time/recommend',
                    'tab'=>'part-time-list'
                ]),
            ])?>
            <?= $this->render('@webroot/../views/site/panel-footer')?>
        </div>
    </div>
</div>