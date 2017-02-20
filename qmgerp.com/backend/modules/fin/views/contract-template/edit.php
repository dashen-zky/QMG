<div class="col-md-12 contract-template">

    <ul class="nav nav-tabs">
        <li class="active"><a href="#contract-template-tab-1" data-toggle="tab">编辑合同模板</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="contract-template-tab-1">
            <?= $this->render('@webroot/../views/site/panel-header',[
                'title'=>'编辑合同模板',
            ])?>
            <?= $this->render('form',[
                'model'=>$model,
                'formClass'=>'ContractTemplateForm',
                'action'=>['/fin/contract-template/update'],
                'contractTemplate'=>$contractTemplate,
            ])?>
            <?= $this->render('@webroot/../views/site/panel-footer')?>
        </div>
    </div>
</div>

