<!-- #modal-without-animation -->
<div class="modal fade role-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">角色</h4>
            </div>
            <div class="modal-body">
                <?= $this->render('form',[
                    'action'=>$action,
                    'formData'=>$formData,
                    'show'=>$show,
                ])?>
            </div>
            <div class="modal-footer">
                <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal">Close</a>
            </div>
        </div>
    </div>
</div>