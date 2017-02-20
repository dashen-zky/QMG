<?php
use backend\models\ViewHelper;
use yii\helpers\Html;
use backend\modules\fin\payment\models\PaymentConfig;
use backend\modules\fin\payment\models\PaymentList;
$paymentConfig = new PaymentConfig();
?>
<?= Html::beginForm($action, 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
    <table class="table ">
        <tbody>
        <tr>
            <td class="col-md-1">银行流水号</td>
            <td class="col-md-3">
                <?= Html::textInput('ListFilterForm[bank_series_number]',
                    isset($formData['bank_series_number'])?$formData['bank_series_number']:null,
                    [
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td class="col-md-1">付款方</td>
            <td class="col-md-3">
                <?= Html::textInput('ListFilterForm[payment]',
                    isset($formData['payment'])?$formData['payment']:null,
                    [
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td class="col-md-1">金额</td>
            <td class="col-md-3">
                <span class="col-md-6" style="padding: 0px">
                    <?= Html::textInput('ListFilterForm[min_money]',
                        isset($formData['min_money'])?$formData['min_money']:'',
                        [
                            'class' => 'form-control col-md-12',
                            'placeholder'=>'最小金额'
                        ]) ?>
                </span>
                <span class="col-md-6" style="padding: 0px">
                    <?= Html::textInput('ListFilterForm[max_money]',
                        isset($formData['max_money'])?$formData['max_money']:'',
                        [
                            'class' => 'form-control col-md-12',
                            'placeholder'=>'最大金额'
                        ]) ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>录入人</td>
            <td>
                <?= Html::textInput('ListFilterForm[created_name]',
                    isset($formData['created_name'])?$formData['created_name']:null,
                    [
                        'class' => 'form-control col-md-12',
                    ]) ?>
            </td>
            <td>录入时间</td>
            <td>
                <span class="col-md-6" style="padding: 0px">
                <?= Html::textInput('ListFilterForm[min_time]',
                    isset($formData['min_time'])?$formData['min_time']:'',
                    [
                        'class' => 'input-section datetimepicker form-control col-md-12',
                        'placeholder'=>'开始日期'
                    ]) ?>
                </span>
                <span class="col-md-6" style="padding: 0px">
                    <?= Html::textInput('ListFilterForm[max_time]',
                        isset($formData['max_time'])?$formData['max_time']:'',
                        [
                            'class' => 'input-section datetimepicker form-control col-md-12',
                            'placeholder'=>'截止日期'
                        ]) ?>
                </span>
            </td>
            <td>收款时间</td>
            <td>
                <span class="col-md-6" style="padding: 0px">
                <?= Html::textInput('ListFilterForm[min_receive_time]',
                    isset($formData['min_receive_time'])?$formData['min_receive_time']:'',
                    [
                        'class' => 'input-section datetimepicker form-control col-md-12',
                        'placeholder'=>'开始日期'
                    ]) ?>
                </span>
                <span class="col-md-6" style="padding: 0px">
                    <?= Html::textInput('ListFilterForm[max_receive_time]',
                        isset($formData['max_receive_time'])?$formData['max_receive_time']:'',
                        [
                            'class' => 'input-section datetimepicker form-control col-md-12',
                            'placeholder'=>'截止日期'
                        ]) ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>
                <?= Html::submitButton('搜索', ['class' => 'form-control btn btn-primary submit col-md-12']) ?>
            </td>
            <td colspan="5"></td>
        </tr>
        </tbody>
    </table>
<?= Html::endForm()?>