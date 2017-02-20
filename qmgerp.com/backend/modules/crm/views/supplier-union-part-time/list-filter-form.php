<?php
use backend\models\ViewHelper;
use yii\helpers\Html;
use backend\modules\fin\payment\models\PaymentConfig;
$paymentConfig = new PaymentConfig();
?>

<?= Html::beginForm($action, 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
    <table class="table supplier-union-part-time-list-filter-table">
        <tbody>
        <tr>
            <td class="col-md-1">类型</td>
            <td class="col-md-3">
                <?= Html::dropDownList(
                    'ListFilterForm[type]',
                    null,
                    ViewHelper::appendElementOnDropDownList(
                        [
                            1=>'供应商',
                            2=>'兼职',
                        ]
                    ),
                    [
                        'class'=>'form-control col-md-12',
                        'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'type'),
                    ]
                ) ?>
            </td>
            <td class="col-md-1">名称</td>
            <td class="col-md-3">
                <?= Html::textInput(
                    'ListFilterForm[name]',
                    isset($formData['name'])?$formData['name']:'',
                    [
                        'class'=>'form-control col-md-12',
                    ]
                )?>
            </td>
            <td class="col-md-1">编码</td>
            <td class="col-md-3">
                <?= Html::textInput(
                    'ListFilterForm[code]',
                    isset($formData['code'])?$formData['code']:'',
                    [
                        'class'=>'form-control col-md-12',
                    ]
                )?>
            </td>
        </tr>
        <tr>
            <td>
                <?= Html::submitButton('搜索', ['class' => 'form-control btn btn-primary submit']) ?>
            </td>
            <td colspan="5"></td>
        </tr>
        </tbody>
    </table>
<?= Html::endForm()?>