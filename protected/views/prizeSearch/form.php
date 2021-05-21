<?php
$this->pageTitle=Yii::app()->name . ' - prizeSearch Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'prizeSearch-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('integral','Apply Prize Form'); ?></strong>
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
                    'submit'=>Yii::app()->createUrl('prizeSearch/index')));
                ?>
            </div>
            <div class="btn-group pull-right" role="group">
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

            <?php
            $this->renderPartial('//site/prizeAddForm',array(
                'form'=>$form,
                'model'=>$model,
                'readonly'=>(true),
            ));
            ?>

            <legend><?php echo Yii::t("app","Credit deduction details")?></legend>

            <div class="form-group">
                <div class="col-lg-10 col-lg-offset-1">
                    <?php
                    //獎項扣除明細
                    echo $model->creditClone();
                    ?>
                </div>
            </div>

        </div>
    </div>
</section>


<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
    'form'=>$form,
    'doctype'=>'RPRI',
    'header'=>Yii::t('dialog','File Attachment'),
    'ronly'=>(true),
));
?>
<?php
Script::genFileUpload($model,$form->id,'RPRI');

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

