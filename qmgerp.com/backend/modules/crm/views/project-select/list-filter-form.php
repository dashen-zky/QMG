<?php
use backend\models\ViewHelper;
use yii\helpers\Html;
use backend\modules\fin\payment\models\PaymentConfig;
$paymentConfig = new PaymentConfig();
?>

<?= Html::beginForm($action, 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
    <input hidden name="ListFilterForm[entrance]" value="<?= isset($entrance)?$entrance:''?>">
    <table class="table project-list-filter-table">
        <tbody>
        <tr>
            <td colspan="2">编码</td>
            <td colspan="3">
                <?= Html::textInput(
                    'ListFilterForm[code]',
                    isset($formData['code'])?$formData['code']:'',
                    [
                        'class'=>'form-control col-md-12',
                    ]
                )?>
            </td>
            <td colspan="2">项目名称</td>
            <td colspan="3">
                <?= Html::textInput(
                    'ListFilterForm[name]',
                    isset($formData['name'])?$formData['name']:'',
                    [
                        'class'=>'form-control col-md-12',
                    ]
                )?>
            </td>
            <td>
                <?= Html::submitButton('搜索', ['class' => 'col-md-2 form-control btn btn-primary submit']) ?>
            </td>
        </tr>
        </tbody>
    </table>
<?= Html::endForm()?>