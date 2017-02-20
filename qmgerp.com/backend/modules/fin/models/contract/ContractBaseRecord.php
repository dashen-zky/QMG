<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/29 0029
 * Time: 下午 3:28
 */
namespace backend\modules\fin\models\contract;

use backend\models\interfaces\PrimaryTable;
use Yii;
use backend\models\UUID;
use backend\models\interfaces\RecordOperator;
use backend\modules\fin\models\FINBaseRecord;
use yii\db\Exception;

class ContractBaseRecord extends FINBaseRecord implements RecordOperator,PrimaryTable
{
    public $attachment;
    public $path_dir;
    public static function tableName()
    {
        return self::FinContract;
    }

    public function deleteAttachment($uuid = null, $path = null) {
        if(empty($uuid) || empty($path)) {
            return false;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return false;
        }

        $paths = unserialize($record->path);
        foreach($paths as $index => $item) {
            if($item === $path) {
                unset($paths[$index]);
                break;
            }
        }

        $record->path = serialize($paths);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $realPath = Yii::getAlias("@app").iconv("UTF-8", "GBK", $path);
            if(file_exists($realPath)) {
                unlink($realPath);
            }
            $record->update();
        } catch(Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function formDataPreHandler(&$formData, $record)
    {
        if(empty($record)) {
            if(!isset($formData['uuid']) || empty($formData['uuid'])) {
                $formData['uuid'] = UUID::getUUID();
            }
        }
        $this->handlerFormDataTime($formData,'start_time');
        $this->handlerFormDataTime($formData,'end_time');
        $this->handlerFormDataTime($formData,'sign_time');
        $formData['create_time'] = time();
        if(empty($record)) {
            parent::clearEmptyField($formData);
        }
    }

    public function recordPreHandler(&$formData, $record = null)
    {
        if(empty($record)) {
            if(isset($formData['attachment'])&&!empty($formData['attachment'])) {
                foreach($formData['attachment'] as $index => $item) {
                    $this->attachment[] = $item;
                }
                $this->path_dir = $formData['path_dir'];
            }
        } else {
            if(isset($formData['attachment'])&&!empty($formData['attachment'])) {
                foreach($formData['attachment'] as $index => $item) {
                    $record->attachment[] = $item;
                }
                $record->path_dir = $formData['path_dir'];
            }
            // 谁添加谁就是负责人
            $this->duty_uuid = Yii::$app->user->getIdentity()->getId();
        }
    }

    public function updateRecord($formData)
    {
        if(empty($formData)) {
            return true;
        }
        $record = self::find()->andWhere(['uuid'=>$formData['uuid']])->one();
        if(!parent::updatePreHandler($formData,$record)) {
            return true;
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        try{
            if(isset($record->attachment) && !empty($record->attachment)) {
                $dir = Yii::getAlias('@app') . $record->path_dir;
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                $paths = unserialize($record->path);
                foreach ($record->attachment as $index => $item) {
                    // 判断是否后重名的文件
                    $tail = '';
                    if(isset($paths[$item->baseName . '.' . $item->extension])) {
                        $tail = rand(0, 1000);
                        $path = $dir . "/" . iconv("UTF-8", "GBK", $item->baseName . $tail)
                            . "." . $item->extension;
                    } else {
                        $path = $dir . "/" . iconv("UTF-8", "GBK", $item->baseName)
                            . "." . $item->extension;
                    }
                    $item->saveAs($path);
                    // 将文件尾加上，在文件重名的时候需要用到
                    $baseName = $item->baseName.$tail;
                    $paths[$baseName . '.' . $item->extension] =
                        $record->path_dir . "/" . $baseName
                        . "." . $item->extension;
                }
                $record->path = serialize($paths);
            }
            $record->update();
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
        if(empty($formData)) {
            return true;
        }

        if (!parent::updatePreHandler($formData)) {
            return true;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            if(isset($this->attachment) && !empty($this->attachment)) {
                $dir = Yii::getAlias('@app').$this->path_dir;
                if(!file_exists($dir)) {
                    mkdir($dir,0777,true);
                }
                $paths = [];
                foreach($this->attachment as $index => $item) {
                    $path = $dir . "/" .   iconv("UTF-8", "GBK", $item->baseName)
                        . "." . $item->extension;
                    $item->saveAs($path);
                    $paths[$item->baseName . '.' . $item->extension] =
                        $this->path_dir . "/" .   $item->baseName
                        . "." . $item->extension;
                }
                $this->path = serialize($paths);
            }
            $this->insert();
        }catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function getRecordByUuid($uuid)
    {
        // TODO: Implement getRecordByUuid() method.
    }

    public function deleteRecordByUuid($uuid)
    {
        if(empty($uuid)) {
            return false;
        }

        $record = self::find()->andWhere(['uuid'=>$uuid])->one();
        if(empty($record)) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try{
            if(!empty($record->path)) {
                $paths = unserialize($record->path);
                $len = count($paths);
                $i = 1;
                foreach($paths as $item) {
                    $path = Yii::getAlias("@app") .iconv("UTF-8", "GBK", $item);
                    if(file_exists($path)) {
                        unlink($path);
                        if($i == $len) {
                            preg_match('/[\/\\\](\w+[\/\\\])+/', $item, $match);
                            $dir = Yii::getAlias('@app').iconv("UTF-8", "GBK", $match[0]);
                            rmdir($dir);
                        }
                    }
                    $i++;
                }
            }
            $record->delete();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
            return false;
        }

        $transaction->commit();
        return true;
    }
}