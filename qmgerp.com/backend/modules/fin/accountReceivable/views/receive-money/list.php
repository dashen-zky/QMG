<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use yii\helpers\Url;
use backend\modules\fin\accountReceivable\models\AccountReceivable;
use yii\web\View;
?>
<div class="panel panel-body receive-money-list-panel">
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
    
    $('.receive-money-list-panel').on('click','.showing', function() {
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
                
                // 附件删除js
                modal.on('click','.attachmentDelete',function() {
                    var url = $(this).attr('url');
                    var self = $(this);
                    $.get(
                    url,
                    function(data,status) {
                        if('success' !== status) {
                            return ;
                        }
                        
                        if (data != 1) {
                            return ;
                        }
                        var form = self.parents('form');
                        self.parentsUntil('td').remove();
                        var evidence_img_field = form.find('.evidence-img');
                        var img_name = self.attr('name');
                        var evidence_img = evidence_img_field.find("[name='"+img_name+"']");
                        var cc = evidence_img.parentsUntil('td');
                        cc.remove();
                    });
                });
                        
                $('.AccountReceivable').on('change', '.receive-company-uuid', function() {
                    var self = $(this);
                    
                    var form = self.parents('form');
                    form.find('.account').val('');
                    form.find('.bank-of-deposit').val('');
                    var url = self.attr('url') + '&uuid=' + self.val();
                    $.get(url, function(data, status) {
                        if(status !== 'success') {
                            return ;
                        }
                        
                        data = JSON.parse(data);
                        form.find('.account').val(data.account);
                        form.find('.bank-of-deposit').val(data.bank_of_deposit);
                    })
                });
                
                // 表单数据验证
                $('.AccountReceivable').on('click', '.submit', function() {
                    var validate_url = $(this).attr('validate-url');
                    var form = $(this).parents('form');
                    $.ajax({
                        url: validate_url,
                        type: 'post',
                        data: form.serialize(),
                        success: function (data) {
                            if (data == 1) {
                                form.submit();
                            }
            
                            var error_alert = form.find('.error-alert').css({display:'none'});
                            data = JSON.parse(data);
                            $.each(data, function(key, value) {
                                var error_field = form.find('.'+ key + '_error');
                                error_field.html(value[0]);
                                error_field.css({display:'block'});
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
    'action'=>['/accountReceivable/receive-money/list-filter'],
    'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
]);?>
<table class="table table-striped">
    <thead>
    <tr>
        <th>银行流水号</th>
        <th>付款方</th>
        <th>金额</th>
        <th>录入人</th>
        <th>录入时间</th>
        <th>收款时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($receiveMoneyList['list'] as $item) :?>
        <tr>
            <td>
                <?php if($item['enable'] == AccountReceivable::Enable) :?>
                    <?= $item['bank_series_number']?>
                <?php else:?>
                    <s><?= $item['bank_series_number']?></s>
                <?php endif;?>
            </td>
            <td><?= $item['payment']?></td>
            <td><?= $item['money']?></td>
            <td><?= $item['created_name']?></td>
            <td><?= $item['time'] !='0'?date("Y-m-d", $item['time']):''?></td>
            <td><?= $item['receive_time'] !='0'?date("Y-m-d", $item['receive_time']):''?></td>
            <td>
                <div>
                    <a url="<?= Url::to([
                        '/accountReceivable/receive-money/show',
                        'uuid'=>$item['uuid'],
                    ])?>" class="showing" href="#">查看</a>
                </div>
                <?php if($item['enable'] == AccountReceivable::Enable) :?>
                <div>
                    <a href="<?= Url::to([
                        '/accountReceivable/receive-money/del',
                        'uuid'=>$item['uuid'],
                    ])?>">删除</a>
                </div>
                <?php endif?>
            </td>
        </tr>
    <?php endforeach?>
    </tbody>
</table>
<?php
if(isset($ser_filter) && !empty($ser_filter)) {
    $pageParams = [
        'pagination' => $receiveMoneyList['pagination'],
        'ser_filter'=>$ser_filter,
    ];
} else {
    $pageParams = [
        'pagination' => $receiveMoneyList['pagination'],
    ];
}
?>
<?= MyLinkPage::widget($pageParams); ?>
<?php Pjax::end(); ?>
<?= $this->render('show',[
    'edit'=>true,
])?>
</div>
<!-- end panel -->