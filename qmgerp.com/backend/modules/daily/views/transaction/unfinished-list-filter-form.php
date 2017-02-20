<?php
use backend\models\ViewHelper;
use yii\helpers\Html;
use backend\modules\fin\payment\models\PaymentConfig;
$paymentConfig = new PaymentConfig();
?>

<?= Html::beginForm(['/daily/transaction/unfinished-list-filter'], 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
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
            <td>
                <?= Html::submitButton('搜索', ['class' => 'form-control btn btn-primary submit']) ?>
            </td>
        </tr>

        </tbody>
    </table>
<?= Html::endForm()?>