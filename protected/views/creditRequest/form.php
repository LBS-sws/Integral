<?php
$this->pageTitle=Yii::app()->name . ' - creditRequest Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'creditRequest-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('integral','Integral Form'); ?></strong>
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
                    'submit'=>Yii::app()->createUrl('creditRequest/index')));
                ?>

                <?php if ($model->scenario!='view' && $model->state != 1 && $model->state != 3 && $model->state != 4): ?>
                    <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                        'submit'=>Yii::app()->createUrl('creditRequest/save')));
                    ?>
                    <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('integral','For Audit'), array(
                        'submit'=>Yii::app()->createUrl('creditRequest/audit')));
                    ?>
                <?php endif ?>
                <?php if ($model->scenario=='edit'&& $model->state == 0): ?>
                    <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                            'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                    );
                    ?>
                <?php endif; ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php
                $counter = ($model->no_of_attm['gral'] > 0) ? ' <span id="docgral" class="label label-info">'.$model->no_of_attm['gral'].'</span>' : ' <span id="docgral"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadgral',)
                );
                ?>
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
            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php if (Yii::app()->user->validFunction('ZR01')): ?>
                        <?php echo $form->dropDownList($model, 'employee_id',$model->getBindingList($model->employee_id),
                            array('readonly'=>($model->scenario=='view'||$model->state == 1||$model->state == 3||$model->state == 4))
                        ); ?>
                    <?php else:?>
                        <?php echo $form->textField($model, 'employee_name',
                            array('readonly'=>(true))
                        ); ?>
                        <?php echo $form->hiddenField($model, 'employee_id'); ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php
            $this->renderPartial('//site/integralAddForm',array(
                'form'=>$form,
                'model'=>$model,
                'readonly'=>($model->scenario=='view'||$model->state == 1||$model->state == 3||$model->state == 4),
            ));
            ?>


        </div>
    </div>
</section>


<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'GRAL',
    'header'=>Yii::t('dialog','File Attachment'),
    'ronly'=>($model->scenario=='view'||$model->state == 1||$model->state == 3||$model->state == 4),
));
?>
<?php
$this->renderPartial('//site/removedialog');
?>
<?php
Script::genFileUpload($model,$form->id,'GRAL');

$js = "
$('#apply_date').datepicker({autoclose: true, format: 'yyyy-mm-dd',language: 'zh_cn'});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('creditRequest/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

