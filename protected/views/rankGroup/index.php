<?php
$this->pageTitle=Yii::app()->name . ' - Dezhi body group beauty';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'rankGroup-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Dezhi body group beauty Top20'); ?></strong>
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
    <!--

    <div class="box">
        <div class="box-body text-right">
            <div class="btn-group" role="group">
                <?php
    echo TbHtml::button('<span class="fa fa-download"></span> '.Yii::t('integral','export'), array(
        'submit'=>Yii::app()->createUrl('rankGroup/export'),
    ));
    ?>
            </div>
        </div>
    </div>
    -->
    <?php
    $modelName = get_class($model);
    $search_add_html = TbHtml::dropDownList($modelName.'[year]',$model->year,$model->getYearList(),array("class"=>"changeCategory"));
    $search_add_html .= TbHtml::dropDownList($modelName.'[category]',$model->category,CreditTypeForm::getCategoryAll(),array("class"=>"changeCategory"));
    $search = array(
            "employee_code",
            "employee_name"
    );

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('integral','Credit list'),
        'model'=>$model,
        'viewhdr'=>'//rankGroup/_listhdr',
        'viewdtl'=>'//rankGroup/_listdtl',
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
$js="
$('.changeCategory').on('change',function(){
    jQuery.yii.submitForm(this,'".Yii::app()->createUrl('rankGroup/index',array("pageNum"=>1))."',{});
    return false;
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

