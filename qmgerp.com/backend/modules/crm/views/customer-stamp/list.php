<?php
use backend\modules\crm\models\customer\model\ContactForm;
use yii\helpers\Url;
use yii\web\View;
?>
<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>'开票信息列表',
    'panelClass'=>'stamp-panel'
])?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th class="col-md-1">公司名称</th>
            <th class="col-md-1">公司地址</th>
            <th class="col-md-1">公司税号</th>
            <th class="col-md-1">公司电话</th>
            <th class="col-md-1">开户行</th>
            <th class="col-md-1">公司账号</th>
            <?php if($enableEdit) :?>
            <th class="col-md-1">操作</th>
            <?php endif;?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($stampList as  $item) :?>
                <tr>
                    <td><?= $item['company_name']?></td>
                    <td><?= $item['company_address']?></td>
                    <td><?= $item['stamp_number']?></td>
                    <td><?= $item['company_phone']?></td>
                    <td><?= $item['bank_of_deposit']?></td>
                    <td><?= $item['account']?></td>
                    <?php if($enableEdit) :?>
                    <td>
                        <div class="btn-group m-r-5 m-b-5">
                            <a href="javascript:;" data-toggle="dropdown" class="btn btn-success dropdown-toggle" aria-expanded="false">
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="javascript:;" class="showStamp"
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
                        <?php endif;?>
                    </td>
                </tr>
        <?php endforeach?>
        </tbody>
    </table>
<?= $this->render('edit',[
    'updateAction'=>$updateAction,
    'model'=>$model,
    'formData'=>$formData,
])?>
<?= $this->render('@webroot/../views/site/panel-footer')?>

<?php
$JS = <<<JS
$(function(){
    $('.stamp-panel').on('click','.showStamp',function() {
        var url = $(this).attr('name');
         $.get(
            url,
            function(data,status) {
                if(status === 'success') {
                    var formData = JSON.parse(data);
                    $.each(formData, function(key, value) {
                        var element = $(".stamp-panel .edit-stamp [name=" + "'Stamp[" +key+ "]'" +"]");
                        element.val(value);
                    });
                    $('.stamp-panel .edit-stamp form .enableEdit').attr("disabled",true);
                    var modal = $(".stamp-panel .edit-stamp");
                    modal.modal('show');
                }
            }
        );
    });
})
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>