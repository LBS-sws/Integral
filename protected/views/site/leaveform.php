
<?php if (!empty($model->reject_cause)): ?>
    <div class="form-group has-error">
        <?php echo $form->labelEx($model,'reject_cause',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-6">
            <?php echo $form->textArea($model, 'reject_cause',
                array('readonly'=>(true),"rows"=>4)
            ); ?>
        </div>
    </div>
<?php endif; ?>
<?php if ($model->scenario!='new'): ?>
    <div class="form-group">
        <?php echo TbHtml::label($model->getAttributeLabel("leave_code").'<span class="required">*</span>',"",array('class'=>"col-sm-2 control-label"));?>
        <div class="col-sm-4">
            <?php echo $form->textField($model, 'leave_code',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif; ?>
<?php if (get_class($model) == "LeaveForm"&&Yii::app()->user->validFunction('ZR06')): ?>
    <div class="form-group">
        <?php echo TbHtml::label($model->getAttributeLabel("employee_id").'<span class="required">*</span>',"",array('class'=>"col-sm-2 control-label"));?>
        <div class="col-sm-4">
            <?php echo $form->dropDownList($model, 'employee_id',LeaveForm::getBindEmployeeList(),
                array('readonly'=>($model->getInputBool()))
            ); ?>
        </div>
    </div>
<?php else:?>
    <div class="form-group">
        <?php echo TbHtml::label($model->getAttributeLabel("employee_id").'<span class="required">*</span>',"",array('class'=>"col-sm-2 control-label"));?>
        <div class="col-sm-4">
            <?php echo $form->textField($model, 'employee_id',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif; ?>
<div class="form-group">
    <?php echo TbHtml::label($model->getAttributeLabel("vacation_id").'<span class="required">*</span>',"",array('class'=>"col-sm-2 control-label"));?>
    <div class="col-sm-3">
        <?php echo $form->dropDownList($model, 'vacation_id',LeaveForm::getLeaveTypeList($model->city),
            array('readonly'=>($model->getInputBool()),"id"=>"leave_type")
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo TbHtml::label($model->getAttributeLabel("start_time").'<span class="required">*</span>',"",array('class'=>"col-sm-2 control-label"));?>
    <div class="col-sm-4">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textField($model, 'start_time',
                array('readonly'=>($model->getInputBool()),"id"=>"start_time")
            ); ?>
            <div class="input-group-btn" style="width: 80px;">
                <?php echo $form->dropDownList($model, 'start_time_lg',LeaveForm::getAMPMList(),
                    array('readonly'=>($model->getInputBool()),"id"=>"start_time_lg","style"=>"border-left:0px;")
                ); ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo TbHtml::label($model->getAttributeLabel("end_time").'<span class="required">*</span>',"",array('class'=>"col-sm-2 control-label"));?>
    <div class="col-sm-4">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textField($model, 'end_time',
                array('readonly'=>($model->getInputBool()),"id"=>"end_time")
            ); ?>
            <div class="input-group-btn" style="width: 80px;">
                <?php echo $form->dropDownList($model, 'end_time_lg',LeaveForm::getAMPMList(),
                    array('readonly'=>($model->getInputBool()),"id"=>"end_time_lg","style"=>"border-left:0px;")
                ); ?>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo TbHtml::label($model->getAttributeLabel("log_time").'<span class="required">*</span>',"",array('class'=>"col-sm-2 control-label"));?>
    <div class="col-sm-3">
        <div class="input-group">
            <?php echo $form->numberField($model, 'log_time',
                array('readonly'=>(true),"id"=>"log_time")
            ); ?>
            <span class="input-group-addon">天</span>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo TbHtml::label($model->getAttributeLabel("leave_cause").'<span class="required">*</span>',"",array('class'=>"col-sm-2 control-label"));?>
    <div class="col-sm-6">
        <?php echo $form->textArea($model, 'leave_cause',
            array('readonly'=>($model->getInputBool()),"rows"=>4)
        ); ?>
    </div>
</div>

<?php if (!empty($model->user_lcu)): ?>
    <legend><?php echo Yii::t("fete","Audit Info")?></legend>
    <div class="form-group">
        <?php echo $form->labelEx($model,'user_lcu',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-3">
            <?php echo $form->textField($model, 'user_lcu',
                array('readonly'=>(true))
            ); ?>
        </div>
        <?php echo $form->labelEx($model,'user_lcd',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-3">
            <?php echo $form->textField($model, 'user_lcd',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif; ?>
<?php if (!empty($model->area_lcu)): ?>
    <div class="form-group">
        <?php echo $form->labelEx($model,'area_lcu',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-3">
            <?php echo $form->textField($model, 'area_lcu',
                array('readonly'=>(true))
            ); ?>
        </div>
        <?php echo $form->labelEx($model,'area_lcd',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-3">
            <?php echo $form->textField($model, 'area_lcd',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif; ?>
<?php if (!empty($model->head_lcu)): ?>
    <div class="form-group">
        <?php echo $form->labelEx($model,'head_lcu',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-3">
            <?php echo $form->textField($model, 'head_lcu',
                array('readonly'=>(true))
            ); ?>
        </div>
        <?php echo $form->labelEx($model,'head_lcd',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-3">
            <?php echo $form->textField($model, 'head_lcd',
                array('readonly'=>(true))
            ); ?>
        </div>
    </div>
<?php endif; ?>