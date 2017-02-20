<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/2 0002
 * Time: 下午 4:37
 */

namespace backend\modules\rbac\controllers;
use backend\models\CompressHtml;
use backend\modules\hr\models\EmployeeAccount;
use backend\modules\hr\models\EmployeeBasicInformation;
use backend\modules\rbac\model\RBACRule;
use backend\modules\rbac\model\RoleManager;
use Yii;
use yii\helpers\Json;

class RoleController extends RBACController
{
    public function actionIndex() {
        return $this->render('index');
    }

    public function  actionDel() {
        $roleName = Yii::$app->request->get('name');
        if(empty($roleName)) {
            return false;
        }

        if(Yii::$app->authManager->removeItem($roleName)) {
            $this->redirect(['index']);
        }
    }

    public function actionAdd() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post('RoleForm');
            $roleManager = new RoleManager();
            if($roleManager->insertRecord($formData)) {
                $this->redirect(['index']);
            }
        }
    }

    public function actionEdit() {
        if(Yii::$app->request->isAjax) {
            $name = Yii::$app->request->get('name');
            $roleManager = new RoleManager();
            $formData = $roleManager->getRecordFromName($name);
            return CompressHtml::compressHtml($this->renderPartial('form',[
                'formData'=>$formData,
                'show'=>true,
                'action'=>['/rbac/role/update'],
            ]));
        }
    }

    public function actionUpdate() {
        if(Yii::$app->request->isPost) {
            $formData = Yii::$app->request->post('RoleForm');
            $roleManager = new RoleManager();
            if($roleManager->updateRecord($formData)) {
                $this->redirect(['index']);
            }
        }
    }
    
    public function actionInit() {
        $authManager = Yii::$app->authManager;
        $authManager->removeAll();
        $ceo = $authManager->createRole('ceo');
        $authManager->add($ceo);
        // human resource
        $hrDr = $authManager->createRole('HumanResourceDirector');
        $authManager->add($hrDr);
        $hrMg = $authManager->createRole('HumanResourceManager');
        $authManager->add($hrMg);
        $hr = $authManager->createRole("HumanResource");
        $authManager->add($hr);
        $hrAt = $authManager->createRole('HumanResourceAssistant');
        $authManager->add($hrAt);
        
        $authManager->addChild($hrDr, $hrMg);
        $authManager->addChild($hrMg, $hr);
        $authManager->addChild($hr, $hrAt);
        // sales
        $slDr = $authManager->createRole('SalesDirector');
        $authManager->add($slDr);
        $slMg = $authManager->createRole('SalesManager');
        $authManager->add($slMg);
        $sl = $authManager->createRole("Sales");
        $authManager->add($sl);
        $slAt = $authManager->createRole('SalesAssistant');
        $authManager->add($slAt);

        $authManager->addChild($slDr, $slMg);
        $authManager->addChild($slMg, $sl);
        $authManager->addChild($sl, $slAt);
        // project
        $pgDr = $authManager->createRole('ProjectDirector');
        $authManager->add($pgDr);
        $pgMg = $authManager->createRole('ProjectManager');
        $authManager->add($pgMg);
        $pg = $authManager->createRole("Project");
        $authManager->add($pg);

        $authManager->addChild($pgDr, $pgMg);
        $authManager->addChild($pgMg, $pg);
        // media
        $mdDr = $authManager->createRole('MediaDirector');
        $authManager->add($mdDr);
        $mdMg = $authManager->createRole('MediaManager');
        $authManager->add($mdMg);
        $md = $authManager->createRole("Media");
        $authManager->add($md);
        $mdAt = $authManager->createRole('MediaAssistant');
        $authManager->add($mdAt);

        $authManager->addChild($mdDr, $mdMg);
        $authManager->addChild($mdMg, $md);
        $authManager->addChild($md, $mdAt);

        // administer
        $adDr = $authManager->createRole('AdministerDirector');
        $authManager->add($adDr);
        $adMg = $authManager->createRole('AdministerManager');
        $authManager->add($adMg);
        $ad = $authManager->createRole("Administer");
        $authManager->add($ad);
        $adAt = $authManager->createRole('AdministerAssistant');
        $authManager->add($adAt);
        $authManager->addChild($adDr, $adMg);
        $authManager->addChild($adMg, $ad);
        $authManager->addChild($ad, $adAt);

        // Financial
        $finDr = $authManager->createRole('FinancialDirector');
        $authManager->add($finDr);
        $finMg = $authManager->createRole('FinancialManager');
        $authManager->add($finMg);
        $fin = $authManager->createRole("Financial");
        $authManager->add($fin);
        $finAt = $authManager->createRole('FinancialAssistant');
        $authManager->add($finAt);

        $authManager->addChild($finDr, $finMg);
        $authManager->addChild($finMg, $fin);
        $authManager->addChild($fin, $finAt);

        // black account 黑户是指在系统里面没有任何特殊操作的用户群体
        $blackDr = $authManager->createRole(RoleManager::BlackAccountDirector);
        $authManager->add($blackDr);
        $blackMg = $authManager->createRole(RoleManager::AdministerManager);
        $authManager->add($blackMg);
        $black = $authManager->createRole(RoleManager::BlackAccount);
        $authManager->add($black);
        $authManager->addChild($blackDr, $blackMg);
        $authManager->addChild($blackMg, $black);

        $authManager->addChild($ceo, $hrDr);
        $authManager->addChild($ceo, $adDr);
        $authManager->addChild($ceo, $mdDr);
        $authManager->addChild($ceo, $slDr);
        $authManager->addChild($ceo, $pgDr);
        $authManager->addChild($ceo, $finDr);
        $authManager->addChild($ceo, $blackDr);


        // admin
        $superAdmin = $authManager->createRole('superAdmin');
        $authManager->add($superAdmin);
        $advanceAdmin = $authManager->createRole('Admin');
        $authManager->add($advanceAdmin);
        $authManager->addChild($superAdmin, $advanceAdmin);
        $authManager->addChild($advanceAdmin, $ceo);

        // 将admin分配给admin
        $admin = EmployeeAccount::find()->andWhere(['username'=>'admin'])->one();
        $authManager->assign($advanceAdmin, $admin->em_uuid);

        $this->redirect(['index']);
    }
}