<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use yii\helpers\Url;
use backend\modules\fin\models\contract\ContractConfig;
use backend\modules\rbac\model\PermissionManager;
use backend\modules\crm\models\project\record\Project;
use backend\modules\crm\models\project\model\ProjectConfig;
?>
<!-- begin panel -->
<div class="panel-body project-list-panel">
    <?php Pjax::begin(); ?>
    <?= $this->render('list-filter-form',[
        'action'=>$filter_action,
        'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
        'model'=>$model,
        'customer_uuid'=>isset($customer_uuid)?$customer_uuid:'',
    ]);?>
    <div class="panel-body">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>编号</th>
                <th class="col-md-1">客户简称</th>
                <th>项目名称</th>
                <th>项目状态</th>
                <th>项目金额</th>
                <th>销售</th>
                <th>项目经理</th>
                <th>业务板块</th>
                <?php if(isset($operator)) {?>
                    <th>操作</th>
                <?php }?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($projectList['projectList'] as $item) :?>
                <tr>
                    <td>
                        <input hidden value="<?= $item['uuid']?>" class="project-uuid">
                        <input hidden value="<?= $item['actual_money_amount']?>" class="project-money">
                        <input hidden value="<?= $item['received_money']?>" class="received-money">
                        <input hidden value="<?= $item['checked_stamp_money']?>" class="checked-stamp-money">
                        <?= \backend\modules\crm\models\project\model\ProjectForm::codePrefix.$item['code']?>
                    </td>
                    <td><?= $item['customer_name']?></td>
                    <td>
                        <a url="<?= Yii::$app->urlManager->createUrl([
                            '/crm/project/edit',
                            'uuid'=>$item['uuid']
                        ])?>" href="#" class="show-project"><?php if($item['enable'] == Project::Disable) :?>
                                <s><?= $item['name']?></s>
                            <?php else:?>
                                <?= $item['name']?>
                            <?php endif?></a>
                    </td>
                    <td><?= $model->getAppointed('projectStatus',$item['status'])?></td>
                    <td><?= $item['actual_money_amount']?></td>
                    <td><?= $item['sales_name']?></td>
                    <td><?= $item['project_manager_name']?></td>
                    <td><?= $model->getAppointed('business',$item['business_map_business_id'])?></td>
                    <?php if(isset($operator)) :?>
                        <td>
                            <div class="btn-group m-r-5 m-b-5">
                                <a href="javascript:;" data-toggle="dropdown" class="btn btn-success dropdown-toggle" aria-expanded="false">
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <?php if($item['status'] == ProjectConfig::StatusTouching) :?>
                                    <li>
                                        <a href="#" class="apply-active"
                                           uuid="<?= $item['uuid']?>">申请立项</a></li>
                                    <?php endif;?>
                                    <?php if($item['status'] == ProjectConfig::StatusExecuting) :?>
                                        <li>
                                            <a href="#" class="apply-done"
                                                url="<?= Url::to([
                                                    '/crm/project/apply-done',
                                                    'uuid'=>$item['uuid']
                                                ])?>" validate-url="<?= Url::to([
                                                '/crm/project/apply-done-validate',
                                                'uuid'=>$item['uuid']
                                            ])?>">申请结案</a></li>
                                    <?php endif;?>
                                    <li><a url="<?= Url::to([
                                            '/crm/project/edit',
                                            'uuid'=>$item['uuid'],
                                            'tab'=>'add-touch-record',
                                        ])?>" href="#" class="show-project">添加跟进记录</a></li>
                                    <li><a url="<?= Url::to([
                                            '/crm/project/edit',
                                            'uuid'=>$item['uuid'],
                                            'tab'=>'add-contract',
                                        ])?>" href="#" class="show-project">添加合同</a></li>
                                    <li><a href="#" class="receive-record" url="<?= Url::to([
                                            '/accountReceivable/account-receivable/receive-record-list',
                                            'uuid'=>$item['uuid']
                                        ])?>">收款记录</a></li>
                                    <li><a href="#" class="apply-stamp" url="<?= Url::to([
                                            '/crm/project-apply-stamp/load-stamp-list',
                                            'uuid'=>$item['uuid'],
                                        ])?>">申请开票</a></li>
                                    <li><a href="#" class="apply-billing-record" url="<?= Url::to([
                                            '/crm/project-apply-stamp/billing-record-list',
                                            'uuid'=>$item['uuid']
                                        ])?>">开票记录</a></li>
                                    <li><a url="<?= Url::to([
                                            '/crm/project/edit',
                                            'uuid'=>$item['uuid'],
                                            'tab'=>'add-brief',
                                        ])?>" href="#" class="show-project">提交方案brief</a></li>
                                    <li><a url="<?= Url::to([
                                            '/crm/project/edit',
                                            'uuid'=>$item['uuid'],
                                            'tab'=>'add-media-brief',
                                        ])?>" href="#" class="show-project">提交媒介brief</a></li>
                                    <li><a href="javascript:;" uuid="<?= $item['uuid']?>" class="failed">失败</a></li>
                                    <?php if(Yii::$app->authManager->canAccess(
                                        PermissionManager::DeleteProject
                                    )):?>
                                        <li><a href="<?= Url::to([
                                                '/crm/project/del',
                                                'uuid'=>$item['uuid']
                                            ])?>">删除</a></li>
                                    <?php endif?>
                                </ul>
                            </div>
                        </td>
                    <?php endif?>
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
    </div>
    <?php Pjax::end(); ?>
