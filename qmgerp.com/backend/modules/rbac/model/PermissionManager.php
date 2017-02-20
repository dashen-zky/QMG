<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/3 0003
 * Time: 下午 3:02
 */

namespace backend\modules\rbac\model;


use backend\models\interfaces\RecordOperator;
use Yii;
use yii\db\Exception;
use yii\rbac\Item;

class PermissionManager implements RecordOperator
{
    // permission name
    const DeleteCustomer = 'DeleteCustomer';
    const DeleteProject = 'DeleteProject';
    const FinancialMenu = 'FinancialMenu';
    const HumanResourceMenu = 'HumanResourceMenu';
    const MyCustomerMenu = 'MyCustomerMenu';
    const ProjectMenu = 'ProjectMenu';
    const SupplierMenu = 'SupplierMenu';
    const SystemMenu = 'SystemMenu';
    const SupplierAndPartTimeAccess = 'SupplierAndPartTimeAccess';
    const EditSupplierAndPartTime = 'EditSupplierAndPartTime';
    const AddRegulation = 'AddRegulation';
    const EditRegulation = 'EditRegulation';
    const DelRegulation = 'DelRegulation';
    const applyRecruitAssess = 'applyRecruitAssess';
    const ProjectAssess = 'ProjectAssess';
    const ProjectBriefAssess = 'ProjectBriefAssess';
    const ProjectMediaBriefAssess = 'ProjectMediaBriefAssess';

    public static function buildItemListForDropDownList() {
        $authManager = Yii::$app->authManager;
        $items = array_merge($authManager->getItems(Item::TYPE_ROLE), $authManager->getItems(Item::TYPE_PERMISSION));
        $_list = [];
        foreach($items as $role) {
            $_list[$role->name] = $role->name;
        }
        return $_list;
    }

    public function insertRecord($formData)
    {
        if(empty($formData) || !isset($formData['name']) || empty($formData['name'])) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $authManager = Yii::$app->authManager;
            $permission = $authManager->createPermission($formData['name']);
            $permission->description = $formData['description'];
            $authManager->add($permission);
            if(isset($formData['parent']) && !empty($formData['parent'])) {
                $parent = $authManager->getItem($formData['parent']);
                if(!empty($parent) && $authManager->canAddChild($parent, $permission)) {
                    $authManager->addChild($parent, $permission);
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

    public function updateRecord($formData)
    {
        if(empty($formData) || !isset($formData['name']) || empty($formData['name'])) {
            return false;
        }
        $authManager = Yii::$app->authManager;
        $record = $authManager->getPermission($formData['name']);
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

    public function getRecordFromName($name) {
        $auth = Yii::$app->authManager;
        $role = $auth->getPermission($name);
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