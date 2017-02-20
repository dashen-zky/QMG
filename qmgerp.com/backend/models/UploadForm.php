<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/15 0015
 * Time: 下午 6:03
 */

namespace backend\models;


use yii\base\Model;

class UploadForm extends Model
{
    /**
     * @var UploadedFile|Null file attribute
     */
    public $file;
    public $fileRules;

    /**
     * @return array the validation rules.
     */
    protected function buildFileRules() {
        $rules = [['file'], 'file'];
        if(empty($this->fileRules) || !is_array($this->fileRules)) {
            return $rules;
        }
        foreach ($this->fileRules as $index => $value) {
            $rules[$index] = $value;
        }

        if(!isset($rules['maxFiles'])) {
            $rules['maxFiles'] = 10;
        }
        return $rules;
    }
    
    public function rules()
    {
        $fileRules = $this->buildFileRules();
        return [
            $fileRules,
            [
                'file',
                'match',
                'pattern'=>'/[^\s]+/u',
                'message'=>'文件名不能包含空格'
            ]
        ];
    }
}