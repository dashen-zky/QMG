<?php
use yii\helpers\Url;
use backend\modules\rbac\model\RoleManager;
?>
<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>'角色列表',
    'panelClass'=>'role-panel'
])?>
<div id="jstree-default" class="jstree jstree-1 jstree-default" tabindex="0"
     role="tree" aria-multiselectable="true" aria-busy="false" aria-activedescendant="j1_7">
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
                    <a href="<?= Url::to([
                        '/rbac/assignment/assignment',
                        'name'=>$roleName,
                        'backUrl'=>'/rbac/assignment/index',
                    ])?>"><?= $roleName?></a>
                </li>
        <?php return ;endif?>
            <li data-jstree='{"opened":true}'>
                <a href="<?= Url::to([
                    '/rbac/assignment/assignment',
                    'name'=>$roleName,
                    'backUrl'=>'/rbac/assignment/index',
                ])?>"><?= $roleName?></a>
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
<?= $this->render('@webroot/../views/site/panel-footer')?>
