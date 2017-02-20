<div class="part-time-panel panel-body">
<?= $this->render('form',[
    'model'=>$model,
    'formClass'=>'PartTimeForm',
    'action'=>['/crm/part-time/increase'],
    'partTime'=>$partTime,
    'show'=>false,
    'backUrl'=>isset($backUrl)?$backUrl:'',
])?>
</div>
