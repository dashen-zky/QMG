<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3 0003
 * Time: 上午 1:43
 */
?>
<?php
use yii\helpers\Json;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-fin',
    'menu-2-stamp',
    'menu-3-import'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'list')?'active':''?>"><a href="#import-stamp-tab-list" data-toggle="tab">进项发票</a></li>
        <li class="<?= (isset($tab) && $tab === 'add')?'active':''?>"><a href="#import-stamp-tab-add" data-toggle="tab">添加发票</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'list')?'active in':''?>" id="import-stamp-tab-list">
            <?= $this->render('list-panel', [
                'stampList'=>$stampList,
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add')?'active in':''?>" id="import-stamp-tab-add">
            <?= $this->render('add',[
                'formData'=>isset($formData)?$formData:null,
                'series_validate_error' => isset($series_validate_error)?$series_validate_error:false,
            ])?>
        </div>
    </div>
</div>
