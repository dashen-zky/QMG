<?php
namespace backend\modules\fin\stamp\models;
use backend\models\Config;
use yii\helpers\Json;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17 0017
 * Time: 上午 11:13
 */
class StampConfig extends Config
{
    public $extraConfig;
    const ImportStamp = 1;
    const ExportStamp = 2;
    const Unchecked = 3;
    const Checked = 4;
    const Unsigned = 5; // 未签收
    const Signed = 6; // 已签收
    const Enable = 7;
    const Disable = 8;
    const SelectEntrance = 9;

    public function init()
    {
        $this->key = 'stamp_config';
        $this->extraConfig = [
            'type'=>[
                self::ImportStamp => '进项发票',
                self::ExportStamp => '销项发票',
            ],
            'import_status'=>[
                self::Unchecked => '未验收',
                self::Checked => '已验收',
            ],
            'export_status' => [
                self::Unsigned => '未签收',
                self::Signed => '已签收'
            ]
        ];
        parent::init();
    }

    public function updateConfig($formData)
    {
        if(empty($formData)) {
            return false;
        }
        $config = $this->generateConfig();
        foreach($formData as $key => $item) {
            if(isset($config[$key])) unset($config[$key]);
            $value = [];
            foreach ($item as $k => $itemValue) {
                if (empty($itemValue) || $itemValue === null
                    || !isset($itemValue['key']) || empty($itemValue['key'])) {
                    unset($value[$k]);
                    continue;
                }
                $value[$itemValue['key']] = $itemValue['value'];
            }
            $config[$key] = $value;
        }

        return $this->updateRecord([
            'uuid' => md5($this->key),
            'config'=>Json::encode($config),
            'uuid_key'=>$this->key,
        ]);
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