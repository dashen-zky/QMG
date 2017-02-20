<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use backend\modules\hr\controllers\PositionController;
use yii\helpers\Url;
use yii\web\View;
use backend\modules\hr\models\DepartmentForm;
?>
<!-- begin panel -->
<div class="panel panel-body">
    <?php Pjax::begin()?>
    <?php
    // 点击编辑，弹出编辑页面
    $JS = <<<Js
        $(document).ready(function() {
            // 部门选择的三级联动效果
            $('.list-filter').on('change','.department-1',function() {
                var url = $(this).attr('id');
                url += "&uuid="+$(this).val();
                $.get(
                url,
                function(data, status) {
                    if(status === 'success') {
                        var form = $('.list-filter');
                        if(data == '<option value="0">未选择</option>') {
                            form.find('.department-3').html(data);
                        }
                        form.find('.department-2').html(data);
                    }
                }
                );
            }).on('change','.department-2',function() {
                var url = $(this).attr('id');
                url += "&uuid="+$(this).val();
                $.get(
                url,
                function(data, status) {
                    if(status === 'success') {
                        var form = $('.list-filter');
                        form.find('.department-3').html(data);
                    }
                }
                );
            });
            // 点击编辑
            $('.editPosition').click(function() {
                var url = $(this).attr('name');
                $.get(
                url,
                function(data,status) {
                    if ('success' === status) {
                        var modal = $("#editPositionContainerModal");
                        modal.find('.panel-body').html(data);
                        modal.modal('show');

                        $('.PositionForm').on('change','.department-1',function() {
                                var url = $(this).attr('id');
                                url += "&uuid="+$(this).val();
                                $.get(
                                url,
                                function(data, status) {
                                    if(status === 'success') {
                                        var form = $('.PositionForm');
                                        if(data == '<option value="0">未选择</option>') {
                                            form.find('.department-3').html(data);
                                        }
                                        form.find('.department-2').html(data);
                                    }
                                }
                                );
                            }).on('change','.department-2',function() {
                                var url = $(this).attr('id');
                                url += "&uuid="+$(this).val();
                                $.get(
                                url,
                                function(data, status) {
                                    if(status === 'success') {
                                        var form = $('.PositionForm');
                                        form.find('.department-3').html(data);
                                    }
                                }
                                );
                            });
                    }
                });
            });
        });
Js;
    $this->registerJs($JS, View::POS_END);
    ?>
    <?= $this->render('list-filter-form',[
        'filters'=>$filter_form_data,
    ])?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th class="col-md-1">职位名称</th>
            <th>
                <div>岗位编制</div>
                <div>在编人数</div>
            </th>
            <th class="col-md-3">岗位职责</th>
            <th class="col-md-3">岗位要求</th>
            <th>所属部门</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php $levelList = DepartmentForm::levelList()?>
        <?php foreach($positionList['positionList'] as $item):?>
            <tr>
                <td><?= $item['code']?></td>
                <td><?= $item['name']?></td>
                <td>
                    <?= $item['members_limit']?>/<?= $item['number_of_active']?>
                </td>
                <td><?= $item['duty']?></td>
                <td><?= $item['requirement']?></td>
                <td>
                <?php if(!empty($item['parent_departments'])) :?>
                    <?php foreach ($item['parent_departments'] as $value) :?>
                        <div><?= $value?></div>
                    <?php endforeach;?>
                <?php endif;?>
                </td>
                <td>
                    <div>
                        <a name='<?=Url::to([
                            '/hr/position/edit',
                            'uuid'=>$item['uuid'],
                        ])?>'  href="#" class="editPosition" data-toggle="#modal">编辑</a>
                    </div>
                    <div>
                        <a href="<?= Yii::$app->urlManager->createUrl(['/hr/position/delete','uuid'=>$item['uuid']])?>">删除</a>
                    </div>
                </td>

            </tr>
        <?php endforeach?>
        </tbody>
    </table>
    <?php
    if(isset($ser_filter) && !empty($ser_filter)) {
        $pageParams = [
            'pagination' => $positionList['pagination'],
            'ser_filter'=>$ser_filter,
        ];
    } else {
        $pageParams = [
            'pagination' => $positionList['pagination'],
        ];
    }
    ?>
    <?= MyLinkPage::widget($pageParams); ?>
    <?php Pjax::end()?>
</div>
<!-- end panel -->
<?= $this->render('edit')?>