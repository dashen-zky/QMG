<?php
use yii\helpers\Url;
use backend\modules\crm\models\project\record\ProjectMediaBriefConfig;
?>
<table class="table">
    <thead>
    <tr>
        <th>#</th>
        <th>标题</th>
        <th>创建人</th>
        <th>状态</th>
        <th>项目</th>
        <th>项目经理</th>
        <th>客户</th>
        <th>销售</th>
        <?php if($enableEdit) :?>
        <th>操作</th>
        <?php endif;?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($briefList['list'] as $item) :?>
        <tr>
            <td><?= $item['id']?></td>
            <td>
                <a url="<?= Url::to([
                    '/crm/project-media-brief/show',
                    'uuid'=>$item['uuid'],
                    'edit'=>true,
                ])?>" class="show" href="#">
                    <?= $item['title']?>
                </a>
            </td>
            <td><?= $item['created_name']?></td>
            <td><?= ProjectMediaBriefConfig::getAppointed('status', $item['status'])?></td>
            <td><?= $item['project_name']?></td>
            <td><?= $item['project_manager_name']?></td>
            <td><?= $item['customer_name']?></td>
            <td><?= $item['sales_name']?></td>
            <?php if($enableEdit) :?>
            <td>
                <?php if(in_array($item['status'], [
                    ProjectMediaBriefConfig::StatusApplying
                ])) :?>
                    <a href="<?= Url::to([
                        '/crm/project-media-brief/del',
                        'uuid'=>$item['uuid'],
                        'project_uuid'=>$item['project_uuid']
                    ])?>">删除</a>
                <?php endif;?>
            </td>
            <?php endif;?>
        </tr>
    <?php endforeach?>
    </tbody>
</table>


