<?php
use yii\widgets\ActiveForm;
use backend\models\ViewHelper;
use yii\helpers\Url;
use backend\modules\crm\models\project\model\ProjectForm;

?>
<?php $form = ActiveForm::begin([
    'options'=>['enctype'=>'multipart/form-data',
        'class' => 'form-horizontal ' . $formClass,
        'data-parsley-validate' => "true",
    ],
    'method' => 'post',
    'action' => $action,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
<?php if(!isset($enableEdit) || $enableEdit):?>
    <div style="float: right">
        <a href="javascript:;" class="editForm" style="font-size: 15px"><i class="fa fa-2x fa-pencil"></i>编辑</a>
    </div>
<?php endif?>
<input hidden name="ProjectForm[customer_uuid]" value="<?= $formData['customer_uuid']?>">
<input hidden name="ProjectForm[uuid]" value="<?= isset($formData['uuid'])?$formData['uuid']:''?>">
    <table class="table project-table">
        <tbody>
        <tr>
            <td>客户名称</td>
            <td>
                <?= $form->field($model,'customer_name')->textInput([
                    
                    'disabled'=>'true',
                    'value'=>isset($formData['customer_name'])?$formData['customer_name']:''
                ])->label(false)?>
            </td>
            <td><span data-color="red">*</span> 项目名称</td>
            <td>
                <?= $form->field($model,'name')->textInput([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    
                    'value'=>isset($formData['name'])?$formData['name']:'',
                    'data-parsley-required'=>'true',
                ])->label(false)?>
            </td>
            <td>项目编号</td>
            <td>
                <input
                    value="<?= isset($formData['code'])?$formData['code']:''?>"
                    class="form-control"
                    type="hidden"
                    name="ProjectForm[code]"
                >
                <input
                    value="<?= isset($formData['code'])?ProjectForm::codePrefix.$formData['code']:''?>"
                   class="form-control"
                   disabled
                   name="ProjectForm[code1]"
                >
            </td>
            <td>状态</td>
            <td>
                <?= $form->field($model, 'status')->
                dropDownList($model->getList('projectStatus'),[
                    'class'=>'form-control',
                    'disabled'=>true,
                    'options'=>ViewHelper::defaultValueForDropDownList($show, $formData,'status'),
                ])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>客户类别</td>
            <td>
                <?php
                    $customerConfig = new \backend\modules\crm\models\customer\model\CustomerConfig();
                    $customerConfig->config = $customerConfig->generateConfig();
                ?>
                <?= $form->field($model,'customer_type')->textInput([
                    
                    'disabled' => true,
                    'value'=>isset($formData['customer_type'])?
                        (
                            isset($customerConfig->config['type'][$formData['customer_type']])?
                            $customerConfig->config['type'][$formData['customer_type']]:''
                        )
                        :''
                ])->label(false)?>
            </td>
            <td>业务板块</td>
            <td>
                <?= $form->field($model, 'business_id')->
                dropDownList($model->getList('business'),[
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'business_id'),
                ])->
                label(false)?>
            </td>
            <td>项目金额</td>
            <td>
                <?= $form->field($model,'money_amount')->textInput([
                    'data-parsley-type'=>"number",
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,

                    'value'=>isset($formData['money_amount'])?$formData['money_amount']:''
                ])->label(false)?>
            </td>
            <td>合同金额</td>
            <td>
                <?= $form->field($model,'actual_money_amount')->textInput([
                    'data-parsley-type'=>"number",
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'value'=>isset($formData['actual_money_amount'])?$formData['actual_money_amount']:''
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>合同状态</td>
            <td>
                <?php $contractConfig = new \backend\modules\fin\models\contract\ContractConfig();?>
                <?= $form->field($model, 'contract_status')->
                dropDownList($contractConfig->getList('status'),[
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'contract_status'),
                ])->
                label(false)?>
            </td>
            <td>合同签订时间</td>
            <td>
                <?= $form->field($model,'sign_time')->textInput([
                    'class'=>'input-section datetimepicker form-control enableEdit',
                    'disabled'=>$show,
                    'value'=>isset($formData['sign_time'])?
                    ($formData['sign_time']!=0?date("Y-m-d",$formData['sign_time']):''):'',
                ])->label(false)?>
            </td>
            <td>回款状态</td>
            <td>
                <?= $form->field($model, 'receive_money_status')->
                dropDownList($model->getList('receiveMoneyStatus'),[
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'receive_money_status'),
                ])->
                label(false)?>
            </td>
            <td>返点金额</td>
            <td>
                <?= $form->field($model,'return_money_amount')->textInput([
                    'class'=>'enableEdit form-control',
                    'data-parsley-type'=>"number",
                    'disabled'=>$show,
                    'value'=>isset($formData['return_money_amount'])?$formData['return_money_amount']:''
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>开始时间</td>
            <td>
                <?= $form->field($model,'start_time')->textInput([
                    'disabled'=>$show,
                    'class'=>'input-section datetimepicker form-control enableEdit',
                    'value'=>isset($formData['start_time'])?
                        ($formData['start_time']!=0?date("Y-m-d",$formData['start_time']):''):''
                ])->label(false)?>
            </td>
            <td>预计结束时间</td>
            <td>
                <?= $form->field($model,'end_time')->textInput([
                    'class'=>'input-section datetimepicker form-control enableEdit',
                    'disabled'=>$show,
                    'value'=>isset($formData['end_time'])?
                        ($formData['end_time']!=0?date("Y-m-d",$formData['end_time']):''):''
                ])->label(false)?>
            </td>
            <td>最近跟进时间</td>
            <td>
                <?= $form->field($model,'last_touch_time')->textInput([

                    'disabled'=>true,
                    'value'=>isset($formData['last_touch_time'])?
                        ($formData['last_touch_time']!=0?date("Y-m-d",$formData['last_touch_time']):''):''
                ])->label(false)?>
            </td>
            <td>下次跟进时间</td>
            <td>
                <?= $form->field($model,'next_touch_time')->textInput([

                    'disabled' => true,
                    'value'=>isset($formData['next_touch_time'])?
                        ($formData['next_touch_time']!=0?date("Y-m-d",$formData['next_touch_time']):''):''
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>开票状态</td>
            <td>
                <?= $form->field($model, 'stamp_status')->
                dropDownList($model->getList('stampStatus'),[
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'stamp_status'),
                ])->
                label(false)?>
            </td>
            <td>创建时间</td>
            <td>
                <input disabled class="form-control"
                   value="<?= isset($formData['create_time'])?(($formData['create_time'] != 0)?date("Y-m-d",$formData['create_time']):''):''?>">
            </td>
            <td>创建人</td>
            <td>
                <input disabled class="form-control"
                       value="<?= isset($formData['created_name'])?$formData['created_name']:''?>">
            </td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td>客户联系人</td>
            <td colspan="2">
                <input type="hidden" name="ProjectForm[project_contact_uuid]"
                       value="<?= isset($formData['project_contact_uuid'])?
                           $formData['project_contact_uuid']:''?>">
                <?= $form->field($model,'project_contact_name')->textInput([

                    'disabled'=>true,
                    'value'=>isset($formData['project_contact_name'])?
                        $formData['project_contact_name']:''
                ])->label(false)?>
            </td>
            <td>
                <a href="javascript:;"
                        class="ShowSelectContactPanel enableEdit"
                   <?= $show?'disabled':''?>
                        name="<?= Url::to([
                            '/crm/project/contact-list',
                            'customer_uuid'=>$formData['customer_uuid'],
                            'selectClass'=>'selectContact'
                        ])?>">
                    <i class="fa fa-2x fa-edit"></i>
                </a>
<!--                <button type="button" class="btn btn-xs btn-primary showRemoveSelectedContactPanel" name="">-->
<!--                    <i class="fa fa-2x fa-minus"></i>-->
<!--                </button>-->
            </td>
            <td>客户负责人</td>
            <td colspan="2">
                <input type="hidden" name="ProjectForm[project_duty_uuid]"
                       value="<?= isset($formData['project_duty_uuid'])?
                    $formData['project_duty_uuid']:''?>">
                <?= $form->field($model,'project_duty_name')->textInput([

                    'disabled'=>true,
                    'value'=>isset($formData['project_duty_name'])?
                        $formData['project_duty_name']:''
                ])->label(false)?>
            </td>
            <td>
                <a href="javascript:;"
                        class="ShowSelectDutyPanel enableEdit"
                    <?= $show?'disabled':''?>
                        name="<?= Url::to([
                            '/crm/project/contact-list',
                            'customer_uuid'=>$formData['customer_uuid'],
                            'selectClass'=>'selectDuty'
                        ])?>">
                    <i class="fa fa-2x fa-edit"></i>
                </a>
            </td>
        </tr>
        <tr>
            <td>项目经理</td>
            <td colspan="2">
                <input type="hidden" data-parsley-required='true' name="ProjectForm[project_manager_uuid]"
                       value="<?= isset($formData['project_manager_uuid'])?
                           $formData['project_manager_uuid']:''?>">
                <?= $form->field($model,'project_manager_name')->textInput([
                    'data-parsley-required'=>'true',

                    'disabled'=>true,
                    'value'=>isset($formData['project_manager_name'])?
                        $formData['project_manager_name']:''
                ])->label(false)?>
            </td>
            <td>
                <a href="javascript:;"
                        class="showSelectManagerPanel enableEdit"
                    <?= $show?'disabled':''?>
                        name="<?= Url::to([
                            '/crm/project/employee-list',
                            'selectClass'=>'selectProjectManager'
                        ])?>">
                    <i class="fa fa-2x fa-edit"></i>
                </a>
            </td>
            <td>项目成员</td>
            <td colspan="2">
                <input type="hidden" name="ProjectForm[project_member_uuid]"
                       value="<?= isset($formData['project_member_uuid'])?
                           $formData['project_member_uuid']:''?>">
                <?= $form->field($model,'project_member_name')->textInput([

                    'disabled'=>true,
                    'value'=>isset($formData['project_member_name'])?$formData['project_member_name']:''
                ])->label(false)?>
            </td>
            <td>
                <a href="javascript:;"
                        class="showSelectMemberPanel enableEdit"
                    <?= $show?'disabled':''?>
                        name="<?= Url::to([
                            '/crm/project/employee-list',
                            'selectClass'=>'selectProjectMember',
                        ])?>">
                    <i class="fa fa-2x fa-edit"></i>
                </a>
            </td>
        </tr>
        <tr>
            <td>项目简介</td>
            <td colspan="7">
                <?= $form->field($model,'description')->
                textarea([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    
                    'rows'=>3,
                    'value'=>isset($formData['description'])?$formData['description']:''
                ])->
                label(false)?>
            </td>
        </tr>
