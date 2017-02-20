<?php
use yii\helpers\Json;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-customer',
    'menu-2-public-customer'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'edit-customer')?'active':''?>"><a href="#customer-tab-1" data-toggle="tab">客户详情</a></li>
        <?php
        if(Yii::$app->authManager->isAuthor(
            Yii::$app->user->getIdentity()->getId(),
            $formData['publicCustomer']['created_uuid']
        ) || Yii::$app->authManager->checkAccess(
            Yii::$app->user->getIdentity()->getId(),
            'MyCustomerMenu'
        )) :?>
        <li class="<?= (isset($tab) && $tab === 'contact-list')?'active':''?>"><a href="#customer-tab-2" data-toggle="tab">联系人</a></li>
        <?php endif?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'edit-customer')?'active in':''?>" id="customer-tab-1">
            <!-- begin panel -->
                <div class="panel-body customer-panel">
                    <?= $this->render('customer-form',[
                        'model'=>$model,
                        'show'=>true,
                        'action'=>['/crm/public-customer/update'],
                        'contactList'=>$formData['contactList'],
                        'contactModel'=>$contactModel,
                        'formData'=>$formData['publicCustomer'],
                        'requireList'=>$formData['requireList'],
                    ])?>
                    </div>
            <!-- end panel -->
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'contact-list')?'active in':''?>" id="customer-tab-2">
            <!-- begin panel -->
            <?php
            if(Yii::$app->authManager->isAuthor(
                Yii::$app->user->getIdentity()->getId(),
                $formData['publicCustomer']['created_uuid']
            ) || Yii::$app->authManager->checkAccess(
                    Yii::$app->user->getIdentity()->getId(),
                    'MyCustomerMenu'
                )) :?>
            <?= $this->render('/contact-tab/add',[
                'model'=>$contactModel,
                'addAction'=>['/crm/public-customer-contact/add'],
                'formData'=>[
                    'object_uuid'=>$formData['publicCustomer']['uuid'],
                ]
            ])?>
            <?= $this->render('/contact-tab/list',[
                'model'=>$contactModel,
                'contactList'=>$formData['contactList'],
                'object_uuid'=>$formData['publicCustomer']['uuid'],
                'updateAction'=>['/crm/public-customer-contact/update'],
                'editAction'=>'/crm/public-customer-contact/edit',
                'delAction'=>'/crm/public-customer-contact/del',
                'formData'=>[
                    'customer_uuid'=>$formData['publicCustomer']['uuid'],
                    'created_uuid'=>$formData['publicCustomer']['created_uuid'],
                ]
            ])?>
            <!-- end panel -->
            <?php endif?>
        </div>
    </div>
</div>