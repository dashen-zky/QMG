<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/13 0013
 * Time: 下午 5:19
 */

namespace backend\models;


use backend\models\interfaces\RecordOperator;
use yii\helpers\Json;

abstract class Config extends BaseRecord implements RecordOperator
{
    public $key;
    abstract public function updateConfig($formData);

    public function generateConfig() {
        if(!empty($this->config)) {
            return $this->config;
        }
        $uuid = md5($this->key);
        $query = self::find()->select(['config'])->andWhere(['uuid'=>$uuid])->one();
        if(empty($query)) {
            return null;
        } else {
            return Json::decode($query->config);
        }
    }

    public function getConfig() {
        return $this->generateConfig();
    }

    public static function tableName()
    {
        return self::COMConfig;
    }

    public function updateRecord($formData)
    {
        if(empty($formData) || !isset($formData['uuid']) || empty($formData['uuid'])) {
            return true;
        }
        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(empty($record)) {
            return $this->insertRecord($formData);
        }
        if(!parent::updatePreHandler($formData, $record)) {
            return true;
        }
        $values = $record->getDirtyAttributes();
        if(empty($values)) {
            return true;
        }
        return $record->update();
    }

    public function insertRecord($formData)
    {
        if(empty($formData) || !isset($formData['uuid']) || empty($formData['uuid'])) {
            return true;
        }

        if(!parent::updatePreHandler($formData)) {
            return true;
        }

        return $this->insert();
    }

    // 通过操作json数据直接更新配置文件
    public function updateDateConfigByJsonString($serialConfig, $uuid_key = null) {
        return $this->updateRecord([
            'uuid'=>md5($this->key),
            'config'=>$serialConfig,
            'uuid_key'=>$uuid_key,
        ]);
    }

    public function getList($key) {
        if(empty($this->config)) {
            $this->config = self::generateConfig();
        }

        return isset($this->config[$key])?$this->config[$key]:[];
    }

    public function getAppointed($key, $index) {
        if(empty($this->config)) {
            $this->config = self::generateConfig();
        }

        return isset($this->config[$key][$index])?
            $this->config[$key][$index]:'';
    }
}