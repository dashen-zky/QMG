<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use backend\modules\hr\models\EmployeeForm;
use backend\modules\hr\models\config\EmployeeConfig;
use yii\helpers\Url;
use backend\models\ViewHelper;
use yii\helpers\Json;
use backend\modules\hr\models\EmployeeAccount;
$config = new EmployeeConfig();
?>

<?php
$JS = <<<Js
    $(document).ready(function() {
            // 附件删除功能
         $('.EmployeeForm').on('click','.attachmentDelete',function() {
            var url = $(this).attr('name');
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

        $('.choosePosition').click(function() {
            var url = $(this).attr('name');
            var position_uuid = $("form#employeeForm input[name='EmployeeForm[position_uuid]']").val();
            url += '&position_uuid='+position_uuid;
            $.get(
            url,
            function(data,status) {
                if('success' == status) {
                    var position_modal = $(".select-position-container-modal");
                    position_modal.find(".panel-body div.position-list").html(data);
                    position_modal.modal('show');
                }
            });
        });
    });
Js;

$this->registerJs($JS, View::POS_END);
?>

<?php $form = ActiveForm::begin([
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'form-horizontal EmployeeForm',
        'data-parsley-validate' => "true",
    ],
    'id'=>'employeeForm',
    'method' => 'post',
    'action' => $action,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
<?= $form->field($model,'uuid')->input('hidden',[ 'value'=>isset($formData['uuid'])?$formData['uuid']:'',])->label(false)?>
    <table class="table">
        <tbody>
        <tr>
            <td class="col-md-1">*姓名</td>
            <td class="col-md-3">
                <?= $form->field($model,'name')->
                textInput([
                    
                    'value'=>isset($formData['name'])?$formData['name']:'',
                    'data-parsley-required'=>'true',
                ])->
                label(false)?>
            </td>
            <td class="col-md-1">英文名字</td>
            <td class="col-md-3">
                <?= $form->field($model,'english_name')->
                textInput([
                    'value'=>isset($formData['english_name'])?$formData['english_name']:'',
                ])->
                label(false)?>
            </td>
            <td class="col-md-1">性别</td>
            <td class="col-md-3">
                <?= $form->field($model,'gender')->dropDownList(EmployeeForm::genderList(),[
                    'options'=>ViewHelper::defaultValueForDropDownList($edit, $formData,'gender'),
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>*员工编号</td>
            <td>
                <span style="float: left; width: 50%">
                    <?= $form->field($model,'code')->
                    textInput([
                        'data-parsley-required'=>'true',
                        'value'=>isset($formData['code'])?$formData['code']:'',
                    ])->
                    label(false)?>
                </span>
                <span style="float: left; width: 50%" >
                    <input disabled
                           class="col-md-6 form-control"
                           placeholder="系统编号"
                           value="<?= isset($formData['system_code'])?$formData['system_code']:''?>">
                </span>
            </td>
            <td>*ERP账号</td>
            <td>
                <?= $form->field($model,'username')->
                textInput([
                    'disabled'=>isset($edit)&&$edit && isset($formData['username']) && !empty($formData['username']),
                    'data-parsley-required'=>'true',
                    'value'=>isset($formData['username'])?$formData['username']:'',
                ])->
                label(false)?>
            </td>
            <td colspan="2">
                该账号是员工ERP系统登陆账号，初始密码为QM888888
            </td>
        </tr>
        <tr>
            <td>身份证号</td>
            <td>
                <?= $form->field($model,'id_card_number')->textInput([
                    'value'=>isset($formData['id_card_number'])?$formData['id_card_number']:'',
                ])->label(false)?>
            </td>
            <td>
                婚姻状况
            </td>
            <td>
                <?= $form->field($model,'marriage_status')->dropDownList(EmployeeForm::marriageStatusList(),[
                    'options'=>ViewHelper::defaultValueForDropDownList($edit, $formData,'marriage_status'),
                ])->label(false)?>
            </td>
            <td>
                生育状况
            </td>
            <td>
                <?= $form->field($model,'generation_status')->dropDownList(EmployeeForm::generationStatusList(),[
                    'options'=>ViewHelper::defaultValueForDropDownList($edit, $formData,'generation_status'),
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>民族</td>
            <td>
                <?= $form->field($model,'ethnic')->textInput([
                    'value'=>isset($formData['ethnic'])?$formData['ethnic']:'',
                ])->label(false)?>
            </td>
            <td>
                紧急联系人
            </td>
            <td>
                <?= $form->field($model,'emergency_contact')->textInput([
                    'value'=>isset($formData['emergency_contact'])?$formData['emergency_contact']:'',
                ])->label(false)?>
            </td>
            <td>
                紧急联系人电话
            </td>
            <td>
                <?= $form->field($model,'emergency_contact_phone')->textInput([
                    'value'=>isset($formData['emergency_contact_phone'])?$formData['emergency_contact_phone']:'',
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>个人邮箱</td>
            <td>
                <?= $form->field($model,'email')->
                textInput([
                    
                    'value'=>isset($formData['email'])?$formData['email']:'',
                ])->
                label(false)?>
            </td>
            <td>工作邮箱</td>
            <td>
                <?= $form->field($model,'work_email')->
                textInput([
                    
                    'value'=>isset($formData['work_email'])?$formData['work_email']:'',
                ])->
                label(false)?>
            </td>
            <td>电话号码</td>
            <td>
                <?= $form->field($model,'phone_number')->
                textInput([
                    
                    'value'=>isset($formData['phone_number'])?$formData['phone_number']:'',
                ])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>qq</td>
            <td>
                <?= $form->field($model,'qq_number')->
                textInput([
                    
                    'value'=>isset($formData['qq_number'])?$formData['qq_number']:'',
                ])->
                label(false)?>
            </td>
            <td>微信号码</td>
            <td>
                <?= $form->field($model,'weichat_number')->
                textInput([
                    
                    'value'=>isset($formData['weichat_number'])?$formData['weichat_number']:'',
                ])->
                label(false)?>
            </td>
            <td>学位</td>
            <td>
                <?= $form->field($model,'education_degree')->dropDownList(EmployeeForm::educationDegreeList(),[
                    'options'=>ViewHelper::defaultValueForDropDownList($edit, $formData,'education_degree'),
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>毕业学校</td>
            <td>
                <?= $form->field($model,'graduated_school')->
                textInput([
                    
                    'value'=>isset($formData['graduated_school'])?$formData['graduated_school']:'',
                ])->
                label(false)?>
            </td>
            <td>专业</td>
            <td>
                <?= $form->field($model,'profession')->
                textInput([
                    
                    'value'=>isset($formData['profession'])?$formData['profession']:'',
                ])->
                label(false)?>
            </td>
            <td>状态</td>
            <td>
                <?= $form->field($model,'status')->dropDownList(EmployeeForm::employeeStatusList(),[
                    'options'=>ViewHelper::defaultValueForDropDownList($edit, $formData,'status'),
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>类别</td>
            <td>
                <?= $form->field($model,'type')->dropDownList(EmployeeForm::employeeTypeList(),[
                    'options'=>ViewHelper::defaultValueForDropDownList($edit, $formData,'type'),
                ])->label(false)?>
            </td>
            <td>公积金账号</td>
            <td>
                <?= $form->field($model,'house_fund_number')->textInput([
                    'value'=>isset($formData['house_fund_number'])?$formData['house_fund_number']:'',
                ])->label(false)?>
            </td>
            <td>居住证账号</td>
            <td>
                <?= $form->field($model,'residence_permit_number')->textInput([
                    'value'=>isset($formData['residence_permit_number'])?$formData['residence_permit_number']:'',
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>入职时间</td>
            <td>
                <?= $form->field($model,'entry_time')->
                textInput([
                    'class'=>'input-section datetimepicker form-control',
                    
                    'value'=>isset($formData['entry_time'])?$formData['entry_time']:'',
                ])->
                label(false)?>
            </td>
            <td>转正时间</td>
            <td>
                <?= $form->field($model,'become_full_member_time')->
                textInput([
                    'class'=>'input-section datetimepicker form-control',
                    
                    'value'=>isset($formData['become_full_member_time'])?$formData['become_full_member_time']:'',
                ])->
                label(false)?>
            </td>
            <td>离职时间</td>
            <td>
                <?= $form->field($model,'out_time')->
                textInput([
                    'class'=>'input-section datetimepicker form-control',
                    'value'=>isset($formData['out_time'])?$formData['out_time']:'',
                ])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>薪资</td>
            <td>
                <?= Html::textInput('EmployeeForm[salary]',
                    isset($formData['salary']) ? $formData['salary'] : '',[
                        'class'=>'form-control',
                        'data-parsley-type'=>'number',
                    ])?>
            </td>
            <td>试用期</td>
            <td>
                <?= Html::dropDownList('EmployeeForm[intern_term]',
                    isset($formData['intern_term']) ? $formData['intern_term'] : '',
                    $config->getList('intern_term'),[
                        'class'=>'form-control',
                        'data-parsley-type'=>'number',
                    ])?>
            </td>
            <td>合同期限</td>
            <td>
                <?= Html::textInput('EmployeeForm[contract_term]',
                    isset($formData['contract_term']) ? $formData['contract_term'] : '',[
                        'class'=>'form-control',
                        'data-parsley-type'=>'number',
                    ])?>
            </td>
        </tr>
        <tr>
            <td>停缴社保时间</td>
            <td>
                <?= Html::textInput('EmployeeForm[stop_social_insurance_time]',
                    isset($formData['stop_social_insurance_time']) && $formData['stop_social_insurance_time'] != 0
                        ? date('Y-m-d', $formData['stop_social_insurance_time']) : '',[
                        'class'=>'input-section datetimepicker form-control'
                    ])?>
            </td>
            <td>公历生日</td>
            <td>
                <?= Html::textInput('EmployeeForm[birthday]',
                    isset($formData['birthday']) && $formData['birthday'] != 0
                        ? date('Y-m-d', $formData['birthday']) : '',[
                        'class'=>'input-section datetimepicker form-control'
                    ])?>
            </td>
            <td>农历生日</td>
            <td>
                <?= Html::textInput('EmployeeForm[lunar_birthday]',
                    isset($formData['lunar_birthday']) && $formData['lunar_birthday'] != 0
                        ? date('Y-m-d', $formData['lunar_birthday']) : '',[
                        'class'=>'input-section datetimepicker form-control'
                    ])?>
            </td>
        </tr>
        <tr>
            <td>职位</td>
            <td colspan="2">
                <input type="hidden"
                       name="EmployeeForm[position_uuid]"
                       value='<?= isset($formData['position_uuid'])?$formData['position_uuid']:""?>'/>
                <input type="text"
                       name="EmployeeForm[position_name]"
                       disabled="disabled"
                       class="form-control col-md-12"
                       value="<?= isset($formData['position_name'])?$formData['position_name']:''?>"/>
            </td>
            <td>
                <a href="javascript:;"
                   class="choosePosition btn-xs"
                   name="<?= Url::to([
                       '/hr/employee/position-list'
                   ])?>">
                    <i class="fa fa-2x fa-edit"></i>
                </a>
            </td>
            <td>户口性质</td>
            <td>
                <?= $form->field($model,'hukou_category')->dropDownList(EmployeeForm::hukouCategoryList(),[
                    'options'=>ViewHelper::defaultValueForDropDownList($edit, $formData,'hukou_category'),
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>现居地</td>
            <td colspan="5">
                <?= $form->field($model,'address')->textInput([
                    'value'=>isset($formData['address'])?$formData['address']:'',
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>户籍地</td>
            <td colspan="5">
                <?= $form->field($model,'hukou_address')->textInput([
                    'value'=>isset($formData['hukou_address'])?$formData['hukou_address']:'',
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>备注</td>
            <td colspan="5">
                <?= $form->field($model,'remarks')->textarea([
                    'rows'=>3,
                    'value'=>isset($formData['remarks'])?$formData['remarks']:'',
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td colspan="3"><h4><strong>银行账号</strong></h4></td>
            <td colspan="3"><h4><strong>调薪记录</strong></h4></td>
        </tr>
        <tr>
            <td colspan="3">
                <?= $this->render('bank-account-list',[
                    'bankAccountList'=>isset($formData['bank_account'])?Json::decode($formData['bank_account']):null
                ])?>
            </td>
            <td colspan="3">
                <?= $this->render('salary-adjust-list',[
                    'salaryAdjustList'=>isset($formData['salary_adjust_record'])?Json::decode($formData['salary_adjust_record']):null
                ])?>
            </td>
        </tr>
        <tr>
            <td colspan="3"><h4><strong>社保调整记录</strong></h4></td>
            <td colspan="3"><h4><strong>公积金调整记录</strong></h4></td>
        </tr>
        <tr>
            <td colspan="3">
                <?= $this->render('social-insurance-adjust-list',[
                    'socialInsuranceAdjustList'=>isset($formData['social_insurance_adjust_record'])?Json::decode($formData['social_insurance_adjust_record']):null
                ])?>
            </td>
            <td colspan="3">
                <?= $this->render('house-fund-adjust-list',[
                    'houseFundAdjustList'=>isset($formData['house_fund_adjust_record'])?Json::decode($formData['house_fund_adjust_record']):null
                ])?>
            </td>
        </tr>
        <tr>
            <td colspan="6"><h4><strong>家庭成员</strong></h4></td>
        </tr>
        <tr>
            <td colspan="6">
                <?= $this->render('family-list',[
                    'familyList'=>$familyList
                ])?>
            </td>
        </tr>
        <tr>
            <td>简历附件</td>
            <td>
                <?= $form->field($model,'attachment[]')->fileInput([
                    'multiple' => true,
                ])?></td>
            <td colspan="4">
                <?php if(isset($formData['path']) && !empty($formData['path'])) :?>
                    <?php
                    // 将path字段解析出来
                    $formData['path'] = unserialize($formData['path']);
                    ?>
                    <?php foreach($formData['path'] as $key=>$path) :?>
                        <div>
                            <a href="<?= Url::to([
                                '/hr/employee/attachment-download',
                                'path'=>$path,
                                'file_name'=>$key,
                            ])?>"><?= $key?></a>
                        <span style="float: right">
                            <a href="#" name="<?= Url::to([
                                '/hr/employee/attachment-delete',
                                'path'=>$path,
                                'uuid'=>$formData['uuid'],
                            ])?>" class="attachmentDelete">删除</a>
                        </span>
                        </div>
                    <?php endforeach?>
                <?php endif?>
            </td>
        </tr>
        </tbody>
    </table>
<span class="col-md-12">
    <span class="col-md-4"></span>
    <span class="col-md-4">
        <input type="submit" value="提交" class="form-control btn-primary">
    </span>
    <span class="col-md-4"></span>
</span>
<?php ActiveForm::end()?>