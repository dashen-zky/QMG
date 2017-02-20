<div class="panel media-brief-list">
<?php
$Js = <<<JS
$(function() {
$('.media-brief-list').on('click','.show', function() {
    var self = $(this);
    $.get(self.attr('url'), function(data, status) {
        if(status !== 'success') {
            return ;
        }
        
        var modal = self.parents('.media-brief-list').find('.show-modal');
        modal.find('.modal-body').html(data);
        modal.modal('show');
        
        modal.on('click','.editForm',function() {
            modal.find('.enableEdit').attr("disabled",false);
            modal.find('.displayBlockWhileEdit').css('display','block');
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
    });
});
});
JS;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
    <div class="list">
        <?= $this->render('list',[
            'briefList'=>$briefList,
            'enableEdit'=>$enableEdit,
        ])?>
    </div>
    <?= $this->render('show-modal')?>
    <?= $this->render('refuse-reason', [
        'action'=>['/crm/project-media-brief/assess-refused']
    ])?>
</div>
