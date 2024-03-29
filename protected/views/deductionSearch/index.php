<?php
$this->pageTitle=Yii::app()->name . ' - Credit deduction details';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'deductionSearch-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Credit deduction details'); ?></strong>
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
    <?php
    $search = array(
        'city_name',
        'employee_name',
    );
    $search_add_html="";
    $modelName = get_class($model);
    $search_add_html .= TbHtml::dropDownList($modelName.'[category]',$model->category,$model->getCategoryAll(),array("class"=>"form-control submit_category"));

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('app','Credit deduction details'),
        'model'=>$model,
        'viewhdr'=>'//deductionSearch/_listhdr',
        'viewdtl'=>'//deductionSearch/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search_add_html'=>$search_add_html,
        'search'=>$search,
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
    $('.submit_category').on('change',function(){
        $('form:first').submit();
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

