<?php
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\web\View;
?>
<div class="panel-body payment-list-panel">
<?php Pjax::begin()?>
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
    
    // 查看付款申请详情
    $('.payment-list-panel').on('click', '.payment-show', function() {
        var url = $(this).attr('url');
        var self = $(this);
        $.get(
        url,
        function(data,status) {
            if(status === 'success') {
                var panel = self.parents('.panel-body');
                var modal = panel.find('.payment-show-modal');
                modal.find('.modal-body').html(data);
                modal.modal('show');
            }
        });
    });
    
    // 单笔验票
    $('.payment-list-panel').on('click','.checking-stamp',function() {
        var url = $(this).attr('url');
        var self = $(this);
        $.get(
            url,
            function(data,status) {
                if('success' == status) {
                    var stamp_modal = self.parents('.panel-body').find(".select-stamp-container-modal");
                    stamp_modal.find(".panel-body div.stamp-list").html(data);
                    var uuid = self.parents('tr').find('.payment-uuid').val();
                    var stamp_status = self.parents('tr').find('.stamp-status').val();
                    stamp_modal.find('.StampCheckForm').find('.payment-uuid').val(uuid);
                    stamp_modal.find('.StampCheckForm').find('.stamp-status').val(stamp_status);
                    var checked_uuid = JSON.parse(stamp_modal.find('.checked-uuid').val());
                    var html = '';
                    for(var key in checked_uuid) {
                        html += '<li>' +
                         '<div class="tag">' +
                           '<span class="tag-content">'+key+'</span>' +
                            '<span class="tag-close" id="'+checked_uuid[key]+'">' +
                             '<a href="javascript:;">×</a>' +
                              '</span>' +
                               '</div>' +
                                '</li>';
                    }
                    var stamp_tags = stamp_modal.find('.selected-stamp-tags ul');
                    stamp_tags.html(html);
                    stamp_modal.modal('show');
                    
                    stamp_modal.find('.list .pagination').on('click', 'li', function() {
                        pagination($(this), function() {
                            var tag = stamp_modal.find('.tag .tag-close');
                            $.each(tag, function() {
                                var id = $(this).attr('id');
                                stamp_modal.find('.list #' + id).attr("checked", true);
                            })
                        });
                    });
                }
            });
    });
    
    // 查看已发票记录
    $('.payment-list-panel').on('click','.checked-stamp-list',function() {
        var self = $(this);
        var url = self.attr('url');
        $.get(
        url,
        function(data, status) {
            if(status == 'success') {
                var modal = self.parents('.panel-body').find(".checked-stamp-modal");
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
});
Js;
$this->registerJs($Js, View::POS_END);
?>
<?= $this->render('list-filter-form',[
    'action'=>['/payment/check-stamp/list-filter'],
    'formData'=>isset($ser_filter)?unserialize($ser_filter):[],
]);?>
<div class="payment-list list">
    <?= $this->render('list', [
        'paymentList'=>$paymentList,
        'ser_filter'=>isset($ser_filter)?$ser_filter:null,
    ])?>
</div>
<?php Pjax::end()?>
<?= $this->render('show')?>
<?= $this->render('@stamp/views/import-stamp/stamp-select-list-panel')?>
<?= $this->render('checked-stamp')?>
<?= $this->render('@stamp/views/import-stamp/show')?>
</div>
