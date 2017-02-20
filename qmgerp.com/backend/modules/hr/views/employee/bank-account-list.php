<?php
use yii\web\View;
use yii\bootstrap\Html;
?>
<?php
$JS = <<<JS
$(function() {
    $(".bank-account").on("click",".removeRow",function() {
        $(this).parentsUntil('tbody').remove();
    });
    $(".bank-account").on('click',' .addRow',function() {
        var index = (new Date()).getTime();
        var html = '<tr>' +
         '<td>' +
          '<input type="text" class="form-control" name="EmployeeForm[bank_account]['+index+'][bank_of_deposit]">' +
           '</td><td>' +
             '<input type="text" data-parsley-type="number" class="form-control" name="EmployeeForm[bank_account]['+index+'][account]">' +
              '</td><td>' +
                  '<button type="button" class="btn btn-primary removeRow btn-xs">' +
                    '<i class="fa fa-2x fa-minus"></i></button></td></tr>';
        var tbody = $(this).parents('.bank-account-toby');
        tbody.append(html);
    });
});
JS;
$this->registerJs($JS, View::POS_END);
?>
<table class="table table-responsive bank-account">
    <thead>
    <th class="col-md-7">
        开户行
    </th>
    <th>
        账号
    </th>
    <th>操作</th>
    </thead>
    <tbody class="bank-account-toby">
    <?php if (!empty($bankAccountList)) :?>
        <?php foreach ($bankAccountList as $index => $item) :?>
            <tr>
                <td><?= Html::textInput("EmployeeForm[bank_account][$index][bank_of_deposit]",
                        $bankAccountList[$index]['bank_of_deposit'],[
                            'class'=>'form-control'
                        ]) ?></td>
                <td><?= Html::textInput("EmployeeForm[bank_account][$index][account]",
                        $bankAccountList[$index]['account'], [
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
        <td><?= Html::textInput("EmployeeForm[bank_account][$i][bank_of_deposit]",
                null,[
                    'class'=>'form-control'
                ]) ?></td>
        <td><?= Html::textInput("EmployeeForm[bank_account][$i][account]",
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