<?php if(isset($formData['failed_reason']) && !empty($formData['failed_reason'])) :?>
            <tr>
                <td>失败原因</td>
                <td colspan="7">
                    <?= \yii\bootstrap\Html::textarea('ProjectForm[failed_reason]',
                        $formData['failed_reason'], [
                            'class'=>'form-control',
                            'disabled'=>true,
                            'rows'=>2
                        ])?>
                </td>
            </tr>
<?php endif;?>
<?php if(isset($formData['active_assess_refuse_reason']) && !empty($formData['active_assess_refuse_reason'])) :?>
        <tr>
            <td>立项不通过原因</td>
            <td colspan="7">
                <?= \yii\bootstrap\Html::textarea('ProjectForm[active_assess_refuse_reason]',
                    $formData['active_assess_refuse_reason'], [
                        'class'=>'form-control',
                        'disabled'=>true,
                        'rows'=>2
                    ])?>
            </td>
        </tr>
<?php endif;?>
<?php if(isset($formData['done_assess_refuse_reason']) && !empty($formData['done_assess_refuse_reason'])) :?>
        <tr>
            <td>结案不通过原因</td>
            <td colspan="7">
                <?= \yii\bootstrap\Html::textarea('ProjectForm[done_assess_refuse_reason]',
                    $formData['done_assess_refuse_reason'], [
                        'class'=>'form-control',
                        'disabled'=>true,
                        'rows'=>2
                    ])?>
            </td>
        </tr>
