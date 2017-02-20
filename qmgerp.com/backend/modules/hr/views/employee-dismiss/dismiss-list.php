<?php
use yii\bootstrap\Html;
?>

<?= Html::beginForm(['/hr/employee/dismiss-list-update'], 'post', [
    'class' => 'form-horizontal EmployeeDismissForm',
    'data-parsley-validate' => "true",
])?>
<input hidden value="<?= $uuid?>" name="EmployeeDismissForm[uuid]">
<table class="table">
<thead>
    <td class="col-md-1">项目</td>
    <td class="col-md-11">内容</td>
</thead>
<tbody>
<tr>
    <td>工作交接</td>
    <td>
        <?php
        $transfer_mission_list = $config->getList('transfer_mission');
        if(!empty($transfer_mission_list)) :
            foreach ($transfer_mission_list as $index=>$value) :
                ?>
                <div style="width: 200px; float: left">
                    <input name="EmployeeDismissForm[transfer_mission][]"
                           class = 'enableEdit' disabled
                           type="checkbox" value="<?= $index?>"
                        <?= isset($dismiss_list['transfer_mission'])&&in_array($index, $dismiss_list['transfer_mission']) ?'checked':''?> >
                    <label><?= $value?></label>
                </div>
            <?php endforeach;?>
        <?php endif;?>
    </td>
</tr>
<tr>
    <td>个人物品归还</td>
    <td>
        <?php
        $assets_return_list = $config->getList('assets_return');
        if(!empty($assets_return_list)) :
            foreach ($assets_return_list as $index=>$value) :
                ?>
                <div style="width: 200px; float: left">
                    <input name="EmployeeDismissForm[assets_return][]"
                           class = 'enableEdit' disabled
                           type="checkbox" value="<?= $index?>"
                        <?= isset($dismiss_list['assets_return'])&&in_array($index, $dismiss_list['assets_return']) ?'checked':''?> >
                    <label><?= $value?></label>
                </div>
            <?php endforeach;?>
        <?php endif;?>
    </td>
</tr>
<tr>
    <td>个人款项</td>
    <td>
        <?php
        $financial_settlement_list = $config->getList('financial_settlement');
        if(!empty($financial_settlement_list)) :
            foreach ($financial_settlement_list as $index=>$value) :
                ?>
                <div style="width: 200px; float: left">
                    <input name="EmployeeDismissForm[financial_settlement][]"
                           class = 'enableEdit' disabled
                           type="checkbox" value="<?= $index?>"
                        <?= isset($dismiss_list['financial_settlement'])&&in_array($index, $dismiss_list['financial_settlement']) ?'checked':''?> >
                    <label><?= $value?></label>
                </div>
            <?php endforeach;?>
        <?php endif;?>
    </td>
</tr>
<tr>
    <td>备注</td>
    <td>
        <?= Html::textarea('EmployeeDismissForm[remarks]',
            isset($dismiss_list['remarks'])?$dismiss_list['remarks']:'',
            [
                'class' => 'enableEdit form-control col-md-12',
                'disabled'=>true,
                'rows'=>3,
            ]
        )?>
    </td>
</tr>
<tr>
    <td colspan="3"></td>
</tr>
</tbody>
</table>
<span class="col-md-12">
<span class="col-md-4"></span>
<span class="col-md-4 enableEditBlock" style="display: none">
    <input type="submit" value="提交" class="form-control btn-primary">
</span>
<span class="col-md-4"></span>
</span>
<?= Html::endForm()?>
