<?php
use backend\modules\crm\models\customer\model\ContactForm;
use yii\helpers\Url;
use yii\web\View;
?>
<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>'联系人列表',
    'panelClass'=>'contact-panel'
])?>
<table class="table table-striped">
    <thead>
    <tr>
        <th class="col-md-1">姓名</th>
        <th class="col-md-1">性别</th>
        <th class="col-md-1">职位</th>
        <th class="col-md-1">类别</th>
        <th class="col-md-1">电话</th>
        <th class="col-md-1">微信</th>
        <th class="col-md-1">qq</th>
        <th class="col-md-1">办公电话</th>
        <th class="col-md-1">邮箱</th>
        <th class="col-md-1">地址</th>
    <?php if(!isset($operator) || $operator) :?>
        <th class="col-md-1">操作</th>
    <?php endif?>
    </tr>
    </thead>
    <tbody>
    <?php
    unset($contactList['contactList']['oldUuids']);
    unset($contactList['customerDutyList']['oldUuids']);
    $statusList = ContactForm::enableList();
    ?>
    <?php foreach ($contactList as  $value) :?>
        <?php foreach ($value as $item) :?>
        <tr>
            <td><?= $item['name']?></td>
            <td><?= ContactForm::getGender($item['gender'])?></td>
            <td><?= $item['position']?>/
                <?= isset($statusList[$item['enable']])?$statusList[$item['enable']]:null?></td>
            <td><?= ContactForm::getType($item['type'])?></td>
            <td><?= $item['phone']?></td>
            <td><?= $item['weichat']?></td>
            <td><?= $item['qq']?></td>
            <td><?= $item['office_phone']?></td>
            <td><?= $item['email']?></td>
            <td><?= $item['address']?></td>
            <td>
                <?php if(!isset($operator) || $operator) :?>
                <div class="btn-group m-r-5 m-b-5">
                    <a href="javascript:;" data-toggle="dropdown" class="btn btn-success dropdown-toggle" aria-expanded="false">
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:;" class="editContact"
                               name="<?= Url::to([
                                   $editAction,
                                   'uuid'=>$item['uuid'],
                                   'object_uuid'=>$object_uuid,
                               ])?>">查看</a></li>
                        <li><a href="<?= Yii::$app->urlManager->createUrl([
                                $delAction,
                                'uuid'=>$item['uuid'],
                                'object_uuid'=>$object_uuid,
                            ])?>">删除</a></li>
                    </ul>
                </div>
                <?php endif?>
            </td>
        </tr>
        <?php endforeach?>
    <?php endforeach?>
    </tbody>
</table>
<?= $this->render('edit',[
    'action'=>$updateAction,
    'formData'=>$formData,
    'model'=>$model,
    'show'=>true,
    'showOnly'=>false,
])?>
<?= $this->render('@webroot/../views/site/panel-footer')?>

<?php
$JS = <<<JS
$(function(){
    $('.contact-panel').on('click','.editContact',function() {
        var url = $(this).attr('name');
         $.get(
            url,
            function(data,status) {
                if(status === 'success') {
                    var formData = JSON.parse(data);
                    $.each(formData, function(key, value) {
                        var element = $(".contact-panel .edit-contact [name=" + "'ContactForm[" +key+ "]'" +"]");
                        element.val(value);
                    });
                    $('.contact-panel .edit-contact form .enableEdit').attr("disabled",true);
                    var cc = $(".contact-panel .edit-contact");
                    cc.modal('show');
                }
            }
        );
    });
})
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>