<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#modify-password-tab-list" data-toggle="tab">修改密码</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="modify-password-tab-list">
            <?= $this->render('@webroot/../views/site/panel-header',[
                'title'=>'修改密码',
                'panelClass'=>'modify-password'
            ])?>
            <?= $this->render('modify-password-form',[
                'model'=>$model,
                'action'=>['/account/password-update'],
                'formClass'=>'PasswordForm'
            ])?>
            <?= $this->render('@webroot/../views/site/panel-footer')?>
        </div>
    </div>
</div>