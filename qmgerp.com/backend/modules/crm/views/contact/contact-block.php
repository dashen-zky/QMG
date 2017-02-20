<?php
use backend\modules\crm\models\customer\model\ContactForm;
use backend\models\ViewHelper;
?>
<tbody>
<tr>
    <td>*姓名</td>
    <td>
        <input type="hidden" name="ContactForm[<?= $i?>][uuid]" value="<?=
        isset($contact['uuid'])?$contact['uuid']:''
        ?>">
        <?= $form->field($model,'['.$i.']name')->textInput([
            
            'value'=>isset($contact['name'])?$contact['name']:'',
            'data-parsley-required'=>'true',
        ])->label(false)?>
    </td>
    <td>性别</td>
    <td>
        <?= $form->field($model, '['.$i.']gender')->
        dropDownList(ContactForm::genderList(),[
             'options'=>ViewHelper::defaultValueForDropDownList(true, $contact,'gender'),
        ])->
        label(false)?>
    </td>
    <td>职位</td>
    <td>
        <?= $form->field($model,'['.$i.']position')->textInput([
            'value'=>isset($contact['position'])?$contact['position']:''
        ])->label(false)?>
    </td>
    <td>类别</td>
    <td>
        <?php if(!empty($contact)) {?>
            <?= $form->field($model, '['.$i.']type')->
            dropDownList(ContactForm::customerTypeList(),[
                'options'=>ViewHelper::defaultValueForDropDownList(true, $contact,'type'),
            ])->
            label(false)?>
        <?php }else{?>
            <?= $form->field($model, '['.$i.']type')->
            dropDownList(ContactForm::customerTypeList(),[
            
            'options'=>[$type=>['Selected'=>true]],
            ])->
            label(false)?>
        <?php }?>
    </td>
</tr>
<tr>
    <td>*电话</td>
    <td>
        <?= $form->field($model,'['.$i.']phone')->textInput([
            'value'=>isset($contact['phone'])?$contact['phone']:'',
            'data-parsley-required'=>'true',
        ])->label(false)?>
    </td>
    <td>微信</td>
    <td>
        <?= $form->field($model,'['.$i.']weichat')->textInput([
            
            'value'=>isset($contact['weichat'])?$contact['weichat']:''
        ])->label(false)?>
    </td>
    <td>qq</td>
    <td>
        <?= $form->field($model,'['.$i.']qq')->textInput([
            'value'=>isset($contact['qq'])?$contact['qq']:''
        ])->label(false)?>
    </td>
    <td>办公电话</td>
    <td>
        <?= $form->field($model,'['.$i.']office_phone')->textInput([
            'value'=>isset($contact['office_phone'])?$contact['office_phone']:''
        ])->label(false)?>
    </td>
</tr>
<tr>
    <td>邮箱</td>
    <td>
        <?= $form->field($model,'['.$i.']email')->textInput([
            'value'=>isset($contact['email'])?$contact['email']:''
        ])->label(false)?>
    </td>
    <td>状态</td>
    <td>
       <?= \yii\bootstrap\Html::dropDownList("ContactForm[$i][enable]",
           isset($contact['enable'])?$contact['enable']:null,
           ContactForm::enableList(),[
               'class'=>'form-control'
           ])?>
    </td>
    <td>地址</td>
    <td colspan="3">
        <?= $form->field($model,'['.$i.']address')->textInput([
            
            'value'=>isset($contact['address'])?$contact['address']:''
        ])->label(false)?>
    </td>
    </tr>
<tr>
    <td>备注</td>
    <td colspan="6">
        <?= $form->field($model,'['.$i.']remarks')->textInput([
            
            'value'=>isset($contact['remarks'])?$contact['remarks']:''
        ])->label(false)?>
    </td>
    <td>
        <button type="button" class="btn btn-sm btn-primary addContactRow" name="">
            <i class="fa fa-2x fa-plus"></i>
        </button>
        <button type="button" class="btn btn-sm btn-primary delContactRow" name="">
            <i class="fa fa-2x fa-minus"></i>
        </button>
    </td>
</tr>
</tbody>




