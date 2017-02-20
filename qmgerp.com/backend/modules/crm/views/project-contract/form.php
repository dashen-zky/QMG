<?php
use yii\widgets\ActiveForm;
use backend\models\ViewHelper;
use backend\modules\fin\models\contract\ContractConfig;
use yii\helpers\Url;

$contractConfig = new ContractConfig();
?>
<?php $form = ActiveForm::begin([
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'form-horizontal ' . $formClass,
        'data-parsley-validate' => "true",
    ],
    'method' => 'post',
    'enableClientValidation'=>true,
    'action' => $action,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
<input hidden name="ProjectContractForm[back_url]" value="<?= isset($back_url)?$back_url:''?>">
<?php if($show) :?>
    <a href="javascript:;" class="editForm" style="font-size: 15px; float: right"><i class="fa fa-2x fa-pencil"></i>编辑</a>
<?php endif?>
<input type="hidden" name="ProjectContractForm[project_uuid]" value="<?= isset($projectContract['project_uuid'])?$projectContract['project_uuid']:''?>">
<input type="hidden" name="ProjectContractForm[code]" value="<?= $projectContract['code']?>">
<input type="hidden" name="ProjectContractForm[uuid]" value="<?=
isset($projectContract['uuid'])?$projectContract['uuid']:''
?>">
<table class="table project-contract-table">
    <tbody>
    <tr>
        <td>客户名称</td>
        <td>
            <?= $form->field($model,'customer_name')->textInput([
                
                'disabled'=>true,
                'value'=>isset($projectContract['customer_name'])?$projectContract['customer_name']:''
            ])->label(false)?>
        </td>
        <td>项目名称</td>
        <td>
            <?= $form->field($model,'project_name')->textInput([
                
                'disabled'=>true,
                'value'=>isset($projectContract['project_name'])?$projectContract['project_name']:''
            ])->label(false)?>
        </td>
        <td>项目经理</td>
        <td>
            <?= $form->field($model,'project_manager_name')->textInput([
                
                'disabled'=>true,
                'value'=>isset($projectContract['project_manager_name'])?$projectContract['project_manager_name']:''
            ])->label(false)?>
        </td>
        <td>销售</td>
        <td>
            <?= $form->field($model,'sales_name')->textInput([
                
                'disabled'=>true,
                'value'=>isset($projectContract['sales_name'])?$projectContract['sales_name']:''
            ])->label(false)?>
        </td>
    </tr>
    <tr>
        <td>合同负责人</td>
        <td>
            <?= $form->field($model,'duty_name')->textInput([
                
                'disabled'=>true,
                'value'=>isset($projectContract['duty_name'])?$projectContract['duty_name']:''
            ])->label(false)?>
        </td>
        <td>*金额</td>
        <td>
            <?= $form->field($model,'money')->textInput([
                
                'data-parsley-required'=>'true',
                'data-parsley-type'=>"number",
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                'value'=>isset($projectContract['money'])?$projectContract['money']:''
            ])->label(false)?>
        </td>
        <td>合同状态</td>
        <td>
            <?= $form->field($model, 'status')->
            dropDownList($contractConfig->getList('status'),[
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'options'=>ViewHelper::defaultValueForDropDownList(true, $projectContract,'status'),
            ])->
            label(false)?>
        </td>
        <td colspan="2">
    </tr>
    <tr>
        <td>合同编码</td>
        <td>
            <?= $form->field($model,'code')->textInput([
                'disabled'=>true,
                'value'=>
                isset($projectContract['code']) && isset($projectContract['type'])
                ?$projectContract['type'] . $projectContract['code']:''
            ])->label(false)?>
        </td>
        <td>开始时间</td>
        <td>
            <?= $form->field($model,'start_time')->textInput([
                'disabled'=>$show,
                'class'=>'enableEdit input-section datetimepicker form-control',
                
                'value'=>isset($projectContract['start_time'])?
                    ($projectContract['start_time']==0?'':date('Y-m-d',$projectContract['start_time']))
                    :''
            ])->label(false)?>
        </td>
        <td>结束时间</td>
        <td>
            <?= $form->field($model,'end_time')->textInput([
                'disabled'=>$show,
                'class'=>'enableEdit input-section datetimepicker form-control',
                
                'value'=>isset($projectContract['end_time'])?
                    ($projectContract['end_time']==0?'':date('Y-m-d',$projectContract['end_time']))
                    :''
            ])->label(false)?>
        </td>
        <td>签订时间</td>
        <td>
            <?= $form->field($model,'sign_time')->textInput([
                'disabled'=>$show,
                'class'=>'enableEdit input-section datetimepicker form-control',
                
                'value'=>isset($projectContract['sign_time'])?
                    ($projectContract['sign_time']==0?'':date('Y-m-d',$projectContract['sign_time']))
                    :''
            ])->label(false)?>
        </td>
    </tr>
    <tr>
        <td>甲方联系人</td>
        <td>
            <?= $form->field($model,'part_a_duty')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($projectContract['part_a_duty'])?$projectContract['part_a_duty']:''
            ])->label(false)?>
        </td>
        <td>甲方电话</td>
        <td>
            <?= $form->field($model,'part_a_phone')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($projectContract['part_a_phone'])?$projectContract['part_a_phone']:''
            ])->label(false)?>
        </td>
        <td>甲方传真</td>
        <td>
            <?= $form->field($model,'part_a_fax')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($projectContract['part_a_fax'])?$projectContract['part_a_fax']:''
            ])->label(false)?>
        </td>
        <td>甲方地址</td>
        <td>
            <?= $form->field($model,'part_a_address')->textInput([
                
                'value'=>isset($projectContract['part_a_address'])?$projectContract['part_a_address']:''
            ])->label(false)?>
        </td>
    </tr>
    <tr>
        <td>乙方联系人</td>
        <td>
            <?= $form->field($model,'part_b_duty')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($projectContract['part_b_duty'])?$projectContract['part_b_duty']:''
            ])->label(false)?>
        </td>
        <td>乙方电话</td>
        <td>
            <?= $form->field($model,'part_b_phone')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($projectContract['part_b_phone'])?$projectContract['part_b_phone']:''
            ])->label(false)?>
        </td>
        <td>乙方传真</td>
        <td>
            <?= $form->field($model,'part_b_fax')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($projectContract['part_b_fax'])?$projectContract['part_b_fax']:''
            ])->label(false)?>
        </td>
        <td>乙方地址</td>
        <td>
            <?= $form->field($model,'part_b_address')->textInput([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'value'=>isset($projectContract['part_b_address'])?$projectContract['part_b_address']:''
            ])->label(false)?>
        </td>
    </tr>
    <tr>
        <td>备注</td>
        <td colspan="7">
            <?= $form->field($model,'remarks')->
            textarea([
                'class'=>'enableEdit form-control',
                'disabled'=>$show,
                
                'rows'=>3,
                'value'=>isset($projectContract['remarks'])?$projectContract['remarks']:''])->
            label(false)?>
        </td>
    </tr>
    <tr>
        <td>合同附件*</td>
        <td><?= $form->field($model,'attachment[]')->fileInput([
                'multiple' => true,
                'class'=>'enableEdit',
                'disabled'=>$show,
                'data-parsley-required'=>'true',
            ])?></td>
        <td colspan="4">
            <?php if(isset($projectContract['path']) && !empty($projectContract['path'])) :?>
                <?php
                // 将path字段解析出来
                $projectContract['path'] = unserialize($projectContract['path']);
                ?>
                <?php foreach($projectContract['path'] as $key=>$path) :?>
                    <div>
                        <a href="<?= Url::to([
                            '/crm/project-contract/attachment-download',
                            'path'=>$path,
                            'file_name'=>$key,
                        ])?>" class="enableEdit" <?= $show?'disabled':''?>><?= $key?></a>
                        <span style="float: right">
                            <a href="#" name="<?= Url::to([
                                '/crm/project-contract/attachment-delete',
                                'path'=>$path,
                                'uuid'=>$projectContract['uuid'],
                            ])?>" class="attachmentDelete enableEdit" <?= $show?'disabled':''?>>删除</a>
                        </span>
                    </div>
                <?php endforeach?>
            <?php endif?>
        </td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td colspan="4">
            <input type="submit" <?= $show?'disabled':''?> value="提交" class="enableEdit form-control btn btn-primary">
        </td>
        <td colspan="2"></td>
    </tr>
    </tbody>
</table>
<?php ActiveForm::end()?>
