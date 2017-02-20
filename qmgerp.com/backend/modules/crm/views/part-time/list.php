<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use yii\helpers\Url;
use backend\models\ListIndex;
use backend\modules\rbac\model\PermissionManager;
?>
<!-- begin panel -->
<div class="part-time-list panel-body">
<?php Pjax::begin(); ?>
<?php
$JS = <<<JS
$(function() {
    $('.part-time-list').on('click','.show-part-time',function() {
        var url = $(this).attr('url');
        window.open(url); 
    });
    
        // 兼职付款列表
    $('.part-time-list').on('click','.payment-list',function() {
        var url = $(this).attr('url');
        var self = $(this);
        $.get(
        url,
        function(data, status) {
          if(status !== 'success') {
                return ;
          }
          
          var modal = self.parents('.panel-body').find('.payment-list-modal');
          modal.find('.modal-body').html(data);
          modal.modal('show');
        });
    });
    
       // 查看已发票记录
    $('.part-time-list').on('click','.stamp-list',function() {
        var self = $(this);
        var url = self.attr('url');
        $.get(
        url,
        function(data, status) {
            if(status == 'success') {
                var modal = self.parents('.panel-body').find(".stamp-list-modal");
                modal.find('.modal-body').html(data);
                modal.modal('show');
                
                modal.on('click','.show-import-stamp', function() {
                    modal.modal('hide');
                    var self = $(this);
                    var url = self.attr('url');
                    $.get(
                    url,
                    function(data, status) {
                        if(status === 'success') {
                            var modal = self.parents('.panel-body').find('.stamp-show-modal');
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
})
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>
<?php if(!isset($recommend) || !$recommend) :?>
<?= $this->render('list-filter-form',[
    'action'=>['/crm/part-time/list-filter'],
    'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
    'model'=>$model,
]);?>
<?php endif?>
<table class="table table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th>名字</th>
        <th>状态</th>
        <th>性别</th>
        <th>职位</th>
        <th>联系方式</th>
        <th>管理者</th>
        <th>最近付款时间</th>
        <th>审核状态</th>
        <?php if(isset($operator)) {?>
            <th>操作</th>
        <?php }?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($partTimeList['list'] as $item) :?>
        <tr>
            <td><?= \backend\modules\crm\models\part_time\model\PartTimeForm::codePrefix.$item['code']?></td>
            <?php
            $showUrl = (isset($recommend) && $recommend) ?
                Url::to([
                    '/crm/part-time/recommend-edit',
                    'uuid'=>$item['uuid'],
                    'tab'=>'edit-part-time',
                ])
                :
                Url::to([
                    '/crm/part-time/edit',
                    'uuid'=>$item['uuid'],
                    'tab'=>'edit-part-time',
                ]);?>
            <td><a url="<?= $showUrl?>" href="#" class="show-part-time"><?= $item['name']?></a></td>
            <td><?= $model->config->getAppointed('status',$item['status'])?></td>
            <td><?= \backend\models\BaseForm::getGender($item['gender'])?></td>
            <td><?= $model->config->getAppointed('position',$item['position'])?></td>
            <td>
                电话:<?= $item['phone']?> <br>
                qq:<?= $item['qq']?><br>
                微信:<?= $item['wechat']?>
            </td>
            <td>
                <?= $item['manager_name']?>
            </td>
            <td></td>
            <td><?= $model->config->getAppointed('check_status',$item['check_status'])?></td>
            <?php if(isset($operator)) {?>
                <td>
                    <div class="btn-group m-r-5 m-b-5">
                        <a href="javascript:;" data-toggle="dropdown" class="btn btn-success dropdown-toggle" aria-expanded="false">
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu pull-right">
                <?php if(!isset($recommend) || !$recommend) :?>
                <?php if(Yii::$app->authManager->canAccess(PermissionManager::SupplierAndPartTimeAccess)) :?>
                            <li>
                                <a url="<?= Url::to([
                                    '/crm/part-time/edit',
                                    'uuid'=>$item['uuid'],
                                    'tab'=>'edit-part-time',
                                ])?>" href="#" class="show-part-time">审核</a>
                            </li>
                            <?php endif?>
                        <?php
                        $enableEditPartTime = Yii::$app->authManager->canAccess(PermissionManager::EditSupplierAndPartTime, [
                            'manager_uuid'=>$item['manager_uuid'],
                        ]);
                        if($enableEditPartTime):?>
                            <li><a url="<?= Url::to([
                                    '/crm/part-time/edit',
                                    'uuid'=>$item['uuid'],
                                    'tab'=>'add-account',
                                ])?>" href="#" class="show-part-time">添加收款账户</a></li>
                <?php endif?>
                    <li><a url="<?= Url::to([
                            '/crm/supplier-apply-payment/part-time-payment-list',
                            'uuid'=>$item['uuid'],
                        ])?>" class="payment-list" href="#">付款记录</a></li>
                    <li><a url="<?= Url::to([
                            '/crm/supplier-apply-check-stamp/checked-stamp-list',
                            'uuid'=>$item['uuid'],
                        ])?>" class="stamp-list" href="#">发票记录</a></li>
                <?php endif?>
                        </ul>
                    </div>
                </td>
            <?php }?>
        </tr>
    <?php endforeach?>
    </tbody>
</table>
<?php
if(isset($ser_filter) && !empty($ser_filter)) {
    $pageParams = [
        'pagination' => $partTimeList['pagination'],
        'ser_filter'=>$ser_filter,
    ];
} else {
    $pageParams = [
        'pagination' => $partTimeList['pagination'],
    ];
}
?>
<?= MyLinkPage::widget($pageParams); ?>
<?php Pjax::end(); ?>
<?= $this->render('/supplier-payment/payment-list-modal')?>
<?= $this->render('/supplier-stamp/stamp-list-modal')?>
<?= $this->render('@stamp/views/import-stamp/show')?>
</div>
<!-- end panel -->