<?= $this->render('failed')?>
<?= $this->render('@accountReceivable/views/account-receivable/receive-record')?>
<?= $this->render('@accountReceivable/views/receive-money/show')?>
<?= $this->render('/project-apply-billing/apply-stamp')?>
<?= $this->render('/project-apply-billing/list-modal')?>
<?= $this->render('project-active-modal')?>
<?= $this->render('@webroot/../views/site/error-modal')?>
</div>
<!-- end panel -->
<?php
$JS = <<<JS
$(function() {
    $('.project-list-panel').on('click', '.apply-done', function() {
        var self = $(this);
        var validate_url = self.attr('validate-url');
        $.get(validate_url, function(data, status) {
            if(status != 'success') {
                return ;
            }
            
            if(data != 1) {
                var panel = self.parents('.project-list-panel');
                var modal = panel.find('.error-modal');
                modal.find('.modal-body').html(data);
                modal.modal('show');
                return ;
            }
            
            window.location.href = self.attr('url');
        });
    });

    $('.project-list-panel').on('click','.apply-active',function() {
        var panel = $(this).parents('.project-list-panel');
        var uuid = $(this).attr('uuid');
        var modal = panel.find('.project-active-modal');
        modal.find('.submit').attr('uuid', uuid);
        modal.find('.error-message').html('');
        modal.modal('show');
    });
    
    $('.project-list-panel').on('click','.failed',function() {
        var panel = $(this).parents('.project-list-panel');
        var modal = panel.find('.failed-modal');
        modal.find('.uuid').val($(this).attr('uuid'));
        modal.modal('show');
    });

    // 收款记录
    $('.project-list-panel').on('click','.receive-record', function() {
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
                
                // 查看收款记录的具体信息
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
                        }
                    });
                });
            }
        });
    });
    
    $('.project-list-panel').on('click','.show-project',function() {
        var url = $(this).attr('url');
        window.open(url); 
    });
    
    // 申请开票
    $('.project-list-panel').on('click','.apply-stamp', function() {
        var url = $(this).attr('url');
        var self = $(this);
        $.get(url, function(data, status) {
            if(status != 'success') {
                return ;
            }
            var panel_body = self.parents('.panel-body');
            var modal = panel_body.find('.apply-stamp-modal');
            // select-stamp-message
            modal.find('.select-stamp-message').html(data);
            var tr = self.parents('tr');
            var project_money = tr.find('.project-money').val();
            var  project_uuid = tr.find('.project-uuid').val();
            var checked_stamp_money = tr.find('.checked-stamp-money').val();
            modal.find('.project-uuid').val(project_uuid);
            modal.find('.project-money').val(project_money);
            modal.find('.checked-stamp-money').val(checked_stamp_money);
            modal.find('.rest-money').val(parseFloat(project_money-checked_stamp_money).toFixed(2));
            modal.find('.stamp-message-area').val('');
            modal.modal('show');
            
            modal.on('change', '.select-stamp-message select', function() {
                var self = $(this);
                var url = self.attr('url') + '&uuid=' + self.val();
                $.get(url, function(data, status) {
                    if(status !== 'success') {
                        return ;
                    }
                    
                    if (data == false || data == '' ||　data == 'null' || data == null) {
                        modal.find('.stamp-message-area').val('');
                        modal.find('.stamp-message').val('');
                        return ;
                    }
                    modal.find('.stamp-message').val(data);
                    data = JSON.parse(data);
                    var html = '公司名:' + data.company_name + '  ' +
                    '公司地址:' + data.company_address + '  '  +
                    '公司电话:' + data.company_phone + '  '  +
                    '公司税号:' + data.stamp_number + '  '  +
                    '开户行:' + data.bank_of_deposit +  '  '  +
                    '银行账号:' + data.account;
                    modal.find('.stamp-message-area').val(html);
                })
            });
        });
    });
    
    // 开票记录
    $('.project-list-panel').on('click','.apply-billing-record', function() {
        var self = $(this);
        var url = self.attr('url');
        $.get(
        url,
        function(data, status) {
            if(status == 'success') {
                var modal = self.parents('.panel-body').find('.apply-billing-list-modal');
                modal.find('.modal-body').html(data);
                modal.modal('show');
            }
        });
    });
});
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>