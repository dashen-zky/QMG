<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>'添加角色',
])?>
<?= $this->render('form',[
    'action'=>['/rbac/role/add'],
    'formData'=>[],
    'show'=>false,
])?>
<?= $this->render('@webroot/../views/site/panel-footer')?>