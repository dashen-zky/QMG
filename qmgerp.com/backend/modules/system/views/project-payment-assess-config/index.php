<?php
use yii\helpers\Json;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-config',
    'menu-2-payment-assess',
    'menu-3-project'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'step-2')?'active':''?>"><a href="#project-payment-assess-config-tab-2" data-toggle="tab">二级审批配置</a></li>
        <li class="<?= (isset($tab) && $tab === 'step-3')?'active':''?>"><a href="#project-payment-assess-config-tab-3" data-toggle="tab">三级审批配置</a></li>
        <li class="<?= (isset($tab) && $tab === 'step-4')?'active':''?>"><a href="#project-payment-assess-config-tab-4" data-toggle="tab">四级审批配置</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'step-2')?'active in':''?>" id="project-payment-assess-config-tab-2">
            <?= $this->render('/payment-assess-config/add',[
                'action'=>['/system/project-payment-assess-config/add'],
                'entrance'=>'project',
                'step'=>'step-2',
                'choose_condition_type_url'=>'/system/project-payment-assess-config/choose-condition-type',
            ])?>
            <?= $this->render('/payment-assess-config/list',[
                'config'=>$config,
                'step'=>'step-2',
                'itemDel'=>'/system/project-payment-assess-config/del',
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'step-3')?'active in':''?>" id="project-payment-assess-config-tab-3">
            <?= $this->render('/payment-assess-config/add',[
                'action'=>['/system/project-payment-assess-config/add'],
                'entrance'=>'project',
                'step'=>'step-3',
                'choose_condition_type_url'=>'/system/project-payment-assess-config/choose-condition-type',
            ])?>
            <?= $this->render('/payment-assess-config/list',[
                'config'=>$config,
                'step'=>'step-3',
                'itemDel'=>'/system/project-payment-assess-config/del',
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'step-4')?'active in':''?>" id="project-payment-assess-config-tab-4">
            <?= $this->render('/payment-assess-config/add',[
                'action'=>['/system/project-payment-assess-config/add'],
                'entrance'=>'project',
                'step'=>'step-4',
                'choose_condition_type_url'=>'/system/project-payment-assess-config/choose-condition-type',
            ])?>
            <?= $this->render('/payment-assess-config/list',[
                'config'=>$config,
                'step'=>'step-4',
                'itemDel'=>'/system/project-payment-assess-config/del',
            ])?>
        </div>
        <div style="clear:left;"></div>
    </div>
</div>
