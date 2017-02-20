<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/2 0002
 * Time: 下午 4:37
 */

namespace backend\modules\rbac\controllers;
use backend\modules\rbac\model\PermissionManager;
use backend\modules\rbac\model\RoleManager;
use Yii;
use backend\models\CompressHtml;
class PermissionController extends RBACController
{
    public function actionIndex() {
        return $this->render('index');
    }

    public function actionAdd() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post('PermissionForm');
            $permissionManager = new PermissionManager();
            if($permissionManager->insertRecord($formData)) {
                return $this->redirect(['index']);
            }
        }
    }

    public function  actionDel() {
        $permissionName = Yii::$app->request->get('name');
        if(empty($permissionName)) {
            return false;
        }

        if(Yii::$app->authManager->removeItem($permissionName)) {
            $this->redirect(['index']);
        }
    }

    public function actionEdit() {
        if(Yii::$app->request->isAjax) {
            $name = Yii::$app->request->get('name');
            $permissionManager = new PermissionManager();
            $formData = $permissionManager->getRecordFromName($name);
            return CompressHtml::compressHtml($this->renderPartial('form',[
                'formData'=>$formData,
                'show'=>true,
                'action'=>['/rbac/permission/update'],
            ]));
        }
    }

    public function actionUpdate() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post('PermissionForm');
            $permissionManager = new PermissionManager();
            if($permissionManager->updateRecord($formData)) {
                $this->redirect(['index']);
            }
        }
    }

    public function actionInit() {
        $auth = Yii::$app->authManager;
        $auth->removeAllPermissions();
        $HumanResourceMenu = $auth->createPermission('HumanResourceMenu');
        $auth->add($HumanResourceMenu);
        $hrAt = $auth->getRole('HumanResourceAssistant');
        if($auth->canAddChild($hrAt,$HumanResourceMenu)) {
            $auth->addChild($hrAt, $HumanResourceMenu);
        }

        $myCustomerMenu = $auth->createPermission('MyCustomerMenu');
        // 将是否是创建者这个规则加入到这个权限中
//        $isAuthor = $auth->getRule('isAuthor');
//        $myCustomerMenu->ruleName = $isAuthor->name;
        $auth->add($myCustomerMenu);
        $sales = $auth->getRole('Sales');
        if($auth->canAddChild($sales, $myCustomerMenu)) {
            $auth->addChild($sales, $myCustomerMenu);
        }

        $projectMenu = $auth->createPermission('ProjectMenu');
        $projectMenu->description = '是否可以显示project的权限，将这个权限只分配给具有项目的角色的人';
        $auth->add($projectMenu);
        $project = $auth->getRole('Project');
        if($auth->canAddChild($project, $projectMenu)) {
            $auth->addChild($project, $projectMenu);
        }

        $supplierMenu = $auth->createPermission('SupplierMenu');
        $auth->add($supplierMenu);
        $media = $auth->getRole('Media');
        if($auth->canAddChild($media, $supplierMenu)) {
            $auth->addChild($media, $supplierMenu);
        }

        $systemMenu = $auth->createPermission('SystemMenu');
        $auth->add($systemMenu);
        $admin = $auth->getRole('Admin');
        if($auth->canAddChild($admin, $systemMenu)) {
            $auth->addChild($admin, $systemMenu);
        }

        $financialMenu = $auth->createPermission('FinancialMenu');
        $auth->add($financialMenu);
        $financial = $auth->getRole('Financial');
        if($auth->canAddChild($financial, $financialMenu)) {
            $auth->addChild($financial, $financialMenu);
        }

        // 删除客户
        $deleteCustomer = $auth->createPermission('DeleteCustomer');
        $auth->add($deleteCustomer);
        $admin = $auth->getRole('Admin');
        if($auth->canAddChild($admin, $deleteCustomer)) {
            $auth->addChild($admin, $deleteCustomer);
        }
        // 删除项目
        $deleteProject = $auth->createPermission('DeleteProject');
        $auth->add($deleteProject);
        $admin = $auth->getRole('Admin');
        if($auth->canAddChild($admin, $deleteProject)) {
            $auth->addChild($admin, $deleteProject);
        }

        // 编辑供应商和兼职的权限
        $editSupplierAndPartTime = $auth->createPermission(PermissionManager::EditSupplierAndPartTime);
        $editSupplierAndPartTime->description = '编辑供应商和兼职，只有管理者以及管理者上级可以编辑';
        $auth->add($editSupplierAndPartTime);
        $media = $auth->getRole(RoleManager::Media);
        if($auth->canAddChild($media, $editSupplierAndPartTime)) {
            $auth->addChild($media, $editSupplierAndPartTime);
        }

        // 审核供应商和兼职 将这个权限交给媒介总监
        $supplierAndPartTimeAccess = $auth->createPermission(PermissionManager::SupplierAndPartTimeAccess);
        $supplierAndPartTimeAccess->description = '审核供应商和兼职';
        $auth->add($supplierAndPartTimeAccess);
        $mediaDirector = $auth->getRole(RoleManager::MediaDirector);
        if($auth->canAddChild($mediaDirector, $supplierAndPartTimeAccess)) {
            $auth->addChild($mediaDirector, $supplierAndPartTimeAccess);
        }
        
        // 添加规章制度 行政人员可以添加规章制度
        $addRegulation = $auth->createPermission(PermissionManager::AddRegulation);
        $addRegulation->description = '添加规章制度';
        $auth->add($addRegulation);
        // 修改规章制度 如果此人是创建者的本人或是上级可以修改这个规章制度
        $editRegulation = $auth->createPermission(PermissionManager::EditRegulation);
        $editRegulation->description = '修改规章制度';
        $auth->add($editRegulation);
        // 行政人员
        $administer = $auth->getRole(RoleManager::Administer);
        if($auth->canAddChild($administer, $addRegulation)) {
            $auth->addChild($administer, $addRegulation);
        }
        // 行政人员
        if($auth->canAddChild($administer, $editRegulation)) {
            $auth->addChild($administer, $editRegulation);
        }

        // 删除规章制度
        $delRegulation = $auth->createPermission(PermissionManager::DelRegulation);
        $delRegulation->description = '删除规章制度';
        $auth->add($delRegulation);
        $admin = $auth->getRole('admin');
        if($auth->canAddChild($admin, $delRegulation)) {
            $auth->addChild($admin, $delRegulation);
        }


        $this->redirect(['index']);
    }
}