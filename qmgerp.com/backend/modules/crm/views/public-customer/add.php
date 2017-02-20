<!-- begin panel -->
<div class="panel-body customer-panel">
    <?= $this->render('customer-form',[
        'model'=>$model,
        'action'=>['/crm/public-customer/add'],
        'contactList'=>'',
        'formData'=>'',
        'requireList'=>'',
        'contactModel'=>$contactModel,
        'show'=>false,
    ])?>
</div>
<!-- end panel -->