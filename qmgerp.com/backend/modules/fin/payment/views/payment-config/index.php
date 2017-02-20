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
            <li class="active"><a href="#payment-tab-1" data-toggle="tab">管理费用</a></li>
            <li class=""><a href="#payment-tab-2" data-toggle="tab">项目媒介费用</a></li>
            <li class=""><a href="#payment-tab-4" data-toggle="tab">项目执行费用</a></li>
            <li class=""><a href="#payment-tab-3" data-toggle="tab">审核金额</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="payment-tab-1">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'管理费用',
                    'key'=>'payment_for_manage',
                    'editKey'=>false,
                ])?>
            </div>
            <div class="tab-pane fade" id="payment-tab-2">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'项目媒介费用',
                    'key'=>'payment_for_project_media',
                    'editKey'=>false,
                ])?>
            </div>
            <div class="tab-pane fade" id="payment-tab-4">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'项目执行费用',
                    'key'=>'payment_for_project_execute',
                    'editKey'=>false,
                ])?>
            </div>
            <div class="tab-pane fade" id="payment-tab-3">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'审核金额',
                    'key'=>'assess_money',
                    'editKey'=>false,
                ])?>
            </div>
        </div>
</div>