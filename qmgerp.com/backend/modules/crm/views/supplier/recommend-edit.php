<?php use yii\helpers\Json;?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-com',
    'menu-2-add-supplier'
])?>'>
<?php
use backend\modules\crm\models\supplier\model\SupplierConfig;
?>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (isset($tab) && $tab === 'edit-supplier')?'active':''?>">
            <a href="#supplier-edit-tab-1" data-toggle="tab">供应商详情</a>
        </li>
        <?php if($formData['supplier']['allocate'] == SupplierConfig::UnAllocate) :?>
        <li class="<?= (isset($tab) && $tab === 'contact-list')?'active':''?>">
            <a href="#supplier-edit-tab-5" data-toggle="tab">联系人</a>
        </li>
        <?php endif?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'edit-supplier')?'active in':''?>" id="supplier-edit-tab-1">
            <?= $this->render('@webroot/../views/site/panel-header',[
                'title'=>'供应商详情',
                'panelClass'=>'supplier-panel'
            ])?>
            <?= $this->render('form',[
                'model'=>$model,
                'formClass'=>'SupplierForm',
                'action'=>['/crm/supplier/update'],
                'supplier'=>$formData['supplier'],
                'show'=>true,
                // 被分配了的供应商, 在日常管理里面是不能被编辑的
                'enableEdit'=>($formData['supplier']['allocate'] == SupplierConfig::UnAllocate),
                'contactModel'=>$contactModel,
                'contactList'=>$formData['contactList'],
                'backUrl'=>Json::encode([
                    '/crm/supplier/recommend',
                    'tab'=>'list',
                ]),
            ])?>
            <?= $this->render('@webroot/../views/site/panel-footer')?>
        </div>
        <?php if($formData['supplier']['allocate'] == SupplierConfig::UnAllocate) :?>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'contact-list')?'active in':''?>" id="supplier-edit-tab-5">
            <!-- begin panel -->
            <?= $this->render('/contact-tab/add',[
                'model'=>$contactModel,
                'addAction'=>['/crm/supplier-contact/recommend-add'],
                'formData'=>[
                    'object_uuid'=>$formData['supplier']['uuid'],
                ],
            ])?>

            <?= $this->render('/contact-tab/list',[
                'model'=>$contactModel,
                'contactList'=>$formData['contactList'],
                'object_uuid'=>$formData['supplier']['uuid'],
                'updateAction'=>['/crm/supplier-contact/recommend-update'],
                'editAction'=>'/crm/supplier-contact/edit',
                'delAction'=>'/crm/supplier-contact/recommend-del',
                'formData'=>[
                    'object_uuid'=>$formData['supplier']['uuid'],
                ],
            ])?>
            <!-- end panel -->
        </div>
        <?php endif?>
    </div>
</div>