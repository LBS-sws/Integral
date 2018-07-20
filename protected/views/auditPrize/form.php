<?php
$this->pageTitle=Yii::app()->name . ' - auditPrize Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'auditPrize-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Prize review'); ?></strong>
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
                    'submit'=>Yii::app()->createUrl('auditPrize/index')));
                ?>
                <?php if ($model->scenario!='view' && $model->state == 1): ?>
                    <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('integral','Audit'), array(
                        'submit'=>Yii::app()->createUrl('auditPrize/audit')));
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
                <?php
                $counter = ($model->no_of_attm['rpri'] > 0) ? ' <span id="docrpri" class="label label-info">'.$model->no_of_attm['rpri'].'</span>' : ' <span id="docrpri"></span>';
                echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('misc','Attachment').$counter, array(
                        'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadrpri',)
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

            <?php
            $this->renderPartial('//site/prizeAddForm',array(
                'form'=>$form,
                'model'=>$model,
                'readonly'=>($model->scenario=='view'||$model->state == 1||$model->state == 3),
            ));
            ?>


        </div>
    </div>
</section>


<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'RPRI',
    'header'=>Yii::t('dialog','File Attachment'),
    'ronly'=>($model->scenario=='view'||$model->state == 1||$model->state == 3),
));
?>
<?php
$this->renderPartial('//site/ject',array('model'=>$model,'form'=>$form,'rejectName'=>"reject_note",'submit'=>Yii::app()->createUrl('auditPrize/reject')));
?>
<?php
$this->renderPartial('//site/removedialog');
?>
<?php
Script::genFileUpload($model,$form->id,'RPRI');

$js = Script::genDeleteData(Yii::app()->createUrl('auditPrize/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

