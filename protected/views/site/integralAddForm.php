
<div class="form-group">
    <?php echo $form->labelEx($model,'integral_type',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'integral_type',IntegralAddForm::getIntegralTypeAll(),
            array('readonly'=>($readonly),'id'=>'int_type')
        ); ?>
    </div>
</div>
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
            var gral = $(this).find("option:selected").attr("gral");
            $("#integral").val(num);
            $("#int_type").val(gral);
        })
        $("#int_type").on("change",function () {
            var gral = $(this).val();
            if(gral==""){
                $("#set_id>option").show();
            }else{
                $("#set_id>option").hide();
                $("#set_id>option[gral='"+gral+"']").show();
            }
        })
    })
</script>