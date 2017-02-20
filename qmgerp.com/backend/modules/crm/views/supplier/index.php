<?php use yii\helpers\Json;?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-supplier',
    'menu-2-supplier'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#supplier-tab-1" data-toggle="tab">供应商列表</a></li>
        <li class=""><a href="#supplier-tab-2" data-toggle="tab">添加供应商</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="supplier-tab-1">
            <?= $this->render('list',[
                'supplierList' => $supplierList,
                'model'=>$model,
                'operator'=>true,
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
            ])?>
        </div>
        <div class="tab-pane fade" id="supplier-tab-2">
            <?= $this->render('add',[
                'model'=>$model,
                'contactModel'=>$contactModel,
            ])?>
        </div>
    </div>
</div>