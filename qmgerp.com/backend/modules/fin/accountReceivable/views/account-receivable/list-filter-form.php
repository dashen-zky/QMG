<?php
use backend\models\ViewHelper;
use yii\helpers\Html;
use backend\modules\crm\models\project\model\ProjectConfig;
use backend\modules\crm\models\project\record\Project;
$projectConfig = new ProjectConfig();
?>
<?= Html::beginForm($action, 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
<input hidden name="ListFilterForm[entrance]" value="<?= Project::AccountReceivableEntrance?>">
    <table class="table ">
        <tbody>
        <tr>
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
        </tr>
        <tr>
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
            <td class="col-md-1">项目状态</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[status]',
                        null,
                        ViewHelper::appendElementOnDropDownList($projectConfig->getList('projectStatus')),
                        [
                            'class'=>'form-control department col-md-12',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'status'),
                        ]
                    ) ?>
                </div>
            </td>
            <td class="col-md-1">合同状态</td>
            <td class="col-md-3">
                <div class="position col-md-12">
                    <?php $contractConfig = new \backend\modules\fin\models\contract\ContractConfig();?>
                    <?= Html::dropDownList(
                        'ListFilterForm[contract_status]',
                        null,
                        ViewHelper::appendElementOnDropDownList($contractConfig->getList('status')),
                        [
                            'class'=>'form-control',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'contract_status'),
                        ]
                    ) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>回款状态</td>
            <td>
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[receive_money_status]',
                        null,
                        ViewHelper::appendElementOnDropDownList($projectConfig->getList('receiveMoneyStatus')),
                        [
                            'class'=>'form-control',
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'receive_money_status'),
                        ]
                    ) ?>
                </div>
            </td>
            <td>编号</td>
            <td>
                <div class="col-md-12">
                    <?= Html::input('text', 'ListFilterForm[code]',
                        isset($formData['code'])?$formData['code']:'',
                        [
                            'class' => 'form-control'
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