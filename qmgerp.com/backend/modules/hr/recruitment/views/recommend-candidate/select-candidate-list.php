<?php
use backend\models\AjaxLinkPage;
use yii\helpers\Url;
use yii\bootstrap\Html;
use backend\modules\hr\recruitment\models\RecruitCandidateMap;
$checked = (new RecruitCandidateMap())->getCandidateUuidsByRecruitUuid($recruit_uuid);

?>
<div class="candidate-list-container">
<a href="#" class="select-all">全选</a>
<table class="table">
    <thead>
    <tr>
        <th>选择</th>
        <th>#</th>
        <th>姓名</th>
        <th>电话</th>
        <th>邮箱</th>
        <th>职位</th>
        <th class="col-md-4">备注</th>
        <th>简历</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($candidateList['list'] as $item) :?>
        <tr>
            <td>
                <input value="<?= $item['uuid']?>"
                       type="checkbox"
                       class="candidate-uuid"
                    <?= in_array($item['uuid'], $checked)?'checked':''?>>
            </td>
            <td><?= $item['id']?></td>
            <td>
                <a url="<?= Url::to([
                    '/recruitment/recommend-candidate/show-candidate',
                    'candidate_uuid'=>$item['uuid'],
                    'recruit_uuid'=>$recruit_uuid,
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
<?= Html::beginForm(['/recruitment/recommend-candidate/update'], 'post', [
    'class' => 'form-horizontal RecommendCandidateForm',
    'data-parsley-validate' => "true",
])?>
<input hidden name="RecommendCandidateForm[candidate_uuid]"
       class="candidate-uuid" value="<?= implode(',', $checked)?>">
<input hidden name="RecommendCandidateForm[recruit_uuid]" value="<?= $recruit_uuid?>">
<span style="float: left; width: 200px">
<input type="button" value="推荐候选人"
       class="submit form-control btn-primary">
</span>
<?php Html::endForm();?>
</div>
<?php
$Js = <<<Js
$(function() {
    $('.list .pagination').on('click', 'li', function() {
        pagination($(this));
    });
    
    $('.candidate-list-container').on('click','.select-all',function() {
        var container = $(this).parents('.candidate-list-container');
        var candidate_uuid = '';
        $.each(container.find('table .candidate-uuid'), function() {
            var checked = $(this).attr('checked');
            if(checked != 'checked') {
                candidate_uuid += ',' + $(this).val();
                $(this).attr('checked', 'checked');
            }
        });
        
        var checked_candidate_uuid_field = container.find('form .candidate-uuid');
        var checked_candidate_uuid = checked_candidate_uuid_field.val() + candidate_uuid;
        checked_candidate_uuid_field.val(checked_candidate_uuid);
    });
    
    $('.candidate-list-container').on('click','table .candidate-uuid', function() {
        var candidate_uuid = $(this).val();
        var checked = $(this).attr('checked');
        var checked_candidate_uuid_field = $(this).parents('.candidate-list-container').find('form .candidate-uuid');
        var checked_candidate_uuid = checked_candidate_uuid_field.val();
        if(checked == 'checked') {
            checked_candidate_uuid += ',' + candidate_uuid;
        } else {
            checked_candidate_uuid = checked_candidate_uuid.replace(candidate_uuid,'');
        }
        
        checked_candidate_uuid_field.val(checked_candidate_uuid);
    });
    
    $('.RecommendCandidateForm').on('click','.submit', function() {
        var form = $(this).parents('form');
        var candidate_uuid_field = form.find('.candidate-uuid');
        var candidate_uuid = candidate_uuid_field.val();
        if (candidate_uuid == '' || typeof(candidate_uuid) == "undefined") {
            return ;
        }
        
        form.submit();
    });
});
Js;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
