
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
    <?php echo $form->labelEx($model,'gift_type',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'gift_name',
            array('readonly'=>(true))
        ); ?>
        <?php echo $form->hiddenField($model, 'gift_type'); ?>
    </div>
    <?php echo TbHtml::link(Yii::t("integral","Item details"),Yii::app()->createUrl('giftType/view',array("index"=>$model->gift_type)),array("target"=>"_blank")); ?>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'bonus_point',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'bonus_point',
            array('readonly'=>(true),'id'=>'bonus_point')
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
