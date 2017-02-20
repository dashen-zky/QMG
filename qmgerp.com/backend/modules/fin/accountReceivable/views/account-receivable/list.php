<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use yii\helpers\Url;
use backend\modules\crm\models\project\model\ProjectConfig;
$projectConfig = new ProjectConfig();
?>
<!-- begin panel -->
<div class="panel panel-body account-receivable-list-panel">
<?php Pjax::begin(); ?>
<?= $this->render('list-filter-form',[
    'action'=>['/accountReceivable/account-receivable/list-filter'],
    'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
]);?>

<table class="table table-striped">
    <thead>
    <tr>
        <th>编号</th>
        <th class="col-md-1">客户全称</th>
        <th>项目名称</th>
        <th>项目状态</th>
        <th>项目金额</th>
        <th>已收款金额</th>
        <th style="color: red">应收金额</th>
        <th>项目经理</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($projectList['projectList'] as $item) :?>
        <tr>
            <td>
                <input hidden value="<?= $item['uuid']?>" class="uuid">
                <input hidden value="<?= $item['actual_money_amount']?>" class="project-money">
                <input hidden value="<?= $item['received_money']?>" class="received-money">
                <input hidden value="<?= $item['actual_money_amount'] - $item['received_money']?>" class="waiting-receive-money">
                <?= \backend\modules\crm\models\project\model\ProjectForm::codePrefix.$item['code']?>
            </td>
            <td><a url="<?= Yii::$app->urlManager->createUrl([
                    '/crm/private-customer/edit',
                    'uuid'=>$item['customer_uuid']
                ])?>" href="#" class="show">
                    <?= $item['customer_full_name']?>
                </a></td>
            <td>
                <a url="<?= Yii::$app->urlManager->createUrl([
                    '/crm/project/edit',
                    'uuid'=>$item['uuid']
                ])?>" href="#" class="show">
                    <?= $item['name']?>
                </a>
            </td>
            <td><?= $projectConfig->getAppointed('projectStatus',$item['status'])?></td>
            <td><?= $item['actual_money_amount']?></td>
            <td><?= $item['received_money']?></td>
            <td style="color: red"><?= $item['actual_money_amount'] - $item['received_money']?></td>
            <td><?= $item['project_manager_name']?></td>
            <td>
                <?php if (in_array($item['status'], [
                    ProjectConfig::StatusExecuting,
                    ProjectConfig::StatusDoneApplying,
                    ProjectConfig::StatusDone,
                ])) :?>
                <div>
                    <a href="#" class="receiving">收款</a>
                </div>
                <div>
                    <a href="#" class="receive-record" url="<?= Url::to([
                        '/accountReceivable/account-receivable/receive-record-list',
                        'uuid'=>$item['uuid']
                    ])?>">收款记录</a>
                </div>
                <?php endif;?>
            </td>
        </tr>
    <?php endforeach?>
    </tbody>
</table>
<?php
if(isset($ser_filter) && !empty($ser_filter)) {
    $pageParams = [
        'pagination' => $projectList['pagination'],
        'ser_filter'=>$ser_filter,
    ];
} else {
    $pageParams = [
        'pagination' => $projectList['pagination'],
    ];
}
?>
<?= MyLinkPage::widget($pageParams); ?>
<?php Pjax::end(); ?>
<?= $this->render('receiving')?>
<?= $this->render('receive-record')?>
<?= $this->render('/receive-money/show')?>
</div>
<!-- end panel -->
<?php
$JS = <<<JS
$(function() {
    // 收款
    $('.account-receivable-list-panel').on('click','.receiving', function() {
        var panel_body = $(this).parents('.panel-body');
        var modal = panel_body.find('.receiving-modal');
        var tr = $(this).parents('tr');
        var project_uuid = tr.find('.uuid').val();
        var project_money = tr.find('.project-money').val();
        var received_money = tr.find('.received-money').val();
        var waiting_receive_money = tr.find('.waiting-receive-money').val();
        modal.find('.ReceiveMoneyForm')[0].reset();
        modal.find('.project-uuid').val(project_uuid);
        modal.find('.project-money').val(project_money);
        modal.find('.received-money').val(received_money);
        modal.find('.waiting-receive-money').val(waiting_receive_money);
        modal.modal('show');
    });
    // 收款记录
    $('.account-receivable-list-panel').on('click','.receive-record', function() {
        var self = $(this);
        var url = self.attr('url');
        $.get(
        url,
        function(data, status) {
            if(status == 'success') {
                var modal = self.parents('.panel-body').find('.receive-record-show-modal');
                modal.find('.modal-body').html(data);
                var tr = self.parents('tr');
                var project_money = tr.find('.project-money').val();
                var received_money = tr.find('.received-money').val();
                modal.find('.project-money').html(project_money);
                modal.find('.received-money').html(received_money);
                modal.find('.waiting-receive-money').html(parseFloat(project_money-received_money).toFixed(2));
                modal.modal('show');
                
                modal.on('click','.show-receive-money', function() {
                    modal.modal('hide');
                    var self = $(this);
                    var url = self.attr('url');
                    $.get(
                    url,
                    function(data, status) {
                        if(status === 'success') {
                            var modal = self.parents('.panel-body').find('.receive-money-show-modal');
                            modal.find('.modal-body').html(data);
                            modal.modal('show');
                            
                            modal.on('click','.editForm',function() {
                                var enableEditField = modal.find('.enableEdit');
                                enableEditField.attr("disabled",false);
                                enableEditField.css('display','block');
                            });
                        }
                    });
                });
            }
        });
    });
    
    $('.account-receivable-list-panel').on('click','.show',function() {
        var url = $(this).attr('url');
        window.open(url); 
    });
});
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>