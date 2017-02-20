<?php
use yii\helpers\Url;
use backend\modules\rbac\model\RoleManager;
?>
<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>'角色列表',
    'panelClass'=>'role-panel'
])?>
<div id="jstree-default">
    <ul>
        <?php
        $rootList = RoleManager::getRootList();
        foreach($rootList as $root) {
            roleTree($root);
        }
        function roleTree($roleName) {
            $auth = Yii::$app->authManager;
            $children = $auth->getChildrenRoles($roleName);
            if(empty($children)):?>
                <li>
                    <span class="showRole" id="<?= Url::to([
                        '/rbac/role/edit',
                        'name'=>$roleName,
                    ])?>" style="margin-right: 15px;text-decoration:underline; color: #0a6aa1"><?= $roleName?></span>
                    <span id="<?= Url::to([
                        '/rbac/role/del',
                        'name'=>$roleName,
                    ])?>" class="deleteRole" style="text-decoration:underline; color: #0a6aa1">删除</span>
                </li>
        <?php return ;endif?>
            <li data-jstree='{"opened":true}'>
                <span class="showRole" id="<?= Url::to([
                    '/rbac/role/edit',
                    'name'=>$roleName,
                ])?>" style="margin-right: 15px;text-decoration:underline; color: #0a6aa1"><?= $roleName?></span>
                <span id="<?= Url::to([
                    '/rbac/role/del',
                    'name'=>$roleName,
                ])?>" class="deleteRole" style="text-decoration:underline; color: #0a6aa1">删除</span>
            <ul>
        <?php
            foreach($children as $child) {
                roleTree($child->name);
            }
        ?>
            </ul>
            </li>
        <?php }?>
    </ul>
</div>
<!--添加角色-->
<?= $this->render('form-modal',[
    'action'=>['/rbac/role/add'],
    'formData'=>[],
    'show'=>false,
])?>
<?= $this->render('@webroot/../views/site/panel-footer')?>


<?php
$Js = <<<JS
$(function() {
    $('.role-panel #jstree-default').on('click','.deleteRole',function() {
        var url = $(this).attr('id');
        location.href = url;
    });

    //$('.role-panel').on('click','.addRole',function() {
    //    var rolePanel = $(this).parents('.role-panel');
    //    var modal = rolePanel.find('.role-modal');
    //    modal.find('form input[type=reset]').trigger("click");
    //    modal.modal('show');
    //});

    $('.role-panel').on('click','.showRole',function() {
        var url = $(this).attr('id');
        $.get(
        url,
        function(data,status) {
            if(status === 'success') {
                var rolePanel = $('.role-panel');
                var modal = rolePanel.find('.role-modal');
                modal.find('.modal-body').html(data);
                modal.modal('show');
                $("form").on('click','.editForm',function() {
                    var form = $(this).parents('form');
                    form.find('.enableEdit').attr("disabled",false);
                });
            }
        });
    });
});
JS;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
