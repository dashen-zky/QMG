<?php
use yii\helpers\Url;
use backend\modules\rbac\model\RoleManager;
use backend\modules\rbac\model\Assignment;
?>
<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>'角色与权限图',
    'panelClass'=>'permission-panel'
])?>
<div id="jstree-default">
    <ul>
        <?php
        $roleList = Yii::$app->authManager->getRoles();
        foreach($roleList as $role) {
            assignmentTree($role->name);
        }
        function assignmentTree($roleName) {
            $permissions = Yii::$app->authManager->getPermissionsByRole($roleName);
            echo '<li data-jstree=\'{"opened":true}\'>';
            echo $roleName;
            echo '<ul>';
            foreach($permissions as $permission) {
                echo '<li>';
                    ?>
                <span class="showPermission" id="<?= Url::to([
                    '/rbac/permission/edit',
                    'name'=>$permission->name,
                ])?>" style="margin-right: 15px;text-decoration:underline; color: #0a6aa1"><?= $permission->name?></span>
                <span id="<?= Url::to([
                    '/rbac/permission/del',
                    'name'=>$permission->name,
                ])?>" class="deletePermission" style="text-decoration:underline; color: #0a6aa1">删除</span>
        <?php
                echo "</li>";
            }
            echo '</ul>';
            echo "</li>";
        }?>
    </ul>
</div>
<?= $this->render('form-modal')?>
<?= $this->render('@webroot/../views/site/panel-footer')?>
<?php
$Js = <<<JS
$(function() {
    $('.permission-panel #jstree-default').on('click','.deletePermission',function() {
        var url = $(this).attr('id');
        location.href = url;
    });
    $('.permission-panel').on('click','.showPermission',function() {
        var url = $(this).attr('id');
        $.get(
        url,
        function(data,status) {
            if(status === 'success') {
                var rolePanel = $('.permission-panel');
                var modal = rolePanel.find('.permission-modal');
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
