<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>'新建合同模板',
])?>
<?= $this->render('form',[
    'model'=>$model,
    'formClass'=>'ContractTemplateForm',
    'action'=>['/fin/contract-template/add'],
])?>
<?= $this->render('@webroot/../views/site/panel-footer')?>
