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
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-supplier',
    'menu-2-config',
    'menu-3-supplier',
])?>'>
<div class="col-md-12 configContainer">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#supplier-config-tab-1" data-toggle="tab">供应商账期</a></li>
            <li class=""><a href="#supplier-config-tab-2" data-toggle="tab">级别</a></li>
            <li class=""><a href="#supplier-config-tab-3" data-toggle="tab">类型</a></li>
            <li class=""><a href="#supplier-config-tab-4" data-toggle="tab">性质</a></li>
            <li class=""><a href="#supplier-config-tab-6" data-toggle="tab">来源</a></li>
            <li class=""><a href="#supplier-config-tab-7" data-toggle="tab">价格有效期</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="supplier-config-tab-1">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'供应商账期',
                    'key'=>'term',
                    'editKey'=>false,
                ])?>
            </div>
            <div class="tab-pane fade" id="supplier-config-tab-2">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'级别',
                    'key'=>'level',
                    'editKey'=>false,
                ])?>
            </div>
            <div class="tab-pane fade" id="supplier-config-tab-3">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'类型',
                    'key'=>'type',
                    'editKey'=>false,
                ])?>
            </div>
            <div class="tab-pane fade" id="supplier-config-tab-4">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'性质',
                    'key'=>'feature',
                    'editKey'=>false,
                ])?>
            </div>
            <div class="tab-pane fade" id="supplier-config-tab-6">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'来源',
                    'key'=>'from',
                    'editKey'=>false,
                ])?>
            </div>
            <div class="tab-pane fade" id="supplier-config-tab-7">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'价格有效期',
                    'key'=>'value_term',
                    'editKey'=>false,
                ])?>
            </div>
        </div>
</div>