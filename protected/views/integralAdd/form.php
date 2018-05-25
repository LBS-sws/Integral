<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('integralAdd/index'));
}
$this->pageTitle=Yii::app()->name . ' - Credit type allocation';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'integralAdd-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('integral','Integral Add Form'); ?></strong>
	</h1>
<!--
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Layout</a></li>
		<li class="active">Top Navigation</li>
	</ol>
-->
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('integralAdd/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php
            echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                'submit'=>Yii::app()->createUrl('integralAdd/new'),
            ));
            echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('integralAdd/save')));
			?>
<?php endif ?>
        <?php if ($model->scenario=='edit'): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                'submit'=>Yii::app()->createUrl('integralAdd/delete')));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'integral_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'integral_name',
                        array('size'=>40,'maxlength'=>250,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'integral_num',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->numberField($model, 'integral_num',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'integral_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'integral_type',IntegralAddForm::getIntegralTypeAll(),
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'year_sw',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'year_sw',array(Yii::t("integral","off"),Yii::t("integral","on")),
                        array('readonly'=>($model->scenario=='view'),'id'=>'yearSw')
                    ); ?>
                </div>
            </div>
            <div class="form-group" style="display: none;">
                <?php echo $form->labelEx($model,'year_num',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->numberField($model, 'year_num',
                        array('readonly'=>($model->scenario=='view'),'id'=>'yearNum')
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'validity',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'validity',array(1=>1,5=>5),
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'s_remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-6">
                    <?php echo $form->textArea($model, 's_remark',
                        array('readonly'=>($model->scenario=='view'),'rows'=>4)
                    );
                    ?>
                </div>
            </div>
		</div>
	</div>
</section>


<?php
$js = "
        $('#yearSw').on('change',function () {
            if($(this).val()==1){
                $('#yearNum').parents('.form-group:first').slideDown(100);
            }else{
                $('#yearNum').parents('.form-group:first').slideUp(100);
            }
        }).trigger('change');
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