<?php endif;?>
        <tr>
            <td>成本预算表</td>
            <td colspan="3">
                <?= $form->field($model,'_budget_attachment[]')->fileInput([
                    'multiple' => true,
                    'class'=>'enableEdit',
                    'disabled'=>$show,
                ])->label(false)?>
                <?php if(isset($formData['budget_attachment']) && !empty($formData['budget_attachment'])) :?>
                    <?php
                    // 将attachment字段解析出来
                    $formData['budget_attachment'] = unserialize($formData['budget_attachment']);
                    ?>
                    <?php foreach($formData['budget_attachment'] as $key=>$path) :?>
                        <div>
                            <a href="<?= Url::to([
                                '/crm/project/attachment-download',
                                'path'=>$path,
                                'file_name'=>$key,
                            ])?>"><?= $key?></a>
                            <span class="displayBlockWhileEdit" style="float: right; display: <?= !isset($show) || !$show?'block':'none'?>">
                            <a href="#" url="<?= Url::to([
                                '/crm/project/budget-attachment-delete',
                                'path'=>$path,
                                'uuid'=>$formData['uuid'],
                            ])?>" class="attachmentDelete">删除</a>
                        </span>
                        </div>
                    <?php endforeach?>
                <?php endif?>
            </td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td>立项资料</td>
            <td colspan="3">
                <?= $form->field($model,'_active_attachment[]')->fileInput([
                    'multiple' => true,
                    'class'=>'enableEdit',
                    'disabled'=>$show,
                ])->label(false)?>
                <?php if(isset($formData['active_attachment']) && !empty($formData['active_attachment'])) :?>
                    <?php
                    // 将attachment字段解析出来
                    $formData['active_attachment'] = unserialize($formData['active_attachment']);
                    ?>
                    <?php foreach($formData['active_attachment'] as $key=>$path) :?>
                        <div>
                            <a href="<?= Url::to([
                                '/crm/project/attachment-download',
                                'path'=>$path,
                                'file_name'=>$key,
                            ])?>"><?= $key?></a>
                            <span class="displayBlockWhileEdit" style="float: right; display: <?= !isset($show) || !$show?'block':'none'?>">
                            <a href="#" url="<?= Url::to([
                                '/crm/project/active-attachment-delete',
                                'path'=>$path,
                                'uuid'=>$formData['uuid'],
                            ])?>" class="attachmentDelete">删除</a>
                        </span>
                        </div>
                    <?php endforeach?>
                <?php endif?>
            </td>
            <td>结案资料</td>
            <td colspan="3">
                <?= $form->field($model,'_done_attachment[]')->fileInput([
                    'multiple' => true,
                    'class'=>'enableEdit',
                    'disabled'=>$show,
                ])->label(false)?>
                <?php if(isset($formData['done_attachment']) && !empty($formData['done_attachment'])) :?>
                    <?php
                    // 将attachment字段解析出来
                    $formData['done_attachment'] = unserialize($formData['done_attachment']);
                    ?>
                    <?php foreach($formData['done_attachment'] as $key=>$path) :?>
                        <div>
                            <a href="<?= Url::to([
                                '/crm/project/attachment-download',
                                'path'=>$path,
                                'file_name'=>$key,
                            ])?>"><?= $key?></a>
                            <span class="displayBlockWhileEdit" style="float: right; display: <?= !isset($show) || !$show?'block':'none'?>">
                            <a href="#" url="<?= Url::to([
                                '/crm/project/done-attachment-delete',
                                'path'=>$path,
                                'uuid'=>$formData['uuid'],
                            ])?>" class="attachmentDelete">删除</a>
                        </span>
                        </div>
                    <?php endforeach?>
                <?php endif?>
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="3">
                <input type="submit"
                       <?= $show?'disabled':''?>
                       value="提交" id="editContactFormSubmit" class="form-control btn-primary col-md-3 enableEdit">
            </td>
            <td></td>
            <td></td>
        </tr>
        </tbody>
    </table>
