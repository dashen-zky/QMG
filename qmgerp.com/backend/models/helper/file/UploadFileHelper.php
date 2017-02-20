<?php
namespace backend\models\helper\file;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/13 0013
 * Time: 上午 11:59
 */
use Yii;
use yii\web\UploadedFile;
class UploadFileHelper
{
    public static function deleteAllAttachments($attachmentPathSeries) {
        if(empty($attachmentPathSeries)) {
            return ;
        }
        
        /**
         * 先删除文件夹下面所有文件，在删除文件夹
         */
        $attachmentPath = unserialize($attachmentPathSeries);
        $dir = null;
        foreach ($attachmentPath as $fileName => $path) {
            $realPath = Yii::getAlias("@app").iconv("UTF-8", "GBK", $path);
            if(file_exists($realPath)) {
                unlink($realPath);
            }

            if(empty($dir)) {
                $dir = Yii::getAlias("@app").iconv("UTF-8", "GBK", str_replace($fileName, '', $path));
            }
        }

        if(is_dir($dir)) {
            rmdir($dir);
        }
    }
    // 把路径字符字符串返回去
    public static function uploadWhileInsert($record, $attachment, $relateDir, $path_filed = 'path', $deleteTempFile = true) {
        if(empty($record)
            || empty($attachment)
            || empty($relateDir)) {
            return null;
        }

        $dir = Yii::getAlias('@app').$relateDir;
        if(!file_exists($dir)) {
            mkdir($dir,0777,true);
        }
        $paths = [];
        foreach($attachment as $index => $item) {
            $path = $dir . "/" .   iconv("UTF-8", "GBK", $item->baseName)
                . "." . $item->extension;
            $item->saveAs($path, $deleteTempFile);
            $paths[$item->baseName . '.' . $item->extension] =
                $relateDir . "/" .   $item->baseName
                . "." . $item->extension;
        }
        $record->$path_filed = serialize($paths);
    }

    public static function uploadWhileUpdate($record, $attachment, $relateDir, $path_filed = 'path', $deleteTempFile = true) {
        if(empty($record)
            || empty($attachment)
            || empty($relateDir)) {
            return null;
        }

        $dir = Yii::getAlias('@app') . $relateDir;
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $paths = unserialize($record->$path_filed);
        foreach ($attachment as $index => $item) {
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
            $item->saveAs($path, $deleteTempFile);
            // 将文件尾加上，在文件重名的时候需要用到
            $baseName = $item->baseName.$tail;
            $paths[$baseName . '.' . $item->extension] =
                $relateDir . "/" . $baseName
                . "." . $item->extension;
        }
        $record->$path_filed = serialize($paths);
    }
}