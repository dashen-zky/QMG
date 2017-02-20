<!-- begin panel -->
<div class="panel-body project-add-panel">
    <?= $this->render('project-form',[
        'model'=>$model,
        'show'=>false,
        'action'=>['/crm/project/add'],
        'formData'=>$formData,
        'formClass'=>'ProjectForm',
        'action'=>['/crm/project/add'],
    ])?>
</div>
<!-- end panel -->