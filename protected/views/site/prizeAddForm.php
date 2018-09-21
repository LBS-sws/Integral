
<div class="form-group">
    <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'employee_id',CreditRequestForm::getBindingList($model->employee_id),
            array('readonly'=>($model->scenario=='view'||$model->state == 1||$model->state == 3),"id"=>"employee_id")
        ); ?>
    </div>
    <?php if (get_class($model) == "PrizeRequestForm"&&!$readonly): ?>
        <?php echo TbHtml::label(Yii::t("integral","Total credits available"),"",array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-3">
            <?php echo TbHtml::textField("available","",array('readonly'=>(true),'id'=>'available')); ?>
        </div>
    <?php endif; ?>
</div>

<div class="form-group">
    <?php echo $form->labelEx($model,'prize_type',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->dropDownListTwo($model, 'prize_type',PrizeTypeForm::getPrizeTypeList(),
            array('readonly'=>($readonly),'id'=>'prize_type')
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'prize_point',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->textField($model, 'prize_point',
            array('readonly'=>(true),'id'=>'prize_point')
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo TbHtml::label(Yii::t("integral","min point"),"",array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo TbHtml::textField("min_point","",array('readonly'=>(true),'id'=>'min_point')); ?>
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
<?php if ($model->scenario!='new'): ?>
    <div class="form-group">
        <?php echo $form->labelEx($model,'apply_date',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-3">
            <?php echo $form->textField($model, 'apply_date',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif ?>

<script>
    $(function () {
        $("#prize_type").on("change",function () {
            var num = $(this).find("option:selected").attr("num");
            var min_point = $(this).find("option:selected").attr("min_point");
            $("#prize_point").val(num);
            $("#min_point").val(min_point);
        }).trigger("change");

        $("#employee_id").on("change",function () {
            if($("#available").length>0){
                $("#available").val("");
                $.ajax({
                    type: 'post',
                    url: '<?php echo Yii::app()->createUrl("prizeRequest/ajaxStaffGift")?>',
                    dataType: 'json',
                    data: {
                        "employee_id":$("#employee_id").val()
                    },
                    success: function(json) {
                        if(json["status"] == 1){
                            $("#available").val(json["val"]);
                        }
                    }
                });
            }
        }).trigger("change");
    })
</script>