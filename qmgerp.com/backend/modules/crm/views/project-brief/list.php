<?php
use yii\helpers\Url;
use backend\modules\crm\models\project\record\ProjectBriefConfig;
?>
<div class="panel brief-list">
    <?php
    $Js = <<<JS
$(function() {
$('.brief-list').on('click','.show', function() {
    var self = $(this);
    $.get(self.attr('url'), function(data, status) {
        if(status !== 'success') {
            return ;
        }
        
        var modal = self.parents('.brief-list').find('.show-modal');
        modal.find('.modal-body').html(data);
        modal.modal('show');
        
        modal.on('click','.editForm',function() {
            modal.find('.enableEdit').attr("disabled",false);
            modal.find('.displayBlockWhileEdit').css('display','block');
        });
        
        // 附件删除js
        modal.on('click','.attachmentDelete',function() {
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
    });
});
});
JS;
    $this->registerJs($Js, \yii\web\View::POS_END);
    ?>

    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>标题</th>
            <th>提案时间</th>
            <th>创建人</th>
            <th>状态</th>
            <th>项目</th>
            <th>项目经理</th>
            <th>客户</th>
            <th>销售</th>
            <?php if($enableEdit) :?>
            <th>操作</th>
            <?php endif;?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($briefList['list'] as $item) :?>
            <tr>
                <td><?= $item['id']?></td>
                <td>
                    <a url="<?= Url::to([
                        '/crm/project-brief/show',
                        'uuid'=>$item['uuid'],
                        'edit'=>true,
                    ])?>" class="show" href="#">
                        <?= $item['title']?>
                    </a>
                </td>
                <td><?= $item['proposal_time'] != 0 ? date('Y-m-d', $item['proposal_time']) : null?></td>
                <td><?= $item['created_name']?></td>
                <td><?= ProjectBriefConfig::getAppointed('status', $item['status'])?></td>
                <td><?= $item['project_name']?></td>
                <td><?= $item['project_manager_name']?></td>
                <td><?= $item['customer_name']?></td>
                <td><?= $item['sales_name']?></td>
                <?php if($enableEdit) :?>
                <td>
                    <?php if(in_array($item['status'], [
                        ProjectBriefConfig::StatusApplying
                    ])) :?>
                        <a href="<?= Url::to([
                            '/crm/project-brief/del',
                            'uuid'=>$item['uuid'],
                            'project_uuid'=>$item['project_uuid']
                        ])?>">删除</a>
                    <?php endif;?>
                </td>
                <?php endif;?>
            </tr>
        <?php endforeach?>
        </tbody>
    </table>
    <?= $this->render('show-modal')?>
</div>
