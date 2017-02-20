<?php use yii\helpers\Json;?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-supplier',
    'menu-2-part-time',
    'menu-3-part-time',
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'edit-part-time')?'active':''?>">
            <a href="#part-time-edit-tab-1" data-toggle="tab">编辑兼职</a>
        </li>
        <li class="<?= (isset($tab) && $tab === 'add-account')?'active':''?>">
            <a href="#part-time-edit-tab-2" data-toggle="tab">收款账户</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'edit-part-time')?'active in':''?>" id="part-time-edit-tab-1">
            <div class="part-time-panel panel-body">
            <?= $this->render('form',[
                'model'=>$model,
                'formClass'=>'PartTimeForm',
                'action'=>['/crm/part-time/update'],
                'partTime'=>$partTime,
                'show'=>true,
                'enableEditPartTime'=>$enableEditPartTime,
            ])?>
            </div>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-account')?'active in':''?>" id="part-time-edit-tab-2">
            <?php if(isset($enableEditPartTime) && $enableEditPartTime) :?>
            <?= $this->render('@fin/views/account/form',[
                'model'=>$finAccountModel,
                'title'=>'新建账户',
                'formData'=>[
                    'object_uuid'=>$partTime['uuid'],
                ],
                'formClass'=>'FINAccountForm',
                'action'=>['/crm/part-time-fin-account/add'],
            ]);?>
            <?php endif?>
            <?= $this->render('@fin/views/account/list',[
                'finAccountList'=>$finAccountList,
                'delUrl'=>'/crm/part-time-fin-account/del',
                'object_uuid'=>$partTime['uuid'],
                'operator'=>$enableEditPartTime,
            ])?>
        </div>
    </div>
</div>