
<div class="form-group">
    <?php echo $form->labelEx($model,'set_id',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownListTwo($model, 'set_id',IntegralAddForm::getIntegralAddList(),
            array('readonly'=>($readonly),'id'=>'set_id')
        ); ?>
    </div>
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
    <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textArea($model, 'remark',
            array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>($readonly))
        ); ?>
    </div>
</div>

<script>
    $(function () {
        $("#set_id").on("change",function () {
            var num = $(this).find("option:selected").attr("num");
            $("#integral").val(num);
        })
    })
</script>