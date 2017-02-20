<!-- begin panel -->
<div class="panel-body">
    <?= $this->render('position-form',[
        'model'=>$model,
        'action'=>['/hr/position/add'],
        'formData'=>$formData,
    ])?>
</div>
<!-- end panel -->  
