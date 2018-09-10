<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('prizeType/index'));
}
$this->pageTitle=Yii::app()->name . ' - Credit type allocation';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'prizeType-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('integral','Prize Type Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('prizeType/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php
            if($model->scenario!='new'){
                echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                    'submit'=>Yii::app()->createUrl('prizeType/new'),
                ));
            }
            echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('prizeType/save')));
			?>
<?php endif ?>
        <?php if ($model->scenario=='edit'): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                'submit'=>Yii::app()->createUrl('prizeType/delete')));
            ?>
        <?php endif ?>
	</div>
            <div class="btn-group pull-right" role="group">
                <?php
                $counter = ($model->no_of_attm['iprize'] > 0) ? ' <span id="dociprize" class="label label-info">'.$model->no_of_attm['iprize'].'</span>' : ' <span id="dociprize"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadiprize',)
                );
                ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>


            <div class="form-group">
                <?php echo $form->labelEx($model,'prize_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'prize_name',
                        array('size'=>40,'maxlength'=>250,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'prize_point',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->numberField($model, 'prize_point',
                        array('min'=>1,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'min_point',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->numberField($model, 'min_point',
                        array('min'=>1,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'tries_limit',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">

                    <div class="input-group">
                        <div class="input-group-btn">
                            <?php echo $form->dropDownList($model, 'tries_limit',$model->getTriesLimtList(),
                                array('min'=>1,'id'=>"tries_limit",'readonly'=>($model->scenario=='view'),"style"=>"width:150px;")
                            ); ?>
                        </div>
                        <?php echo $form->numberField($model, 'limit_number',
                            array('min'=>1,'id'=>"limit_number",'readonly'=>($model->scenario=='view'),"style"=>"display:none")
                        ); ?>
                    </div>
                </div>
            </div>
		</div>
	</div>
</section>


<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'IPRIZE',
    'header'=>Yii::t('dialog','File Attachment'),
    'ronly'=>($model->scenario=='view'),
));
?>

<?php
$js = "
    $('#tries_limit').on('change',function(){
        if($(this).val()==0){
            $('#limit_number').hide();
        }else{
            $('#limit_number').show();
        }
    }).trigger('change');
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
Script::genFileUpload($model,$form->id,'IPRIZE');
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


