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
    'action'=>['/hr/ask-for-leave/list-filter'],
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
            
            panel.on('click','.editForm',function() {
                panel.find('.enableEdit').attr("disabled",false);
                panel.find('.displayBlockWhileEdit').css('display','block');
            });
            
            $(".datetimepicker").datetimepicker({
                lang:"ch",           //语言选择中文
                format:"Y-m-d H:i",      //格式化日期H:i
                timepicker:true,
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
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($askForLeaveList['list'] as $item) :?>
            <tr>
                <td><?= $item['id']?></td>
                <td>
                    <a url="<?= Url::to([
                        '/hr/ask-for-leave/show',
                        'uuid'=>$item['uuid'],
                        'edit'=>true,
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
                            '/hr/ask-for-leave/del',
                            'uuid'=>$item['uuid'],
                        ])?>">删除</a>
                    </div>
                    <?php endif;?>
                </td>
            </tr>
        <?php endforeach?>
        </tbody>
    </table>
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
<?php Pjax::end()?>
<?= $this->render('edit')?>
</div>