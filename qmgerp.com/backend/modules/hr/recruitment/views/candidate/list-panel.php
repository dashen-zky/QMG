<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use yii\helpers\Url;
use backend\modules\hr\recruitment\models\ApplyRecruitConfig;
$config = new ApplyRecruitConfig();
?>
<div class="panel candidate-list">
<?php
$Js = <<<JS
$(function() {
$('.candidate-list').on('click','.show', function() {
    var self = $(this);
    $.get(self.attr('url'), function(data, status) {
        if(status !== 'success') {
            return ;
        }
        
        var modal = self.parents('.candidate-list').find('.candidate-show');
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
        
        var form = modal.find('.CandidateForm');
        form.on('click', '.submit', function() {
            var form = $(this).parents('.CandidateForm');
            var url = $(this).attr('url') + '&phone=' + form.find('.phone').val();
            $.get(url, function(data, status) {
                if ('success' !== status) {
                    return ;
                }
                
                var uuid = form.find('.uuid').val();
                if (data != 1 && data !== uuid) {
                    form.find('.phone-error').css('display','block');
                    return false;
                }
                
                form.submit();
            });
        })
    });
});
});
JS;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
    <?= $this->render('list-filter-form',[
        'action'=>['/recruitment/candidate/list-filter'],
    ]);?>
    <div class="list">
    <?= $this->render('list',[
        'candidateList'=>$candidateList,
        'ser_filter'=>$ser_filter,
    ])?>
    </div>
    <?= $this->render('show-modal')?>
</div>
