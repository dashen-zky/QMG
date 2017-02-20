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
            
            var modal = self.parents('.panel').find('.week-report-show');
            modal.find('.modal-body').html(data);
            modal.modal('show');
        });
    });
});
JS;
    $this->registerJs($Js, \yii\web\View::POS_END);
    ?>
    <?= $this->render('list-filter-form',[
        'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
    ]);?>
    <table class="table">
        <thead>
        <tr>
            <th class="col-md-2">标题</th>
            <th>创建时间</th>
            <td>创建人</td>
            <th class="col-md-7">内容</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($weekReportList['list'] as $item) :?>
            <tr>
                <td><a href="#" class="show" url="<?= Url::to([
                        '/daily/week-report/show',
                        'uuid'=>$item['uuid'],
                    ])?>"><?= $item['title']?></a></td>
                <td><?= isset($item['created_time']) &&
                    $item['created_time'] != 0
                        ?date("Y-m-d",$item['created_time'])
                        :null?></td>
                <td><?= $item['created_name']?></td>
                <td><?= $item['content']?></td>
                <td>
                    <a href="<?= Url::to([
                        '/daily/week-report/del',
                        'uuid'=>$item['uuid']
                    ])?>">删除</a>
                </td>
            </tr>
        <?php endforeach?>
        </tbody>
    </table>
    <?php
    if(isset($ser_filter) && !empty($ser_filter)) {
        $pageParams = [
            'pagination' => $weekReportList['pagination'],
            'ser_filter'=>$ser_filter,
        ];
    } else {
        $pageParams = [
            'pagination' => $weekReportList['pagination'],
        ];
    }
    ?>
    <?= MyLinkPage::widget($pageParams); ?>
    <?php Pjax::end(); ?>
    <?= $this->render('show-modal')?>
</div>
