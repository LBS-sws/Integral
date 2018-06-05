<?php
$this->pageTitle=Yii::app()->name . ' - Credit type allocation';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'sumSearch-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Total credit search'); ?></strong>
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
    <div class="box">
    </div>
    <?php
    $search = array(
        'activity_name',
        'integral',
        'city_name',
        'employee_name',
    );
    $search_add_html="";
    $modelName = get_class($model);
    $search_add_html .= TbHtml::dropDownList($modelName.'[year]',$model->year,ReportY03Form::getYearList(),array("class"=>"form-control"));

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('integral','Credit list'),
        'model'=>$model,
        'viewhdr'=>'//sumSearch/_listhdr',
        'viewdtl'=>'//sumSearch/_listdtl',
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
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

