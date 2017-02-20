<?php
use yii\bootstrap\Html;
?>

<?= Html::beginForm(['/hr/employee/entry-list-update'], 'post', [
    'class' => 'form-horizontal EmployeeEntryListForm',
    'data-parsley-validate' => "true",
])?>
<input hidden value="<?= $uuid?>" name="EmployeeEntryListForm[uuid]">
<table class="table">
<thead>
    <td class="col-md-1">项目</td>
    <td class="col-md-4">内容</td>
    <td class="col-md-7">备注</td>
</thead>
<tbody>
<tr>
    <td>个人资料</td>
    <td>
        <?php
        $basic_information_list = $config->getList('basic_information');
        if(!empty($basic_information_list)) :
            foreach ($basic_information_list as $index=>$value) :
                ?>
                <div style="width: 150px; float: left">
                    <input name="EmployeeEntryListForm[basic_information][]"
                           class = 'enableEdit' disabled
                           type="checkbox" value="<?= $index?>"
                        <?= isset($entry_list['basic_information'])&&in_array($index, $entry_list['basic_information']) ?'checked':''?> >
                    <label><?= $value?></label>
                </div>
            <?php endforeach;?>
        <?php endif;?>
    </td>
    <td>
        <?= Html::textarea('EmployeeEntryListForm[basic_information][remarks]',
            isset($entry_list['basic_information']['remarks'])?$entry_list['basic_information']['remarks']:'',
            [
                'class' => 'enableEdit form-control col-md-12',
                'disabled'=>true,
                'rows'=>3,
            ]
        )?>
    </td>
</tr>
<tr>
    <td>入职阅知</td>
    <td>
        <?php
        $entry_regulation_list = $config->getList('entry_regulation_list');
        if(!empty($entry_regulation_list)) :
            foreach ($entry_regulation_list as $index=>$value) :
                ?>
                <div style="width: 150px; float: left">
                    <input name="EmployeeEntryListForm[entry_regulation_list][]"
                           class = 'enableEdit' disabled
                           type="checkbox" value="<?= $index?>"
                        <?= isset($entry_list['entry_regulation_list'])&&in_array($index, $entry_list['entry_regulation_list']) ?'checked':''?> >
                    <label><?= $value?></label>
                </div>
            <?php endforeach;?>
        <?php endif;?>
    </td>
    <td>
        <?= Html::textarea('EmployeeEntryListForm[entry_regulation_list][remarks]',
            isset($entry_list['entry_regulation_list']['remarks'])?$entry_list['entry_regulation_list']['remarks']:'',
            [
                'class' => 'enableEdit form-control col-md-12',
                'disabled'=>true,
                'rows'=>3,
            ]
        )?>
    </td>
</tr>
<tr>
    <td>劳动合同</td>
    <td>
        <?php
        $contract_list = $config->getList('contract');
        if(!empty($contract_list)) :
            foreach ($contract_list as $index=>$value) :
                ?>
                <div style="width: 150px; float: left">
                    <input name="EmployeeEntryListForm[contract][]"
                           class = 'enableEdit' disabled
                           type="checkbox" value="<?= $index?>"
                        <?= isset($entry_list['contract'])&&in_array($index, $entry_list['contract']) ?'checked':''?> >
                    <label><?= $value?></label>
                </div>
            <?php endforeach;?>
        <?php endif;?>
    </td>
    <td>
        <?= Html::textarea('EmployeeEntryListForm[contract][remarks]',
            isset($entry_list['contract']['remarks'])?$entry_list['contract']['remarks']:'',
            [
                'class' => 'enableEdit form-control col-md-12',
                'disabled'=>true,
                'rows'=>3,
            ]
        )?>
    </td>
</tr>
<tr>
    <td>培训</td>
    <td>
        <?php
        $training_list = $config->getList('training');
        if(!empty($training_list)) :
            foreach ($training_list as $index=>$value) :
                ?>
                <div style="width: 150px; float: left">
                    <input name="EmployeeEntryListForm[training][]"
                           class = 'enableEdit' disabled
                           type="checkbox" value="<?= $index?>"
                        <?= isset($entry_list['training'])&&in_array($index, $entry_list['training']) ?'checked':''?> >
                    <label><?= $value?></label>
                </div>
            <?php endforeach;?>
        <?php endif;?>
    </td>
    <td>
        <?= Html::textarea('EmployeeEntryListForm[training][remarks]',
            isset($entry_list['training']['remarks'])?$entry_list['training']['remarks']:'',
            [
                'class' => 'enableEdit form-control col-md-12',
                'disabled'=>true,
                'rows'=>3,
            ]
        )?>
    </td>
</tr>
<tr>
    <td>办公物品</td>
    <td>
        <?php
        $office_assets_list = $config->getList('office_assets');
        if(!empty($office_assets_list)) :
            foreach ($office_assets_list as $index=>$value) :
                ?>
                <div style="width: 150px; float: left">
                    <input name="EmployeeEntryListForm[office_assets][]"
                           class = 'enableEdit' disabled
                           type="checkbox" value="<?= $index?>"
                        <?= isset($entry_list['office_assets'])&&in_array($index, $entry_list['office_assets']) ?'checked':''?> >
                    <label><?= $value?></label>
                </div>
            <?php endforeach;?>
        <?php endif;?>
    </td>
    <td>
        <?= Html::textarea('EmployeeEntryListForm[office_assets][remarks]',
            isset($entry_list['office_assets']['remarks'])?$entry_list['office_assets']['remarks']:'',
            [
                'class' => 'enableEdit form-control col-md-12',
                'disabled'=>true,
                'rows'=>3,
            ]
        )?>
    </td>
</tr>
<tr>
    <td>工作通讯</td>
    <td>
        <?php
        $communication_list = $config->getList('communication');
        if(!empty($communication_list)) :
            foreach ($communication_list as $index=>$value) :
                ?>
                <div style="width: 150px; float: left">
                    <input name="EmployeeEntryListForm[communication][]"
                           class = 'enableEdit' disabled
                           type="checkbox" value="<?= $index?>"
                        <?= isset($entry_list['communication'])&&in_array($index, $entry_list['communication']) ?'checked':''?> >
                    <label><?= $value?></label>
                </div>
            <?php endforeach;?>
        <?php endif;?>
    </td>
    <td>
        <?= Html::textarea('EmployeeEntryListForm[communication][remarks]',
            isset($entry_list['communication']['remarks'])?$entry_list['communication']['remarks']:'',
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
