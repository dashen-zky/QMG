<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/4 0004
 * Time: 上午 12:02
 */

namespace backend\modules\rbac\controllers;

use backend\modules\rbac\model\PermissionManager;
use backend\modules\rbac\model\RoleManager;
use backend\modules\rbac\model\Rule;
use backend\modules\rbac\model\RuleManager;
use Yii;
class RuleController extends RBACController
{
    public function actionIndex() {
        return $this->render('index');
    }

    public function actionInit() {
        // 新建‘判断是不是兼职或是供应商的管理者’的规则，
        // 并且将规则分配给EditSupplierAndPartTime 这个权限
        $authManager = Yii::$app->authManager;
        $authManager->removeAllRules();

        $isMediaAndPartTimeManager = new Rule([
            'name'=>RuleManager::isMediaAndPartTimeManager,
            'data'=>'判断是不是兼职或是供应商的管理者',
        ]);
        $authManager->add($isMediaAndPartTimeManager);
        $editSupplierAndPartTime = $authManager->getPermission(PermissionManager::EditSupplierAndPartTime);
        $editSupplierAndPartTime->ruleName = RuleManager::isMediaAndPartTimeManager;
        $authManager->update($editSupplierAndPartTime->name, $editSupplierAndPartTime);
        $this->redirect(['index']);

        // 创建一个规则表示是不是管理者
        $isManager = new Rule([
            'name'=>RuleManager::isManager,
            'data'=>'判断是不是的管理者',
        ]);
        $authManager->add($isManager);
        $editRegulation = $authManager->getPermission(PermissionManager::EditRegulation);
        $editRegulation->ruleName = RuleManager::isManager;
        // 给编辑规章制度的权限加一个规则，既本人或是上级领导可以编辑这个规章制度
        $authManager->update($editRegulation->name, $editRegulation);

        $this->redirect(['index']);
    }
}