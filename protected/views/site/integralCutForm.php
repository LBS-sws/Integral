
<div class="form-group">
    <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'employee_name',
            array('readonly'=>(true))
        ); ?>
        <?php echo $form->hiddenField($model, 'employee_id'); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'set_id',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'set_name',
            array('readonly'=>(true))
        ); ?>
        <?php echo $form->hiddenField($model, 'set_id'); ?>
    </div>
    <?php echo TbHtml::link(Yii::t("integral","Item details"),Yii::app()->createUrl('integralCut/view',array("index"=>$model->set_id)),array("target"=>"_blank")); ?>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'integral',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'integral',
            array('readonly'=>(true),'id'=>'integral')
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'apply_num',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->numberField($model, 'apply_num',
            array('readonly'=>($readonly),'id'=>'apply_num')
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textArea($model, 'remark',
            array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>($readonly))
        ); ?>
    </div>
</div>
