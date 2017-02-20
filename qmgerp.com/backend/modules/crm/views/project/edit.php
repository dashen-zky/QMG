<?php

use yii\helpers\Json;
use backend\modules\crm\models\project\record\ProjectContractMap;
use backend\modules\crm\models\touchrecord\TouchRecord;
use backend\modules\crm\models\project\record\ProjectBrief;
use backend\modules\crm\models\project\record\ProjectMediaBrief;
// 合同
$contract = new ProjectContractMap();
$brief = new ProjectBrief();
use backend\modules\rbac\model\PermissionManager;

$enableEdit = Yii::$app->authManager->checkAccess(
    Yii::$app->user->getIdentity()->getId(),
    PermissionManager::ProjectMenu
);
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-project',
    'menu-2-project'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (isset($tab) && $tab === 'edit-project')?'active':''?>">
            <a href="#project-edit-tab-list" data-toggle="tab">项目</a>
        </li>
        <li class="<?= (isset($tab) && $tab === 'add-touch-record')?'active':''?>">
            <a href="#project-edit-tab-touch-record" data-toggle="tab">跟进记录</a>
        </li>
        <?php if($enableEdit) :?>
        <li class="<?= (isset($tab) && $tab === 'add-contract')?'active':''?>">
            <a href="#project-edit-tab-create-contract" data-toggle="tab">创建合同</a>
        </li>
        <?php endif;?>
        <li class="<?= (isset($tab) && $tab === 'contract-list')?'active':''?>">
            <a href="#project-edit-tab-contract-list" data-toggle="tab">合同列表</a>
        </li>
        <?php if($enableEdit) :?>
        <li class="<?= (isset($tab) && $tab === 'add-brief')?'active':''?>">
            <a href="#project-edit-tab-add-brief" data-toggle="tab">添加brief</a>
        </li>
        <?php endif;?>
        <li class="<?= (isset($tab) && $tab === 'brief-list')?'active':''?>">
            <a href="#project-edit-tab-brief-list" data-toggle="tab">brief列表</a>
        </li>
        <?php if($enableEdit) :?>
        <li class="<?= (isset($tab) && $tab === 'add-media-brief')?'active':''?>">
            <a href="#project-edit-tab-add-media-brief" data-toggle="tab">添加媒介brief</a>
        </li>
        <?php endif;?>
        <li class="<?= (isset($tab) && $tab === 'media-brief-list')?'active':''?>">
            <a href="#project-edit-tab-media-brief-list" data-toggle="tab">媒介brief列表</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'edit-project')?'active in':''?>" id="project-edit-tab-list">
            <?= $this->render('edit-project-panel',[
                'model'=>$model,
                'formData'=>$formData,
                'enableEdit'=>$enableEdit,
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-touch-record')?'active in':''?>" id="project-edit-tab-touch-record">
            <?= $this->render('/touch-record/project-touch-record-panel',[
                'model'=>$touchRecordModel,
                'contactList'=>$formData['contact_list'],
                'project_uuid'=>$formData['uuid'],
                'enableEdit'=>$enableEdit,
                'touchRecordList'=>isset($touchRecordList)
                    ?$touchRecordList:(new TouchRecord())->getRecordFromObjectUuid($formData['uuid'],'project'),
            ])?>
        </div>
        <?php if($enableEdit) :?>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-contract')?'active in':''?>" id="project-edit-tab-create-contract">
            <?= $this->render('/project-contract/form',[
                'formClass'=>'ProjectContractForm',
                'model'=>$contractForm,
                'projectContract'=>[
                    'project_uuid'=>$formData['uuid'],
                    'project_name'=>$formData['name'],
                    'customer_name'=>$formData['customer_name'],
                    'project_manager_name'=>$formData['project_manager_name'],
                    'sales_name'=>$formData['sales_name'],
                    'code'=>$contractForm->code,
                    'type'=>'P',
                    'duty_name'=>Yii::$app->user->getIdentity()->getEmployeeName(),
                ],
                'show'=>false,
                'action'=>['/crm/project-contract/add'],
            ])?>
        </div>
        <?php endif;?>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'contract-list')?'active in':''?>" id="project-edit-tab-contract-list">
            <?= $this->render('/project-contract/list',[
                'enableEdit'=>$enableEdit,
                'contractList'=>isset($contractList)?$contractList:$contract->contractListByProjectUuid($formData['uuid']),
            ])?>
        </div>
        <?php if($enableEdit) :?>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-brief')?'active in':''?>" id="project-edit-tab-add-brief">
            <?= $this->render('/project-brief/add', [
                'formData'=> [
                    'project_uuid'=>$formData['uuid'],
                    'project_name'=>$formData['name'],
                    'customer_name'=>$formData['customer_name'],
                    'project_manager_name'=>$formData['project_manager_name'],
                    'sales_name'=>$formData['sales_name'],
                    'contact_name'=>isset($formData['project_contact_name'])?
                        $formData['project_contact_name']:'',
                    'project_member_name'=>isset($formData['project_member_name'])?$formData['project_member_name']:'',
                ],
            ])?>
        </div>
        <?php endif;?>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'brief-list')?'active in':''?>" id="project-edit-tab-brief-list">
            <?= $this->render('/project-brief/list',[
                'ser_filter'=>isset($brief_ser_filter)?$brief_ser_filter:'',
                'enableEdit'=>$enableEdit,
                'briefList'=>isset($briefList)?$briefList:$brief->getBriefListByProjectUuid($formData['uuid']),
            ])?>
        </div>
        <?php if($enableEdit) :?>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-media-brief')?'active in':''?>" id="project-edit-tab-add-media-brief">
            <?= $this->render('/project-media-brief/add', [
                'formData'=> [
                    'project_uuid'=>$formData['uuid'],
                    'project_name'=>$formData['name'],
                    'customer_name'=>$formData['customer_name'],
                    'project_manager_name'=>$formData['project_manager_name'],
                    'sales_name'=>$formData['sales_name'],
                    'contact_name'=>isset($formData['project_contact_name'])?
                        $formData['project_contact_name']:'',
                    'project_member_name'=>isset($formData['project_member_name'])?$formData['project_member_name']:'',
                ],
            ])?>
        </div>
        <?php endif;?>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'media-brief-list')?'active in':''?>" id="project-edit-tab-media-brief-list">
            <?= $this->render('/project-media-brief/list-panel',[
                'enableEdit'=>$enableEdit,
                'briefList'=>isset($briefList)?$briefList:(new ProjectMediaBrief())->getBriefListByProjectUuid($formData['uuid']),
            ])?>
        </div>
    </div>
</div>