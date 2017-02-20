<!-- begin panel -->
<div class="panel-body customer-panel">
    <?= $this->render('customer-form',[
        'model'=>$model,
        'show'=>false,
        'action'=>['/crm/private-customer/add'],
        'contactList'=>'',
        'formData'=>'',
        'requireList'=>'',
        'contactModel'=>$contactModel,
    ])?>
</div>
<!-- end panel -->