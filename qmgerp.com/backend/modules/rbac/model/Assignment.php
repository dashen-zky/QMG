<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/3 0003
 * Time: 下午 5:53
 */

namespace backend\modules\rbac\model;

use backend\modules\hr\models\EmployeeBasicInformation;
use Yii;
use backend\models\interfaces\DeleteRecordOperator;
use backend\models\interfaces\RecordOperator;
use yii\db\Exception;

class Assignment implements RecordOperator
{
    public function insertRecord($formData)
    {

    }

    public function updateRecord($formData)
    {
        if(empty($formData) || !isset($formData['name']) || empty($formData['name'])) {
            return false;
        }

        $new_uuids = array_unique(explode(',', trim($formData['role_employee_uuid'])));
        $old_uuids = $this->getUserIdsByRole($formData['name']);

        $should_insert = array_diff($new_uuids, $old_uuids);
        $should_delete = array_diff($old_uuids, $new_uuids);

        $transaction = Yii::$app->db->beginTransaction();
        try{
            $authManager = Yii::$app->authManager;
            $role = $authManager->getRole($formData['name']);
            foreach($should_insert as $uuid) {
                $authManager->assign($role,$uuid);
            }

            foreach($should_delete as $uuid) {
                $authManager->revoke($role, $uuid);
            }
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }


    public function getUserIdsByRole($roleName) {
        $authManager = Yii::$app->authManager;
        $userIds = $authManager->getUserIdsByRole($roleName);
        return $userIds;
    }

    public function getAssignmentsByRole($roleName , $asArray = false) {
        $userIds = $this->getUserIdsByRole($roleName);
        $list = EmployeeBasicInformation::find()
            ->select(['uuid','name'])
            ->andWhere(['uuid'=>$userIds])
            ->asArray()->all();
        if($asArray) {
            return $list;
        }
        $_return = [
            'name'=>'',
            'uuid'=>'',
        ];
        $i = 0;
        foreach($list as $item) {
            if($i === 0) {
                $_return['name'] .= $item['name'];
                $_return['uuid'] .= $item['uuid'];
                $i++;
                continue;
            }

            $_return['name'] .= ','.$item['name'];
            $_return['uuid'] .= ','.$item['uuid'];
        }
        return $_return;
    }
}