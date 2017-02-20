<?php
use yii\helpers\Json;
use backend\modules\rbac\model\PermissionManager;

$enableEdit = Yii::$app->authManager->checkAccess(
    Yii::$app->user->getIdentity()->getId(),
    PermissionManager::MyCustomerMenu
);
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-customer',
    'menu-2-private-customer'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'edit-customer')?'active':''?>">
            <a href="#edit-customer-1" data-toggle="tab">客户详情</a>
        </li>
        <li class="<?= (isset($tab) && $tab === 'contact-list')?'active':''?>">
            <a href="#edit-customer-8" data-toggle="tab">联系人</a>
        </li>
        <li class="<?= (isset($tab) && $tab === 'add-touch-record')?'active':''?>">
            <a href="#edit-customer-4" data-toggle="tab">跟进记录</a>
        </li>
        <li class="<?= (isset($tab) && $tab === 'stamp')?'active':''?>">
            <a href="#edit-customer-9" data-toggle="tab">开票信息</a>
        </li>
        <?php if($enableEdit) :?>
        <li class="<?= (isset($tab) && $tab === 'add-project')?'active':''?>">
            <a href="#edit-customer-5" data-toggle="tab">添加项目</a>
        </li>
        <?php endif;?>
        <li class="<?= (isset($tab) && $tab === 'project-list')?'active':''?>">
            <a href="#edit-customer-3" data-toggle="tab">项目列表</a>
        </li>
        <?php if($enableEdit) :?>
        <li class="<?= (isset($tab) && $tab === 'add-contract')?'active':''?>">
            <a href="#edit-customer-6" data-toggle="tab">添加合同</a>
        </li>
        <?php endif;?>
        <li class="<?= (isset($tab) && $tab === 'contract-list')?'active':''?>"><a href="#edit-customer-7" data-toggle="tab">合同列表</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'edit-customer')?'active in':''?>" id="edit-customer-1">
            <?= $this->render('edit-customer-panel',[
                'model'=>$model,
                'action'=>['/crm/private-customer/update'],
                'contactList'=>$formData['contactList'],
                'contactModel'=>$contactModel,
                'enableEdit'=>$enableEdit,
                'formData'=>$formData['privateCustomer'],
                'requireList'=>$formData['requireList'],
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'contact-list')?'active in':''?>" id="edit-customer-8">
            <!-- begin panel -->
            <?php if($enableEdit) :?>
            <?= $this->render('/contact-tab/add',[
                'model'=>$contactModel,
                'addAction'=>['/crm/private-customer-contact/add'],
                'formData'=>[
                    'object_uuid'=>$formData['privateCustomer']['uuid'],
                ]
            ])?>
            <?php endif;?>
            <?= $this->render('/contact-tab/list',[
                'model'=>$contactModel,
                'contactList'=>$formData['contactList'],
                'object_uuid'=>$formData['privateCustomer']['uuid'],
                'updateAction'=>['/crm/private-customer-contact/update'],
                'editAction'=>'/crm/private-customer-contact/edit',
                'delAction'=>'/crm/private-customer-contact/del',
                'operator'=>$enableEdit,
                'formData'=>[
                    'customer_uuid'=>$formData['privateCustomer']['uuid'],
                ]
            ])?>
            <!-- end panel -->
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-touch-record')?'active in':''?>" id="edit-customer-4">
            <?= $this->render('/touch-record/customer-touch-record-panel',[
                'model'=>$touchRecordModel,
                'contactList'=>$contactList,
                'enableEdit'=>$enableEdit,
                'customer_uuid'=>$formData['privateCustomer']['uuid'],
                'touchRecordList'=>$touchRecordList,
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'project-list')?'active in':''?>" id="edit-customer-3">
            <?= $this->render('/project/list',[
                'title'=>'项目列表',
                'projectList'=>$projectList,
                'model'=>$projectModel,
                'customer_uuid'=>$formData['privateCustomer']['uuid'],
                'filter_action'=>['/crm/private-customer/project-list-filter'],
                'ser_filter'=>isset($ser_filter)?$ser_filter:serialize([
                    'uuid'=>$formData['privateCustomer']['uuid'],
                ]),
            ])?>
        </div>
        <?php if($enableEdit) :?>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-project')?'active in':''?>" id="edit-customer-5">
            <?= $this->render('/project/add',[
                'model'=>$projectModel,
                'formData' => [
                    'customer_uuid'=>$formData['privateCustomer']['uuid'],
                    'customer_name'=>$formData['privateCustomer']['name'],
                    'customer_type'=>$formData['privateCustomer']['type'],
                ],
            ])?>
        </div>
        <?php endif;?>
        <?php if($enableEdit) :?>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-contract')?'active in':''?>" id="edit-customer-6">
            <?= $this->render('/customer-contract/form',[
                'formClass'=>'CustomerContractForm',
                'model'=>$customerContractModel,
                'show'=>false,
                'customerContract'=>[
                    'customer_uuid'=>$formData['privateCustomer']['uuid'],
                    'customer_name'=>$formData['privateCustomer']['name'],
                    'sales_name'=>$formData['privateCustomer']['sales_name'],
                    'duty_name'=>Yii::$app->user->getIdentity()->getEmployeeName(),
                    'type'=>$customerContractModel->type,
                    'code'=>$customerContractModel->code,
                ],
                'templateList'=>$templateList,
                'action'=>['/crm/customer-contract/add'],
            ])?>
        </div>
        <?php endif;?>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'contract-list')?'active in':''?>" id="edit-customer-7">
            <?= $this->render('/customer-contract/list',[
                'contractList'=>$contractList,
                'enableEdit'=>$enableEdit,
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'stamp')?'active in':''?>" id="edit-customer-9">
            <?php if($enableEdit) :?>
            <?= $this->render('/customer-stamp/add',[
                'addAction'=>['/crm/customer-stamp/add'],
                'formData'=>[
                    'object_uuid'=>$formData['privateCustomer']['uuid'],
                ],
                'model'=>$stamp,
            ])?>
            <?php endif;?>
            <?= $this->render('/customer-stamp/list',[
                'stampList'=>$stampList,
                'editAction'=>'/crm/customer-stamp/edit',
                'delAction'=>'/crm/customer-stamp/del',
                'updateAction'=>['/crm/customer-stamp/update'],
                'object_uuid'=>$formData['privateCustomer']['uuid'],
                'enableEdit'=>$enableEdit,
                'formData'=>[
                'object_uuid'=>$formData['privateCustomer']['uuid'],
                ],
                'model'=>$stamp,
            ])?>
        </div>
    </div>
</div>