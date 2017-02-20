<?php
use backend\models\AjaxLinkPage;
use yii\helpers\Url;
?>
<table class="table">
    <thead>
    <tr>
        <th>#</th>
        <th>姓名</th>
        <th>电话</th>
        <th>邮箱</th>
        <th>职位</th>
        <th class="col-md-4">备注</th>
        <th>简历</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($candidateList['list'] as $item) :?>
        <tr>
            <td><?= $item['id']?></td>
            <td>
                <a url="<?= Url::to([
                    '/recruitment/candidate/show',
                    'uuid'=>$item['uuid'],
                    'edit'=>true,
                ])?>" class="show" href="#">
                    <?= $item['name']?>
                </a>
            </td>
            <td><?= $item['phone']?></td>
            <td><?= $item['email']?></td>
            <td><?= $item['position']?></td>
            <td><?= $item['remarks']?></td>
            <td>
                <?php if(isset($item['resume']) && !empty($item['resume'])) :?>
                    <?php
                    // 将attachment字段解析出来
                    $item['resume'] = unserialize($item['resume']);
                    ?>
                    <?php foreach($item['resume'] as $key=>$path) :?>
                        <div>
                            <a href="<?= Url::to([
                                '/recruitment/candidate/resume-download',
                                'path'=>$path,
                                'file_name'=>$key,
                            ])?>"><?= $key?></a>
                        </div>
                    <?php endforeach?>
                <?php endif?>
            </td>
            <td><a href="<?= Url::to([
                    '/recruitment/candidate/add-black-list',
                    'uuid'=>$item['uuid']
                ])?>">加入黑名单</a></td>
        </tr>
    <?php endforeach?>
    </tbody>
</table>
<?php
if(isset($ser_filter) && !empty($ser_filter)) {
    $pageParams = [
        'pagination' => $candidateList['pagination'],
        'ser_filter'=>$ser_filter,
    ];
} else {
    $pageParams = [
        'pagination' => $candidateList['pagination'],
    ];
}
?>
<?= AjaxLinkPage::widget($pageParams); ?>
<?php
$Js = <<<Js
$(function() {
    $('.list .pagination').on('click', 'li', function() {
            pagination($(this));
    });
});
Js;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
