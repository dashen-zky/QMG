<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>'添加权限',
])?>
<?= $this->render('form',[
    'action'=>['/rbac/permission/add'],
    'formData'=>[],
    'show'=>false,
])?>
<?= $this->render('@webroot/../views/site/panel-footer')?>