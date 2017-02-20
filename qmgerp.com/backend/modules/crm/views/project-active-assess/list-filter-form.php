<?php
use backend\models\ViewHelper;
use yii\helpers\Html;
$config = new \backend\modules\crm\models\project\model\ProjectConfig();
?>
<?= Html::beginForm(['/crm/project/active-assess-list-filter'], 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
    <table class="table ">
        <tbody>
        <tr>
            <td class="col-md-1">编号</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::input('text', 'ListFilterForm[code]',
                        isset($formData['code'])?$formData['code']:'',
                        [
                            'class' => 'form-control'
                        ]) ?>
                </div>
            </td>
            <td class="col-md-1">客户简称</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::textInput('ListFilterForm[customer_name]',
                        isset($formData['customer_name'])?$formData['customer_name']:'',
                        [
                            'class' => 'form-control col-md-12',
                            'disabled'=>isset($customer_uuid) && !empty($customer_uuid)?true:false,
                        ]) ?>
                </div>
            </td>
            <td class="col-md-1">项目名称</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::textInput('ListFilterForm[name]',
                        isset($formData['name'])?$formData['name']:'',
                        [
                            'class' => 'form-control col-md-12'
                        ]) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td class="col-md-1">项目经理</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::textInput('ListFilterForm[project_manager_name]',
                        isset($formData['project_manager_name'])?$formData['project_manager_name']:'',
                        [
                            'class' => 'form-control col-md-12'
                        ]) ?>
                </div>
            </td>
            <td class="col-md-1">销售</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::textInput('ListFilterForm[sales_name]',
                        isset($formData['sales_name'])?$formData['sales_name']:'',
                        [
                            'class' => 'form-control col-md-12'
                        ]) ?>
                </div>
            </td>
            <td colspan="2"></td>
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