<?php
$this->pageTitle=Yii::app()->name . ' - confirmCredit Info';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'confirmCredit-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Confirm review'); ?></strong>
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
    <?php if (Yii::app()->user->validFunction('GA04')): ?>
        <div class="box">
            <div class="box-body">
                <div class="btn-group" role="group">
                    <?php
                    echo TbHtml::button('<span class="fa fa-glass"></span> '.Yii::t('integral','batch confirm'), array(
                        'submit'=>Yii::app()->createUrl('confirmCredit/batch'),
                    ));
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
	<?php $this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('app','Confirm review'),
			'model'=>$model,
				'viewhdr'=>'//confirmCredit/_listhdr',
				'viewdtl'=>'//confirmCredit/_listdtl',
				'search'=>array(
                    'credit_name',
                    'credit_point',
                    'employee_name',
                    'city_name',
				),
		));
	?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>

<?php
$js = "
$('.che').on('click', function(e){
e.stopPropagation();
});

$('body').on('click','#all',function() {
	var val = $(this).prop('checked');
	$('input[type=checkbox][name*=\"confirmCreditList[attr][]\"]').prop('checked',val);
});
";
Yii::app()->clientScript->registerScript('selectAll',$js,CClientScript::POS_READY);
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

