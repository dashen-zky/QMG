<!-- #modal-without-animation -->
<div class="modal fade error-modal" style="width: 35%; margin: 80px auto;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body" style="color: red">
            <?= isset($message)?$message:null?>
        </div>
        <div class="modal-footer">
            <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal">Close</a>
        </div>
    </div>
</div>
