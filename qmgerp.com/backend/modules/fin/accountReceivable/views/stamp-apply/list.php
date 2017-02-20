<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use backend\modules\crm\models\project\model\ProjectForm;
use backend\modules\crm\models\project\record\ProjectApplyStamp;
use yii\helpers\Url;
use yii\web\View;
use backend\modules\fin\stamp\models\StampConfig;
$config = new StampConfig();
?>
<!-- begin panel -->
<div class="project-apply-stamp-list-panel panel panel-body">
<?php Pjax::begin(); ?>
<?php
$Js = <<<Js
$(function() {
$(".datetimepicker").datetimepicker({
        lang:"ch",           //语言选择中文
        format:"Y-m-d",      //格式化日期H:i
        i18n:{
          // 以中文显示月份
          de:{
            months:["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月",],
            // 以中文显示每周（必须按此顺序，否则日期出错）
            dayOfWeek:["日","一","二","三","四","五","六"]
          }
        }
        // 显示成年月日，时间--
    });
    
    // 开票
    $('.project-apply-stamp-list-panel').on('click','.billing', function() {
        var self = $(this);
        $.get(self.attr('url'), function(data, status) {
            if(status !== 'success') {
                return ;
            }
            
            var panel_body = self.parents('.panel-body');
            var modal = panel_body.find('.billing-modal');
            
            var tr = self.parents('tr');
            var project_apply_stamp_uuid = tr.find('.uuid').val();
            var project_apply_stamp_money = tr.find('.money').val();
            var checked_stamp_money = tr.find('.checked-stamp-money').val();
            var remarks = tr.find('.remarks').val();
            var type = tr.find('.type').val();
            var feature = tr.find('.feature').val();
            var wait_checking_stamp_money = tr.find('.wait-checking-stamp-money').val();
            modal.find('.project-apply-stamp-uuid').val(project_apply_stamp_uuid);
            modal.find('.project-apply-stamp-money').val(project_apply_stamp_money);
            modal.find('.checked-stamp-money').val(checked_stamp_money);
            modal.find('.wait-checking-stamp-money').val(wait_checking_stamp_money);
            modal.find('.remarks').val(remarks);
            modal.find('.type').val(type);
            modal.find('.feature').val(feature);
            
            if (data == false || data == '' ||　data == 'null' || data == null) {
                modal.find('.stamp-message').val('');
                modal.modal('show');
                return ;
            }
            
            data = JSON.parse(data);
            var html = '公司名:' + data.company_name + '  ' +
                        '公司地址:' + data.company_address + '  '  +
                        '公司电话:' + data.company_phone + '  '  +
                        '公司税号:' + data.stamp_number + '  '  +
                        '开户行:' + data.bank_of_deposit +  '  '  +
                        '银行账号:' + data.account;
            modal.find('.stamp-message').val(html);
            modal.modal('show');
        })
    });
    
    // 开票记录
    $('.project-apply-stamp-list-panel').on('click','.billing-record', function() {
        var self = $(this);
        var url = self.attr('url');
        $.get(
        url,
        function(data, status) {
            if(status == 'success') {
                var modal = self.parents('.panel-body').find('.billing-record-modal');
                modal.find('.modal-body').html(data);
                var tr = self.parents('tr');
                var money = tr.find('.money').val();
                var checked_stamp_money = tr.find('.checked-stamp-money').val();
                modal.find('.money').html(money);
                modal.find('.checked-stamp-money').html(checked_stamp_money);
                modal.find('.waiting-checked-money').html(parseFloat(money-checked_stamp_money).toFixed(2));
                modal.modal('show');
                
                modal.on('click','.show-export-stamp', function() {
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
});
Js;
$this->registerJs($Js, View::POS_END);
?>
<?= $this->render('list-filter-form',[
    'action'=>['/accountReceivable/stamp-apply/list-filter'],
    'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
]);?>

<table class="table table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th>项目编号</th>
        <th>项目名称</th>
        <th>申请金额</th>
        <th>已开票金额</th>
        <th style="color:red">待开票金额</th>
        <th>申请人</th>
        <th>申请时间</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($projectApplyStampList['list'] as $item) :?>
        <tr>
            <td>
                <input hidden class="feature"
                       value="<?= !empty($item['feature'])?
                           $config->getAppointed('feature', $item['feature']):''?>">
                <input hidden class="type"
                       value="<?= !empty($item['type'])?
                           $config->getAppointed('service_type', $item['type']):''?>">
                <input hidden class="remarks" value="<?= $item['remarks']?>">
                <input hidden class="uuid" value="<?= $item['uuid']?>">
                <input hidden class="money" value="<?= $item['money']?>">
                <input hidden class="checked-stamp-money" value="<?= $item['checked_stamp_money']?>">
                <input hidden class="wait-checking-stamp-money" value="<?= $item['money'] - $item['checked_stamp_money']?>">
                <?= $item['id']?>
            </td>
            <td>
                <?= ProjectForm::codePrefix.$item['project_code']?>
            </td>
            <td><?= $item['project_name']?></td>
            <td><?= $item['money']?></td>
            <td><?= $item['checked_stamp_money']?></td>
            <td style="color:red"><?= $item['money'] - $item['checked_stamp_money']?></td>
            <td><?= $item['created_name']?></td>
            <td><?= $item['created_time'] !='0'?date("Y-m-d", $item['created_time']):''?></td>
            <td><?= ProjectApplyStamp::$status[$item['status']]?></td>
            <td>
            <?php if($item['status'] != ProjectApplyStamp::CheckedStamp) :?>
            <div>
                <a class="billing" href="#" url="<?= Url::to([
                    '/accountReceivable/stamp-apply/load-stamp-message',
                    'uuid'=>$item['stamp_message_uuid'],
                ])?>">开票</a>
            </div>
            <?php endif;?>
            <div>
                <a url="<?= Url::to([
                    '/accountReceivable/stamp-apply/billing-record-list',
                    'uuid'=>$item['uuid'],
                ])?>" href="#" class="billing-record">开票记录</a>
            </div>
            <?php if($item['enable'] == ProjectApplyStamp::Enable) :?>
                <div>
                    <a href="<?= Url::to([
                        '/accountReceivable/stamp-apply/disable',
                        'uuid'=>$item['uuid'],
                    ])?>">打回</a>
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
        'pagination' => $projectApplyStampList['pagination'],
        'ser_filter'=>$ser_filter,
    ];
} else {
    $pageParams = [
        'pagination' => $projectApplyStampList['pagination'],
    ];
}
?>
<?= MyLinkPage::widget($pageParams); ?>
<?php Pjax::end(); ?>
<?= $this->render('billing')?>
<?= $this->render('billing-record')?>
<?= $this->render('@stamp/views/export-stamp/show')?>
</div>
<!-- end panel -->