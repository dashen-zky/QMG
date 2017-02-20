<!-- begin panel -->
<div class="panel-body customer-panel">
    <?= $this->render('customer-form',[
        'model'=>$model,
        'show'=>true,
        'enableEdit'=>$enableEdit,
        'action'=>['/crm/private-customer/update'],
        'contactList'=>$contactList,
        'contactModel'=>$contactModel,
        'formData'=>$formData,
        'requireList'=>$requireList,
    ])?>
</div>
<!-- end panel -->