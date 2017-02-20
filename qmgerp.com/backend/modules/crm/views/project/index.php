<?php 
use yii\helpers\Json;
use backend\modules\crm\models\project\record\ProjectContractMap;
use backend\modules\crm\models\project\record\ProjectApplyStamp;
use backend\modules\crm\models\project\record\ProjectTouchRecordMap;
use backend\modules\crm\models\project\record\Project;
use backend\modules\crm\models\project\model\ProjectForm;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-project',
    'menu-2-project'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= !isset($tab) || $tab == 'list'?'active':''?>"><a href="#project-list" data-toggle="tab">我的项目</a></li>
        <li class="<?= isset($tab) && $tab == 'contract-list'?'active':''?>"><a href="#project-contract-list" data-toggle="tab">我的合同</a></li>
        <li class="<?= isset($tab) && $tab == 'stamp-list'?'active':''?>"><a href="#project-stamp-list" data-toggle="tab">我的开票</a></li>
        <li class="<?= isset($tab) && $tab == 'touch-record-list'?'active':''?>"><a href="#project-touch-record-list" data-toggle="tab">跟进记录</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= !isset($tab) || $tab == 'list'?'active in':''?>" id="project-list">
            <?= $this->render('list',[
                'title'=>'我的项目',
                'projectList' => isset($projectList)?$projectList:(new Project())->myProjectList(),
                'model'=>isset($model)?$model:new ProjectForm(),
                'operator'=>true,
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
                'filter_action'=>['/crm/project/list-filter'],
            ])?>
        </div>
        <div class="tab-pane fade <?= isset($tab) && $tab == 'contract-list'?'active in':''?>" id="project-contract-list">
            <?= $this->render('/project-contract/list',[
                'contractList'=>isset($contractList)?$contractList:(new ProjectContractMap())->myProjectContractList(),
                'filter'=>true,
                'ser_filter'=>isset($contract_list_ser_filter)?$contract_list_ser_filter:'',
                'back_url'=>\yii\helpers\Url::to([
                    '/crm/project/index',
                    'tab'=>'contract-list',
                ]),
            ])?>
        </div>
        <div class="tab-pane fade <?= isset($tab) && $tab == 'stamp-list'?'active in':''?>" id="project-stamp-list">
            <?= $this->render('/project-apply-billing/my-stamp-list',[
                'ser_filter'=>isset($stamp_list_ser_filter)?$stamp_list_ser_filter:'',
                'projectApplyStampList'=>isset($projectApplyStampList)?$projectApplyStampList:(new ProjectApplyStamp())->myStampList(),
            ])?>
        </div>
        <div class="tab-pane fade <?= isset($tab) && $tab == 'touch-record-list'?'active in':''?>" id="project-touch-record-list">
            <?= $this->render('/project-touch-record/touch-record-list', [
                'touchRecordList'=>isset($touchRecordList)?$touchRecordList:(new ProjectTouchRecordMap())->allTouchRecord(),
                'ser_filter'=>isset($touch_record_list_ser_filter)?$touch_record_list_ser_filter:'',
            ])?>
        </div>
    </div>
</div>