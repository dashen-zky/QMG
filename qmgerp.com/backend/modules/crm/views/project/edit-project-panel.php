<!-- begin panel -->
<div class="panel-body">
    <?= $this->render('project-form',[
        'formClass'=>'ProjectForm',
        'model'=>$model,
        'show'=>true,
        'action'=>['/crm/project/update'],
        'formData'=>$formData,
        'enableEdit'=>$enableEdit,
    ])?>
</div>
<!-- end panel -->