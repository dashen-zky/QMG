<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3 0003
 * Time: 上午 1:43
 */
?>
<?php
use yii\helpers\Json;
use backend\modules\rbac\model\PermissionManager;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-com',
    'menu-2-regulation'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'list')?'active':''?>"><a href="#regulation-tab-list" data-toggle="tab">文档列表</a></li>
        <?php
        $canAddRegulation = Yii::$app->authManager->canAccess(PermissionManager::AddRegulation);
        if($canAddRegulation) :?>
        <li class="<?= (isset($tab) && $tab === 'add-regulation')?'active':''?>"><a href="#regulation-tab-add" data-toggle="tab">添加文档</a></li>
        <?php endif?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'list')?'active in':''?>" id="regulation-tab-list">
            <?= $this->render('list',[
                'title'=>'文档列表',
                'list'=>$list,
                'operator'=>$canAddRegulation,
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
            ])?>
        </div>
        <?php if($canAddRegulation) :?>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-regulation')?'active in':''?>" id="regulation-tab-add">
            <?php $userName = Yii::$app->user->getIdentity()->getEmployeeName()?>
            <?= $this->render('form',[
                'title'=>'添加文档',
                'action'=>['/daily/regulation/add'],
                'formData'=>[
                    'created_name'=>$userName,
                    'update_name'=>$userName,
                ],
            ])?>
        </div>
        <?php endif?>
    </div>
</div>
