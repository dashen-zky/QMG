<?php
use backend\modules\hr\models\config\EmployeeBasicConfig;
use backend\models\MyLinkPage;
use yii\helpers\Url;
use yii\widgets\Pjax;
$config = new EmployeeBasicConfig();
?>
<div class="panel panel-body ask-for-leave-list">
    <?php Pjax::begin()?>
    <?= $this->render('list-filter-form',[
        'formData'=>isset($ser_filter)?unserialize($ser_filter):'',
        'action'=>['/hr/ask-for-leave/assess-list-filter'],
    ])?>
    <?php
    $JS = <<<JS
$(function() {
    $('.ask-for-leave-list').on('click','.showing', function() {
        var url = $(this).attr('url');
        var self = $(this);
        $.get(url, function(data, status) {
            if(status !== 'success') {
                return ;
            }
            
            var panel = self.parents('.panel');
            var modal = panel.find('.ask-leave-showing');
            modal.find('.modal-body').html(data);
            modal.modal('show');
        });
    });
})
JS;
    $this->registerJs($JS, \yii\web\View::POS_END);
    ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>申请人</th>
            <th>类别</th>
            <th>部门</th>
            <th>起止时间</th>
            <th class="col-md-3">事由</th>
            <th>代理人</th>
            <th>审核人</th>
            <th>状态</th>
            <th class="col-md-1">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($askForLeaveList['list'])) :?>
        <?php foreach ($askForLeaveList['list'] as $item) :?>
            <tr>
                <td><?= $item['id']?></td>
                <td>
                    <a url="<?= Url::to([
                        '/hr/ask-for-leave/show',
                        'uuid'=>$item['uuid'],
                    ])?>" class="showing" href="javascript:;">
                        <?= $item['applied_name']?>
                    </a>
                </td>
                <td><?= $config->getAppointed('ask_leave_type', $item['type'])?></td>
                <td>
                    <?= $item['department']?>
                </td>
                <td>
                    <div><?= $item['start_time'] == 0 ? '' : date('Y-m-d H:i:s', $item['start_time'])?></div>
                    <div><?= $item['end_time'] == 0 ? '' : date('Y-m-d H:i:s', $item['end_time'])?></div>
                </td>
                <td>
                    <?= $item['reason']?>
                </td>
                <td><?= $item['proxy']?></td>
                <td><?= $item['assess_name']?></td>
                <td><?= $config->getAppointed('ask_for_leave_status', $item['status'])?></td>
                <td>
                    <?php if($item['status'] == EmployeeBasicConfig::AskLeaveApplying) :?>
                    <div>
                        <a href="<?= Url::to([
                            '/hr/ask-for-leave/assess-passed',
                            'uuid'=>$item['uuid'],
                        ])?>">通过</a>
                    </div>
                    <div>
                        <a href="<?= Url::to([
                            '/hr/ask-for-leave/assess-refused',
                            'uuid'=>$item['uuid'],
                        ])?>">不通过</a>
                    </div>
                    <?php endif;?>
                </td>
            </tr>
        <?php endforeach?>
        <?php endif;?>
        </tbody>
    </table>
    <?php if(isset($askForLeaveList['pagination'])) :?>
    <?php
    if(isset($ser_filter) && !empty($ser_filter)) {
        $pageParams = [
            'pagination' => $askForLeaveList['pagination'],
            'ser_filter'=>$ser_filter,
        ];
    } else {
        $pageParams = [
            'pagination' => $askForLeaveList['pagination'],
        ];
    }
    ?>
    <?= MyLinkPage::widget($pageParams); ?>
    <?php endif;?>
    <?php Pjax::end()?>
    <?= $this->render('edit')?>
</div>