<div class="panel panel-body stamp-list-panel">
<?= $this->render('list-filter-form',[
    'action'=>['/stamp/import-stamp/list-filter'],
]);?>
<div class="list">
    <?= $this->render('list',[
        'stampList'=>$stampList,
    ])?>
</div>
<?= $this->render('show',[
    'edit'=>true,
])?>
<?= $this->render('@webroot/../views/site/confirm')?>
</div>
<?php
$Js = <<<Js
$(function() {
    // 查看发票详情
    $('.stamp-list-panel').on('click','.show-stamp', function() {
        var self = $(this);
        var url = self.attr('url');
        $.get(
        url,
        function(data, status) {
            if(status === 'success') {
                var panel = self.parents('.panel-body');
                var modal = panel.find('.stamp-show-modal');
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
                        if('success' == status) {
                            if(data) {
                                self.parentsUntil('td').remove();
                            }
                        }
                    });
                 });
            }
        });
    });
    
    // 作废
    $('.stamp-list-panel').on('click','.disable', function() {
        var url = $(this).attr('url');
        var confirm_modal = $(this).parents('.panel-body').find('.confirm-modal');
        confirm_modal.find('.confirm').attr('href', url);
        confirm_modal.modal('show');
    });
});
Js;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
