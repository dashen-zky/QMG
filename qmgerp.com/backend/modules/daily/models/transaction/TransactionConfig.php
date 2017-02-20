<?php
namespace backend\modules\daily\models\transaction;
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-11-28
 * Time: 下午3:52
 */
class TransactionConfig extends \backend\models\Config
{
    public $extraConfig;
    const StatusUnfinished = 1;
    const StatusFinished = 2;
    const StatusDropped = 3;
    const Top = 999999999; // 置顶
    const CurrentWeekTransaction = 1;
    const NextWeekTransaction = 2;

    public function init()
    {
        $this->key = 'transaction_config';
        $this->extraConfig = [
            'status'=>[
                self::StatusUnfinished => '未完成',
                self::StatusFinished => '已完成',
                self::StatusDropped => '已放弃',
            ],
        ];
        parent::init();
    }

    public function updateConfig($formData)
    {
        // TODO: Implement updateConfig() method.
    }

    public function getList($key)
    {
        $list =  parent::getList($key);
        if(empty($list)) {
            $list = isset($this->extraConfig[$key])?$this->extraConfig[$key]:[];
        }
        return $list;
    }

    public function getAppointed($key, $index)
    {
        $value =  parent::getAppointed($key, $index);
        if(empty($value)) {
            $value =  isset($this->extraConfig[$key][$index])?$this->extraConfig[$key][$index]:'';
        }
        return $value;
    }
}