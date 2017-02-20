<?php use yii\helpers\Json;?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-supplier',
    'menu-2-supplier',
    'menu-3-supplier',
])?>'>
<?php
use backend\modules\crm\models\supplier\model\SupplierConfig;
?>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (isset($tab) && $tab === 'edit-supplier')?'active':''?>">
            <a href="#supplier-edit-tab-1" data-toggle="tab">供应商详情</a>
        </li>
        <li class="<?= (isset($tab) && $tab === 'contact-list')?'active':''?>">
            <a href="#supplier-edit-tab-5" data-toggle="tab">联系人</a>
        </li>
        <li class="<?= (isset($tab) && $tab === 'add-account')?'active':''?>">
            <a href="#supplier-edit-tab-2" data-toggle="tab">收款账户</a>
        </li>
        <?php if($enableEditSupplier) :?>
        <li class="<?= (isset($tab) && $tab === 'add-contract')?'active':''?>">
            <a href="#supplier-edit-tab-3" data-toggle="tab">添加合同</a>
        </li>
        <?php endif?>
        <li class="<?= (isset($tab) && $tab === 'contract-list')?'active':''?>">
            <a href="#supplier-edit-tab-4" data-toggle="tab">合同列表</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'edit-supplier')?'active in':''?>" id="supplier-edit-tab-1">
            <div class="panel-body supplier-panel">
            <?= $this->render('form',[
                'model'=>$model,
                'formClass'=>'SupplierForm',
                'action'=>['/crm/supplier/update'],
                'supplier'=>$formData['supplier'],
                'show'=>true,
                'contactModel'=>$contactModel,
                'enableEditSupplier'=>$enableEditSupplier,
                'contactList'=>$formData['contactList'],
            ])?>
            </div>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'contact-list')?'active in':''?>" id="supplier-edit-tab-5">
            <!-- begin panel -->
            <?php if($enableEditSupplier) :?>
            <?= $this->render('/contact-tab/add',[
                'model'=>$contactModel,
                'addAction'=>['/crm/supplier-contact/add'],
                'formData'=>[
                    'object_uuid'=>$formData['supplier']['uuid'],
                ]
            ])?>
            <?php endif?>
            <?= $this->render('/contact-tab/list',[
                'model'=>$contactModel,
                'contactList'=>$formData['contactList'],
                'object_uuid'=>$formData['supplier']['uuid'],
                'updateAction'=>['/crm/supplier-contact/update'],
                'editAction'=>'/crm/supplier-contact/edit',
                'delAction'=>'/crm/supplier-contact/del',
                'formData'=>[
                    'object_uuid'=>$formData['supplier']['uuid'],
                ],
                'operator'=>$enableEditSupplier,
            ])?>
            <!-- end panel -->
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-account')?'active in':''?>" id="supplier-edit-tab-2">
            <?php if($enableEditSupplier) :?>
            <?= $this->render('@fin/views/account/form',[
                'model'=>$finAccountModel,
                'title'=>'新建账户',
                'formData'=>[
                    'object_uuid'=>$formData['supplier']['uuid'],
                ],
                'formClass'=>'FINAccountForm',
                'action'=>['/crm/supplier-fin-account/add'],
            ]);?>
            <?php endif?>
            <?= $this->render('@fin/views/account/list',[
                'finAccountList'=>$finAccountList,
                'delUrl'=>'/crm/supplier-fin-account/del',
                'object_uuid'=>$formData['supplier']['uuid'],
                'operator'=>$enableEditSupplier,
            ])?>
        </div>
        <?php if($enableEditSupplier) :?>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-contract')?'active in':''?>" id="supplier-edit-tab-3">
            <?= $this->render('/supplier-contract/form',[
                'formClass'=>'SupplierContractForm',
                'model'=>$contractForm,
                'supplierContract'=>[
                    'supplier_uuid'=>$formData['supplier']['uuid'],
                    'supplier_name'=>$formData['supplier']['name'],
                    'supplier_manager_name'=>
                        isset($formData['supplier']['supplier_manager_name'])?$formData['supplier']['supplier_manager_name']:'',
                    'code'=>$model->contract_code,
                    'type'=>SupplierConfig::NormalContract,
                    'duty_name'=>Yii::$app->user->getIdentity()->getEmployeeName(),
                ],
                'show'=>false,
                'templateList'=>$templateList,
                'action'=>['/crm/supplier-contract/add'],
            ])?>
        </div>
        <?php endif?>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'contract-list')?'active in':''?>" id="supplier-edit-tab-4">
            <?= $this->render('/supplier-contract/list',[
                'operator'=>$enableEditSupplier,
                'contractList'=>$contractList,
                'model'=>$contractForm,
                'object_uuid'=>$formData['supplier']['uuid'],
            ])?>
        </div>
    </div>
</div>