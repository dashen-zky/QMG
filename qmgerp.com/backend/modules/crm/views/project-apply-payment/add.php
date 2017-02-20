<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>$title,
])?>
<?= $this->render('form',[
    'action'=>['/crm/project-apply-payment/submit-apply'],
    'formData'=>[],
])?>
<?= $this->render('@webroot/../views/site/panel-footer')?>
