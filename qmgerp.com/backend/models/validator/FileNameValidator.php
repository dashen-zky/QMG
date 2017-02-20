<?php
namespace backend\models\validator;
use yii\validators\Validator;
use yii\bootstrap\Html;
use Yii;
use yii\web\JsExpression;
use yii\validators\ValidationAsset;
use yii\helpers\Json;
use yii\web\UploadedFile;

/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 17-1-8
 * Time: 下午8:31
 */
class FileNameValidator extends Validator
{
    public $not = false;
    public $pattern;

    public function validateValue($value) {
        $value = (array)$value;
        foreach ($value as $item) {
            if (!$item instanceof UploadedFile) {
                return [$this->message, []];
            }

            if (!preg_match($this->pattern, $item->name)) {
                return [$this->message, []];
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $pattern = Html::escapeJsRegularExpression($this->pattern);

        $options = [
            'pattern' => new JsExpression($pattern),
            'not' => $this->not,
            'message' => Yii::$app->getI18n()->format($this->message, [
                'attribute' => $model->getAttributeLabel($attribute),
            ], Yii::$app->language),
        ];
        if ($this->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        ValidationAsset::register($view);

        return 'yii.validation.regularExpression(value, messages, ' . Json::htmlEncode($options) . ');';
    }
}