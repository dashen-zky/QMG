<div class="panel brief-list">
    <?php
    $Js = <<<JS
$(function() {
$('.brief-list').on('click','.show', function() {
    var self = $(this);
    $.get(self.attr('url'), function(data, status) {
        if(status !== 'success') {
            return ;
        }
        
        var modal = self.parents('.panel').find('.show-modal');
        modal.find('.modal-body').html(data);
        modal.modal('show');
    });
});
});
JS;
    $this->registerJs($Js, \yii\web\View::POS_END);
    ?>
    <?= $this->render('list-filter-form',[
        'action'=>['/crm/project-brief/assess-list-filter'],
    ])?>
    <div class="list">
        <?= $this->render('assess-list',[
            'briefList'=>$briefList,
            'ser_filter'=>$ser_filter,
        ])?>
    </div>
    <?= $this->render('show-modal')?>
    <?= $this->render('refuse-reason', [
        'action'=>['/crm/project-brief/assess-refused']
    ])?>
</div>
