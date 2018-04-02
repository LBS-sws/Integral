<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('integralCut/index'));
}
$this->pageTitle=Yii::app()->name . ' - Credits for';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'integralCut-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('integral','Integral Cut Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('integralCut/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php
            echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                'submit'=>Yii::app()->createUrl('integralCut/new'),
            ));
            echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('integralCut/save')));
			?>
<?php endif ?>
        <?php if ($model->scenario=='edit'): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                'submit'=>Yii::app()->createUrl('integralCut/delete')));
            ?>
        <?php endif ?>
        <?php if ($model->scenario!='new'): ?>
            <?php
            echo TbHtml::button('<span class="fa  fa-cube"></span> '.Yii::t('app','Exchange'), array(
                    'class'=>'btnIntegralApply','data-id'=>$model->id)
            );
            ?>
        <?php endif ?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php
                $counter = ($model->no_of_attm['icut'] > 0) ? ' <span id="docicut" class="label label-info">'.$model->no_of_attm['icut'].'</span>' : ' <span id="docicut"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadicut',)
                );
                ?>
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
                <?php echo $form->labelEx($model,'inventory',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->numberField($model, 'inventory',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'ICUT',
    'header'=>Yii::t('dialog','File Attachment'),
    'ronly'=>($model->scenario=='view'),
));
?>

<?php $this->renderPartial('//site/integralApply',array(
    'submit'=> Yii::app()->createUrl('integralCut/apply')
));
?>
<?php

$js = "
        $('.btnIntegralApply').on('click',function () {
            var tr = $(this).parents('tr:first');
            $('#set_id').val('".$model->id."');
            $('#integral_name').val('".$model->integral_name."');
            $('#integral').val('".$model->integral_num."');
            $('#integralApply').modal('show');
            return false;
        })
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
Script::genFileUpload($model,$form->id,'ICUT');
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<script>
    $(function () {
        $(".btnIntegralApply").on("click",function () {
            var $tr = $(this).parents("tr:first");
            $("#set_id").val($(this).data("id"));
            $("#integral_name").val($tr.find(".integral_name:first").text());
            $("#integral").val($tr.find(".integral_num:first").text());
            $('#integralApply').modal('show');
            return false;
        })
    })
</script>
<?php $this->endWidget(); ?>


