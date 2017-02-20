<!-- #modal-without-animation -->
<div class="modal scroll fade stamp-show-modal">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">发票详情</h4>
            <?php if(isset($edit) && $edit) :?>
            <div>
                <a href="javascript:;" class="editForm"
                   style="font-size: 15px; float: right; margin-right: 30px;"><i class="fa fa-2x fa-pencil"></i>编辑</a>
            </div>
            <?php endif;?>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
            <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal">Close</a>
        </div>
    </div>
</div>