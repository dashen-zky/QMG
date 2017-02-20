<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/3 0003
 * Time: 下午 3:02
 */

namespace backend\modules\rbac\model;

use backend\models\interfaces\DeleteRecordOperator;
use Yii;
use yii\db\Exception;

class RoleManager implements DeleteRecordOperator
{
    const ceo = 'ceo';
    const HumanResourceDirector= 'HumanResourceDirector';
    const HumanResourceManager= 'HumanResourceManager';
    const HumanResource= 'HumanResource';
    const HumanResourceAssistant= 'HumanResourceAssistant';
    const SalesDirector= 'SalesDirector';
    const SalesManager= 'SalesManager';
    const Sales= 'Sales';
    const SalesAssistant= 'SalesAssistant';
    const ProjectDirector= 'ProjectDirector';
    const ProjectManager= 'ProjectManager';
    const Project= 'Project';
    const MediaDirector= 'MediaDirector';
    const MediaManager= 'MediaManager';
    const Media= 'Media';
    const MediaAssistant= 'MediaAssistant';
    const AdministerDirector= 'AdministerDirector';
    const AdministerManager= 'AdministerManager';
    const Administer= 'Administer';
    const AdministerAssistant= 'AdministerAssistant';
    const superAdmin= 'superAdmin';
    const Admin= 'Admin';
    const BlackAccountDirector = 'BlackAccountDirector';
    const BlackAccountManager = 'BlackAccountManager';
    const BlackAccount = 'BlackAccount';
    const VicePresident = 'VicePresident';
    const applyRecruitAssessor = 'applyRecruitAssessor';
    const ProjectAssessor = 'ProjectAssessor';

    public static function buildRoleListForDropDownList() {
        $roleList = Yii::$app->authManager->getRoles();
        $_list = [];
        foreach($roleList as $role) {
            $_list[$role->name] = $role->name;
        }
        return $_list;
    }

    public static function getRootList() {
        $authManager = Yii::$app->authManager;
        $roleList = $authManager->getRoles();
        $rootList = [];
        foreach($roleList as $role) {
            if(!$authManager->getParent($role->name)) {
                $rootList[] = $role->name;
            }
        }
        return $rootList;
    }

    public static function roleList() {
        return Yii::$app->authManager->getRoles();
    }

    public function updateRecord($formData)
    {
        if(empty($formData) || !isset($formData['name']) || empty($formData['name'])) {
            return false;
        }
        $authManager = Yii::$app->authManager;
        $record = $authManager->getRole($formData['name']);
        if(empty($record)) {
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $record->description = $formData['description'];
            $authManager->update($formData['name'], $record);
            $parent = $authManager->getParent($formData['name']);
            if($formData['parent'] !== $parent[0]) {
                $oldParent = $authManager->getRole($parent[0]);
                if(!empty($oldParent)) {
                    $authManager->removeChild($oldParent, $record);
                }

                $newParent = $authManager->getRole($formData['parent']);
                if(!empty($newParent)) {
                    $authManager->addChild($newParent, $record);
                }
            }
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function insertRecord($formData)
    {
        if(empty($formData) && !isset($formData['name']) && empty($formData['name'])) {
            return false;
        }
        $authManager = Yii::$app->authManager;
        $role = $authManager->getRole($formData['name']);
        if(!empty($role)) {
            var_dump('这个角色已经被占用了');die;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {

            $role = $authManager->createRole($formData['name']);
            $role->description = $formData['description'];
            $authManager->add($role);
            if(isset($formData['parent']) && !empty($formData['parent'])) {
                $parent = $authManager->getRole($formData['parent']);
                if(!empty($parent)) {
                    $authManager->addChild($parent, $role);
                }
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }
        $transaction->commit();
        return true;
    }

    public function deleteRecord($uuid)
    {
        // TODO: Implement deleteRecord() method.
    }

    public function getRecordFromName($name) {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);
        if(empty($role)) {
            return [];
        }
        $formData = [];
        $parent = $auth->getParent($name);
        if($parent) {
            $formData['parent'] = $parent[0];
        }

        foreach($role as $key=>$value) {
            $formData[$key] = $value;
        }
        return $formData;
    }

}