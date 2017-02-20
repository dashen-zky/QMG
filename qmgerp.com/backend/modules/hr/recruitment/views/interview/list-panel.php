<?php
use backend\modules\hr\recruitment\models\RecruitCandidateMap;
?>
<div class="panel candidate-list">
    <?php
    $Js = <<<JS
$(function() {
$('.candidate-list').on('click','.show', function() {
    var self = $(this);
    $.get(self.attr('url'), function(data, status) {
        if(status !== 'success') {
            return ;
        }
        
        var modal = self.parents('.candidate-list').find('.candidate-show');
        modal.find('.modal-body').html(data);
        modal.modal('show');
        
        modal.on('click','.editForm',function() {
            modal.find('.enableEdit').attr("disabled",false);
            modal.find('.displayBlockWhileEdit').css('display','block');
        });
        
        $(".datetimepicker").live("click",function(){
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
});
});
JS;
    $this->registerJs($Js, \yii\web\View::POS_END);
    ?>
    <?= $this->render('list-filter-form',[
        'action'=>$list_filter_action,
    ]);?>
    <div class="list">
        <?= $this->render(isset($list_file)?$list_file:'list',[
            'candidateList'=>$candidateList,
            'ser_filter'=>$ser_filter,
        ])?>
    </div>
    <?= $this->render('/candidate/show-modal')?>
</div>
