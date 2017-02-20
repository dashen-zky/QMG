<?php
use yii\web\View;
$JS = <<<JS
$(function() {
    $('table.config-table').on("click",".removeRow",function() {
        $(this).parentsUntil('tbody').remove();
    });
    $('table.config-table').on('click',' .addRow',function() {
        var name = $(this).attr('name');
        var editKey = $(this).attr('id') === '1';
        var index = (new Date()).getTime();
        if(editKey) {
            var html = '<tr>' +
         '<td><input class="index form-control" name=Config['+name+']['+index+'][key]></td>' +
          '<td><input class="form-control" name=Config['+name+']['+index+'][value]></td>' +
          '<td>' +
           '<button type="button" class="btn btn-primary removeRow">' +
            '<i class="fa fa-2x fa-minus"></i>' +
             '</button></td></tr>';
        } else {
            var html = '<tr>' +
         '<td><input class="index form-control" name=Config['+name+']['+index+'][key] value='+index+' readonly></td>' +
          '<td><input class="form-control" name=Config['+name+']['+index+'][value]></td>' +
          '<td>' +
           '<button type="button" class="btn btn-primary removeRow">' +
            '<i class="fa fa-2x fa-minus"></i>' +
             '</button></td></tr>';
        }

        var parent = $(this).parent().parent();
        parent.before(html).fadeIn();
    });
})

JS;
$this->registerJs($JS, View::POS_END);
?>
<div class="col-md-12 configContainer">

        <ul class="nav nav-tabs">
            <li class="<?= !isset($tab) || $tab == 'tab-1'?'active':''?>"><a href="#stamp-tab-1" data-toggle="tab">发票类型</a></li>
            <li class="<?= isset($tab) && $tab == 'tab-2'?'active':''?>"><a href="#stamp-tab-2" data-toggle="tab">发票性质</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade <?= !isset($tab) || $tab == 'tab-1'?'active in':''?>" id="stamp-tab-1">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'发票类型',
                    'key'=>'service_type',
                    'tab'=>'tab-1',
                    'editKey'=>false,
                ])?>
            </div>
            <div class="tab-pane fade <?= isset($tab) && $tab == 'tab-2'?'active in':''?>" id="stamp-tab-2">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'发票性质',
                    'key'=>'feature',
                    'tab'=>'tab-2',
                    'editKey'=>false,
                ])?>
            </div>
        </div>
</div>