<?= $this->render('form',[
    'action'=>['/accountReceivable/receive-money/add'],
    'formData'=>$formData,
    'scenario'=>'add',
    'model'=>$model,
    'receiveCompanyList'=>$receiveCompanyList,
])?>

