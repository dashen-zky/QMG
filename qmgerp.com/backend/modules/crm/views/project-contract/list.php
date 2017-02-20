<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use yii\helpers\Url;
use backend\modules\fin\models\contract\ContractConfig;
$config = new ContractConfig();
if(!isset($enableEdit)) {
    $enableEdit = true;
}
?>
<div class="panel-body">
<?php Pjax::begin(); ?>
    <?php
    $JS = <<<JS
$(function() {
$('.project-contract-list .showProjectContract').click(function() {
    var url = $(this).attr('name');
    $.get(
    url,
    function(data, status) {
        if(status === 'success') {
            var modal = $('.edit-project-contract');
            modal.find('.modal-body').html(data);
            modal.modal('show');
            $("form").on('click','.editForm',function() {
                var form = $(this).parents('form');
                form.find('.enableEdit').attr("disabled",false);
            });
            $(".datetimepicker").datetimepicker({
                lang:"ch",           //语言选择中文
                format:"Y-m-d",      //格式化日期H:i
                i18n:{
                  // 以中文显示月份
                  de:{
                    months:["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月",],
                    // 以中文显示每周（必须按此顺序，否则日期出错）
                    dayOfWeek:["日","一","二","三","四","五","六"]
                  }
                }
                // 显示成年月日，时间--
            });
            // 附件删除js
             $('.ProjectContractForm .project-contract-table').on('click','.attachmentDelete',function() {
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
        }
    }
    )
});
})
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>

    <?php if(isset($filter) && $filter) :?>
    <?= $this->render('list-filter-form',[
        'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
    ]);?>
    <?php endif;?>
    <div class="panel-body project-contract-list">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>项目名称</th>
                <th>项目编号</th>
                <th>客户名称</th>
                <th>合同编号</th>
                <th>合同负责人</th>
                <th>合同状态</th>
                <th>创建时间</th>
                <th>合同金额</th>
                <?php if($enableEdit) :?>
                <th>操作</th>
                <?php endif;?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($contractList['list'] as $item) :?>
                <tr>
                    <td><?= $item['project_name']?></td>
                    <td><?= \backend\modules\crm\models\project\model\ProjectForm::codePrefix . $item['project_code']?></td>
                    <td><?= $item['customer_name']?></td>
                    <td><?= $item['type'].$item['code']?></td>
                    <td><?= $item['duty_name']?></td>
                    <td><?= $config->getAppointed('status',$item['status'])?></td>
                    <td><?= ($item['create_time'] != 0)?date("Y-m-d",$item['create_time']):''?></td>
                    <td><?= $item['money']?></td>
                    <?php if($enableEdit) :?>
                    <td>
                        <div class="btn-group m-r-5 m-b-5">
                            <a href="javascript:;" data-toggle="dropdown" class="btn btn-success dropdown-toggle" aria-expanded="false">
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a class="showProjectContract"
                                       name="<?= Url::to([
                                           '/crm/project-contract/edit',
                                           'uuid'=>$item['uuid'],
                                           'object_uuid'=>$item['project_uuid'],
                                           'back_url'=>isset($back_url)?$back_url:'',
                                       ])?>">查看</a>
                                </li>
                                <li>
                                    <a href="<?= Url::to([
                                            '/crm/project-contract/del',
                                            'uuid'=>$item['uuid'],
                                            'object_uuid'=>$item['project_uuid'],
                                            'back_url'=>isset($back_url)?$back_url:'',
                                        ])?>">删除</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <?php endif;?>
                </tr>
            <?php endforeach?>
            </tbody>
        </table>
        <?php
        if(isset($ser_filter) && !empty($ser_filter)) {
            $pageParams = [
                'pagination' => $contractList['pagination'],
                'ser_filter'=>$ser_filter,
            ];
        } else {
            $pageParams = [
                'pagination' => $contractList['pagination'],
            ];
        }
        ?>
        <?= MyLinkPage::widget($pageParams); ?>
    </div>
<?php Pjax::end(); ?>
</div>
<?= $this->render('edit',[])?>
