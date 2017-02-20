<?php
use yii\helpers\Url;
use yii\web\View;
use backend\modules\crm\models\customer\model\ContactForm;
?>
<div class="modal fade ContactPanel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<ul class="nav nav-tabs">
            <li class="active"><a href="#contact-tab-1" data-toggle="tab">联系人</a></li>
            <li><a href="#contact-tab-2" data-toggle="tab">负责人</a></li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade active in" id="contact-tab-1">
        <?= $this->render('contact-list',[
            'formClass'=>'editContactForm',
            'model'=>$model,
            'list'=>$contactList,
            'type'=>ContactForm::CustomerContact,
        ])?>
    </div>
    <div class="tab-pane fade" id="contact-tab-2">
        <?= $this->render('contact-list',[
            'formClass'=>'editContactForm',
            'model'=>$model,
            'list'=>$customerDutyList,
            'type'=>ContactForm::CustomerDuty,
        ])?>
    </div>
</div>
</div>
<?php

// 联系人功能模块的js
$customerUrl = Url::to(['/crm/contact/update','uuid'=>$uuid,'type'=>'customer']);
$supplierUrl = Url::to(['/crm/contact/update','uuid'=>$uuid,'type'=>'supplier']);
$JS = <<<JS
$(function() {
    //显示联系人面板
     $(".customer").on("click",'.showContactList',function () {
            $(".ContactPanel").modal('show');
     });
     $(".SupplierForm").on("click",'.showContactList',function () {
            $(".ContactPanel").modal('show');
     });
     // ajax提交contact的表单，在数据库后先插入数据，然后将插入的contact的uuid打回来
    $(".supplier-panel .ContactPanel").on('click', 'form.editContactForm .editContactFormSubmit',function (e) {
        var url = "$supplierUrl";
        var form = $(this).parent().parent().parent();
        $.ajax({
            url: url,
            type: 'post',
            data: form.serialize(),
            success: function (data) {
                // 将ajax返回来的contact 的uuid放入到contactUuids中
                $(".SupplierForm").find("input.contactUuids").val(data);
                var contactPanel = $(".ContactPanel");
                contactPanel.find("form.editContactForm input[name='ContactForm[oldUuids]']").val(data);
                var uuids = JSON.parse(data);
                // uuids数组与正常的数组正好反转过来 uuid=>i
                //是在contact里面故意倒过来的，有利于后面的操作
                for (uuid in uuids) {
                    var i = uuids[uuid];
                    var uuidFiled = contactPanel.find("form.editContactForm table tbody input[name='ContactForm["+i+"][uuid]']");
                    uuidFiled.val(uuid);
                }
                contactPanel.modal('hide');
            }
        });
    }).on('submit', function (e) {
        e.preventDefault();
    });
    // ajax提交contact的表单，在数据库后先插入数据，然后将插入的contact的uuid打回来
    $(".customer-panel .ContactPanel").on('click', 'form.editContactForm .editContactFormSubmit',function (e) {
        var url = "$customerUrl";
        var form = $(this).parent().parent().parent();
        $.ajax({
            url: url,
            type: 'post',
            data: form.serialize(),
            success: function (data) {
                // 将ajax返回来的contact 的uuid放入到contactUuids中
                $("#CustomerForm").find("input.contactUuids").val(data);
                var contactPanel = $(".ContactPanel");
                contactPanel.find("form.editContactForm input[name='ContactForm[oldUuids]']").val(data);
                var uuids = JSON.parse(data);
                // uuids数组与正常的数组正好反转过来 uuid=>i
                //是在contact里面故意倒过来的，有利于后面的操作
                for (uuid in uuids) {
                    var i = uuids[uuid];
                    var uuidFiled = contactPanel.find("form.editContactForm table tbody input[name='ContactForm["+i+"][uuid]']");
                    uuidFiled.val(uuid);
                }
                contactPanel.modal('hide');
            }
        });
    });
    // 在联系人框里面点击'—'号，删除当前行
    $(".contact-table").on('click','.delContactRow',function() {
        $(this).parentsUntil("table").remove();
    });
    // 在联系人框里面点击‘+’号，添加一行
    $(".contact-table").on('click','.addContactRow',function() {
        var index = (new Date()).getTime();
        //var index = $(this).parent().parent().parent().siblings('tbody').length + 1;
        // 组建html追加在table后面
        var html = "<tbody>"+$(this).parent().parent().parent()[0].innerHTML+"</tbody>";
        var pattern1 = /\[\d+\]/g;
        html = html.replace(pattern1,'['+index+']');
        // 将uuid替换掉，不需要
        var pattern2 = /name="ContactForm\[(\d+)\]\[uuid\]".*value=".*"/;
        html = html.replace(pattern2,'name="ContactForm\[$1]\[uuid\]" value=""');
        var table = $(this).parent().parent().parent().parent();
        table.append(html);
    });
});
JS;
$this->registerJs($JS, View::POS_END);
?>

