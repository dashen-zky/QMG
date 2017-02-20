<?php
use yii\web\View;
use yii\bootstrap\Html;
?>
<?php
$JS = <<<JS
$(function() {
    $(".social-insurance-adjust-record").on("click",".removeRow",function() {
        $(this).parentsUntil('tbody').remove();
    });
    $(".social-insurance-adjust-record").on('click',' .addRow',function() {
        var index = (new Date()).getTime();
        var html = '<tr>' +
         '<td>' +
          '<input type="text" class="input-section datetimepicker form-control" ' +
           'name="EmployeeForm[social_insurance_adjust_record]['+index+'][time]">' +
           '</td><td>' +
             '<input type="text" data-parsley-type="number" class="form-control" ' +
              'name="EmployeeForm[social_insurance_adjust_record]['+index+'][base]">' +
              '</td><td>' +
                  '<button type="button" class="btn btn-primary removeRow btn-xs">' +
                    '<i class="fa fa-2x fa-minus"></i></button></td></tr>';
        var tbody = $(this).parents('.social-insurance-adjust-record-toby');
        tbody.append(html);
        
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
    });
});
JS;
$this->registerJs($JS, View::POS_END);
?>
<table class="table table-responsive social-insurance-adjust-record">
    <thead>
    <th>
        开始日期
    </th>
    <th>
        基数
    </th>
    <th>操作</th>
    </thead>
    <tbody class="social-insurance-adjust-record-toby">
    <?php if (!empty($socialInsuranceAdjustList)) :?>
        <?php foreach ($socialInsuranceAdjustList as $index => $item) :?>
            <tr>
                <td><?= Html::textInput("EmployeeForm[social_insurance_adjust_record][$index][time]",
                        $socialInsuranceAdjustList[$index]['time'],[
                            'class'=>'input-section datetimepicker form-control'
                        ]) ?></td>
                <td><?= Html::textInput("EmployeeForm[social_insurance_adjust_record][$index][base]",
                        $socialInsuranceAdjustList[$index]['base'], [
                            'class'=>'form-control',
                            'data-parsley-type'=>'number',
                        ])?></td>
                <td>
                    <button type="button" class="btn btn-xs btn-primary removeRow" name="">
                        <i class="fa fa-2x fa-minus"></i>
                    </button>
                </td>
            </tr>
        <?php endforeach;?>
    <?php endif;?>
    <tr>
        <?php $i = !isset($index)? 0 : $index + 1?>
        <td><?= Html::textInput("EmployeeForm[social_insurance_adjust_record][$i][time]",
                null,[
                    'class'=>'input-section datetimepicker form-control'
                ]) ?></td>
        <td><?= Html::textInput("EmployeeForm[social_insurance_adjust_record][$i][base]",
                null, [
                    'class'=>'form-control',
                    'data-parsley-type'=>'number',
                ])?></td>
        <td>
            <button type="button" class="btn btn-xs btn-primary addRow" name="">
                <i class="fa fa-2x fa-plus"></i>
            </button>
        </td>
    </tr>
    </tbody>
</table>