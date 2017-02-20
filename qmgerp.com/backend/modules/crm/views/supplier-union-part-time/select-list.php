<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
?>

<?= $this->render('list-filter-form',[
    'action'=>['/crm/supplier-union-part-time/list-filter'],
    'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
]);?>
<table class="table">
    <thead>
    <th>选择</th>
    <th>类型</th>
    <th>名称</th>
    <th>编码</th>
    </thead>
    <tbody>
    <?php foreach($list['list'] as $item) :?>
    <tr>
        <td>
            <input type="radio" name="uuid" value="<?= $item['uuid']?>">
            <input type="hidden" value="<?= $item['type']?>" class="type">
        </td>
        <td><?= $item['type_name']?></td>
        <td class="name"><?= $item['name']?></td>
        <td><?= $item['code']?></td>
    </tr>
    <?php endforeach?>
    </tbody>
</table>

<?php
if(isset($ser_filter) && !empty($ser_filter)) {
    $pageParams = [
        'pagination' => $list['pagination'],
        'ser_filter'=>$ser_filter,
    ];
} else {
    $pageParams = [
        'pagination' => $list['pagination'],
    ];
}
?>
<?= MyLinkPage::widget($pageParams); ?>
