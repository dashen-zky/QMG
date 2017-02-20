<?php
use yii\web\View;
use yii\bootstrap\Html;
?>
<?php
$JS = <<<JS
$(function() {
    $(".salary-adjust-record").on("click",".removeRow",function() {
        $(this).parentsUntil('tbody').remove();
    });

    $(".salary-adjust-record").on('click',' .addRow',function() {
        var index = (new Date()).getTime();
        var html = '<tr>' +
         '<td>' +
          '<input type="text" class="input-section datetimepicker form-control" ' +
           'name="EmployeeForm[salary_adjust_record]['+index+'][time]">' +
           '</td><td>' +
             '<input type="text" class="form-control" ' +
              'name="EmployeeForm[salary_adjust_record]['+index+'][salary]" data-parsley-type="number">' +
              '</td><td>' +
                '<input type="text" class="form-control" ' +
                 'name="EmployeeForm[salary_adjust_record]['+index+'][reason]">' +
                 '</td><td>' +
                  '<button type="button" class="btn btn-primary removeRow btn-xs">' +
                    '<i class="fa fa-2x fa-minus"></i></button></td></tr>';
        var tbody = $(this).parents('.salary-adjust-record-tbody');
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
<table class="table table-responsive salary-adjust-record">
    <thead>
    <th>
        时间
    </th>
    <th>
        薪资
    </th>
    <th class="col-md-7">
        理由
    </th>
    <th>操作</th>
    </thead>
    <tbody class="salary-adjust-record-tbody">
        <?php if (!empty($salaryAdjustList)) :?>
            <?php foreach ($salaryAdjustList as $index => $item) :?>
                <tr>
                    <td><?= Html::textInput("EmployeeForm[salary_adjust_record][$index][time]",
                            $salaryAdjustList[$index]['time'],[
                                'class'=>'input-section datetimepicker form-control'
                            ]) ?></td>
                    <td><?= Html::textInput("EmployeeForm[salary_adjust_record][$index][salary]",
                            $salaryAdjustList[$index]['salary'], [
                                'class'=>'form-control',
                                'data-parsley-type'=>'number',
                            ])?></td>
                    <td><?= Html::textInput("EmployeeForm[salary_adjust_record][$index][reason]",
                            $salaryAdjustList[$index]['reason'], [
                                'class'=>'form-control'
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
            <td><?= Html::textInput("EmployeeForm[salary_adjust_record][$i][time]",
                    null,[
                        'class'=>'input-section datetimepicker form-control'
                    ]) ?></td>
            <td><?= Html::textInput("EmployeeForm[salary_adjust_record][$i][salary]",
                    null, [
                        'class'=>'form-control',
                        'data-parsley-type'=>'number',
                    ])?></td>
            <td><?= Html::textInput("EmployeeForm[salary_adjust_record][$i][reason]",
                    null, [
                        'class'=>'form-control'
                    ])?></td>
            <td>
                <button type="button" class="btn btn-xs btn-primary addRow" name="">
                    <i class="fa fa-2x fa-plus"></i>
                </button>
            </td>
        </tr>
    </tbody>
</table>