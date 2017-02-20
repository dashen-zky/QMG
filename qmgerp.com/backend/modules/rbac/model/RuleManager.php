<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/4 0004
 * Time: 上午 12:50
 */

namespace backend\modules\rbac\model;


use Yii;
use backend\modules\hr\models\Department;

class RuleManager
{
    const isMediaAndPartTimeManager = 'isMediaAndPartTimeManager';
    const isManager = 'isManager';
    public function isMediaAndPartTimeManager($user, $item, $params) {
        // 实现是不是供应商或是兼职的管理者的规则
        if(!isset($params['manager_uuid']) || empty($params['manager_uuid'])) {
            $roles = Yii::$app->authManager->getRolesByUser($user);
            // 如果他是总监，那么可以编辑没有被分配的供应商或是兼职
            if(array_key_exists(RoleManager::MediaDirector, $roles)) {
                return true;
            }
            return false;
        }
        // 检查供应商的管理者是不是本人
        if($params['manager_uuid'] == $user) {
            return true;
        }

        // 检查一下这个供应商的管理者是不是他的下属
        $departments = Department::getDepartmentUuidsFromUserId($user);
        $uuids = Yii::$app->authManager->getOrdinateFromUserId(
            $user, RBACManager::Media,$departments);
        if(in_array($params['manager_uuid'], $uuids)) {
            return true;
        }
        return false;
    }

    public function isManager($user, $item, $params) {
        // 实现是不是供应商或是兼职的管理者的规则
        if(!isset($params['manager_uuid']) || empty($params['manager_uuid'])) {
            return false;
        }
        // 检查供应商的管理者是不是本人
        if($params['manager_uuid'] == $user 
            || (is_array($params['manager_uuid']) && in_array($user, $params['manager_uuid']))) {
            return true;
        }

        if(!isset($params['module']) || empty($params['module'])) {
            return false;
        }
        // 检查一下这个对象的管理者是不是他的下属
        $departments = Department::getDepartmentUuidsFromUserId($user);
        $uuids = Yii::$app->authManager->getOrdinateFromUserId(
            $user, $params['module'],$departments);
        if(in_array($params['manager_uuid'], $uuids)) {
            return true;
        }
        return false;
    }
}