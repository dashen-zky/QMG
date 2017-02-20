<!-- #modal-without-animation -->
<?php
use backend\modules\crm\models\project\model\ProjectConfig;
use yii\helpers\Url;
$config = new ProjectConfig();
?>
<div class="modal scroll fade project-active-modal" style="width: 35%; margin: 80px auto;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">项目立项清单检查</h4>
        </div>
        <div class="modal-body">
            <table class="table">
                <?php
                $activeList = $config->getList('active');
                foreach ($activeList as $item) :?>
                    <tr>
                        <td><?= $item?></td>
                    </tr>
                <?php endforeach;?>
            </table>
            <div class="error-message" style="color: red;font-size: 14px"></div>
            <div>
                <button class="submit btn btn-primary" uuid="" validate-url="<?= Url::to([
                    '/crm/project/apply-active-validate'
                ])?>" url="<?= Url::to([
                    '/crm/project/apply-active'
                ])?>">提交</button>
            </div>
        </div>
        <div class="modal-footer">
            <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal">Close</a>
        </div>
    </div>
</div>
<?php
$JS = <<<JS
$('.project-active-modal').on('click','.submit',function() {
    var self = $(this);
    var uuid = self.attr('uuid');
    var validate_url = self.attr('validate-url') + '&uuid=' + uuid;
    $.get(validate_url, function(data, status) {
        if(status !== 'success') {
            return ;
        }
        
        var modal = self.parents('.modal');
        if(data != 1) {
            modal.find('.error-message').html(data);
            return ;
        }
        
        window.location.href = self.attr('url') + '&uuid=' + uuid;
    });
});
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>