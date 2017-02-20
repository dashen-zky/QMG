<?php
use yii\widgets\Pjax;
?>
<!-- #modal-without-animation -->
<div class="modal scroll fade supplier-union-part-time-list">
    <div>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">供应商/兼职列表</h4>
            </div>
            <?php Pjax::begin()?>
            <?php
            $JS = <<<JS
$(function(){
    $('.supplier-union-part-time-list').on('click','.choose', function() {
        var modal = $(this).parents('.modal');
        var checked = modal.find('input[type=radio]:checked');
        var checked_type = checked.siblings('.type').val();
        var checked_uuid = checked.val();
        var checked_name = checked.parents('tr').find('.name').html();
        var field_information = JSON.parse($(this).attr('name'));
        var contianer = $(this).parents('.tab-pane');
        var uuid_filed = null;
        for(var i = 0; i < field_information.length; i++) {
            if(field_information[i][1].indexOf("uuid") > -1) {
                uuid_filed = contianer.find('.'+field_information[i][0]).find('.'+field_information[i][1]);
                uuid_filed.val(checked_uuid);
            } else if(field_information[i][1].indexOf("name") > -1) {
                contianer.find('.'+field_information[i][0]).find('.'+field_information[i][1]).val(checked_name);
           } else {
                contianer.find('.'+field_information[i][0]).find('.'+field_information[i][1]).val(checked_type);
           }
        }
        modal.modal('hide');
        uuid_filed.change();
    });
})
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
            ?>
            <div class="modal-body">
            </div>
            <?php Pjax::end()?>
            <div class="modal-footer">
                <button type="button" name=<?= $fieldInformation?> class="btn btn-primary choose">选择</button>
            </div>
        </div>
    </div>
</div>