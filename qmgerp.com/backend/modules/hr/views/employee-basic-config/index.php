<?php
use yii\web\View;
use yii\helpers\Json;
$JS = <<<JS
$(function() {
    $('table.config-table').on("click",".removeRow",function() {
        $(this).parentsUntil('tbody').remove();
    });
    $('table.config-table').on('click',' .addRow',function() {
        var name = $(this).attr('name');
        var index = (new Date()).getTime();

        var html = '<tr>' +
         '<td><input class="index form-control" name=Config['+name+']['+index+'][key] value='+index+' readonly></td>' +
          '<td><input class="form-control" name=Config['+name+']['+index+'][value]></td>' +
          '<td>' +
           '<button type="button" class="btn-xs btn-primary removeRow">' +
            '<i class="fa fa-2x fa-minus"></i>' +
             '</button></td></tr>';
        var parent = $(this).parent().parent();
        parent.before(html).fadeIn();
    });
})

JS;
$this->registerJs($JS, View::POS_END);
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-hr',
    'menu-2-config',
    'menu-3-basic'
])?>'>
<div class="col-md-12 configContainer">

        <ul class="nav nav-tabs">
            <li class="<?= !isset($tab) || $tab == 'tab-1'?'active':''?>"><a href="#default-tab-1" data-toggle="tab">请假类型</a></li>
            <li class="<?= isset($tab) && $tab == 'tab-2'?'active':''?>"><a href="#default-tab-2" data-toggle="tab">请假周期审批</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade <?= !isset($tab) || $tab == 'tab-1'?'active in':''?>" id="default-tab-1">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'key'=>'ask_leave_type',
                    'tab'=>'tab-1'
                ])?>
            </div>
            <div class="tab-pane fade <?= isset($tab) && $tab == 'tab-2'?'active in':''?>" id="default-tab-2">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'key'=>'ask_for_leave_assess_period',
                    'tab'=>'tab-2'
                ])?>
            </div>
        </div>
</div>