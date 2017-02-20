<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use yii\helpers\Url;
?>
<div class="panel">
<?php Pjax::begin(); ?>
<?php
$Js = <<<JS
$(function() {
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
    
    $('.panel').on('click','.show', function() {
        var self = $(this);
        $.get(self.attr('url'), function(data, status) {
            if(status !== 'success') {
                return ;
            }
            
            var modal = self.parents('.panel').find('.transaction-show');
            modal.find('.modal-body').html(data);
            modal.modal('show');
            
            modal.on('click','.editForm',function() {
                modal.find('.enableEdit').attr("disabled",false);
                modal.find('.displayBlockWhileEdit').css('display','block');
            });
        });
    });
});
JS;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
<?= $this->render('unfinished-list-filter-form',[
    'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
]);?>
    <table class="table">
        <thead>
        <tr>
            <th class="col-md-2">标题</th>
            <th>任务截止日期</th>
            <th class="col-md-1">执行人</th>
            <th class="col-md-6">内容</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($transactionList['list'] as $item) :?>
            <tr>
                <td><a href="#" class="show" url="<?= Url::to([
                    '/daily/transaction/show',
                    'uuid'=>$item['uuid']
                    ])?>"><?= $item['title']?></a></td>
                <td><?= isset($item['expect_finish_time']) &&
                    $item['expect_finish_time'] != 0
                        ?date("Y-m-d",$item['expect_finish_time'])
                        :null?></td>
                <td><?= $item['execute_name']?></td>
                <td><?= $item['content']?></td>
                <td>
                    <a href="<?= Url::to([
                        '/daily/transaction/finish',
                        'uuid'=>$item['uuid'],
                    ])?>">完成</a>
                    <a href="<?= Url::to([
                        '/daily/transaction/drop',
                        'uuid'=>$item['uuid'],
                    ])?>">放弃</a>
                    <a href="<?= Url::to([
                        '/daily/transaction/set-top',
                        'uuid'=>$item['uuid'],
                    ])?>">置顶</a>
                    <?php if(empty($item['transaction_week_report_map_transaction_uuid'])) :?>
                        <a href="<?= Url::to([
                            '/daily/transaction/del',
                            'uuid'=>$item['uuid'],
                        ])?>">删除</a>
                    <?php endif;?>
                </td>
            </tr>
        <?php endforeach?>
        </tbody>
    </table>
    <?php
    if(isset($ser_filter) && !empty($ser_filter)) {
        $pageParams = [
            'pagination' => $transactionList['pagination'],
            'ser_filter'=>$ser_filter,
        ];
    } else {
        $pageParams = [
            'pagination' => $transactionList['pagination'],
        ];
    }
    ?>
    <?= MyLinkPage::widget($pageParams); ?>
<?php Pjax::end(); ?>
<?= $this->render('show', [
    'edit'=>true
])?>
</div>
