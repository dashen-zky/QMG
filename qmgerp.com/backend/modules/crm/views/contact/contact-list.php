<div class="panel panel-body" data-sortable-id="table-basic-4">
    <!-- begin panel -->
    <div class="panel-heading">
        <div class="panel-heading-btn">
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></a>
        </div>
    </div>

    <div class="panel-body">
        <?php
        $oldUuids = '';
        if(isset($list['oldUuids'])) {
            $oldUuids = $list['oldUuids'];
            unset($list['oldUuids']);
        }
        ?>
        <?= $this->render('contact-form',[
            'formClass'=>$formClass,
            'model'=>$model,
            'list'=>$list,
            'title'=>'联系人',
            'oldUuids'=>$oldUuids,
            'type'=>$type,
        ])?>
    </div>
    <!-- end panel -->
</div>