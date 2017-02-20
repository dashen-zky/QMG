<?php
use backend\models\AjaxLinkPage;
use yii\helpers\Url;
use backend\modules\crm\models\project\record\ProjectBriefConfig;
?>
<table class="table">
    <thead>
    <tr>
        <th>#</th>
        <th>标题</th>
        <th>提案时间</th>
        <th>创建人</th>
        <th>状态</th>
        <th>项目</th>
        <th>项目经理</th>
        <th>客户</th>
        <th>销售</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($briefList['list'] as $item) :?>
        <tr>
            <td><?= $item['id']?></td>
            <td>
                <a url="<?= Url::to([
                    '/crm/project-brief/show',
                    'uuid'=>$item['uuid'],
                ])?>" class="show" href="#">
                    <?= $item['title']?>
                </a>
            </td>
            <td><?= $item['proposal_time'] != 0 ? date('Y-m-d', $item['proposal_time']) : null?></td>
            <td><?= $item['created_name']?></td>
            <td><?= ProjectBriefConfig::getAppointed('status', $item['status'])?></td>
            <td><?= $item['project_name']?></td>
            <td><?= $item['project_manager_name']?></td>
            <td><?= $item['customer_name']?></td>
            <td><?= $item['sales_name']?></td>
            <td>
                <?php if(in_array($item['status'], [
                    ProjectBriefConfig::StatusApplying
                ])) :?>
                    <a href="<?= Url::to([
                        '/crm/project-brief/assess-succeed',
                        'uuid'=>$item['uuid'],
                    ])?>">通过</a>
                    <a href="#" class="assess-refused" uuid="<?= $item['uuid']?>">不通过</a>
                <?php endif;?>
            </td>
        </tr>
    <?php endforeach?>
    </tbody>
</table>
<?php
if(isset($ser_filter) && !empty($ser_filter)) {
    $pageParams = [
        'pagination' => $briefList['pagination'],
        'ser_filter'=>$ser_filter,
    ];
} else {
    $pageParams = [
        'pagination' => $briefList['pagination'],
    ];
}
?>
<?= AjaxLinkPage::widget($pageParams); ?>
<?php
$Js = <<<Js
$(function() {
    $('.brief-list').on('click','.assess-refused', function() {
        var panel = $(this).parents('.brief-list');
        var modal = panel.find('.refuse-reason-modal');
        modal.find('.uuid').val($(this).attr('uuid'));
        modal.modal('show');
    });

    $('.list .pagination').on('click', 'li', function() {
            pagination($(this));
    });
});
Js;
$this->registerJs($Js, \yii\web\View::POS_END);
?>

