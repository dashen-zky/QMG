<!-- begin panel -->
<?php if($enableEdit) :?>
<div class="panel-body">
    <?= $this->render('project-touch-record-form',[
        'model'=>$model,
        'formClass'=>'editProjectTouchRecordForm',
        'touchRecord'=>'',
        'contactList'=>$contactList,
        'project_uuid'=>$project_uuid,
    ])?>
</div>
<?php endif;?>
<!-- end panel -->
<!-- begin panel -->
<div class="panel panel-inverse" data-sortable-id="form-stuff-2">
    <div class="panel-heading">
        <div class="panel-heading-btn">
        </div>
        <h4 class="panel-title">跟进记录</h4>
    </div>
    <div class="panel-body">
        <?= $this->render('project-touch-record-list',[
            'touchRecordList'=>$touchRecordList,
            'model'=>$model,
        ]);?>
    </div>
</div>
<!-- end panel -->