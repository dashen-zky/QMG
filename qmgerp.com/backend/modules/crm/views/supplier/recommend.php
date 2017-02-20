<?php
use yii\helpers\Json;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-com',
    'menu-2-add-supplier'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (isset($tab) && $tab === 'list')?'active':''?>"><a href="#supplier-tab-1" data-toggle="tab">我推荐的供应商</a></li>
        <li class="<?= (!isset($tab) || $tab === 'add')?'active':''?>"><a href="#supplier-tab-2" data-toggle="tab">推荐供应商</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'list')?' active in':''?>" id="supplier-tab-1">
            <?= $this->render('list',[
                'title'=>'供应商列表',
                'supplierList' => $supplierList,
                'model'=>$model,
                'operator'=>true,
                'recommend'=>true,
            ])?>
        </div>
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'add')?' active in':''?>" id="supplier-tab-2">
            <?= $this->render('add',[
                'model'=>$model,
                'backUrl'=>$backUrl,
                'title'=>'推荐供应商',
                'contactModel'=>$contactModel,
            ])?>
        </div>
    </div>
</div>