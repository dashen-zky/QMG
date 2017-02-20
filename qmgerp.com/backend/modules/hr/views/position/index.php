<?php use yii\helpers\Json;?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-hr',
    'menu-2-position',
])?>'>

<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#default-tab-list" data-toggle="tab">职位列表</a></li>
        <li class=""><a href="#default-tab-add" data-toggle="tab">添加职位</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="default-tab-list">
            <?= $this->render('position-list',[
                'positionList' => $positionList,
                'ser_filter'=>isset($ser_filter)?$ser_filter:null,
                'filter_form_data'=>$filter_form_data,
            ])?>
        </div>
        <div class="tab-pane fade" id="default-tab-add">
        <?= $this->render('add',[
            'model'=>$model,
            'formData'=>$formData,
        ])?>
        </div>
    </div>
</div>
