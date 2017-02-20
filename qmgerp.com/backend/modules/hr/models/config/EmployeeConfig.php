<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16-11-24
 * Time: 下午3:00
 */

namespace backend\modules\hr\models\config;


use backend\models\Config;

class EmployeeConfig extends Config
{
    public $extraConfig;
    public function init()
    {
        $this->key = 'employee_config';
        $this->extraConfig = [
            'intern_term' => [
                0 => '无试用期',
                1 => '一个月',
                2 => '两个月',
                3 => '三个月',
                4 => '四个月',
                5 => '五个月',
                6 => '六个月',
                7 => '七个月',
                8 => '八个月',
                9 => '九个月',
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