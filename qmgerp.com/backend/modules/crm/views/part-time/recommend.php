<?php use yii\helpers\Json;?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-com',
    'menu-2-add-part-time'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= isset($tab) && $tab === 'part-time-list'?'active':''?>"><a href="#part-time-tab-1" data-toggle="tab">我推荐的兼职</a></li>
        <li class="<?= !isset($tab) || $tab === 'add-part-time'?'active':''?>"><a href="#part-time-tab-2" data-toggle="tab">推荐兼职</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= isset($tab) && $tab === 'part-time-list'?'active in':''?>" id="part-time-tab-1">
            <?= $this->render('list',[
                'title'=>'兼职列表',
                'partTimeList' => $partTimeList,
                'model'=>$model,
                'operator'=>true,
                'recommend'=>true,
            ])?>
        </div>
        <div class="tab-pane fade <?= !isset($tab) || $tab === 'add-part-time'?'active in':''?>" id="part-time-tab-2">
            <?= $this->render('add',[
                'model'=>$model,
                'title'=>'推荐兼职',
                'partTime'=>[
                    'code'=>$model->code,
                ],
                'backUrl'=>Json::encode([
                    '/crm/part-time/recommend',
                    'tab'=>'part-time-list'
                ]),
            ])?>
        </div>
    </div>
</div>