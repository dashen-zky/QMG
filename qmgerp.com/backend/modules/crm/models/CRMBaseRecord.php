<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/17 0017
 * Time: 下午 8:34
 */

namespace backend\modules\crm\models;
use backend\models\BaseRecord;

class CRMBaseRecord extends BaseRecord
{
    const new_record = 1; // 新数据是值新系统里面的数据
    const old_record = 2;  //旧数据是指，erp 1.0里面的数据
    // 将formData里面的数据写入到对象里面去
    public function filterRepeatField($types, $values, $type) {
        if (empty($values)) {
            return null;
        }
        $_return  = [];
        $typeList = explode(",",$types);
        $idList = explode(",",$values);
        for($i = 0; $i < count($idList); $i++) {
            if ($typeList[$i] == $type) {
                $_return[$i] = $idList[$i];
            }
        }
        return $this->getDistinctValue($_return);
    }

    // 将数组里面重复的，和空的值去掉，返回以逗号隔开的字符串
    public function getDistinctValue($value) {
        if (empty($value)) {
            return null;
        }
        return implode(",",$this->getDistinctValueAsArray($value));
    }

    // 将数组里面重复的，和空的值去掉，返回数组
    public function getDistinctValueAsArray($value) {
        if(empty($value)) {
            return null;
        }

        $uniArr = array_unique($value);
        foreach ($uniArr as $index => $value) {
            if(empty($value)) {
                unset($uniArr[$index]);
            }
        }
        return $uniArr;
    }

    // 将数组里面的空的元素删除掉
    public function unsetEmptyElement(&$arr) {
        if(empty($value)) {
            return null;
        }

        foreach($arr as $index=>$value) {
            if (empty($value)) {
                unset($arr[$index]);
            }
        }
    }

    /**
     * 根据数据查出来的值，拿出指定的字段，作为dropDownList的数据
     * 比如有uuid和name作为dropDownList的数据
     */
    protected function dropDownListDataBuilder($records, $index, $valueIndex) {
        if(empty($records)) {
            return null;
        }
        $_return  = [];
        foreach($records as $record) {
            $_return[$record[$index]] = $record[$valueIndex];
        }
        return $_return;
    }
}