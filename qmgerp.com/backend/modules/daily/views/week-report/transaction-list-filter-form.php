<?php
use yii\helpers\Html;
use backend\models\ViewHelper;
use backend\modules\daily\models\transaction\TransactionConfig;
$config = new TransactionConfig();
?>

<?= Html::beginForm(['/daily/transaction/effective-list-filter'], 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
    <input class='transaction-title' disabled hidden
           value="<?= isset($formData['transaction_title'])?$formData['transaction_title']:''?>">
    <input class='transaction-uuid' name="ListFilterForm[transaction_uuid]" hidden
           value="<?= isset($formData['transaction_uuid'])?$formData['transaction_uuid']:''?>">
    <table class="table">
        <tbody>
        <tr>
            <td class="col-md-1">标题</td>
            <td class="col-md-3">
                <?= Html::input('text', 'ListFilterForm[title]',
                    isset($formData['title'])?$formData['title']:'',
                    [
                        'class' => 'form-control'
                    ]) ?>
            </td>
            <td>任务截止日期</td>
            <td>
                <span class="col-md-6" style="padding: 0px">
                <?= Html::textInput('ListFilterForm[min_expect_finish_time]',
                    isset($formData['min_expect_finish_time'])?$formData['min_expect_finish_time']:'',
                    [
                        'class' => 'input-section datetimepicker form-control col-md-12',
                        'placeholder'=>'开始日期'
                    ]) ?>
                </span>
                <span class="col-md-6" style="padding: 0px">
                    <?= Html::textInput('ListFilterForm[max_expect_finish_time]',
                        isset($formData['max_expect_finish_time'])?$formData['max_expect_finish_time']:'',
                        [
                            'class' => 'input-section datetimepicker form-control col-md-12',
                            'placeholder'=>'截止日期'
                        ]) ?>
                </span>
            </td>
            <td>状态</td>
            <td>
                <?= Html::dropDownList('ListFilterForm[status]',
                    isset($formData['status'])?$formData['status']:'',
                    ViewHelper::appendElementOnDropDownList($config->getList('status')),[
                        'class' => 'form-control'
                    ])?>
            </td>
            <td>
                <?= Html::submitButton('搜索', ['class' => 'form-control btn btn-primary submit']) ?>
            </td>
        </tr>

        </tbody>
    </table>
<?= Html::endForm()?>