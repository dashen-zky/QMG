<?php
use yii\web\View;
use yii\widgets\Pjax;
?>
<div id="selectDepartmentContainerModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="panel panel-body" data-sortable-id="table-basic-4">
<?php
$JS = <<<JS
    $(document).ready(function() {

        $("#parentDepartmentSelect").click(function() {
            var seletedRadio = $("#selectDepartmentContainerModal input[name='uuid']:checked");
            var departmentName = seletedRadio.parent().siblings(".departmentName")[0];
            var selectedRadioValue = seletedRadio.val();
            $(".department-form form input[name='DepartmentForm[parent_uuid]']").val(selectedRadioValue);
            $(".department-form form input[name='DepartmentForm[parent_name]']").val(departmentName.innerHTML);
            $('#selectDepartmentContainerModal').modal('hide');
        });

        //$("#positionDepartmentSelect").click(function() {
        //    var seletedRadio = $("#selectDepartmentContainerModal input[name='uuid']:checked");
        //    var departmentName = seletedRadio.parent().siblings(".departmentName")[0];
        //    var selectedRadioValue = seletedRadio.val();
        //    $("#default-tab-add form input[name='PositionForm[de_uuid]']").val(selectedRadioValue);
        //    $("#default-tab-add form input[name='PositionForm[departmentName]']").val(departmentName.innerHTML);
        //    $('#selectDepartmentContainerModal').modal('hide');
        //});
    });
JS;
$this->registerJs($JS, View::POS_END);
?>
    <div></div>
    <a href="#" id="<?= $selectId?>"><button class="btn btn-primary col-md-3" style="float: right">选择</button></a>
    </div>
</div>

