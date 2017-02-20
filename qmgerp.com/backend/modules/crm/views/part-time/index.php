<?php use yii\helpers\Json;?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-supplier',
    'menu-2-part-time',
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#part-time-tab-1" data-toggle="tab">兼职列表</a></li>
        <li class=""><a href="#part-time-tab-2" data-toggle="tab">添加兼职</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="part-time-tab-1">
            <?= $this->render('list',[
                'partTimeList' => $partTimeList,
                'model'=>$model,
                'operator'=>true,
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
            ])?>
        </div>
        <div class="tab-pane fade" id="part-time-tab-2">
            <?= $this->render('add',[
                'model'=>$model,
                'partTime'=>[
                    'code'=>$model->code,
                ],
            ])?>
        </div>
    </div>
</div>