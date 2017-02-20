<?php
use yii\helpers\Url;
use backend\modules\rbac\model\RoleManager;
use backend\modules\rbac\model\Assignment;
?>
<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>'角色分配',
    'panelClass'=>'role-panel'
])?>
<div id="jstree-default">
    <ul>
        <?php
        $roleList = Yii::$app->authManager->getRoles();
        foreach($roleList as $role) {
            assignmentTree($role->name);
        }
        function assignmentTree($roleName) {
            $assignment = new Assignment();
            $users = $assignment->getAssignmentsByRole($roleName, true);
            echo '<li data-jstree=\'{"opened":true}\'>';
            echo "<a href='".Url::to([
                    '/rbac/assignment/assignment',
                    'name'=>$roleName,
                    'backUrl'=>'/rbac/assignment/index2',
                ])."'>".$roleName."</a>";
            echo '<ul>';
            foreach($users as $user) {
                echo '<li>';
                    echo '<a href="#" name="'.$user['uuid'].'">'.$user['name'].'</a>';
                echo "</li>";
            }
            echo '</ul>';
            echo "</li>";
        }?>
    </ul>
</div>
<?= $this->render('@webroot/../views/site/panel-footer')?>
