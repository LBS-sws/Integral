<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('auditCut/index'));
}
$this->pageTitle=Yii::app()->name . ' - Integral Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'auditCut-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Exchange review'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('auditCut/index')));
		?>

        <?php if ($model->scenario!='view' && $model->state == 1): ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('integral','Audit'), array(
                'submit'=>Yii::app()->createUrl('auditCut/audit')));
            ?>
        <?php endif ?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='view' && $model->state == 1): ?>
                    <?php
                    echo TbHtml::button('<span class="fa fa-mail-reply-all"></span> '.Yii::t('integral','Rejected'), array(
                        'name'=>'btnJect','id'=>'btnJect','data-toggle'=>'modal','data-target'=>'#jectdialog'));
                    ?>
                <?php endif ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'state'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
            <?php if ($model->state == 2): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'reject_note',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-5">
                        <?php echo $form->textArea($model, 'reject_note',
                            array('readonly'=>true)
                        ); ?>
                    </div>
                </div>
                <legend></legend>
            <?php endif; ?>

            <?php
            $this->renderPartial('//site/integralCutForm',array(
                'form'=>$form,
                'model'=>$model,
                'readonly'=>(true),
            ));
            ?>


		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/ject',array('model'=>$model,'form'=>$form,'rejectName'=>"reject_note",'submit'=>Yii::app()->createUrl('auditCut/reject')));
?>
<?php
$js = "";
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

