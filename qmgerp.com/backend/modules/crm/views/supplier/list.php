<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use yii\helpers\Url;
use backend\modules\rbac\model\PermissionManager;
?>
<!-- begin panel -->
<div class="panel-body supplier-list">
    <?php Pjax::begin(); ?>
<?php
$JS = <<<JS
$(function() {
  $('.supplier-list').on('click','.show-supplier',function() {
        var url = $(this).attr('url');
        window.open(url); 
    });
    
    // 供应商付款列表
  $('.supplier-list').on('click','.payment-list',function() {
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
    $('.supplier-list').on('click','.stamp-list',function() {
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
            'action'=>['/crm/supplier/list-filter'],
            'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
            'model'=>$model,
        ]);?>
    <?php endif?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>供应商名字</th>
                <th>类型</th>
                <th>级别</th>
                <th>性质</th>
                <th>状态</th>
                <th>管理者</th>
                <th>分配状态</th>
                <?php if(isset($operator)) {?>
                    <th>操作</th>
                <?php }?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($supplierList['list'] as $item) :?>
                <tr>
                    <td><?= \backend\modules\crm\models\supplier\model\SupplierForm::codePrefix.$item['code']?></td>
                    <?php
                    // 在不同入口进入到供应商的编辑对应的action是不一样的
                    $showUrl = (isset($recommend) && $recommend) ?
                        Url::to([
                            '/crm/supplier/recommend-edit',
                            'uuid'=>$item['uuid'],
                            'tab'=>'edit-supplier',
                        ])
                        :
                        Url::to([
                            '/crm/supplier/edit',
                            'uuid'=>$item['uuid'],
                            'tab'=>'edit-supplier',
                        ]);
                    ?>
                    <td><a url="<?= $showUrl?>" class="show-supplier" href="#"><?= $item['name']?></a></td>
                    <td><?= $model->config->getAppointed('type',$item['type'])?></td>
                    <td><?= $model->config->getAppointed('level',$item['level'])?></td>
                    <td><?= $model->config->getAppointed('feature',$item['feature'])?></td>
                    <td><?= $model->config->getAppointed('status',$item['status'])?></td>
                    <td><?= $item['manager_name']?></td>
                    <td><?= $model->config->getAppointed('allocate', $item['allocate'])?></td>
                    <?php if(isset($operator)) :?>
                        <td>
                            <div class="btn-group m-r-5 m-b-5">
                                <a href="javascript:;" data-toggle="dropdown" class="btn btn-success dropdown-toggle" aria-expanded="false">
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <?php if(Yii::$app->authManager->canAccess(PermissionManager::SupplierAndPartTimeAccess)) :?>
                                    <li><a url="<?= Url::to([
                                                    '/crm/supplier/edit',
                                                    'uuid'=>$item['uuid'],
                                                    'tab'=>'edit-supplier',
                                                ])?>" class="show-supplier" href="#">审核</a></li>
                                    <?php endif;?>
                                    <?php if(!isset($recommend) || !$recommend) :?>
                                    <?php
                                    $enableEditSupplier = Yii::$app->authManager->canAccess(PermissionManager::EditSupplierAndPartTime, [
                                        'manager_uuid'=>$item['manager_uuid'],
                                    ]);
                                    if($enableEditSupplier):?>
                                        <li><a url="<?= Url::to([
                                                '/crm/supplier/edit',
                                                'uuid'=>$item['uuid'],
                                                'tab'=>'add-account',
                                            ])?>" class="show-supplier" href="#">添加收款账户</a></li>
                                        <li><a url="<?= Url::to([
                                                '/crm/supplier/edit',
                                                'uuid'=>$item['uuid'],
                                                'tab'=>'add-contract',
                                            ])?>" class="show-supplier" href="#">添加合同</a></li>
                                    <?php endif;?>
                                        <li><a url="<?= Url::to([
                                            '/crm/supplier-apply-payment/supplier-payment-list',
                                            'uuid'=>$item['uuid'],
                                        ])?>" class="payment-list" href="#">付款记录</a></li>
                                        <li><a url="<?= Url::to([
                                                '/crm/supplier-apply-check-stamp/checked-stamp-list',
                                                'uuid'=>$item['uuid'],
                                            ])?>" class="stamp-list" href="#">发票记录</a></li>
                                    <?php endif;?>
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
                'pagination' => $supplierList['pagination'],
                'ser_filter'=>$ser_filter,
            ];
        } else {
            $pageParams = [
                'pagination' => $supplierList['pagination'],
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