<?php
use yii\web\View;
use backend\modules\fin\stamp\models\StampConfig;
use backend\modules\fin\payment\models\PaymentConfig;
use yii\helpers\Html;
$stampConfig = new StampConfig();
$paymentConfig = new PaymentConfig();
?>
<div class="select-stamp-container-modal scroll modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <div style="overflow: hidden">
                <div>
                    <span class="selected-stamp-tags" style="margin-bottom: 10px; float: left">
                        <ul class="float-left">
                            <?php
                            if(isset($filters['stamp_uuid']) && !empty($filters['stamp_uuid'])) :
                                $stamp_uuids = explode(',', $filters['stamp_uuid']);
                                $stamp_names = explode(',', $filters['stamp_uuid']);?>
                                <?php for($i = 0; $i < count($stamp_uuids); $i++) :?>
                                <li>
                                    <div class="tag">
                                        <span class="tag-content"><?= $stamp_names[$i]?></span>
                                        <span class="tag-close" id="<?= $stamp_uuids[$i]?>">
                                            <a href="javascript:;">×</a>
                                        </span>
                                    </div>
                                </li>
                            <?php endfor?>
                            <?php endif?>
                        </ul>
                    </span>
                </div>
            </div>
        <div style="overflow: hidden">
            <?= Html::beginForm(['/payment/check-stamp/check-stamp'], 'post', ['class' => 'StampCheckForm']); ?>
            <input class="stamp-uuid" hidden name="StampCheckForm[stamp_uuid]">
            <input hidden name="StampCheckForm[payment_uuid]" class='payment-uuid'>
            <div class="col-md-3" style="margin-left: 25px">
                <?= Html::dropDownList(
                    'StampCheckForm[stamp_status]',
                    PaymentConfig::StampChecking,
                    $paymentConfig->getList('stamp_status'),
                    [
                        'class' => 'form-control stamp-status',
                    ]
                )?>
            </div>
            <div class="col-md-2">
                <input type="submit" class="btn btn-primary col-md-12" value="提交">
            </div>
            <?= Html::endForm()?>
        </div>
        </div>
        <div class="panel panel-body" data-sortable-id="table-basic-4">
            <?php
            $JS = <<<JS
$(document).ready(function() {
    // 删除tag的按钮
    $('.select-stamp-container-modal').on('click','.selected-stamp-tags .tag-close',function(){
        var uuid = $(this).attr('id');
        var modal = $(this).parents('.modal');
        var stamp_uuid = $('.select-stamp-container-modal').find('.stamp-list #'+uuid);
        stamp_uuid.attr('checked', false);
        $(this).parentsUntil('ul').remove();
        // 将删除操作同步到listform里面去
        
        var stamp_name_filed = modal.find('.ListFilterForm .stamp-name');
        var stamp_uuid_filed = modal.find('.ListFilterForm .stamp-uuid');
        var stamp_uuid_checked = modal.find('.StampCheckForm .stamp-uuid');
        var stamp_name_value = stamp_name_filed.val();
        stamp_name_value = stamp_name_value.split(',');
        var stamp_uuid_value = stamp_uuid_filed.val();
        stamp_uuid_value = stamp_uuid_value.split(',');
        for(var i = 0; i < stamp_uuid_value.length; i++) {
            if(stamp_uuid_value[i] === uuid) {
                stamp_name_value.splice(i,1);
                stamp_uuid_value.splice(i,1);
                break;
            }
        }
        stamp_name_filed.val(stamp_name_value.join(','));
        stamp_uuid_filed.val(stamp_uuid_value.join(','));
        stamp_uuid_checked.val(stamp_uuid_value.join(','));
    });
    // checkbox点击事件，作的功能
    $('.select-stamp-container-modal').on('click','.stamp-list .stamp-uuid',function() {
        var checked = $(this).attr('checked');
        var stamp_tags = $('.select-stamp-container-modal .selected-stamp-tags ul');
        var uuid = $(this).val();
        var stamp_name = $(this).parents('tr').find('.stamp-name')[0].innerHTML;
        // listfilter 里面两个元素保存已选择的职位信息
        var modal = $(this).parents('.select-stamp-container-modal');
        
        var stamp_name_filed = modal.find('.ListFilterForm .stamp-name');
        var stamp_uuid_filed = modal.find('.ListFilterForm .stamp-uuid');
        var stamp_uuid_checked = modal.find('.StampCheckForm .stamp-uuid');
        if(checked === 'checked') {
            var html = '<li>' +
             '<div class="tag">' +
               '<span class="tag-content">'+stamp_name+'</span>' +
                '<span class="tag-close" id="'+uuid+'">' +
                 '<a href="javascript:;">×</a>' +
                  '</span>' +
                   '</div>' +
                    '</li>';
            stamp_tags.append(html);
            // 将元素保存到listfilterform里面去

            var stamp_name_value = stamp_name_filed.val();
            // 将其以逗号分开
            stamp_name_value = stamp_name_value.split(',');
            // 将空的元素删除掉
            stamp_name_value = $.grep(stamp_name_value, function(n) {return $.trim(n).length > 0;});

            var stamp_uuid_value = stamp_uuid_filed.val();
            stamp_uuid_value = stamp_uuid_value.split(',');
            stamp_uuid_value = $.grep(stamp_uuid_value, function(n) {return $.trim(n).length > 0;});

            stamp_name_value.push(stamp_name);
            stamp_uuid_value.push(uuid);
            stamp_name_filed.val(stamp_name_value.join(','));
            stamp_uuid_filed.val(stamp_uuid_value.join(','));
            stamp_uuid_checked.val(stamp_uuid_value.join(','));
        } else {
            var uuid_filed = stamp_tags.find('#'+uuid);
            uuid_filed.parentsUntil('ul').remove();
            // 将元素从listfilterform里面删去
            var stamp_name_value = stamp_name_filed.val();
            stamp_name_value = stamp_name_value.split(',');
            var stamp_uuid_value = stamp_uuid_filed.val();
            stamp_uuid_value = stamp_uuid_value.split(',');
            for(var i = 0; i < stamp_uuid_value.length; i++) {
                if(stamp_uuid_value[i] === uuid) {
                    stamp_name_value.splice(i,1);
                    stamp_uuid_value.splice(i,1);
                    break;
                }
            }
            stamp_name_filed.val(stamp_name_value.join(','));
            stamp_uuid_filed.val(stamp_uuid_value.join(','));
            stamp_uuid_checked.val(stamp_uuid_value.join(','));
        }
    });
});
JS;
$this->registerJs($JS, View::POS_END);
?>
            <?= $this->render('select-list-filter-form',[
                'action'=>['/stamp/import-stamp/select-list-filter'],
                'entrance'=>StampConfig::SelectEntrance,
            ])?>
            <div class="stamp-list list"></div>
        </div>
    </div>
</div>

