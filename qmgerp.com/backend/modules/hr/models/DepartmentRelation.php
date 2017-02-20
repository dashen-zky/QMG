<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/4 0004
 * Time: 上午 12:56
 */

namespace backend\modules\hr\models;

use backend\models\interfaces\DeleteMapRecord;
use backend\modules\hr\models\HrBaseActiveRecord;

/**
 * department model
 *
 * @property integer $id
 * @property string $parent_uuid
 * @property string $child_uuid
 */
class DepartmentRelation extends HrBaseActiveRecord implements DeleteMapRecord
{
    static public function tableName()
    {
        return self::DepartmentRelationTableName;
    }

    public function updateSingleRecord($formData)
    {
        if(empty($formData) || !isset($formData['child_uuid']) || empty($formData['child_uuid'])) {
            return false;
        }
        $record = self::find()->andWhere(['child_uuid'=>$formData['child_uuid']])->one();
        if(empty($record) || !$this->updatePreHandler($formData, $record)) {
            return false;
        }

        return $record->update();
    }

    public function insertSingleRecord($formData)
    {
        if(empty($formData)) {
            return false;
        }

        if(!$this->updatePreHandler($formData)) {
            return false;
        }

        return $this->insert();
    }

    public function deleteSingleRecord($uuid1, $uuid2)
    {
        // TODO: Implement deleteSingleRecord() method.
    }

    public function getChildrenForDropDownList($uuid) {
        $_children = '<option value="0">未选择</option>';
        if(empty($uuid) || !$uuid) {
            return $_children;
        }

        $children = self::find()
            ->alias('t1')
            ->select(['t2.uuid','t2.name'])
            ->leftJoin(self::DepartmentTableName . ' t2', 't1.child_uuid = t2.uuid')
            ->andWhere(['t1.parent_uuid'=>$uuid])->asArray()->all();
        if(empty($children)) {
            return $_children;
        }

        foreach($children as $child) {
            $_children .= "<option value='". $child['uuid'] ."'>".$child['name']."</option>";
        }
        return $_children;
    }
}