<?php
use yii\web\View;
?>
<div class="container">
    <div style="margin-bottom: 60px">
    <?= $this->render('form', [
        'action'=>['/daily/week-report/add']
    ])?>
    </div>
    <?php if(!empty($transactionList)) :?>
    <?= $this->render('@webroot/../views/site/panel-header',[
        'title'=>'事项列表',
    ])?>
    <?= $this->render('select-transaction-list', [
        'transactionList'=>$transactionList,
        'ser_filter'=>$transaction_ser_filter,
    ])?>
    <?= $this->render('@webroot/../views/site/panel-footer')?>
    <?php endif;?>
</div>
<?php
$JS = <<<JS
$(document).ready(function() {
    // 删除tag的按钮
    $('.container').on('click','.selected-transaction-tags .tag-close',function(){
        var uuid = $(this).attr('id');
        var container = $(this).parents('.container');
        var transaction_uuid = container.find('.transaction-list #'+uuid);
        transaction_uuid.attr('checked', false);
        $(this).parentsUntil('ul').remove();

        // 将删除操作同步到listform里面去
        var transaction_title_filed = container.find('.ListFilterForm .transaction-title');
        var transaction_uuid_filed = container.find('.ListFilterForm .transaction-uuid');
        var transaction_uuid_checked = container.find('.WeekReportForm .transaction-uuid');
        var transaction_title_value = transaction_title_filed.val();
        transaction_title_value = transaction_title_value.split(',');
        var transaction_uuid_value = transaction_uuid_filed.val();
        transaction_uuid_value = transaction_uuid_value.split(',');
        for(var i = 0; i < transaction_uuid_value.length; i++) {
            if(transaction_uuid_value[i] === uuid) {
                transaction_title_value.splice(i,1);
                transaction_uuid_value.splice(i,1);
                break;
            }
        }
        transaction_title_filed.val(transaction_title_value.join(','));
        transaction_uuid_filed.val(transaction_uuid_value.join(','));
        transaction_uuid_checked.val(transaction_uuid_value.join(','));
    });
    
    // checkbox点击事件，作的功能
    $('.container').on('click','.transaction-uuid',function() {
        var checked = $(this).attr('checked');
        var container = $(this).parents('.container');
        var transaction_tags = container.find('.selected-transaction-tags ul');
        var uuid = $(this).val();
        var transaction_title = $(this).parents('tr').find('.transaction-title')[0].innerHTML;
        
        // listfilter 里面两个元素保存已选择的职位信息
        var transaction_title_filed = container.find('.ListFilterForm .transaction-title');
        var transaction_uuid_filed = container.find('.ListFilterForm .transaction-uuid');
        var transaction_uuid_checked = container.find('.WeekReportForm .transaction-uuid');
        if(checked === 'checked') {
            var html = '<li>' +
             '<div class="tag">' +
               '<span class="tag-content">'+transaction_title+'</span>' +
                '<span class="tag-close" id="'+uuid+'">' +
                 '<a href="javascript:;">×</a>' +
                  '</span>' +
                   '</div>' +
                    '</li>';
            transaction_tags.append(html);
            // 将元素保存到listfilterform里面去

            var transaction_title_value = transaction_title_filed.val();
            // 将其以逗号分开
            transaction_title_value = transaction_title_value.split(',');
            // 将空的元素删除掉
            transaction_title_value = $.grep(transaction_title_value, function(n) {return $.trim(n).length > 0;});

            var transaction_uuid_value = transaction_uuid_filed.val();
            transaction_uuid_value = transaction_uuid_value.split(',');
            transaction_uuid_value = $.grep(transaction_uuid_value, function(n) {return $.trim(n).length > 0;});

            transaction_title_value.push(transaction_title);
            transaction_uuid_value.push(uuid);
            transaction_title_filed.val(transaction_title_value.join(','));
            transaction_uuid_filed.val(transaction_uuid_value.join(','));
            transaction_uuid_checked.val(transaction_uuid_value.join(','));
        } else {
            var uuid_filed = transaction_tags.find('#'+uuid);
            uuid_filed.parentsUntil('ul').remove();
            // 将元素从listfilterform里面删去
            var transaction_title_value = transaction_title_filed.val();
            transaction_title_value = transaction_title_value.split(',');
            var transaction_uuid_value = transaction_uuid_filed.val();
            transaction_uuid_value = transaction_uuid_value.split(',');
            for(var i = 0; i < transaction_uuid_value.length; i++) {
                if(transaction_uuid_value[i] === uuid) {
                    transaction_title_value.splice(i,1);
                    transaction_uuid_value.splice(i,1);
                    break;
                }
            }
            transaction_title_filed.val(transaction_title_value.join(','));
            transaction_uuid_filed.val(transaction_uuid_value.join(','));
            transaction_uuid_checked.val(transaction_uuid_value.join(','));
        }
    });
});
JS;
$this->registerJs($JS, View::POS_END);
?>