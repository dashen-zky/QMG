<?php
use yii\web\View;
?>
<?php
$JS = <<<JS
$(function() {
    $(function() {
    $(".family-table").on("click",".removeRow",function() {
        $(this).parentsUntil('tbody').remove();
    });
    $(".family-table").on('click',' .addRow',function() {
        var prev = $(this).prev();
        var lastIndex = prev.val();
        var index = parseInt(lastIndex) + 1;
        prev.val(index);
        var html = '<tr>' +
         '<td>' +
          '<input type="text" class="form-control" name="EmployeeForm[family]['+index+'][name]">' +
           '</td><td>' +
             '<input type="text" class="form-control" name="EmployeeForm[family]['+index+'][relation]">' +
              '</td><td>' +
                '<input type="text" class="form-control" name="EmployeeForm[family]['+index+'][id_card_number]">' +
                 '</td><td>' +
                  '<input type="text" class="form-control" name="EmployeeForm[family]['+index+'][work_company]">' +
                   '</td><td>' +
                    '<input type="text" class="form-control" name="EmployeeForm[family]['+index+'][position]">' +
                     '</td><td>' +
                      '<input type="text" class="form-control" name="EmployeeForm[family]['+index+'][phone]">' +
                       '</td><td><input hidden value="'+index+'" class="index"><button type="button" class="btn btn-primary removeRow btn-xs" name="">' +
                        '<i class="fa fa-2x fa-minus"></i></button></td></tr>';
        var tbody = $(this).parent().parent().parent();
        tbody.append(html);
    });
})
});
JS;
$this->registerJs($JS, View::POS_END);
?>
<table class="table table-responsive family-table">
    <thead>
    <th>
        姓名
    </th>
    <th>
        关系
    </th>
    <th>
        身份证
    </th>
    <th>
        工作单位
    </th>
    <th>
        职位
    </th>
    <th>
        联系方式
    </th>
    <th></th>
    </thead>
    <tbody>
    <?php $i = 0; $oldFamilyUuids = '';?>
    <?php if(isset($familyList) && !empty($familyList)) :?>
    <?php  foreach($familyList as $family):?>
        <tr>
            <td>
                <?php $oldFamilyUuids .= $family['uuid'] . " ";?>
                <input type="hidden" name="EmployeeForm[family][<?= $i?>][uuid]" value="<?= $family['uuid']?>">
                <input type="text" class="form-control" name="EmployeeForm[family][<?= $i?>][name]" value="<?= $family['name']?>">
            </td>
            <td><input type="text" class="form-control" name="EmployeeForm[family][<?= $i?>][relation]" value="<?= $family['relation']?>"></td>
            <td><input type="text" class="form-control" name="EmployeeForm[family][<?= $i?>][id_card_number]" value="<?= $family['id_card_number']?>"></td>
            <td><input type="text" class="form-control" name="EmployeeForm[family][<?= $i?>][company]" value="<?= $family['company']?>"></td>
            <td><input type="text" class="form-control" name="EmployeeForm[family][<?= $i?>][position]" value="<?= $family['position']?>"></td>
            <td><input type="text" class="form-control" name="EmployeeForm[family][<?= $i?>][phone]" value="<?= $family['phone']?>"></td>
            <td><input hidden value="'+index+'" class="index"><button type="button" class="btn btn-xs btn-primary removeRow" name=""><i class="fa fa-2x fa-minus"></i></button></td>
        </tr>
        <?php $i++?>
    <?php endforeach?>
    <?php endif?>
    <tr>
        <td>
            <input hidden value="<?= $oldFamilyUuids?>" name="EmployeeForm[family][oldFamilyUuids]"/>
            <input type="text" class="form-control" name="EmployeeForm[family][<?= $i?>][name]">
        </td>
        <td><input type="text" class="form-control" name="EmployeeForm[family][<?= $i?>][relation]"></td>
        <td><input type="text" class="form-control" name="EmployeeForm[family][<?= $i?>][id_card_number]"></td>
        <td><input type="text" class="form-control" name="EmployeeForm[family][<?= $i?>][company]"></td>
        <td><input type="text" class="form-control" name="EmployeeForm[family][<?= $i?>][position]"></td>
        <td><input type="text" class="form-control" name="EmployeeForm[family][<?= $i?>][phone]"></td>
        <td>
            <input hidden value="<?= $i?>">
            <button type="button" class="btn btn-primary addRow btn-xs" name="">
                <i class="fa fa-2x fa-plus"></i>
            </button>
        </td>
    </tr>
    </tbody>
</table>