<?php
use yii\helpers\Html;
?>
<?= Html::beginForm(['/crm/touch-record/project-touch-record-list-filter'], 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
    <table class="table">
        <tbody>
        <tr>
            <td>项目</td>
            <td>
                <div class="col-md-12">
                    <?= Html::input('text', 'ListFilterForm[project_name]',
                        isset($formData['project_name'])?$formData['project_name']:'',
                        [
                            'class' => 'form-control'
                        ]) ?>
                </div>
            </td>
            <td class="col-md-1">跟进人</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::textInput('ListFilterForm[follow_name]',
                        isset($formData['follow_name'])?$formData['follow_name']:'',
                        [
                            'class' => 'form-control col-md-12'
                        ]) ?>
                </div>
            </td>
            <td class="col-md-1">跟进时间</td>
            <td class="col-md-3">
                <div class="col-md-12">
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
                </div>
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