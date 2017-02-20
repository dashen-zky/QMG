<!-- #modal-without-animation -->
<div class="modal fade edit-stamp">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">开票信息</h4>
            </div>
            <div class="modal-body">
                <?= $this->render('form',[
                    'action'=>$updateAction,
                    'formData'=>$formData,
                    'model'=>$model,
                    'show'=>true,
                ])?>
            </div>
            <div class="modal-footer">
                <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal">Close</a>
            </div>
        </div>
    </div>
</div>