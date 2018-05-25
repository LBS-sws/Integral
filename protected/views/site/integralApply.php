
<?php
	$ftrbtn = array();
$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_DEFAULT,"class"=>"pull-left"));
$ftrbtn[] = TbHtml::button(Yii::t('integral','apply'), array('color'=>TbHtml::BUTTON_COLOR_PRIMARY,'submit'=>$submit));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'integralApply',
					'header'=>Yii::t('integral','apply cut'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>

    <div class="form-group">
        <?php echo TbHtml::hiddenField('CutForm[activity_id]',$activity_id,
            array('id'=>'activity_id')
        ); ?>
        <?php echo TbHtml::label(Yii::t('integral','Cut Name'),'',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-4">
            <?php echo TbHtml::hiddenField('CutForm[set_id]','',
                array('id'=>'set_id')
            ); ?>
            <?php echo TbHtml::textField('CutForm[integral_name]','',
                array('readonly'=>(true),'id'=>'integral_name')
            ); ?>
        </div>
        <?php echo TbHtml::label(Yii::t('integral','Cut Integral'),'',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-4">
            <?php echo TbHtml::numberField('CutForm[integral]','',
                array('readonly'=>(true),'id'=>'integral')
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo TbHtml::label(Yii::t('integral','Number of applications'),'',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-4">
            <?php echo TbHtml::numberField('CutForm[apply_num]','1',
                array('readonly'=>(false))
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo TbHtml::label(Yii::t('integral','Remark'),'',array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-8">
            <?php echo TbHtml::textArea('CutForm[remark]','',
                array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>(false))
            ); ?>
        </div>
    </div>
<?php
	$this->endWidget();
?>