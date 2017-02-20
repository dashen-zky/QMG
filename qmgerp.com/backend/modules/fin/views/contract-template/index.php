<div class="col-md-12 contract-template">

    <ul class="nav nav-tabs">
        <li class="<?= isset($validateError)?"":"active"?>"><a href="#contract-template-tab-1" data-toggle="tab">模板列表</a></li>
        <li class="<?= isset($validateError)?"active":""?>"><a href="#contract-template-tab-2" data-toggle="tab">新建模板</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= isset($validateError)?"":"active in"?>" id="contract-template-tab-1">
            <?= $this->render('list',[
                'contractTemplateList'=>$contractTemplateList,
            ])?>
        </div>
        <div class="tab-pane fade <?= isset($validateError)?"active in":""?>" id="contract-template-tab-2">
            <?= $this->render('add',[
                'model'=>$model,
            ])?>
        </div>
    </div>
</div>