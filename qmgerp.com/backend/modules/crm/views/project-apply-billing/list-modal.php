<?php
use yii\widgets\Pjax;
?>
<!-- #modal-without-animation -->
<div class="modal scroll fade apply-billing-list-modal">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4>开票记录</h4>
        </div>
        <?php Pjax::begin(); ?>
        <div class="modal-body">
        </div>
        <?php Pjax::end(); ?>
        <div class="modal-footer">
            <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal">Close</a>
        </div>
    </div>
</div>