<!--选择联系人列表-->
<?php ActiveForm::end()?>
<?= $this->render('/contact/contact-select-panel',[
]);?>
<!--// 选择项目经理和项目成员-->
<?= $this->render('@hr/views/employee/employee-select-list-panel.php',[
]);?>
<?php
$JS = <<<JS
$(function() {
    //显示选择联系人列表
    $('.project-table').on('click','.ShowSelectContactPanel',function() {
    var disabled = $(this).attr('disabled');
            if(disabled === 'disabled') {
                return false;
            }
            var url = $(this).attr('name');
            var uuids = $(this).parents('form').find('input[name="ProjectForm[project_contact_uuid]"]').val();
            url += '&uuids='+uuids;
            $.get(
            url,
            function(data,status) {
                if('success' == status) {
                    $(".SelectContactPanel .panel-body .contact-list").html(data);
                    $(".SelectContactPanel").modal('show');
                }
            });
        });
    // 选择客户负责人
    $('.project-table').on('click','.ShowSelectDutyPanel',function() {
    var disabled = $(this).attr('disabled');
            if(disabled === 'disabled') {
                return false;
            }
        var url = $(this).attr('name');
        var uuids = $(this).parents('form').find('input[name="ProjectForm[project_duty_uuid]"]').val();
        url += '&uuids='+uuids;
        $.get(
        url,
        function(data,status) {
            if('success' == status) {
                $(".SelectContactPanel .panel-body .contact-list").html(data);
                $(".SelectContactPanel").modal('show');
            }
        });
    });

    // 显示选择项目经理
    $('.project-table').on('click','.showSelectManagerPanel',function() {
        var disabled = $(this).attr('disabled');
        if(disabled === 'disabled') {
            return false;
        }
        var url = $(this).attr('name');
        url += '&uuids=' + $(this).parents('form').find('input[name="ProjectForm[project_manager_uuid]"]').val();
        $.get(
        url,
        function(data,status) {
            if('success' == status) {
                $(".employeeSelectListPanel .panel-body div").html(data);
                $(".employeeSelectListPanel").modal('show');
            }
        });
    });

    // 显示选择项目成员
    $('.project-table').on('click','.showSelectMemberPanel',function() {
        var disabled = $(this).attr('disabled');
        if(disabled === 'disabled') {
            return false;
        }
        var url = $(this).attr('name');
        url += '&uuids=' + $(this).parents('form').find('input[name="ProjectForm[project_member_uuid]"]').val();
        $.get(
        url,
        function(data,status) {
            if('success' == status) {
                $(".employeeSelectListPanel .panel-body div").html(data);
                $(".employeeSelectListPanel").modal('show');
            }
        });
    });
    
    $('.project-table').on('click','.attachmentDelete',function() {
        var url = $(this).attr('url');
        var self = $(this);
        $.get(
        url,
        function(data,status) {
            if('success' == status) {
                if(data) {
                    self.parentsUntil('td').remove();
                }
            }
        });
    });
});
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>